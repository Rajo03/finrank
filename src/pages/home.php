<?php
$meta_title = 'FinRank – Rankingi Finansowe 2025 | Kryptowaluty, Pożyczki, Konta';
$meta_desc  = 'FinRank to niezależny portal z rankingami produktów finansowych. Porównaj giełdy kryptowalut, pożyczki online i konta bankowe. Aktualny ranking 2025.';
require_once dirname(__DIR__) . '/templates/head.php';
require_once dirname(__DIR__) . '/templates/navbar.php';
require_once dirname(__DIR__) . '/templates/product_card.php';
$products = PRODUCTS;
$categories = CATEGORIES;
?>

<!-- HERO -->
<section class="hero">
  <div class="container" style="position:relative;z-index:1">
    <div class="hero-badge">⚡ Aktualizacja rankingów: <?= date('F Y') ?></div>
    <h1 class="hero-title">
      Znajdź <span>najlepsze</span><br>produkty finansowe
    </h1>
    <p class="hero-desc">
      Niezależne rankingi giełd kryptowalut, pożyczek i kont bankowych.
      Sprawdzone opinie i aktualne oferty w jednym miejscu.
    </p>
    <div class="flex gap-2" style="justify-content:center;flex-wrap:wrap">
      <a href="/kryptowaluty" class="btn btn-gold btn-lg">Giełdy kryptowalut →</a>
      <a href="/pozyczki" class="btn btn-outline btn-lg">Ranking pożyczek</a>
    </div>
    <div class="hero-stats">
      <div>
        <div class="hero-stat-num">9</div>
        <div class="hero-stat-label">Produktów w rankingu</div>
      </div>
      <div>
        <div class="hero-stat-num">3</div>
        <div class="hero-stat-label">Kategorie finansowe</div>
      </div>
      <div>
        <div class="hero-stat-num">100%</div>
        <div class="hero-stat-label">Niezależne recenzje</div>
      </div>
    </div>
  </div>
</section>

<!-- KATEGORIE -->
<section class="section-sm">
  <div class="container">
    <div class="section-header text-center">
      <div class="section-label">Kategorie</div>
      <h2 class="section-title">Wybierz kategorię</h2>
    </div>
    <div class="grid-3">
      <?php foreach ($categories as $slug => $cat): ?>
      <a href="/<?= $slug ?>" class="cat-card">
        <span class="cat-icon"><?= $cat['icon'] ?></span>
        <div class="cat-name"><?= htmlspecialchars($cat['name']) ?></div>
        <div class="cat-desc"><?= htmlspecialchars($cat['description']) ?></div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- TOP KRYPTOWALUTY -->
<section class="section" style="background:linear-gradient(180deg,transparent,rgba(247,147,26,.04),transparent)">
  <div class="container">
    <div class="flex-between mb-4">
      <div>
        <div class="section-label">₿ Kryptowaluty</div>
        <h2 class="section-title" style="margin-bottom:.5rem">Ranking giełd kryptowalut</h2>
        <p>Porównaj najlepsze platformy do handlu Bitcoin i altcoinami</p>
      </div>
      <a href="/kryptowaluty" class="btn btn-outline hide-mobile">Zobacz wszystkie →</a>
    </div>
    <div style="display:flex;flex-direction:column;gap:1rem">
      <?php
      $crypto = array_filter($products, fn($p) => $p['category'] === 'kryptowaluty');
      $rank = 1;
      foreach ($crypto as $p):
        echo render_product_card($p, $rank, $rank === 1);
        $rank++;
      endforeach;
      ?>
    </div>
    <div class="text-center mt-4">
      <a href="/kryptowaluty" class="btn btn-outline">Pełny ranking giełd kryptowalut →</a>
    </div>
  </div>
</section>

<!-- TOP POŻYCZKI -->
<section class="section">
  <div class="container">
    <div class="flex-between mb-4">
      <div>
        <div class="section-label">💳 Pożyczki</div>
        <h2 class="section-title" style="margin-bottom:.5rem">Ranking pożyczek online</h2>
        <p>Sprawdź które chwilówki mają najniższe RRSO i najszybszą decyzję</p>
      </div>
      <a href="/pozyczki" class="btn btn-outline hide-mobile">Zobacz wszystkie →</a>
    </div>
    <div style="display:flex;flex-direction:column;gap:1rem">
      <?php
      $loans = array_filter($products, fn($p) => $p['category'] === 'pozyczki');
      $rank = 1;
      foreach ($loans as $p):
        echo render_product_card($p, $rank, $rank === 1);
        $rank++;
      endforeach;
      ?>
    </div>
    <div class="text-center mt-4">
      <a href="/pozyczki" class="btn btn-outline">Pełny ranking pożyczek →</a>
    </div>
  </div>
</section>

<!-- TOP KONTA -->
<section class="section" style="background:linear-gradient(180deg,transparent,rgba(99,102,241,.04),transparent)">
  <div class="container">
    <div class="flex-between mb-4">
      <div>
        <div class="section-label">🏦 Konta bankowe</div>
        <h2 class="section-title" style="margin-bottom:.5rem">Ranking kont bankowych</h2>
        <p>Które konto płaci premię za założenie i ma zerowe opłaty?</p>
      </div>
      <a href="/konta-bankowe" class="btn btn-outline hide-mobile">Zobacz wszystkie →</a>
    </div>
    <div style="display:flex;flex-direction:column;gap:1rem">
      <?php
      $banks = array_filter($products, fn($p) => $p['category'] === 'konta-bankowe');
      $rank = 1;
      foreach ($banks as $p):
        echo render_product_card($p, $rank, $rank === 1);
        $rank++;
      endforeach;
      ?>
    </div>
    <div class="text-center mt-4">
      <a href="/konta-bankowe" class="btn btn-outline">Pełny ranking kont bankowych →</a>
    </div>
  </div>
</section>

<!-- WHY US -->
<section class="section-sm">
  <div class="container">
    <div class="section-header text-center">
      <div class="section-label">Dlaczego FinRank</div>
      <h2 class="section-title">Niezależne rankingi finansowe</h2>
    </div>
    <div class="grid-3">
      <?php foreach ([
        ['🔍','Niezależna analiza','Każdy produkt oceniamy według tych samych kryteriów. Bez sponsorowania wyników.'],
        ['🔄','Aktualizowane na bieżąco','Rankingi są aktualizowane co miesiąc. Zawsze masz dostęp do aktualnych ofert.'],
        ['⭐','Prawdziwe opinie','Gromadzimy opinie prawdziwych użytkowników produktów finansowych.'],
      ] as [$icon, $title, $desc]): ?>
      <div class="card card-body text-center">
        <div style="font-size:2rem;margin-bottom:.75rem"><?= $icon ?></div>
        <h4 style="margin-bottom:.5rem"><?= $title ?></h4>
        <p style="font-size:.875rem"><?= $desc ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php require_once dirname(__DIR__) . '/templates/footer.php'; ?>
