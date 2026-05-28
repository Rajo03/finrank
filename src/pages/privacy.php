<?php
$meta_title = 'Polityka Prywatności | FinRank';
$meta_desc  = 'Polityka prywatności serwisu FinRank. Dowiedz się jak przetwarzamy Twoje dane osobowe zgodnie z RODO.';
require_once dirname(__DIR__) . '/templates/head.php';
require_once dirname(__DIR__) . '/templates/navbar.php';
?>

<section class="section-sm">
  <div class="container" style="max-width:800px">
    <nav class="breadcrumbs">
      <a href="/">Strona główna</a>
      <span class="sep">›</span>
      <span class="current">Polityka prywatności</span>
    </nav>

    <h1 style="margin-bottom:.5rem">Polityka Prywatności</h1>
    <p style="margin-bottom:3rem;color:var(--text-muted)">Ostatnia aktualizacja: <?= date('d.m.Y') ?></p>

    <?php
    $sections = [
        ['Administrator danych', 'Administratorem danych osobowych jest FinRank (finrank.pl). W sprawach związanych z przetwarzaniem danych możesz skontaktować się z nami przez formularz kontaktowy.'],
        ['Jakie dane zbieramy', "Zbieramy wyłącznie dane które sam/a nam przekazujesz:\n• Adres email przy zapisie do newslettera\n• Imię, email i treść wiadomości przy kontakcie\nNie zbieramy danych wrażliwych. Nie profilujemy użytkowników."],
        ['Cel i podstawa przetwarzania', "Email z newslettera: wysyłka co miesięcznego przeglądu ofert finansowych — podstawa: Twoja zgoda (Art. 6 ust. 1 lit. a RODO). Możesz wycofać zgodę w każdej chwili klikając link wypisz.\n\nDane z formularza kontaktowego: obsługa zapytania — podstawa: prawnie uzasadniony interes (Art. 6 ust. 1 lit. f RODO)."],
        ['Linki afiliacyjne', 'Serwis zawiera linki afiliacyjne do produktów finansowych (m.in. MyLead). Kliknięcie linku może spowodować zapisanie cookie afiliacyjnego na Twoim urządzeniu przez firmę partnerską. FinRank może otrzymać wynagrodzenie za polecenie produktu, co nie wpływa na niezależność rankingów.'],
        ['Prawa użytkownika', "Masz prawo do:\n• Dostępu do swoich danych (Art. 15 RODO)\n• Sprostowania danych (Art. 16 RODO)\n• Usunięcia danych (Art. 17 RODO)\n• Przenoszenia danych (Art. 20 RODO)\n• Wycofania zgody w dowolnym momencie\n\nAby skorzystać z praw, skontaktuj się przez formularz kontaktowy."],
        ['Jak długo przechowujemy dane', 'Adresy email subskrybentów przechowujemy do momentu wypisania się z newslettera. Dane z formularza kontaktowego usuwamy po 12 miesiącach od rozwiązania sprawy.'],
        ['Pliki cookie', 'Serwis używa minimalnej liczby plików cookie niezbędnych do działania strony. Nie używamy cookies śledzących ani remarketingowych. Partnerzy afiliacyjni mogą używać własnych cookies — ich politykę prywatności znajdziesz na ich stronach.'],
        ['Zmiany polityki', 'Zastrzegamy prawo do zmiany polityki prywatności. O istotnych zmianach poinformujemy subskrybentów newslettera. Data ostatniej aktualizacji jest widoczna na górze strony.'],
    ];
    foreach ($sections as [$title, $content]):
    ?>
    <div style="margin-bottom:2.5rem">
      <h2 style="font-size:1.2rem;margin-bottom:.75rem"><?= htmlspecialchars($title) ?></h2>
      <div style="color:var(--text-muted);line-height:1.8;white-space:pre-line;font-size:.95rem"><?= htmlspecialchars($content) ?></div>
    </div>
    <?php endforeach; ?>

  </div>
</section>

<?php require_once dirname(__DIR__) . '/templates/footer.php'; ?>
