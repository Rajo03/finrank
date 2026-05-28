<?php
/**
 * Newsletter manager
 * - subscribe()   — zapisuje email do bazy
 * - send_campaign() — wysyła kampanię do wszystkich subskrybentów
 */

define('NEWSLETTER_DB', dirname(__DIR__) . '/data/newsletter.db');

function newsletter_db(): PDO {
    $pdo = new PDO('sqlite:' . NEWSLETTER_DB);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS subscribers (
            id         INTEGER PRIMARY KEY AUTOINCREMENT,
            email      TEXT UNIQUE NOT NULL,
            source     TEXT DEFAULT 'form',
            confirmed  INTEGER DEFAULT 0,
            token      TEXT,
            created_at TEXT DEFAULT (datetime('now')),
            unsubbed_at TEXT
        );
        CREATE TABLE IF NOT EXISTS campaigns (
            id         INTEGER PRIMARY KEY AUTOINCREMENT,
            subject    TEXT,
            body_html  TEXT,
            sent_count INTEGER DEFAULT 0,
            sent_at    TEXT DEFAULT (datetime('now'))
        );
    ");
    return $pdo;
}

function newsletter_subscribe(string $email, string $source = 'form'): array {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['ok' => false, 'msg' => 'Nieprawidłowy adres email.'];
    }

    $db    = newsletter_db();
    $token = bin2hex(random_bytes(16));

    $stmt = $db->prepare("
        INSERT INTO subscribers (email, source, token, confirmed)
        VALUES (:email, :source, :token, 0)
        ON CONFLICT(email) DO UPDATE SET
            source = excluded.source,
            token  = excluded.token
    ");
    $stmt->execute([':email' => strtolower(trim($email)), ':source' => $source, ':token' => $token]);

    // Send confirmation email
    newsletter_send_confirmation($email, $token);

    return ['ok' => true, 'msg' => 'Sprawdź skrzynkę i potwierdź zapis.'];
}

function newsletter_confirm(string $token): bool {
    $db   = newsletter_db();
    $stmt = $db->prepare("UPDATE subscribers SET confirmed=1, token=NULL WHERE token=:token AND confirmed=0");
    $stmt->execute([':token' => $token]);
    return $stmt->rowCount() > 0;
}

function newsletter_unsubscribe(string $token): bool {
    $db   = newsletter_db();
    $stmt = $db->prepare("UPDATE subscribers SET unsubbed_at=datetime('now'), confirmed=0 WHERE token=:token OR email=:token");
    $stmt->execute([':token' => $token]);
    return $stmt->rowCount() > 0;
}

function newsletter_send_confirmation(string $email, string $token): void {
    $confirm_url = SITE_DOMAIN . '/newsletter/confirm?token=' . urlencode($token);
    $subject     = 'Potwierdź zapis do newslettera FinRank';
    $body        = "
    <div style='font-family:Inter,sans-serif;max-width:520px;margin:0 auto;background:#111827;color:#f1f5f9;padding:2rem;border-radius:12px'>
      <div style='font-size:1.4rem;font-weight:800;color:#f0b429;margin-bottom:1rem'>FinRank</div>
      <h2 style='margin-bottom:.75rem'>Potwierdź swój zapis</h2>
      <p style='color:#94a3b8;margin-bottom:1.5rem'>
        Kliknij poniższy przycisk, aby potwierdzić zapis do newslettera FinRank i zaczać otrzymywać co miesięczny przegląd najlepszych ofert finansowych.
      </p>
      <a href='$confirm_url' style='display:inline-block;background:#f0b429;color:#000;padding:.75rem 2rem;border-radius:8px;font-weight:700;text-decoration:none'>
        Potwierdź zapis →
      </a>
      <p style='color:#64748b;font-size:.8rem;margin-top:1.5rem'>
        Jeśli nie zapisywałeś/aś się na nasz newsletter, zignoruj tę wiadomość.
      </p>
    </div>";

    newsletter_send_mail($email, $subject, $body);
}

