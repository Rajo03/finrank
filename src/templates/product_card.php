<?php
// $product = array, $rank = int (1-based)
function render_stars(float $rating): string {
    $html = '<div class="stars">';
    for ($i = 1; $i <= 5; $i++) {
        $html .= $i <= round($rating) ? '<span class="star">★</span>' : '<span class="star empty">★</span>';
    }
    $html .= '<span class="rating-score">' . number_format($rating, 1) . '</span>';
    $html .= '</div>';
    return $html;
}

function render_product_card(array $p, int $rank = 0, bool $featured = false): string {
    $rank_class = $rank === 1 ? 'rank-1' : ($rank === 2 ? 'rank-2' : ($rank === 3 ? 'rank-3' : ''));
    $url = '/' . $p['category'] . '/' . $p['slug'];
    $badge_style = 'background:' . ($p['badge_color'] ?? '#f0b429') . '22; color:' . ($p['badge_color'] ?? '#f0b429') . '; border:1px solid ' . ($p['badge_color'] ?? '#f0b429') . '44;';
    $mylead = htmlspecialchars($p['mylead_url'] ?? '#');

    ob_start();
?>
<div class="product-card <?= $featured ? 'featured' : '' ?>">
  <?php if ($rank > 0): ?>
    <div class="product-card-rank <?= $rank_class ?>"><?= $rank ?></div>
  <?php endif; ?>

  <div class="product-card-header">
    <div class="product-logo">
      <?php if (!empty($p['logo']) && file_exists('.' . $p['logo'])): ?>
        <img src="<?= htmlspecialchars($p['logo']) ?>" alt="<?= htmlspecialchars($p['name']) ?> logo" loading="lazy" width="40" height="40">
      <?php else: ?>
        <div class="product-logo-placeholder" style="background:<?= htmlspecialchars($p['badge_color'] ?? '#f0b429') ?>">
          <?= mb_substr($p['name'], 0, 1) ?>
        </div>
      <?php endif; ?>
    </div>
    <div>
      <div class="product-name"><?= htmlspecialchars($p['name']) ?></div>
      <div class="product-tagline"><?= htmlspecialchars($p['tagline']) ?></div>
    </div>
    <?php if (!empty($p['badge'])): ?>
      <div class="product-badge ms-auto" style="<?= $badge_style ?>"><?= htmlspecialchars($p['badge']) ?></div>
    <?php endif; ?>
  </div>

  <?= render_stars((float)$p['rating']) ?>
  <span class="rating-count">(<?= number_format($p['reviews'] ?? 0) ?> opinii)</span>

  <div class="product-features mt-3">
    <div class="product-feature">
      <span class="product-feature-val"><?= htmlspecialchars($p['feature1'] ?? '') ?></span>
      <span class="product-feature-label">Główna zaleta</span>
    </div>
    <div class="product-feature">
      <span class="product-feature-val"><?= htmlspecialchars($p['feature2'] ?? '') ?></span>
      <span class="product-feature-label">Prowizja / opłata</span>
    </div>
    <div class="product-feature">
      <span class="product-feature-val"><?= htmlspecialchars($p['feature3'] ?? '') ?></span>
      <span class="product-feature-label">Dodatkowe</span>
    </div>
  </div>

  <div class="product-card-footer">
    <a href="<?= $url ?>" class="btn btn-ghost btn-sm">Czytaj recenzję</a>
    <a href="<?= $mylead ?>" class="btn btn-gold btn-sm" target="_blank" rel="nofollow noopener sponsored">
      <?= htmlspecialchars($p['cta'] ?? 'Sprawdź ofertę') ?> →
    </a>
  </div>
</div>
<?php
    return ob_get_clean();
}
?>
