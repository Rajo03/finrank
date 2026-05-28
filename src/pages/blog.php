<?php
$meta_title = 'Blog finansowy – Rankingi, Porównania i Porady | FinRank';
$meta_desc  = 'Blog FinRank – rankingi, porównania i porady finansowe. Pekao vs mBank, najlepsze konto dla studenta, jak kupić Bitcoin. Aktualizowane co miesiąc.';

$all_articles = require dirname(__DIR__) . '/data/articles.php';
$articles = array_values($all_articles);

require_once dirname(__DIR__) . '/templates/head.php';
require_once dirname(__DIR__) . '/templates/navbar.php';
?>

<section class="section-sm">
  <div class="container">
    <nav class="breadcrumbs">
      <a href="/">Strona główna</a>
      <span class="sep">›</span>
      <span class="current">Blog finansowy</span>
    </nav>

    <div class="section-header">
      <div class="section-label">Blog</div>
      <h1 class="section-title">Blog finansowy FinRank</h1>
      <p class="section-desc">Praktyczne porady o kryptowalutach, pożyczkach i kontach bankowych.</p>
    </div>

    <div class="grid-3">
      <?php foreach ($articles as $a): ?>
      <article class="card">
        <div class="card-body">
          <div class="flex gap-1 mb-2" style="flex-wrap:wrap">
            <?php $cat_data = CATEGORIES[$a['category']] ?? null; ?>
            <?php if ($cat_data): ?>
              <span class="tag"><?= htmlspecialchars($cat_data['name']) ?></span>
            <?php endif; ?>
            <span class="tag"><?= $a['read_time'] ?> czytania</span>
            <?php if ($a['type'] === 'comparison'): ?>
              <span class="tag pill-gold">Porównanie</span>
            <?php endif; ?>
          </div>
          <h2 style="font-size:1rem;margin-bottom:.5rem;line-height:1.4">
            <a href="/blog/<?= $a['slug'] ?>" style="color:var(--text)"><?= htmlspecialchars($a['title']) ?></a>
          </h2>
          <p style="font-size:.875rem;margin-bottom:1rem"><?= htmlspecialchars($a['meta_desc']) ?></p>
          <div class="flex-between">
            <span style="font-size:.8rem;color:var(--text-dim)">🔄 <?= $a['updated'] ?></span>
            <a href="/blog/<?= $a['slug'] ?>" class="btn btn-ghost btn-sm">Czytaj →</a>
          </div>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php require_once dirname(__DIR__) . '/templates/footer.php'; ?>
