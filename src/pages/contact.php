<?php
$meta_title = 'Kontakt | FinRank';
$meta_desc  = 'Skontaktuj się z redakcją FinRank. Masz pytanie o ranking lub chcesz zgłosić błąd? Napisz do nas.';
$sent = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once dirname(__DIR__) . '/newsletter.php';
    $name    = htmlspecialchars(trim($_POST['name'] ?? ''));
    $email   = trim($_POST['email'] ?? '');
    $message = htmlspecialchars(trim($_POST['message'] ?? ''));

    if ($name && filter_var($email, FILTER_VALIDATE_EMAIL) && strlen($message) > 10) {
        $body = "<p><strong>Od:</strong> $name ($email)</p><p><strong>Wiadomość:</strong><br>$message</p>";
        newsletter_send_mail(getenv('SMTP_USER') ?: 'redakcja@finrank.pl', "Kontakt FinRank: $name", $body);
        $sent = true;
    } else {
        $error = 'Wypełnij wszystkie pola poprawnie.';
    }
}

require_once dirname(__DIR__) . '/templates/head.php';
require_once dirname(__DIR__) . '/templates/navbar.php';
?>

<section class="section-sm">
  <div class="container" style="max-width:700px">
    <nav class="breadcrumbs">
      <a href="/">Strona główna</a>
      <span class="sep">›</span>
      <span class="current">Kontakt</span>
    </nav>

    <h1 style="margin-bottom:.5rem">Kontakt</h1>
    <p style="margin-bottom:3rem">Masz pytanie, chcesz zgłosić błąd lub zaproponować współpracę? Napisz do nas.</p>

    <?php if ($sent): ?>
      <div class="info-box success">
        <div class="info-box-icon">✅</div>
        <p style="margin:0">Dziękujemy za wiadomość! Odpiszemy w ciągu 24–48 godzin.</p>
      </div>
    <?php else: ?>

    <?php if ($error): ?>
      <div class="info-box warning mb-3">
        <div class="info-box-icon">⚠️</div>
        <p style="margin:0"><?= $error ?></p>
      </div>
    <?php endif; ?>

    <form method="post" style="display:flex;flex-direction:column;gap:1.25rem">
      <div>
        <label style="font-weight:600;display:block;margin-bottom:.4rem">Imię i nazwisko</label>
        <input type="text" name="name" required placeholder="Jan Kowalski"
               style="width:100%;background:var(--bg-card2);border:1px solid var(--border);color:var(--text);padding:.75rem 1rem;border-radius:var(--radius-sm);font-size:.95rem">
      </div>
      <div>
        <label style="font-weight:600;display:block;margin-bottom:.4rem">Adres email</label>
        <input type="email" name="email" required placeholder="jan@example.com"
               style="width:100%;background:var(--bg-card2);border:1px solid var(--border);color:var(--text);padding:.75rem 1rem;border-radius:var(--radius-sm);font-size:.95rem">
      </div>
      <div>
        <label style="font-weight:600;display:block;margin-bottom:.4rem">Wiadomość</label>
        <textarea name="message" required rows="6" placeholder="Napisz do nas..."
                  style="width:100%;background:var(--bg-card2);border:1px solid var(--border);color:var(--text);padding:.75rem 1rem;border-radius:var(--radius-sm);font-size:.95rem;resize:vertical"></textarea>
      </div>
      <div>
        <p style="font-size:.8rem;color:var(--text-dim);margin-bottom:.75rem">
          Wysyłając wiadomość akceptujesz <a href="/polityka-prywatnosci">Politykę Prywatności</a>.
          Twoje dane nie będą przekazywane osobom trzecim.
        </p>
        <button type="submit" class="btn btn-gold btn-lg">Wyślij wiadomość →</button>
      </div>
    </form>

    <!-- Newsletter signup in contact page -->
    <div class="card card-body mt-5" style="background:var(--bg-card2)">
      <h3 style="margin-bottom:.5rem">📬 Zapisz się do newslettera</h3>
      <p style="font-size:.9rem;margin-bottom:1rem">Co miesiąc — nowe promocje bankowe, rankingi i poradniki. Zero spamu.</p>
      <form action="/newsletter/subscribe" method="post" class="flex gap-2" style="flex-wrap:wrap">
        <input type="hidden" name="source" value="contact_page">
        <input type="email" name="email" placeholder="Twój email" required
               style="flex:1;min-width:200px;background:var(--bg-card);border:1px solid var(--border);color:var(--text);padding:.65rem 1rem;border-radius:var(--radius-sm);font-size:.9rem">
        <button type="submit" class="btn btn-gold">Zapisz się</button>
      </form>
    </div>

    <?php endif; ?>
  </div>
</section>

<?php require_once dirname(__DIR__) . '/templates/footer.php'; ?>
