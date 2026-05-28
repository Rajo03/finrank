<?php
$articles = require dirname(__DIR__) . '/data/articles.php';

if (!isset($articles[$slug])) {
    http_response_code(404);
    require __DIR__ . '/404.php';
    exit;
}

$a        = $articles[$slug];
$products = PRODUCTS;
$cat      = CATEGORIES[$a['category']] ?? null;

$meta_title = $a['meta_title'];
$meta_desc  = $a['meta_desc'];
$canonical  = SITE_DOMAIN . '/blog/' . $slug;

// Article schema
$schema = [
    '@context' => 'https://schema.org',
    '@graph'   => [
        [
            '@type'            => 'Article',
            'headline'         => $a['title'],
            'description'      => $a['meta_desc'],
            'datePublished'    => $a['date'],
            'dateModified'     => $a['updated'],
            'author'           => ['@type' => 'Organization', 'name' => 'Redakcja FinRank'],
            'publisher'        => ['@type' => 'Organization', 'name' => 'FinRank', 'url' => SITE_DOMAIN],
            'url'              => $canonical,
            'mainEntityOfPage' => $canonical,
        ],
        [
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type'=>'ListItem','position'=>1,'name'=>'Strona główna','item'=>SITE_DOMAIN],
                ['@type'=>'ListItem','position'=>2,'name'=>'Blog','item'=>SITE_DOMAIN.'/blog'],
                ['@type'=>'ListItem','position'=>3,'name'=>$a['title'],'item'=>$canonical],
            ],
        ],
        // FAQ schema if present
        ...(!empty($a['faq']) ? [[
            '@type'      => 'FAQPage',
            'mainEntity' => array_map(fn($faq) => [
                '@type'          => 'Question',
                'name'           => $faq[0],
                'acceptedAnswer' => ['@type'=>'Answer','text'=>$faq[1]],
            ], $a['faq']),
        ]] : []),
    ],
];
$extra_head = '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';

require_once dirname(__DIR__) . '/templates/head.php';
require_once dirname(__DIR__) . '/templates/navbar.php';
require_once dirname(__DIR__) . '/templates/product_card.php';
?>

