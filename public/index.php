<?php
require_once dirname(__DIR__) . '/src/router.php';

$route = route();

switch ($route['page']) {
    case 'home':     render('home');     break;
    case 'category': render('category', ['cat_slug' => $route['category']]); break;
    case 'product':  render('product',  ['slug' => $route['slug'], 'cat_slug' => $route['category']]); break;
    case 'blog':     render('blog');     break;
    case 'blog_post':render('blog_post',['slug' => $route['slug']]); break;
    case 'newsletter_subscribe':
    case 'newsletter_confirm':
    case 'newsletter_unsubscribe':
        require_once dirname(__DIR__) . '/src/newsletter.php';
        render('newsletter', ['action' => $route['page']]);
        break;
    case 'about':    render('about');   break;
    case 'contact':  render('contact'); break;
    case 'privacy':  render('privacy'); break;
    case 'sitemap':
        header('Content-Type: application/xml; charset=utf-8');
        render('sitemap');
        break;
    case 'robots':
        header('Content-Type: text/plain');
        render('robots');
        break;
    default:
        http_response_code(404);
        render('404');
}