function newsletter_send_campaign(string $subject, string $body_html): int {
    $db   = newsletter_db();
    $rows = $db->query("SELECT email FROM subscribers WHERE confirmed=1 AND unsubbed_at IS NULL")->fetchAll(PDO::FETCH_COLUMN);

    $sent = 0;
    foreach ($rows as $email) {
        $unsub_url    = SITE_DOMAIN . '/newsletter/unsubscribe?email=' . urlencode($email);
        $personalized = $body_html . "
        <div style='border-top:1px solid #1e2d45;margin-top:2rem;padding-top:1rem;font-size:.75rem;color:#64748b;font-family:sans-serif'>
          Otrzymujesz ten email ponieważ zapisałeś/aś się na newsletter FinRank.<br>
          <a href='$unsub_url' style='color:#64748b'>Wypisz się</a> |
          <a href='" . SITE_DOMAIN . "' style='color:#64748b'>finrank.pl</a>
        </div>";

        if (newsletter_send_mail($email, $subject, $personalized)) {
            $sent++;
        }
    }

    $db->prepare("INSERT INTO campaigns (subject, body_html, sent_count) VALUES (?,?,?)")
       ->execute([$subject, $body_html, $sent]);

    return $sent;
}

function newsletter_send_mail(string $to, string $subject, string $body): bool {
    // Use PHP mail() or configure SMTP via settings
    $smtp_host = getenv('SMTP_HOST') ?: '';
    $smtp_user = getenv('SMTP_USER') ?: '';
    $smtp_pass = getenv('SMTP_PASSWORD') ?: '';
    $smtp_port = (int)(getenv('SMTP_PORT') ?: 587);
    $from      = getenv('SMTP_FROM') ?: ($smtp_user ?: 'noreply@finrank.pl');

    if ($smtp_host && $smtp_user) {
        return newsletter_smtp($to, $subject, $body, $smtp_host, $smtp_port, $smtp_user, $smtp_pass, $from);
    }

    // Fallback: PHP mail()
    $headers  = "From: FinRank <$from>\r\n";
    $headers .= "Reply-To: $from\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    return mail($to, $subject, $body, $headers);
}

function newsletter_smtp(string $to, string $subject, string $body, string $host, int $port, string $user, string $pass, string $from): bool {
    try {
        $sock = fsockopen(($port === 465 ? 'ssl://' : '') . $host, $port, $errno, $errstr, 15);
        if (!$sock) return false;

        $read = fn() => fgets($sock, 512);
        $send = fn(string $cmd) => fputs($sock, $cmd . "\r\n");

        $read(); // greeting
        $send("EHLO finrank.pl"); while (($r = $read()) && substr($r,3,1) === '-');
        if ($port !== 465) {
            $send("STARTTLS"); $read();
            stream_socket_enable_crypto($sock, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
            $send("EHLO finrank.pl"); while (($r = $read()) && substr($r,3,1) === '-');
        }
        $send("AUTH LOGIN"); $read();
        $send(base64_encode($user)); $read();
        $send(base64_encode($pass)); $read();
        $send("MAIL FROM:<$from>"); $read();
        $send("RCPT TO:<$to>"); $read();
        $send("DATA"); $read();

        $msg  = "From: FinRank <$from>\r\n";
        $msg .= "To: $to\r\n";
        $msg .= "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=\r\n";
        $msg .= "MIME-Version: 1.0\r\nContent-Type: text/html; charset=UTF-8\r\n\r\n";
        $msg .= $body . "\r\n.";
        $send($msg); $read();
        $send("QUIT"); fclose($sock);
        return true;
    } catch (\Throwable $e) {
        return false;
    }
}

function newsletter_stats(): array {
    $db = newsletter_db();
    return [
        'total'     => (int)$db->query("SELECT COUNT(*) FROM subscribers WHERE confirmed=1 AND unsubbed_at IS NULL")->fetchColumn(),
        'pending'   => (int)$db->query("SELECT COUNT(*) FROM subscribers WHERE confirmed=0")->fetchColumn(),
        'unsubbed'  => (int)$db->query("SELECT COUNT(*) FROM subscribers WHERE unsubbed_at IS NOT NULL")->fetchColumn(),
        'campaigns' => (int)$db->query("SELECT COUNT(*) FROM campaigns")->fetchColumn(),
    ];
}
