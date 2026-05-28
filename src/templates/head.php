<?php
// Called with: $meta_title, $meta_desc, $canonical (optional)
$canonical = $canonical ?? SITE_DOMAIN . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$og_image  = SITE_DOMAIN . '/assets/img/og-finrank.jpg';
?>
<!DOCTYPE html>
<html lang="pl" prefix="og: https://ogp.me/ns#">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($meta_title ?? SITE_NAME) ?></title>
  <meta name="description" content="<?= htmlspecialchars($meta_desc ?? SITE_DESCRIPTION) ?>">
  <link rel="canonical" href="<?= htmlspecialchars($canonical) ?>">

  <!-- Open Graph -->
  <meta property="og:type"        content="website">
  <meta property="og:title"       content="<?= htmlspecialchars($meta_title ?? SITE_NAME) ?>">
  <meta property="og:description" content="<?= htmlspecialchars($meta_desc ?? SITE_DESCRIPTION) ?>">
  <meta property="og:url"         content="<?= htmlspecialchars($canonical) ?>">
  <meta property="og:image"       content="<?= $og_image ?>">
  <meta property="og:site_name"   content="FinRank">
  <meta property="og:locale"      content="pl_PL">

  <!-- Twitter -->
  <meta name="twitter:card"        content="summary_large_image">
  <meta name="twitter:title"       content="<?= htmlspecialchars($meta_title ?? SITE_NAME) ?>">
  <meta name="twitter:description" content="<?= htmlspecialchars($meta_desc ?? SITE_DESCRIPTION) ?>">
  <meta name="twitter:image"       content="<?= $og_image ?>">

  <!-- Robots -->
  <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large">

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- CSS -->
  <link rel="stylesheet" href="/assets/css/main.css">

  <!-- Schema.org WebSite -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "WebSite",
    "name": "FinRank",
    "url": "<?= SITE_DOMAIN ?>",
    "description": "<?= SITE_DESCRIPTION ?>",
    "potentialAction": {
      "@type": "SearchAction",
      "target": "<?= SITE_DOMAIN ?>/szukaj?q={search_term_string}",
      "query-input": "required name=search_term_string"
    }
  }
  </script>
  <?= $extra_head ?? '' ?>
</head>
<body>
