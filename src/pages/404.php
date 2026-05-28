<?php
$meta_title = '404 – Strona nie znaleziona | FinRank';
$meta_desc  = 'Strona nie istnieje. Wróć do strony głównej FinRank.';
require_once dirname(__DIR__) . '/templates/head.php';
require_once dirname(__DIR__) . '/templates/navbar.php';
?>
<section class="section" style="text-align:center;min-height:60vh;display:flex;align-items:center">
  <div class="container">
    <div style="font-size:5rem;margin-bottom:1rem">404</div>
    <h1 style="margin-bottom:1rem">Strona nie znaleziona</h1>
    <p style="margin-bottom:2rem">Przepraszamy, ta strona nie istnieje lub została przeniesiona.</p>
    <a href="/" class="btn btn-gold btn-lg">Wróć do strony głównej →</a>
  </div>
</section>
<?php require_once dirname(__DIR__) . '/templates/footer.php'; ?>
