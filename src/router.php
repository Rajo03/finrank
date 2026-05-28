<?php
require_once __DIR__ . '/config.php';

function route(): array {
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $uri = rtrim($uri, '/') ?: '/';

    // Home
    if ($uri === '/') return ['page' => 'home'];

    $parts = explode('/', ltrim($uri, '/'));

    // Category pages: /kryptowaluty  /pozyczki  /konta-bankowe
    if (count($parts) === 1 && isset(CATEGORIES[$parts[0]])) {
        return ['page' => 'category', 'category' => $parts[0]];
    }

    // Product pages: /kryptowaluty/binance  /pozyczki/vivus
    if (count($parts) === 2) {
        $cat  = $parts[0];
        $slug = $parts[1];
        $products = PRODUCTS;
        if (isset(CATEGORIES[$cat]) && isset($products[$slug]) && $products[$slug]['category'] === $cat) {
            return ['page' => 'product', 'category' => $cat, 'slug' => $slug];
        }
    }

    // Blog index
    if ($uri === '/blog') return ['page' => 'blog'];

    // Blog post: /blog/jak-kupic-bitcoin
    if (count($parts) === 2 && $parts[0] === 'blog') {
        return ['page' => 'blog_post', 'slug' => $parts[1]];
    }

    // Newsletter
    if ($uri === '/newsletter/subscribe')    return ['page' => 'newsletter_subscribe'];
    if ($uri === '/newsletter/confirm')      return ['page' => 'newsletter_confirm'];
    if ($uri === '/newsletter/unsubscribe')  return ['page' => 'newsletter_unsubscribe'];

    // Static pages
    if ($uri === '/o-nas')                   return ['page' => 'about'];
    if ($uri === '/kontakt')                 return ['page' => 'contact'];
    if ($uri === '/polityka-prywatnosci')    return ['page' => 'privacy'];
    if ($uri === '/sitemap.xml')             return ['page' => 'sitemap'];
    if ($uri === '/robots.txt')              return ['page' => 'robots'];

    return ['page' => '404'];
}

function render(string $template, array $data = []): void {
    extract($data);
    $tpl = __DIR__ . '/pages/' . $template . '.php';
    if (!file_exists($tpl)) {
        http_response_code(404);
        include __DIR__ . '/pages/404.php';
        return;
    }
    include $tpl;
}
