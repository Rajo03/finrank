<?php
define('SITE_NAME', 'FinRank');
define('SITE_DOMAIN', 'https://finrank.pl');
define('SITE_TAGLINE', 'Rankingi i recenzje produktów finansowych');
define('SITE_DESCRIPTION', 'FinRank to niezależny serwis z rankingami kont bankowych, pożyczek, chwilówek i giełd kryptowalut. Porównaj oferty i wybierz najlepszą dla siebie.');

// MyLead affiliate base (append product-specific ID)
define('MYLEAD_BASE', 'https://mylead.global/sl/');
define('MYLEAD_UTM', '?utm_source=finrank&utm_medium=ranking&utm_campaign=');

// Categories
define('CATEGORIES', [
    'kryptowaluty' => [
        'name'        => 'Kryptowaluty',
        'slug'        => 'kryptowaluty',
        'icon'        => '₿',
        'description' => 'Rankingi i recenzje giełd kryptowalut. Porównaj prowizje, bezpieczeństwo i dostępne coiny.',
        'color'       => '#f7931a',
        'meta_title'  => 'Najlepsze giełdy kryptowalut 2025 – Ranking i Recenzje | FinRank',
        'meta_desc'   => 'Sprawdź ranking najlepszych giełd kryptowalut w Polsce. Porównaj Binance, Zonda, Coinbase i inne. Aktualne opinie i prowizje.',
    ],
    'pozyczki' => [
        'name'        => 'Pożyczki',
        'slug'        => 'pozyczki',
        'icon'        => '💳',
        'description' => 'Ranking chwilówek i pożyczek online. Znajdź najtańszą ofertę bez wychodzenia z domu.',
        'color'       => '#10b981',
        'meta_title'  => 'Najlepsze Pożyczki Online 2025 – Ranking Chwilówek | FinRank',
        'meta_desc'   => 'Porównaj najlepsze pożyczki online i chwilówki. Ranking aktualnych ofert z RRSO, kwotą i czasem decyzji. Pożycz bezpiecznie.',
    ],
    'konta-bankowe' => [
        'name'        => 'Konta bankowe',
        'slug'        => 'konta-bankowe',
        'icon'        => '🏦',
        'description' => 'Ranking kont osobistych i oszczędnościowych. Sprawdź które konto płaci Ci za założenie.',
        'color'       => '#6366f1',
        'meta_title'  => 'Najlepsze Konta Bankowe 2025 – Ranking i Porównanie | FinRank',
        'meta_desc'   => 'Ranking najlepszych kont bankowych w Polsce. Porównaj konta osobiste, oszczędnościowe i promocje bankowe. Wybierz konto z premią.',
    ],
]);

// Products data
define('PRODUCTS', require __DIR__ . '/data/products.php');
