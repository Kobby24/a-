from pathlib import Path

root = Path(r"C:\Users\USER\OneDrive\Desktop\clone")
replacements = {
    "about-us/about-us": "about-us/about-us",
    "contact-us/contact-us": "contact-us/contact-us",
    "landing-page/": "landing-page/",
    "landing-page": "landing-page",
}
updated_files = []
for path in sorted(root.rglob("*")):
    if path.is_file() and path.suffix.lower() in {".html", ".py", ".js", ".css"}:
        text = path.read_text(encoding="utf8")
        new_text = text
        for old, new in replacements.items():
            new_text = new_text.replace(old, new)
        if new_text != text:
            bak = path.with_suffix(path.suffix + ".bak")
            if not bak.exists():
                bak.write_text(text, encoding="utf8")
            path.write_text(new_text, encoding="utf8")
            updated_files.append(path.relative_to(root))
            print(f"UPDATED {path.relative_to(root)}")
print(f"TOTAL UPDATED: {len(updated_files)}")
