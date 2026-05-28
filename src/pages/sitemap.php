<?php
$today = date('Y-m-d');
$urls  = [];

// Home
$urls[] = ['loc' => SITE_DOMAIN . '/', 'changefreq' => 'weekly', 'priority' => '1.0', 'lastmod' => $today];

// Categories
foreach (CATEGORIES as $slug => $cat) {
    $urls[] = ['loc' => SITE_DOMAIN . '/' . $slug, 'changefreq' => 'weekly', 'priority' => '0.9', 'lastmod' => $today];
}

// Products
foreach (PRODUCTS as $slug => $p) {
    $urls[] = ['loc' => SITE_DOMAIN . '/' . $p['category'] . '/' . $slug, 'changefreq' => 'monthly', 'priority' => '0.8', 'lastmod' => $today];
}

// Blog index
$urls[] = ['loc' => SITE_DOMAIN . '/blog', 'changefreq' => 'weekly', 'priority' => '0.7', 'lastmod' => $today];

// Blog articles
$articles = require __DIR__ . '/../data/articles.php';
foreach ($articles as $slug => $a) {
    $urls[] = ['loc' => SITE_DOMAIN . '/blog/' . $slug, 'changefreq' => 'monthly', 'priority' => '0.7', 'lastmod' => $a['updated']];
}

// Static
$urls[] = ['loc' => SITE_DOMAIN . '/kontakt',               'changefreq' => 'yearly', 'priority' => '0.4', 'lastmod' => $today];
$urls[] = ['loc' => SITE_DOMAIN . '/polityka-prywatnosci',  'changefreq' => 'yearly', 'priority' => '0.3', 'lastmod' => $today];

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($urls as $url): ?>
  <url>
    <loc><?= htmlspecialchars($url['loc']) ?></loc>
    <lastmod><?= $url['lastmod'] ?></lastmod>
    <changefreq><?= $url['changefreq'] ?></changefreq>
    <priority><?= $url['priority'] ?></priority>
  </url>
<?php endforeach; ?>
</urlset>
