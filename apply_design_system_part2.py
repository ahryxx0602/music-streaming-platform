import os
import re

directories = [
    'frontend/src/views/admin/playlists',
    'frontend/src/components/admin/features/playlists',
    'frontend/src/views/admin/settings',
    'frontend/src/components/admin/features/settings',
    'frontend/src/views/admin/audit',
    'frontend/src/components/admin/features/audit',
    'frontend/src/views/admin/genres',
]

replacements = {
    r'\bbg-white\b': 'bg-theme-surface',
    r'\btext-slate-900\b': 'text-theme-text',
    r'\btext-slate-800\b': 'text-theme-text',
    r'\btext-slate-700\b': 'text-theme-text-sec',
    r'\btext-slate-600\b': 'text-theme-text-sec',
    r'\btext-slate-500\b': 'text-theme-text-sec',
    r'\btext-slate-400\b': 'text-theme-text-sec',
    r'\bbg-slate-50\b': 'bg-theme-surface-hover',
    r'\bbg-slate-100\b': 'bg-theme-surface-hover',
    r'\bbg-slate-200\b': 'bg-theme-surface-hover',
    r'\bborder-slate-100\b': 'border-theme-border',
    r'\bborder-slate-200\b': 'border-theme-border',
    r'\bborder-slate-300\b': 'border-theme-border',
    r'\bborder-slate-800\b': 'border-theme-border',
    r'\bhover:bg-slate-50\b': 'hover:bg-theme-bg',
    r'\bhover:bg-slate-100\b': 'hover:bg-theme-surface-hover',
    r'\btext-admin-primary\b': 'text-theme-primary',
    r'\bbg-admin-primary\b': 'bg-theme-primary',
    r'\bhover:text-admin-primary\b': 'hover:text-theme-primary',
    r'\bborder-admin-primary\b': 'border-theme-primary',
    r'\bdivide-slate-200\b': 'divide-theme-border',
    r'\bdivide-slate-100\b': 'divide-theme-border',
    r'\bshadow-sm\b': 'shadow-[var(--shadow-glow)]',
    r'\bshadow-md\b': 'shadow-[var(--shadow-glow)]',
    r'\bdark:bg-\[?[^ ]+\]?\b': '',
    r'\bdark:text-[^ ]+\b': '',
    r'\bdark:border-[^ ]+\b': '',
    r'\bdark:hover:bg-[^ ]+\b': '',
    r'\bdark:hover:text-[^ ]+\b': '',
}

def process_file(filepath):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()
        
    original = content
    for pattern, repl in replacements.items():
        content = re.sub(pattern, repl, content)
        
    content = re.sub(r'  +', ' ', content)
    content = re.sub(r' class=" "', ' class=""', content)
        
    if content != original:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(content)
        print(f"Updated: {filepath}")

def main():
    base_dir = "/home/vanthanh/Ind-Project/Laravel/music-streaming-platform"
    for d in directories:
        full_path = os.path.join(base_dir, d)
        if not os.path.exists(full_path):
            continue
        for root, _, files in os.walk(full_path):
            for file in files:
                if file.endswith('.vue'):
                    process_file(os.path.join(root, file))

if __name__ == "__main__":
    main()
