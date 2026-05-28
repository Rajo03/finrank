<?php
require_once dirname(__DIR__) . '/newsletter.php';

$msg = '';
$ok  = false;

if ($action === 'newsletter_subscribe') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email  = trim($_POST['email'] ?? '');
        $result = newsletter_subscribe($email, $_POST['source'] ?? 'form');
        $msg    = $result['msg'];
        $ok     = $result['ok'];
    }
    if ($ok) {
        $meta_title = 'Zapisano! Sprawdź email | FinRank Newsletter';
        $meta_desc  = 'Dziękujemy za zapis do newslettera FinRank. Sprawdź skrzynkę i potwierdź zapis.';
    } else {
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'), true, 302);
        exit;
    }
}

if ($action === 'newsletter_confirm') {
    $token = $_GET['token'] ?? '';
    $ok    = newsletter_confirm($token);
    $meta_title = $ok ? 'Email potwierdzony! | FinRank' : 'Link wygasł | FinRank';
    $meta_desc  = $ok ? 'Twój zapis do newslettera FinRank został potwierdzony.' : 'Link potwierdzający wygasł lub jest nieprawidłowy.';
}

if ($action === 'newsletter_unsubscribe') {
    $token = $_GET['email'] ?? $_GET['token'] ?? '';
    $ok    = newsletter_unsubscribe($token);
    $meta_title = 'Wypisano z newslettera | FinRank';
    $meta_desc  = 'Zostałeś/aś wypisany/a z newslettera FinRank.';
}

require_once dirname(__DIR__) . '/templates/head.php';
require_once dirname(__DIR__) . '/templates/navbar.php';
?>

<section class="section" style="text-align:center;min-height:60vh;display:flex;align-items:center">
  <div class="container">
    <?php if ($action === 'newsletter_subscribe' && $ok): ?>
      <div style="font-size:4rem;margin-bottom:1rem">📬</div>
      <h1 style="margin-bottom:1rem">Sprawdź swoją skrzynkę!</h1>
      <p style="max-width:480px;margin:0 auto 2rem">Wysłaliśmy Ci email z linkiem potwierdzającym. Kliknij go, żeby aktywować zapis do newslettera FinRank.</p>
      <a href="/" class="btn btn-gold">Wróć do strony głównej →</a>
    <?php elseif ($action === 'newsletter_confirm'): ?>
      <div style="font-size:4rem;margin-bottom:1rem"><?= $ok ? '🎉' : '⚠️' ?></div>
      <h1 style="margin-bottom:1rem"><?= $ok ? 'Zapis potwierdzony!' : 'Link wygasł' ?></h1>
      <p style="max-width:480px;margin:0 auto 2rem">
        <?= $ok
          ? 'Witamy w FinRank Newsletter! Będziesz co miesiąc otrzymywać przegląd najlepszych ofert finansowych.'
          : 'Ten link jest nieprawidłowy lub już wygasł. Spróbuj zapisać się ponownie.' ?>
      </p>
      <a href="/" class="btn btn-gold">Przejdź do rankingów →</a>
    <?php elseif ($action === 'newsletter_unsubscribe'): ?>
      <div style="font-size:4rem;margin-bottom:1rem">👋</div>
      <h1 style="margin-bottom:1rem">Wypisano z newslettera</h1>
      <p style="max-width:480px;margin:0 auto 2rem">Twój adres email został usunięty z listy mailingowej FinRank. Nie będziesz już otrzymywać naszych wiadomości.</p>
      <a href="/" class="btn btn-outline">Wróć do strony głównej</a>
    <?php endif; ?>
  </div>
</section>

<?php require_once dirname(__DIR__) . '/templates/footer.php'; ?>