<div class="container" style="max-width:900px">

  <!-- Breadcrumbs -->
  <nav class="breadcrumbs" style="padding-top:2rem">
    <a href="/">Strona główna</a>
    <span class="sep">›</span>
    <a href="/blog">Blog</a>
    <?php if ($cat): ?>
    <span class="sep">›</span>
    <a href="/<?= $a['category'] ?>"><?= htmlspecialchars($cat['name']) ?></a>
    <?php endif; ?>
    <span class="sep">›</span>
    <span class="current"><?= htmlspecialchars($a['short']) ?></span>
  </nav>

  <!-- Article header -->
  <header style="padding:2rem 0 3rem">
    <div class="flex gap-2 mb-3" style="flex-wrap:wrap">
      <?php foreach ($a['tags'] as $tag): ?>
        <span class="tag"><?= htmlspecialchars($tag) ?></span>
      <?php endforeach; ?>
      <span class="tag"><?= $a['read_time'] ?> czytania</span>
    </div>
    <h1 style="font-size:clamp(1.6rem,4vw,2.5rem);margin-bottom:1rem;line-height:1.2">
      <?= htmlspecialchars($a['title']) ?>
    </h1>
    <div class="flex gap-3" style="color:var(--text-muted);font-size:.875rem;flex-wrap:wrap">
      <span>✍️ <?= htmlspecialchars($a['author']) ?></span>
      <span>📅 Opublikowano: <?= $a['date'] ?></span>
      <span>🔄 Aktualizacja: <?= $a['updated'] ?></span>
    </div>
  </header>

  <div class="grid-2" style="gap:3rem;align-items:start">

    <!-- Article content -->
    <div style="grid-column:1/3">

      <!-- Intro -->
      <div class="info-box success mb-5" style="font-size:1.05rem">
        <div class="info-box-icon">💡</div>
        <p style="margin:0;color:var(--text)"><?= htmlspecialchars($a['intro']) ?></p>
      </div>

      <!-- Table of contents -->
      <div class="card card-body mb-5" style="background:var(--bg-card2)">
        <div style="font-weight:700;margin-bottom:.75rem">📋 Spis treści</div>
        <ol style="padding-left:1.25rem;color:var(--text-muted);display:flex;flex-direction:column;gap:.4rem">
          <?php foreach ($a['sections'] as $i => $sec): ?>
            <li><a href="#section-<?= $i ?>" style="color:var(--text-muted)"><?= htmlspecialchars($sec['heading']) ?></a></li>
          <?php endforeach; ?>
          <?php if (!empty($a['faq'])): ?>
            <li><a href="#faq" style="color:var(--text-muted)">Najczęstsze pytania</a></li>
          <?php endif; ?>
        </ol>
      </div>

      <!-- Sections -->
      <?php foreach ($a['sections'] as $i => $sec): ?>
      <section id="section-<?= $i ?>" style="margin-bottom:2.5rem">
        <h2 style="font-size:1.3rem;margin-bottom:1rem;padding-bottom:.5rem;border-bottom:1px solid var(--border)">
          <?= htmlspecialchars($sec['heading']) ?>
        </h2>
        <p style="line-height:1.8;color:var(--text-muted)"><?= htmlspecialchars($sec['content']) ?></p>
      </section>
      <?php endforeach; ?>

      <!-- Verdict -->
      <?php if (!empty($a['verdict'])): ?>
      <div class="cta-block mb-5">
        <div style="font-size:1.5rem;margin-bottom:.5rem">🏆</div>
        <h3 style="margin-bottom:.75rem">Nasz werdykt</h3>
        <p><?= htmlspecialchars($a['verdict']) ?></p>
        <?php if (!empty($a['winner_url'])): ?>
          <a href="<?= htmlspecialchars($a['winner_url']) ?>" class="btn btn-gold mt-3">
            Sprawdź: <?= htmlspecialchars($a['winner'] ?? 'najlepszą opcję') ?> →
          </a>
        <?php endif; ?>
      </div>
      <?php endif; ?>

      <!-- Featured products -->
      <?php if (!empty($a['products'])): ?>
      <h2 style="font-size:1.2rem;margin-bottom:1.25rem">Polecane produkty</h2>
      <div style="display:flex;flex-direction:column;gap:1rem;margin-bottom:3rem">
        <?php foreach ($a['products'] as $i => $prod_slug):
          if (!isset($products[$prod_slug])) continue;
          echo render_product_card($products[$prod_slug], $i + 1, $i === 0);
        endforeach; ?>
      </div>
      <?php endif; ?>

      <!-- FAQ -->
      <?php if (!empty($a['faq'])): ?>
      <section id="faq">
        <h2 style="font-size:1.3rem;margin-bottom:1.25rem">❓ Najczęstsze pytania</h2>
        <div style="display:flex;flex-direction:column;gap:.75rem">
          <?php foreach ($a['faq'] as $faq): ?>
          <details class="card">
            <summary class="card-body" style="cursor:pointer;font-weight:600;list-style:none;display:flex;justify-content:space-between;align-items:center">
              <?= htmlspecialchars($faq[0]) ?>
              <span style="color:var(--gold);flex-shrink:0;margin-left:1rem">+</span>
            </summary>
            <div style="padding:0 1.5rem 1.25rem;color:var(--text-muted);font-size:.9rem">
              <?= htmlspecialchars($faq[1]) ?>
            </div>
          </details>
          <?php endforeach; ?>
        </div>
      </section>
      <?php endif; ?>

      <!-- Newsletter CTA -->
      <div class="card card-body mt-5" style="background:linear-gradient(135deg,rgba(240,180,41,.08),rgba(240,180,41,.02));border-color:rgba(240,180,41,.3);text-align:center">
        <div style="font-size:2rem;margin-bottom:.75rem">📬</div>
        <h3 style="margin-bottom:.5rem">Nie przegap nowych promocji</h3>
        <p style="font-size:.9rem;margin-bottom:1.25rem">Co miesiąc wysyłamy przegląd najlepszych ofert bankowych i finansowych. Zero spamu.</p>
        <form action="/newsletter/subscribe" method="post" class="flex gap-2" style="justify-content:center;flex-wrap:wrap">
          <input type="email" name="email" placeholder="Twój adres email" required
                 style="background:var(--bg-card2);border:1px solid var(--border);color:var(--text);padding:.65rem 1rem;border-radius:var(--radius-sm);font-size:.95rem;min-width:240px">
          <button type="submit" class="btn btn-gold">Zapisz się →</button>
        </form>
        <p style="font-size:.75rem;color:var(--text-dim);margin-top:.75rem">Możesz wypisać się w każdej chwili. Zgodnie z RODO.</p>
      </div>

    </div>
  </div>
</div>

<?php require_once dirname(__DIR__) . '/templates/footer.php'; ?>
