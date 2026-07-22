import os
import re

def update_main_css():
    filepath = '/home/vanthanh/Ind-Project/Laravel/music-streaming-platform/frontend/src/assets/main.css'
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Add to @theme block
    if '--color-theme-success:' not in content:
        content = content.replace(
            '--color-theme-danger: var(--color-danger);',
            '--color-theme-danger: var(--color-danger);\n  --color-theme-success: var(--color-success);\n  --color-theme-warning: var(--color-warning);\n  --color-theme-info: var(--color-info);'
        )
        
    # Add to Light Mode
    if '--color-success:' not in content:
        content = content.replace(
            '--color-danger: #EF4444;',
            '--color-danger: #EF4444;\n  --color-success: #10B981;\n  --color-warning: #F59E0B;\n  --color-info: #3B82F6;'
        )
        
    # Add to Dark Mode
    if '--color-success:' not in content.split(':root.dark {')[-1]:
        # This is a bit hacky, let's just do a string replace on the dark mode section
        dark_section = content.split(':root.dark {')[1]
        new_dark_section = dark_section.replace(
            '--color-danger: #F87171;',
            '--color-danger: #F87171;\n  --color-success: #34D399;\n  --color-warning: #FBBF24;\n  --color-info: #60A5FA;'
        )
        content = content.split(':root.dark {')[0] + ':root.dark {' + new_dark_section

    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(content)
        
if __name__ == '__main__':
    update_main_css()
