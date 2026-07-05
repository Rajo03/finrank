#!/usr/bin/env python3
"""
FinRank Blog Generator
======================
Czyta blog-articles.json i generuje:
  - blog/<slug>/index.html — osobna strona dla każdego artykułu
  - blog/index.html — lista wszystkich artykułów
  - Aktualizuje karuzelę blogową w index.html
  - sitemap.xml
"""

import io
import json
import re
import sys
from datetime import date
from pathlib import Path

sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding="utf-8", errors="replace")

ROOT = Path(__file__).parent
DATA_FILE = ROOT / "blog-articles.json"
SITE_URL = "https://finrank-psi.vercel.app"

CATEGORY_ICONS = {
    "konta-bankowe": ("🏦", "rgba(220,38,38,.15)", "rgba(220,38,38,.05)"),
    "kryptowaluty": ("₿", "rgba(247,147,26,.15)", "rgba(247,147,26,.05)"),
    "pozyczki": ("💳", "rgba(16,185,129,.15)", "rgba(16,185,129,.05)"),
}

CATEGORY_LABELS = {
    "konta-bankowe": "Konta bankowe",
    "kryptowaluty": "Kryptowaluty",
    "pozyczki": "Pożyczki",
}

MONTHS_PL = {
    1: "Sty", 2: "Lut", 3: "Mar", 4: "Kwi", 5: "Maj", 6: "Cze",
    7: "Lip", 8: "Sie", 9: "Wrz", 10: "Paź", 11: "Lis", 12: "Gru"
}


def load_articles():
    with open(DATA_FILE, encoding="utf-8") as f:
        return json.load(f)


def format_date_pl(iso_date: str) -> str:
    d = date.fromisoformat(iso_date)
    return f"{d.day} {MONTHS_PL[d.month]} {d.year}"


def generate_offers_html(offers: list) -> str:
    if not offers:
        return ""
    cards = ""
    for o in offers:
        badge = f'<span class="offer-badge">{o["badge"]}</span>' if o.get("badge") else ""
        featured_cls = " offer-card--featured" if o.get("featured") else ""
        logo = o.get("logo", "")
        logo_html = ""
        if logo:
            logo_html = (
                f'<img src="https://www.google.com/s2/favicons?domain={logo}&sz=64" '
                f'alt="{o["shop"]}" class="offer-logo-img" loading="lazy">'
            )
        plusy = "".join(
            f'<li>{p}</li>' for p in (o.get("plusy") or [])[:3]
        )
        cards += f'''
  <div class="offer-card{featured_cls}">
    {badge}
    <div class="offer-head">
      <div class="offer-logo">{logo_html}</div>
      <div class="offer-info">
        <div class="offer-name">{o["name"]}</div>
        <div class="offer-meta">{o["shop"]} &middot; {o["typ"]}</div>
      </div>
    </div>
    <p class="offer-desc">{o["opis"]}</p>
    {"<ul class='offer-plusy'>" + plusy + "</ul>" if plusy else ""}
    <a href="{o["link"]}" class="offer-btn" target="_blank" rel="noopener sponsored">Sprawdz &rarr;</a>
  </div>'''
    return f'''
<div class="offers-section">
  <h2 class="offers-title">Polecane oferty finansowe</h2>
  <p class="offers-sub">Linki sponsorowane &mdash; wybrane pod katem jakosci i warunkow</p>
  {cards}
  <p class="offers-legal">* Linki afiliacyjne. Oferty maja charakter informacyjny &mdash; sprawdz aktualne warunki u partnera.</p>
</div>'''


