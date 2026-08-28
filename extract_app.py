import json

transcript_path = '/Users/rasya2121/.gemini/antigravity-ide/brain/675dfe70-0ccd-49e4-8adc-b0d1cd0f38e4/.system_generated/logs/transcript_full.jsonl'
workspace = '/Users/rasya2121/Documents/code/template-poin-siswa/'

count = 0
with open(transcript_path, 'r') as f:
    for line in f:
        try:
            step = json.loads(line)
            if step.get('type') == 'PLANNER_RESPONSE':
                tool_calls = step.get('tool_calls', [])
                for tc in tool_calls:
                    if tc.get('name') == 'run_command':
                        args = tc.get('args', {})
                        cmd = args.get('CommandLine', '')
                        if 'cat << \'EOF\' >' in cmd and ('app.blade.php' in cmd or 'app.css' in cmd or 'sidebar.blade.php' in cmd):
                            count += 1
                            with open(f'recovery/from_transcript/cmd_{count}.sh', 'w') as out:
                                out.write(cmd)
        except Exception as e:
            pass
