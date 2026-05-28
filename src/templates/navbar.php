<?php
$current_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
function nav_active(string $path): string {
    global $current_uri;
    return str_starts_with($current_uri, $path) ? ' active' : '';
}
?>
<nav class="navbar" aria-label="Nawigacja główna">
  <div class="container">
    <a href="/" class="navbar-brand">FinRank</a>

    <button class="hamburger" id="hamburger" aria-label="Menu" aria-expanded="false">
      <span></span><span></span><span></span>
    </button>

    <ul class="navbar-nav" id="navbar-nav">
      <li><a href="/kryptowaluty" class="<?= nav_active('/kryptowaluty') ?>">Kryptowaluty</a></li>
      <li><a href="/pozyczki" class="<?= nav_active('/pozyczki') ?>">Pożyczki</a></li>
      <li><a href="/konta-bankowe" class="<?= nav_active('/konta-bankowe') ?>">Konta bankowe</a></li>
      <li><a href="/blog" class="<?= nav_active('/blog') ?>">Blog</a></li>
      <li><a href="/kryptowaluty/binance" class="navbar-cta">Najlepsze oferty →</a></li>
    </ul>
  </div>
</nav>
<script>
  document.getElementById('hamburger').addEventListener('click', function() {
    const nav = document.getElementById('navbar-nav');
    const open = nav.classList.toggle('open');
    this.setAttribute('aria-expanded', open);
  });
</script>