def generate_blog_post_html(article: dict) -> str:
    cat_label = CATEGORY_LABELS.get(article["category"], article["category"])

    sections_html = ""
    for sec in article["sections"]:
        sections_html += f'<h2>{sec["heading"]}</h2>\n<p>{sec["content"]}</p>\n\n'

    faq_html = ""
    if article.get("faq"):
        faq_html = '<h2>Najczesciej zadawane pytania</h2>\n'
        for faq in article["faq"]:
            q, a = faq[0], faq[1]
            faq_html += f'<div class="faq-item"><h3>{q}</h3><p>{a}</p></div>\n'

    offers_html = generate_offers_html(article.get("offers", []))

    faq_schema = ""
    if article.get("faq"):
        faq_entries = ",".join(
            f'{{"@type":"Question","name":"{_esc(f[0])}","acceptedAnswer":{{"@type":"Answer","text":"{_esc(f[1])}"}}}}'
            for f in article["faq"]
        )
        faq_schema = f',{{"@type":"FAQPage","mainEntity":[{faq_entries}]}}'

    return f'''<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{article["meta_title"]}</title>
  <meta name="description" content="{article["meta_desc"]}">
  <link rel="canonical" href="{SITE_URL}/blog/{article["slug"]}/">
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-9R80DCEM55"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){{dataLayer.push(arguments);}}
    gtag('js', new Date());
    gtag('config', 'G-9R80DCEM55');
  </script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Syne:wght@700;800&display=swap" rel="stylesheet">
  <script type="application/ld+json">
  {{"@context":"https://schema.org","@graph":[
    {{"@type":"Article","headline":"{_esc(article["title"])}","description":"{_esc(article["meta_desc"])}","datePublished":"{article["date"]}","dateModified":"{article["updated"]}","author":{{"@type":"Organization","name":"Redakcja FinRank"}},"publisher":{{"@type":"Organization","name":"FinRank","url":"{SITE_URL}"}},"url":"{SITE_URL}/blog/{article["slug"]}/","mainEntityOfPage":"{SITE_URL}/blog/{article["slug"]}/"}},
    {{"@type":"BreadcrumbList","itemListElement":[{{"@type":"ListItem","position":1,"name":"Strona glowna","item":"{SITE_URL}"}},{{"@type":"ListItem","position":2,"name":"Blog","item":"{SITE_URL}/blog/"}},{{"@type":"ListItem","position":3,"name":"{_esc(article["title"])}","item":"{SITE_URL}/blog/{article["slug"]}/"}}]}}
    {faq_schema}
  ]}}
  </script>
  <style>
    *, *::before, *::after {{ box-sizing: border-box; margin: 0; padding: 0; }}
    :root {{
      --bg: #07090f; --bg1: #0d1117; --bg2: #111827; --border: #1e2d45;
      --gold: #f0b429; --gold2: #fcd34d; --text: #f0f4ff; --muted: #8b9ab5;
    }}
    body {{ font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); line-height: 1.7; }}
    a {{ color: var(--gold); text-decoration: none; }}
    a:hover {{ color: var(--gold2); }}
    .container {{ max-width: 760px; margin: 0 auto; padding: 0 1.5rem; }}
    header {{ background: var(--bg1); border-bottom: 1px solid var(--border); padding: 1rem 0; }}
    header .container {{ display: flex; justify-content: space-between; align-items: center; }}
    .logo {{ font-family: 'Syne', sans-serif; font-size: 1.4rem; font-weight: 800; color: var(--gold); }}
    .breadcrumb {{ font-size: .85rem; color: var(--muted); margin: 2rem 0 1rem; }}
    .breadcrumb a {{ color: var(--muted); }}
    .breadcrumb a:hover {{ color: var(--gold); }}
    .article-meta {{ display: flex; gap: 1rem; color: var(--muted); font-size: .85rem; margin-bottom: 2rem; }}
    .tag {{ background: rgba(240,180,41,.15); color: var(--gold); padding: .25rem .75rem; border-radius: 4px; font-size: .75rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; }}
    h1 {{ font-family: 'Syne', sans-serif; font-size: 2rem; font-weight: 800; line-height: 1.2; margin-bottom: 1rem; }}
    h2 {{ font-family: 'Syne', sans-serif; font-size: 1.4rem; font-weight: 700; margin: 2.5rem 0 1rem; color: var(--gold2); }}
    h3 {{ font-size: 1.1rem; font-weight: 600; margin: 1.5rem 0 .5rem; }}
    p {{ margin-bottom: 1.25rem; color: var(--text); opacity: .92; }}
    .intro {{ font-size: 1.1rem; color: var(--muted); border-left: 3px solid var(--gold); padding-left: 1rem; margin-bottom: 2rem; }}
    .faq-item {{ background: var(--bg2); border: 1px solid var(--border); border-radius: 8px; padding: 1.25rem; margin-bottom: 1rem; }}
    .faq-item h3 {{ margin-top: 0; color: var(--gold); }}
    .faq-item p {{ margin-bottom: 0; }}
    footer {{ background: var(--bg1); border-top: 1px solid var(--border); padding: 2rem 0; margin-top: 4rem; text-align: center; color: var(--muted); font-size: .85rem; }}
    @media(max-width:600px) {{ h1 {{ font-size: 1.5rem; }} .container {{ padding: 0 1rem; }} }}
    /* ---- Oferty affiliate ---- */
    .offers-section {{ background: var(--bg2); border: 1px solid var(--border); border-radius: 12px; padding: 1.75rem; margin: 2.5rem 0; }}
    .offers-title {{ font-family:'Syne',sans-serif; font-size:1.2rem; font-weight:800; color:var(--gold); margin:0 0 .25rem; }}
    .offers-sub {{ font-size:.8rem; color:var(--muted); margin:0 0 1.25rem; }}
    .offer-card {{ background:var(--bg1); border:1px solid var(--border); border-radius:10px; padding:1.1rem 1.25rem; margin-bottom:.85rem; position:relative; transition:border-color .2s; }}
    .offer-card:hover {{ border-color:rgba(240,180,41,.35); }}
    .offer-card--featured {{ border-color:rgba(240,180,41,.45); background:rgba(240,180,41,.04); }}
    .offer-badge {{ position:absolute; top:-10px; right:14px; background:var(--gold); color:#07090f; font-size:.65rem; font-weight:800; text-transform:uppercase; letter-spacing:.06em; padding:.2rem .6rem; border-radius:20px; }}
    .offer-head {{ display:flex; align-items:center; gap:.85rem; margin-bottom:.6rem; }}
    .offer-logo {{ width:36px; height:36px; border-radius:8px; background:rgba(255,255,255,.06); display:flex; align-items:center; justify-content:center; flex-shrink:0; overflow:hidden; }}
    .offer-logo-img {{ width:28px; height:28px; object-fit:contain; }}
    .offer-name {{ font-weight:700; font-size:.95rem; color:var(--text); }}
    .offer-meta {{ font-size:.75rem; color:var(--muted); margin-top:.1rem; }}
    .offer-desc {{ font-size:.88rem; color:var(--muted); margin:.3rem 0 .5rem; opacity:1; }}
    .offer-plusy {{ list-style:none; display:flex; flex-wrap:wrap; gap:.4rem; margin:.4rem 0 .75rem; }}
    .offer-plusy li {{ background:rgba(240,180,41,.1); color:var(--gold2); font-size:.72rem; font-weight:600; padding:.2rem .6rem; border-radius:20px; }}
    .offer-btn {{ display:inline-block; background:var(--gold); color:#07090f; font-weight:700; font-size:.82rem; padding:.45rem 1.1rem; border-radius:6px; text-decoration:none; transition:background .15s,transform .15s; }}
    .offer-btn:hover {{ background:var(--gold2); color:#07090f; transform:translateY(-1px); }}
    .offers-legal {{ font-size:.72rem; color:var(--muted); margin:1rem 0 0; opacity:.7; }}
    @media(max-width:480px) {{ .offer-head {{ flex-wrap:wrap; }} }}
  </style>
</head>
<body>

<header>
  <div class="container">
    <a href="/" class="logo">FinRank</a>
    <nav><a href="/">Strona glowna</a></nav>
  </div>
</header>

<main class="container" style="padding-top:1rem; padding-bottom:3rem;">
  <div class="breadcrumb">
    <a href="/">FinRank</a> &rsaquo; <a href="/blog/">Blog</a> &rsaquo; {article["short"]}
  </div>

  <span class="tag">{cat_label}</span>
  <h1>{article["title"]}</h1>
  <div class="article-meta">
    <span>{format_date_pl(article["date"])}</span>
    <span>{article["read_time"]}</span>
    <span>{article["author"]}</span>
  </div>

  <p class="intro">{article["intro"]}</p>

  {sections_html}

  {offers_html}

  {faq_html}
</main>

<footer>
  <div class="container">
    <p>&copy; {date.today().year} FinRank. Wszystkie prawa zastrzezone.</p>
  </div>
</footer>

</body>
</html>'''


