import re

file_path = 'resources/views/admin/staff/index.blade.php'
with open(file_path, 'r') as f:
    content = f.read()

# 1. Class Replacements
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

# 2. Extract Form and Tambah Button
page_actions_start = content.find("@section('page-actions')")
page_actions_end = content.find("@endsection", page_actions_start)
page_actions_content = content[page_actions_start + len("@section('page-actions')"):page_actions_end]

# Extract form
form_match = re.search(r'(<form method="GET".*?</form>)', page_actions_content, re.DOTALL)
form_html = form_match.group(1) if form_match else ""

# Modify the search input placeholder logic (add icon)
if '<input type="text"' in form_html:
    # Wrap input in relative div with icon
    form_html = form_html.replace(
        '<input type="text"',
        '<div class="relative w-full sm:w-auto">\n                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>\n                <input type="text"'
    )
    form_html = form_html.replace('class="px-4', 'class="pl-9 pr-4')
    # Close the relative div before the select or submit button
    form_html = re.sub(r'(<input type="text"[\s\S]*?>)', r'\1\n            </div>', form_html)
    
# Extract Tambah button
tambah_match = re.search(r'(<button x-data @click="\$dispatch\(\'open-modal\'\)".*?</button>)', page_actions_content, re.DOTALL)
tambah_html = tambah_match.group(1) if tambah_match else ""

# Remove margin left from tambah button since it's going inside header flex
tambah_html = tambah_html.replace(' ml-3', '')

# 3. Create Export Dropdown for page-actions
export_pdf_route = re.search(r"route\('([^']+)', 'pdf'\)", page_actions_content)
export_excel_route = re.search(r"route\('([^']+)', 'excel'\)", page_actions_content)

if export_pdf_route and export_excel_route:
    route_name_pdf = export_pdf_route.group(1)
    route_name_excel = export_excel_route.group(1)
    export_dropdown = f"""
    <div class="flex items-center gap-3">
        <div x-data="{{ open: false }}" class="relative">
            <button @click="open = !open" @click.away="open = false" type="button" class="flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-50 transition-colors shadow-sm cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Export
            </button>
            <div x-show="open" style="display: none;"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
                 class="absolute right-0 mt-2 w-36 bg-white rounded-xl shadow-xl border border-slate-200 overflow-hidden z-50 transform origin-top-right">
                <a href="{{{{ route('{route_name_pdf}', 'pdf') }}}}" target="_blank" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors">
                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 7h2v6h-2zM11 15h2v2h-2z"/></svg>
                    PDF
                </a>
                <a href="{{{{ route('{route_name_excel}', 'excel') }}}}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors">
                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Excel
                </a>
            </div>
        </div>
    </div>
"""
    new_page_actions = f"@section('page-actions')\n{export_dropdown}@endsection"
    content = content[:page_actions_start] + new_page_actions + content[page_actions_end + len("@endsection"):]

# 4. Inject into Table Card
table_start = content.find('<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">')
if table_start != -1:
    header_html = f"""
    <div class="p-4 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-50/50">
        {form_html}
        <div class="flex items-center gap-3">
            {tambah_html}
        </div>
    </div>
"""
    insert_pos = table_start + len('<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">')
    content = content[:insert_pos] + header_html + content[insert_pos:]

with open(file_path, 'w') as f:
    f.write(content)
print("Done")
