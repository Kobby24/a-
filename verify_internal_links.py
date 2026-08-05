from pathlib import Path
import re
base = Path(r'C:\Users\USER\OneDrive\Desktop\clone')
page_urls = {
    'https://infinitepropertyimprovement.com/',
    'https://infinitepropertyimprovement.com/about-us/',
    'https://infinitepropertyimprovement.com/basement-renovations/',
    'https://infinitepropertyimprovement.com/contact-us/',
    'https://infinitepropertyimprovement.com/deck-construction/',
    'https://infinitepropertyimprovement.com/fencing/',
    'https://infinitepropertyimprovement.com/home-additions/',
    'https://infinitepropertyimprovement.com/kitchen-and-bath/',
    'https://infinitepropertyimprovement.com/painting/',
    'https://infinitepropertyimprovement.com/quote/',
}
pattern = re.compile(r'<a[^>]+href=["\'](?P<url>https://infinitepropertyimprovement.com/[^"\']*/)["\']', re.IGNORECASE)
remaining = []
for file in sorted(base.rglob('index.html')):
    text = file.read_text(encoding='utf8')
    hits = sorted({m.group('url') for m in pattern.finditer(text) if m.group('url') in page_urls})
    if hits:
        remaining.append((file.relative_to(base), hits))
if remaining:
    for file, hits in remaining:
        print(f'FILE: {file}')
        for url in hits:
            print('  ', url)
else:
    print('NO remaining mapped internal page absolute URLs')