def generate_blog_index(articles: list) -> str:
    cards = ""
    for a in sorted(articles, key=lambda x: x["date"], reverse=True):
        icon, c1, c2 = CATEGORY_ICONS.get(a["category"], ("📄", "rgba(99,102,241,.15)", "rgba(99,102,241,.05)"))
        cat_label = CATEGORY_LABELS.get(a["category"], a["category"])
        cards += f'''
    <a href="/blog/{a["slug"]}/" class="blog-list-card">
      <div class="blog-list-icon" style="background:linear-gradient(135deg,{c1},{c2})">{icon}</div>
      <div class="blog-list-body">
        <span class="tag">{cat_label}</span>
        <h2>{a["title"]}</h2>
        <p>{a["meta_desc"]}</p>
        <div class="blog-list-meta">{format_date_pl(a["date"])} &middot; {a["read_time"]}</div>
      </div>
    </a>'''

    return f'''<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Blog finansowy | FinRank</title>
  <meta name="description" content="Artykuly o kryptowlautach, kontach bankowych i pozyczkach. Porady finansowe 2026.">
  <link rel="canonical" href="{SITE_URL}/blog/">
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-9R80DCEM55"></script>
  <script>window.dataLayer=window.dataLayer||[];function gtag(){{dataLayer.push(arguments)}}gtag('js',new Date());gtag('config','G-9R80DCEM55');</script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Syne:wght@700;800&display=swap" rel="stylesheet">
  <style>
    *,*::before,*::after{{box-sizing:border-box;margin:0;padding:0}}
    :root{{--bg:#07090f;--bg1:#0d1117;--bg2:#111827;--border:#1e2d45;--gold:#f0b429;--gold2:#fcd34d;--text:#f0f4ff;--muted:#8b9ab5}}
    body{{font-family:'Inter',sans-serif;background:var(--bg);color:var(--text);line-height:1.7}}
    a{{color:var(--gold);text-decoration:none}}a:hover{{color:var(--gold2)}}
    .container{{max-width:900px;margin:0 auto;padding:0 1.5rem}}
    header{{background:var(--bg1);border-bottom:1px solid var(--border);padding:1rem 0}}
    header .container{{display:flex;justify-content:space-between;align-items:center}}
    .logo{{font-family:'Syne',sans-serif;font-size:1.4rem;font-weight:800;color:var(--gold)}}
    h1{{font-family:'Syne',sans-serif;font-size:2rem;font-weight:800;margin:2rem 0 1.5rem}}
    .blog-list-card{{display:flex;gap:1.25rem;background:var(--bg2);border:1px solid var(--border);border-radius:12px;padding:1.25rem;margin-bottom:1rem;color:var(--text);transition:border-color .2s,transform .2s}}
    .blog-list-card:hover{{border-color:rgba(240,180,41,.3);transform:translateY(-2px)}}
    .blog-list-icon{{width:60px;height:60px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.5rem;flex-shrink:0}}
    .blog-list-body h2{{font-size:1.05rem;font-weight:700;margin:.25rem 0 .35rem;color:var(--text)}}
    .blog-list-body p{{font-size:.85rem;color:var(--muted);margin:0}}
    .blog-list-meta{{font-size:.78rem;color:var(--muted);margin-top:.35rem}}
    .tag{{background:rgba(240,180,41,.15);color:var(--gold);padding:.15rem .5rem;border-radius:4px;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em}}
    footer{{background:var(--bg1);border-top:1px solid var(--border);padding:2rem 0;margin-top:4rem;text-align:center;color:var(--muted);font-size:.85rem}}
  </style>
</head>
<body>
<header><div class="container"><a href="/" class="logo">FinRank</a><nav><a href="/">Strona glowna</a></nav></div></header>
<main class="container">
  <h1>Blog finansowy</h1>
  {cards}
</main>
<footer><div class="container"><p>&copy; {date.today().year} FinRank. Wszystkie prawa zastrzezone.</p></div></footer>
</body>
</html>'''


