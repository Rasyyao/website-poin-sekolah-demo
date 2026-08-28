import os
import json
import shutil
import glob
from pathlib import Path

history_dirs = [
    os.path.expanduser('~/Library/Application Support/Antigravity IDE/User/History'),
    os.path.expanduser('~/Library/Application Support/Code/User/History'),
    os.path.expanduser('~/Library/Application Support/Cursor/User/History'),
    os.path.expanduser('~/Library/Application Support/Antigravity/User/History')
]

workspace = '/Users/rasya2121/Documents/code/template-poin-siswa/'

lost_files = [
    'app/Http/Controllers/Admin/AcademicYearController.php',
    'app/Http/Controllers/Admin/ReportController.php',
    'app/Http/Controllers/Admin/StudentController.php',
    'app/Services/ReportService.php',
    'resources/css/app.css',
    'resources/views/admin/academic-years/index.blade.php',
    'resources/views/admin/classes/index.blade.php',
    'resources/views/admin/dashboard.blade.php',
    'resources/views/admin/points-log/index.blade.php',
    'resources/views/admin/reports/ranking.blade.php',
    'resources/views/admin/staff/index.blade.php',
    'resources/views/admin/students/index.blade.php',
    'resources/views/layouts/app.blade.php',
    'resources/views/layouts/partials/sidebar.blade.php',
    'resources/views/super-admin/schools/index.blade.php',
    'routes/web.php'
]

recovered = []

for hdir in history_dirs:
    if not os.path.exists(hdir): continue
    for root, dirs, files in os.walk(hdir):
        if 'entries.json' in files:
            with open(os.path.join(root, 'entries.json'), 'r') as f:
                try:
                    data = json.load(f)
                    file_uri = data.get('resource', '')
                    if file_uri.startswith('file://' + workspace):
                        rel_path = file_uri[len('file://' + workspace):]
                        if rel_path in lost_files:
                            entries = data.get('entries', [])
                            if entries:
                                entries.sort(key=lambda x: x.get('timestamp', 0), reverse=True)
                                
                                # Pick the entry that is NOT from the exact moment of our reset.
                                # To be safe, let's just pick the latest entry and copy it. If it's the reset one, we might need to pick the second latest.
                                # Let's print out the timestamps!
                                print(f"Found history for {rel_path} in {hdir}")
                                for e in entries[:3]:
                                    print(f"  Entry {e['id']} timestamp {e.get('timestamp', 0)}")
                                
                                # Let's pick the entry with the largest size or the one right before the crash.
                                # Let's copy the top 3 into recovery_dir
                                for i, e in enumerate(entries[:3]):
                                    latest_id = e['id']
                                    source_file = os.path.join(root, latest_id)
                                    recovery_dir = os.path.join(workspace, 'recovery', f'v{i}')
                                    os.makedirs(os.path.join(recovery_dir, os.path.dirname(rel_path)), exist_ok=True)
                                    shutil.copy2(source_file, os.path.join(recovery_dir, rel_path))
                                recovered.append(rel_path)
                except Exception as e:
                    pass

print("Recovered files:")
for f in set(recovered):
    print(f)
