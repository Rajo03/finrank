<?php
$cat      = CATEGORIES[$cat_slug];
$products = array_values(array_filter(PRODUCTS, fn($p) => $p['category'] === $cat_slug));

$meta_title = $cat['meta_title'];
$meta_desc  = $cat['meta_desc'];
$canonical  = SITE_DOMAIN . '/' . $cat_slug;

// Schema: ItemList
$schema_items = [];
foreach ($products as $i => $p) {
    $schema_items[] = [
        '@type'    => 'ListItem',
        'position' => $i + 1,
        'name'     => $p['name'],
        'url'      => SITE_DOMAIN . '/' . $cat_slug . '/' . $p['slug'],
    ];
}
$extra_head = '<script type="application/ld+json">' . json_encode([
    '@context'        => 'https://schema.org',
    '@type'           => 'ItemList',
    'name'            => $cat['name'] . ' – Ranking ' . date('Y'),
    'description'     => $cat['description'],
    'numberOfItems'   => count($products),
    'itemListElement' => $schema_items,
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';

require_once dirname(__DIR__) . '/templates/head.php';
require_once dirname(__DIR__) . '/templates/navbar.php';
require_once dirname(__DIR__) . '/templates/product_card.php';
?>

<section class="section-sm">
  <div class="container">

    <!-- Breadcrumbs -->
    <nav class="breadcrumbs" aria-label="Breadcrumb">
      <a href="/">Strona główna</a>
      <span class="sep">›</span>
      <span class="current"><?= htmlspecialchars($cat['name']) ?></span>
    </nav>

    <!-- Header -->
    <div style="display:flex;align-items:center;gap:1rem;margin-bottom:2rem;flex-wrap:wrap">
      <span style="font-size:3rem"><?= $cat['icon'] ?></span>
      <div>
        <h1 style="margin-bottom:.25rem">Ranking: <?= htmlspecialchars($cat['name']) ?> <?= date('Y') ?></h1>
        <p><?= htmlspecialchars($cat['description']) ?></p>
      </div>
    </div>

    <div class="info-box mb-4">
      <div class="info-box-icon">ℹ️</div>
      <p>Ranking aktualizowany w <?= date('F Y') ?>. Oceniamy produkty na podstawie warunków, opinii użytkowników i bezpieczeństwa.</p>
    </div>

    <!-- Products list -->
    <div style="display:flex;flex-direction:column;gap:1.25rem">
      <?php foreach ($products as $i => $p): ?>
        <?= render_product_card($p, $i + 1, $i === 0) ?>
      <?php endforeach; ?>
    </div>

    <!-- FAQ section -->
    <div class="mt-5">
      <h2 class="mb-3">Najczęstsze pytania — <?= htmlspecialchars($cat['name']) ?></h2>
      <?php
      $faqs = [];
      if ($cat_slug === 'kryptowaluty') $faqs = [
          ['Która giełda kryptowalut jest najlepsza?',  'Według naszego rankingu najlepszą giełdą jest Binance — oferuje najniższe prowizje (0.1%), największy wybór kryptowalut (350+) i zaawansowane narzędzia tradingowe.'],
          ['Czy giełdy kryptowalut są bezpieczne?',     'Renomowane giełdy jak Binance czy Coinbase są bezpieczne dla większości użytkowników. Zawsze włącz uwierzytelnianie 2FA i nie przechowuj dużych kwot na giełdzie długoterminowo.'],
          ['Jak kupić Bitcoin w Polsce?',               'Najłatwiej kupić Bitcoin na giełdzie Zonda (możesz zapłacić w PLN) lub Binance. Wymagana jest rejestracja i weryfikacja tożsamości (KYC).'],
      ];
      if ($cat_slug === 'pozyczki') $faqs = [
          ['Która pożyczka jest najtańsza?',            'Najtańsza pierwsza pożyczka to Vivus — nowi klienci pożyczają do 3000 zł za darmo (0% kosztów). Porównaj RRSO przed podpisaniem umowy.'],
          ['Jak szybko dostanę pieniądze?',             'Najszybsze firmy pożyczkowe jak Smart Pożyczka przelewają pieniądze w 5-15 minut od decyzji kredytowej, całą dobę.'],
          ['Czy pożyczka bez BIK jest możliwa?',        'Tak, niektóre firmy jak Smart Pożyczka nie weryfikują historii w BIK. Jednak zawsze sprawdzają inne bazy dłużników.'],
      ];
      if ($cat_slug === 'konta-bankowe') $faqs = [
          ['Które konto bankowe jest najlepsze?',       'Według rankingu najlepszym kontem jest mBank eKonto — zero opłat, premia 300 zł na start i jedna z najlepszych aplikacji mobilnych.'],
          ['Jak założyć konto bankowe online?',         'Wszystkie konta w rankingu można założyć w 100% online. Potrzebujesz dowodu osobistego i numeru telefonu. Weryfikacja zajmuje 5-10 minut.'],
          ['Czy konto bankowe jest bezpłatne?',         'Wiele kont jest bezpłatnych pod warunkiem spełnienia warunków aktywności (np. 3 transakcje kartą miesięcznie). Szczegóły sprawdź w recenzji każdego konta.'],
      ];
      ?>
      <div style="display:flex;flex-direction:column;gap:.75rem">
        <?php foreach ($faqs as $faq): ?>
        <details class="card">
          <summary class="card-body" style="cursor:pointer;font-weight:600;list-style:none;display:flex;justify-content:space-between;align-items:center">
            <?= htmlspecialchars($faq[0]) ?>
            <span style="color:var(--gold);flex-shrink:0;margin-left:1rem">+</span>
          </summary>
          <div style="padding:0 1.5rem 1.25rem;color:var(--text-muted);font-size:.9rem"><?= htmlspecialchars($faq[1]) ?></div>
        </details>
        <?php endforeach; ?>
      </div>
    </div>

  </div>
</section>

<?php require_once dirname(__DIR__) . '/templates/footer.php'; ?>
