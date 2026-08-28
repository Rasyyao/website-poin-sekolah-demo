import re

file_path = 'resources/views/admin/students/index.blade.php'
with open(file_path, 'r') as f:
    content = f.read()

# 1. Replace classes
class_map = {
    'bg-surface-light': 'bg-white',
    'bg-surface-lighter/30': 'bg-slate-50/50',
    'bg-surface-lighter/50': 'bg-slate-50',
    'bg-surface-lighter': 'bg-slate-50',
    'bg-surface': 'bg-slate-100',
    'border-gray-700/50': 'border-slate-200',
    'border-gray-700/30': 'border-slate-100',
    'border-gray-700': 'border-slate-200',
    'border-gray-600/50': 'border-slate-200',
    'text-gray-200': 'text-slate-900',
    'text-gray-300': 'text-slate-700',
    'text-gray-400': 'text-slate-500',
    'text-gray-500': 'text-slate-400',
    'hover:bg-surface-lighter/30': 'hover:bg-slate-50',
    'focus:ring-primary-500': 'focus:ring-blue-500',
    'text-primary-300': 'text-blue-600',
    'bg-primary-600': 'bg-blue-600',
    'hover:bg-primary-500': 'hover:bg-blue-700',
    'divide-gray-700/30': 'divide-slate-100'
}

for old, new in class_map.items():
    content = content.replace(old, new)

# 2. Re-arrange page-actions
# We know page-actions has Export, Import, and Tambah Siswa.
# Let's extract the Import and Tambah Siswa.
import_pattern = r'(<div x-data="\{ open: false \}" class="relative">\s*<button @click="open = !open".*?Upload Excel/CSV\s*</button>\s*</div>\s*</div>)'
# The Tambah button
tambah_pattern = r'(<button x-data @click="\$dispatch\(\'open-modal\'\)".*?\+ Tambah Siswa</button>)'

import_match = re.search(import_pattern, content, re.DOTALL)
tambah_match = re.search(tambah_pattern, content, re.DOTALL)

if import_match and tambah_match:
    import_html = import_match.group(1)
    tambah_html = tambah_match.group(1)
    
    # Remove them from page-actions
    content = content.replace(import_html, '')
    content = content.replace(tambah_html, '')
    
    # Find the table header
    header_pattern = r'(<div class="p-4 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-50/50">\s*<form method="GET" class="flex items-center gap-3 w-full sm:w-auto">.*?</form>)\s*<div class="text-sm text-slate-500 whitespace-nowrap">.*?</div>'
    header_match = re.search(header_pattern, content, re.DOTALL)
    
    if header_match:
        new_header = header_match.group(1) + f'\n        <div class="flex items-center gap-3">\n            {import_html}\n            {tambah_html}\n        </div>'
        content = content.replace(header_match.group(0), new_header)

with open(file_path, 'w') as f:
    f.write(content)
print("Done")
