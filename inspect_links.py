from pathlib import Path
import re
base = Path(r'c:\Users\USER\OneDrive\Desktop\clone')
page_slugs = ['', 'about-us', 'basement-renovations', 'contact-us', 'deck-construction', 'fencing', 'home-additions', 'kitchen-and-bath', 'painting', 'quote']
pattern = re.compile(r'<a[^>]+href="(https://infinitepropertyimprovement.com/([^"/]+/))"', re.IGNORECASE)
for file in sorted(base.rglob('index.html')):
    content = file.read_text(encoding='utf8')
    matches = pattern.findall(content)
    if matches:
        print(f'FILE: {file.relative_to(base)}')
        for full, slug in matches:
            if slug in [s + '/' for s in page_slugs if s]:
                print(f'  {full}')
            elif full == 'https://infinitepropertyimprovement.com/':
                print(f'  {full}')
        print()
