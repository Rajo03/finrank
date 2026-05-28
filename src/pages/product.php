<?php
$p        = PRODUCTS[$slug];
$cat      = CATEGORIES[$cat_slug];
$products = array_values(array_filter(PRODUCTS, fn($x) => $x['category'] === $cat_slug));

$meta_title = $p['meta_title'];
$meta_desc  = $p['meta_desc'];
$canonical  = SITE_DOMAIN . '/' . $cat_slug . '/' . $slug;

// Schema: FinancialProduct + Review + BreadcrumbList
$schema = [
    '@context' => 'https://schema.org',
    '@graph'   => [
        [
            '@type'       => 'FinancialProduct',
            'name'        => $p['name'],
            'description' => $p['description'],
            'url'         => $canonical,
            'aggregateRating' => [
                '@type'       => 'AggregateRating',
                'ratingValue' => $p['rating'],
                'reviewCount' => $p['reviews'],
                'bestRating'  => 5,
                'worstRating' => 1,
            ],
        ],
        [
            '@type'    => 'BreadcrumbList',
            'itemListElement' => [
                ['@type'=>'ListItem','position'=>1,'name'=>'Strona główna','item'=>SITE_DOMAIN],
                ['@type'=>'ListItem','position'=>2,'name'=>$cat['name'],'item'=>SITE_DOMAIN.'/'.$cat_slug],
                ['@type'=>'ListItem','position'=>3,'name'=>$p['name'].' Recenzja','item'=>$canonical],
            ],
        ],
    ],
];
$extra_head = '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';

require_once dirname(__DIR__) . '/templates/head.php';
require_once dirname(__DIR__) . '/templates/navbar.php';
require_once dirname(__DIR__) . '/templates/product_card.php';

// Star rendering helper (reuse from product_card.php already included)
$stars_html = render_stars((float)$p['rating']);
?>