def update_index_carousel(articles: list):
    index_path = ROOT / "index.html"
    html = index_path.read_text(encoding="utf-8")

    latest = sorted(articles, key=lambda x: x["date"], reverse=True)[:6]

    slides = ""
    for a in latest:
        icon, c1, c2 = CATEGORY_ICONS.get(a["category"], ("📄", "rgba(99,102,241,.15)", "rgba(99,102,241,.05)"))
        cat_label = CATEGORY_LABELS.get(a["category"], a["category"])
        slides += f'''
        <div class="swiper-slide" style="height:auto">
          <div class="blog-card">
            <div class="blog-card-img" style="background:linear-gradient(135deg,{c1},{c2})">{icon}</div>
            <div class="blog-card-body">
              <div class="blog-tag">{cat_label}</div>
              <div class="blog-title"><a href="/blog/{a["slug"]}/">{a["short"]}</a></div>
              <div class="blog-meta"><span>{format_date_pl(a["date"])}</span><span>{a["read_time"]}</span></div>
            </div>
          </div>
        </div>
'''

    pattern = r'(<div class="swiper-wrapper">)\s*\n.*?(</div>\s*<div class="swiper-pagination")'
    replacement = f'\\1\n{slides}\n      \\2'
    new_html = re.sub(pattern, replacement, html, flags=re.DOTALL)

    new_html = new_html.replace(
        '<a href="#blog" class="btn btn-outline btn-sm hide-m">Wszystkie artykuły →</a>',
        '<a href="/blog/" class="btn btn-outline btn-sm hide-m">Wszystkie artykuły →</a>'
    )

    index_path.write_text(new_html, encoding="utf-8")
    print(f"  zaktualizowano karuzelę w index.html ({len(latest)} kart)")


