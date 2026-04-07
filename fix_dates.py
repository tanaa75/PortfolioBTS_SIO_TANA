import sys, io, re
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')

file = "index.html"

with open(file, "r", encoding="utf-8") as f:
    content = f.read()

# Nettoyer les dates cassees (DD...c. etc.)
content = re.sub(r'>D+[^<]*c\.2025 - Avr\.2026<', '>' + 'D\u00e9c.2025 - Avr.2026<', content)

def fix_annee(content, title_search, new_date):
    pattern = r'(<span class="projet-annee">)[^<]*(</span>)([\s\S]{1,400}?' + re.escape(title_search) + r')'
    result, n = re.subn(pattern, r'\g<1>' + new_date + r'\2\3', content, count=1)
    print(f"{'OK' if n else 'MISS'} {title_search}: {new_date}")
    return result

content = fix_annee(content, "Portfolio Personnel", "Nov.2025 - Avr.2026")
content = fix_annee(content, "Click &amp; Collect", "D\u00e9c.2025 - Avr.2026")
content = fix_annee(content, "Jeu de M\u00e9moire", "D\u00e9c.2025 - Avr.2026")
content = fix_annee(content, "WebCaisseFX", "Avr.2025")
content = fix_annee(content, "Guess What", "02/05/2025")
content = fix_annee(content, "Calculatrice Swing", "18/03/2025")
content = fix_annee(content, "CV Web Syntax", "20/12/2024")
content = fix_annee(content, "Restiloc", "23/03/2026")
content = fix_annee(content, "HackatInnov", "Avr.2025")

# GitHub pour Restiloc
content = re.sub(
    r'(<h3 class="projet-titre">Restiloc</h3>[\s\S]*?<div class="projet-actions">)\s*<span class="projet-btn-secondary" style="cursor:default;"><i class="fas fa-graduation-cap"></i> \u00c9tude de cas</span>',
    r'\1\n                            <a href="https://github.com/tanaa75/Restiloc" target="_blank" rel="noopener noreferrer" class="projet-btn-primary"><i class="fab fa-github"></i> GitHub</a>',
    content, count=1
)

# GitHub pour HackatInnov
content = re.sub(
    r'(<h3 class="projet-titre">HackatInnov</h3>[\s\S]*?<div class="projet-actions">)\s*<span class="projet-btn-secondary" style="cursor:default;"><i class="fas fa-graduation-cap"></i> \u00c9tude de cas</span>',
    r'\1\n                            <a href="https://github.com/tanaa75/HackatInnov" target="_blank" rel="noopener noreferrer" class="projet-btn-primary"><i class="fab fa-github"></i> GitHub</a>',
    content, count=1
)

with open(file, "w", encoding="utf-8") as f:
    f.write(content)

print("\nDone!")
with open(file, "r", encoding="utf-8") as f:
    lines = f.readlines()
print("\n=== DATES FINALES ===")
for i, line in enumerate(lines):
    if 'projet-annee' in line or 'projet-titre' in line:
        print(f"L{i+1}: {line.strip()}")