<section class="section-sm">
  <div class="container">

    <!-- Breadcrumbs -->
    <nav class="breadcrumbs" aria-label="Breadcrumb">
      <a href="/">Strona główna</a>
      <span class="sep">›</span>
      <a href="/<?= $cat_slug ?>"><?= htmlspecialchars($cat['name']) ?></a>
      <span class="sep">›</span>
      <span class="current"><?= htmlspecialchars($p['name']) ?> Recenzja</span>
    </nav>

    <div class="grid-2" style="gap:3rem;align-items:start">

      <!-- LEFT: Main content -->
      <div>
        <!-- Header -->
        <div class="flex gap-2 mb-3" style="align-items:flex-start">
          <div class="product-logo" style="width:72px;height:72px;flex-shrink:0">
            <div class="product-logo-placeholder" style="background:<?= htmlspecialchars($p['badge_color'] ?? '#f0b429') ?>;font-size:1.5rem">
              <?= mb_substr($p['name'], 0, 1) ?>
            </div>
          </div>
          <div>
            <h1 style="font-size:1.8rem;margin-bottom:.25rem"><?= htmlspecialchars($p['name']) ?> — Recenzja <?= date('Y') ?></h1>
            <p style="margin:0"><?= htmlspecialchars($p['tagline']) ?></p>
            <div class="flex gap-1 mt-2" style="flex-wrap:wrap">
              <?= $stars_html ?>
              <span class="rating-count">(<?= number_format($p['reviews']) ?> opinii)</span>
              <?php if (!empty($p['badge'])): ?>
                <span class="product-badge" style="background:<?= $p['badge_color'] ?>22;color:<?= $p['badge_color'] ?>;border:1px solid <?= $p['badge_color'] ?>44">
                  <?= htmlspecialchars($p['badge']) ?>
                </span>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <!-- Quick verdict -->
        <div class="info-box success mb-4">
          <div class="info-box-icon">✅</div>
          <div>
            <strong>Nasz werdykt:</strong>
            <p style="margin:.25rem 0 0"><?= htmlspecialchars($p['description']) ?></p>
          </div>
        </div>

        <!-- Pros / Cons -->
        <h2 style="font-size:1.2rem;margin-bottom:1rem">Zalety i wady</h2>
        <div class="pros-cons mb-4">
          <div>
            <div style="font-weight:600;margin-bottom:.75rem;color:var(--green)">✓ Zalety</div>
            <ul class="pros-list">
              <?php foreach (($p['pros'] ?? []) as $pro): ?>
                <li><?= htmlspecialchars($pro) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
          <div>
            <div style="font-weight:600;margin-bottom:.75rem;color:var(--red)">✗ Wady</div>
            <ul class="cons-list">
              <?php foreach (($p['cons'] ?? []) as $con): ?>
                <li><?= htmlspecialchars($con) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>

        <!-- Full review text -->
        <h2 style="font-size:1.2rem;margin-bottom:1rem"><?= htmlspecialchars($p['name']) ?> — pełna recenzja</h2>
        <p style="line-height:1.8;margin-bottom:1rem"><?= htmlspecialchars($p['full_review'] ?? '') ?></p>

        <!-- Key data table -->
        <h2 style="font-size:1.2rem;margin-bottom:1rem">Kluczowe parametry</h2>
        <div style="overflow-x:auto">
          <table class="comparison-table mb-4">
            <tbody>
              <tr><th>Opłata / prowizja</th><td><?= htmlspecialchars($p['fee'] ?? '—') ?></td></tr>
              <tr><th>Minimalny depozyt</th><td><?= htmlspecialchars($p['min_deposit'] ?? '—') ?></td></tr>
              <tr><th>Ocena ogólna</th><td><?= $stars_html ?></td></tr>
              <tr><th>Liczba opinii</th><td><?= number_format($p['reviews'] ?? 0) ?></td></tr>
            </tbody>
          </table>
        </div>

        <!-- Disclaimer -->
        <div class="info-box warning">
          <div class="info-box-icon">⚠️</div>
          <p style="font-size:.8rem">Inwestowanie w kryptowaluty i produkty finansowe wiąże się z ryzykiem. Treści na tej stronie mają charakter informacyjny i nie stanowią porady finansowej.</p>
        </div>
      </div>

      <!-- RIGHT: Sticky CTA + other products -->
      <div>
        <div class="cta-block mb-4" style="position:sticky;top:90px">
          <div style="font-size:2.5rem;margin-bottom:.75rem"><?= $cat['icon'] ?></div>
          <h3><?= htmlspecialchars($p['name']) ?></h3>
          <div class="flex gap-1" style="justify-content:center;margin-bottom:.5rem">
            <?= $stars_html ?>
          </div>
          <p style="font-size:.9rem;margin-bottom:1.5rem"><?= htmlspecialchars($p['tagline']) ?></p>

          <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;margin-bottom:1.5rem;text-align:left">
            <?php foreach ([
              [$p['feature1'] ?? '','Główna zaleta'],
              [$p['feature2'] ?? '','Prowizja / opłata'],
              [$p['feature3'] ?? '','Dodatkowe'],
              [$p['fee'] ?? '','Koszty'],
            ] as [$val, $label]): ?>
            <div>
              <div style="font-weight:700;font-size:.95rem"><?= htmlspecialchars($val) ?></div>
              <div style="font-size:.75rem;color:var(--text-muted)"><?= $label ?></div>
            </div>
            <?php endforeach; ?>
          </div>

          <a href="<?= htmlspecialchars($p['mylead_url'] ?? '#') ?>"
             class="btn btn-gold btn-lg btn-block mb-2"
             target="_blank" rel="nofollow noopener sponsored">
            <?= htmlspecialchars($p['cta'] ?? 'Sprawdź ofertę') ?> →
          </a>
          <p style="font-size:.75rem;color:var(--text-dim);margin-top:.5rem">
            Może to być link afiliacyjny. Nie wpływa to na naszą ocenę.
          </p>
        </div>

        <!-- Other products in category -->
        <div class="mt-3">
          <div style="font-weight:600;margin-bottom:1rem;color:var(--text-muted);font-size:.875rem">INNE W RANKINGU</div>
          <?php foreach ($products as $i => $other):
            if ($other['slug'] === $slug) continue;
          ?>
          <div class="card" style="margin-bottom:.75rem">
            <div class="card-body" style="display:flex;align-items:center;justify-content:space-between;gap:1rem">
              <div class="flex gap-2">
                <span style="font-size:.875rem;font-weight:700;color:var(--text-muted)">#<?= $i+1 ?></span>
                <div>
                  <div style="font-weight:600;font-size:.9rem"><?= htmlspecialchars($other['name']) ?></div>
                  <?= render_stars((float)$other['rating']) ?>
                </div>
              </div>
              <a href="/<?= $cat_slug ?>/<?= $other['slug'] ?>" class="btn btn-ghost btn-sm" style="flex-shrink:0">Zobacz →</a>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

  </div>
</section>

<?php require_once dirname(__DIR__) . '/templates/footer.php'; ?>
