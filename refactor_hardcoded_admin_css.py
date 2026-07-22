import os
import re

directories = [
    'frontend/src/views/admin',
    'frontend/src/components/admin',
]

replacements = {
    # Grays / Slates
    r'\btext-slate-300\b': 'text-theme-text-sec',
    r'\btext-slate-400\b': 'text-theme-text-sec',
    r'\bbg-slate-900/20\b': 'bg-theme-bg/50',
    r'\bbg-slate-900/40\b': 'bg-theme-bg/60',
    r'\bbg-slate-900/60\b': 'bg-theme-bg/80',
    r'\bborder-slate-[12]00\b': 'border-theme-border',
    r'\bborder-slate-[34]00\b': 'border-theme-border',
    r'\bbg-slate-300\b': 'bg-theme-border',

    # Indigos (Primary)
    r'\bbg-indigo-50\b': 'bg-theme-primary/10',
    r'\bbg-indigo-100\b': 'bg-theme-primary/15',
    r'\btext-indigo-400\b': 'text-theme-primary',
    r'\btext-indigo-600\b': 'text-theme-primary',
    r'\bborder-indigo-100\b': 'border-theme-primary/20',
    r'\bg-indigo-[56]00\b': 'bg-theme-primary',

    # Roses (Danger)
    r'\bbg-rose-50\b': 'bg-theme-danger/10',
    r'\bbg-rose-100\b': 'bg-theme-danger/15',
    r'\btext-rose-[56]00\b': 'text-theme-danger',
    r'\btext-rose-700\b': 'text-theme-danger',
    r'\bborder-rose-200\b': 'border-theme-danger/20',
    r'\bborder-rose-300\b': 'border-theme-danger/30',
    r'\bg-rose-[56]00\b': 'bg-theme-danger',
    r'\bhover:bg-rose-50\b': 'hover:bg-theme-danger/10',
    r'\bhover:text-rose-500\b': 'hover:text-theme-danger',
    r'\bhover:border-rose-300\b': 'hover:border-theme-danger/30',
    r'\bhover:bg-rose-700\b': 'hover:bg-theme-danger/80',

    # Emeralds (Success)
    r'\bbg-emerald-50\b': 'bg-theme-success/10',
    r'\bbg-emerald-100\b': 'bg-theme-success/15',
    r'\btext-emerald-[56]00\b': 'text-theme-success',
    r'\btext-emerald-700\b': 'text-theme-success',
    r'\bborder-emerald-200\b': 'border-theme-success/20',
    r'\bbg-emerald-[56]00\b': 'bg-theme-success',

    # Ambers (Warning)
    r'\bbg-amber-50\b': 'bg-theme-warning/10',
    r'\bbg-amber-100\b': 'bg-theme-warning/15',
    r'\btext-amber-[67]00\b': 'text-theme-warning',
    r'\bborder-amber-200\b': 'border-theme-warning/20',
    r'\bbg-amber-600\b': 'bg-theme-warning',
    r'\bhover:bg-amber-700\b': 'hover:bg-theme-warning/80',

    # Blues (Info)
    r'\bbg-blue-50\b': 'bg-theme-info/10',
    r'\bbg-blue-100\b': 'bg-theme-info/15',
    r'\btext-blue-[67]00\b': 'text-theme-info',
    r'\bborder-blue-200\b': 'border-theme-info/20',
    r'\bbg-blue-600\b': 'bg-theme-info',
    r'\bhover:bg-blue-700\b': 'hover:bg-theme-info/80',

    # Clean up dark mode specific variants that clash with /15 opacity
    r'\bdark:bg-[a-z]+-[0-9]+/15\b': '',
    r'\bdark:text-[a-z]+-[0-9]+\b': '',
    r'\bdark:border-[a-z]+-[0-9]+/20\b': '',
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
