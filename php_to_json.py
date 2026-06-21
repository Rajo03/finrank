#!/usr/bin/env python3
"""Convert articles.php to blog-articles.json"""
import io
import re
import json
import sys

sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding="utf-8", errors="replace")

with open("src/data/articles.php", "r", encoding="utf-8") as f:
    php = f.read()

slugs = re.findall(r"'([^']+)'\s*=>\s*\[\s*\n\s*'slug'", php)


def extract_val(block, key):
    pattern = "'" + key + r"'\s*=>\s*'((?:[^'\\]|\\.)*)'"
    m = re.search(pattern, block)
    if m:
        return m.group(1).replace("\\'", "'")
    return ""


def extract_sections(block):
    secs = []
    pattern = r"'heading'\s*=>\s*'((?:[^'\\]|\\.)*)'\s*,\s*'content'\s*=>\s*'((?:[^'\\]|\\.)*)'"
    for m in re.finditer(pattern, block):
        secs.append({
            "heading": m.group(1).replace("\\'", "'"),
            "content": m.group(2).replace("\\'", "'"),
        })
    return secs


def extract_faq(block):
    faq = []
    faq_match = re.search(r"'faq'\s*=>\s*\[(.*?)\],\s*\]", block, re.DOTALL)
    if faq_match:
        pattern = r"\['((?:[^'\\]|\\.)*)'\s*,\s*'((?:[^'\\]|\\.)*)'\]"
        for m in re.finditer(pattern, faq_match.group(1)):
            faq.append([
                m.group(1).replace("\\'", "'"),
                m.group(2).replace("\\'", "'"),
            ])
    return faq


articles = []
for slug in slugs:
    idx = php.find(f"'{slug}' => [")
    if idx == -1:
        continue
    end_idx = php.find("\n    '", idx + len(slug) + 10)
    if end_idx == -1:
        end_idx = len(php)
    block = php[idx:end_idx]

    a = {
        "slug": extract_val(block, "slug") or slug,
        "type": extract_val(block, "type") or "guide",
        "title": extract_val(block, "title"),
        "short": extract_val(block, "short"),
        "category": extract_val(block, "category"),
        "date": extract_val(block, "date"),
        "updated": extract_val(block, "updated"),
        "read_time": extract_val(block, "read_time"),
        "author": extract_val(block, "author") or "Redakcja FinRank",
        "meta_title": extract_val(block, "meta_title"),
        "meta_desc": extract_val(block, "meta_desc"),
        "intro": extract_val(block, "intro"),
        "sections": extract_sections(block),
        "faq": extract_faq(block),
    }

    if a["title"] and a["sections"]:
        articles.append(a)
        print(f"  OK: {slug} ({len(a['sections'])} sections, {len(a['faq'])} faq)")

with open("blog-articles.json", "w", encoding="utf-8") as f:
    json.dump(articles, f, ensure_ascii=False, indent=2)

print(f"\nZapisano {len(articles)} artykulow do blog-articles.json")