def generate_sitemap(articles: list):
    today_str = date.today().isoformat()
    urls = [
        (SITE_URL + "/", "1.0"),
        (SITE_URL + "/blog/", "0.9"),
    ]
    for a in articles:
        urls.append((f"{SITE_URL}/blog/{a['slug']}/", "0.8"))

    existing_sitemap = ROOT / "sitemap.xml"
    if existing_sitemap.exists():
        content = existing_sitemap.read_text(encoding="utf-8")
        existing_urls = set(re.findall(r'<loc>([^<]+)</loc>', content))
        for url in existing_urls:
            if not any(u[0] == url for u in urls):
                urls.append((url, "0.7"))

    sitemap = '<?xml version="1.0" encoding="UTF-8"?>\n'
    sitemap += '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n'
    for url, priority in urls:
        sitemap += f'  <url><loc>{url}</loc><lastmod>{today_str}</lastmod><priority>{priority}</priority></url>\n'
    sitemap += '</urlset>'
    (ROOT / "sitemap.xml").write_text(sitemap, encoding="utf-8")
    print(f"  sitemap.xml ({len(urls)} URL)")


def _esc(s: str) -> str:
    return s.replace('"', '&quot;').replace("'", "&#39;")


def build():
    articles = load_articles()
    print(f"FinRank Blog Generator — {len(articles)} artykulow\n")

    blog_dir = ROOT / "blog"
    blog_dir.mkdir(exist_ok=True)

    for a in articles:
        post_dir = blog_dir / a["slug"]
        post_dir.mkdir(parents=True, exist_ok=True)
        html = generate_blog_post_html(a)
        (post_dir / "index.html").write_text(html, encoding="utf-8")
    print(f"  wygenerowano {len(articles)} stron blogu")

    index_html = generate_blog_index(articles)
    (blog_dir / "index.html").write_text(index_html, encoding="utf-8")
    print("  wygenerowano blog/index.html")

    update_index_carousel(articles)
    generate_sitemap(articles)

    print("\nGotowe!")


if __name__ == "__main__":
    build()
