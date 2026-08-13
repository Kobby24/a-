from pathlib import Path
root = Path(r'C:\Users\USER\OneDrive\Desktop\a-')
for p in sorted(root.rglob('index.html')):
    s = p.read_text(encoding='utf-8', errors='ignore')
    if 'elementor-element-37817048' not in s:
        continue
    social_start = s.index('<div class="elementor-element elementor-element-37817048')
    social_end_marker = '</div>\n            </div>\n            <div class="elementor-element elementor-element-45db4a37'
    social_end = s.index(social_end_marker, social_start)
    social_block = s[social_start:social_end + len('</div>')]
    s = s[:social_start] + s[social_end + len('</div>'):]
    quick_marker = '<div class="elementor-element elementor-element-259a632d e-con-full e-flex e-con e-child" data-id="259a632d" data-element_type="container">'
    quick_start = s.index(quick_marker)
    nav_marker = '<div class="elementor-element elementor-element-743f538c elementor-nav-menu--dropdown-none elementor-widget elementor-widget-nav-menu"'
    nav_start = s.index(nav_marker, quick_start)
    s = s[:nav_start] + social_block + '\n' + s[nav_start:]
    p.write_text(s, encoding='utf-8')
    print(f'updated {p.relative_to(root)}')
