import json
import os

transcript_path = '/Users/rasya2121/.gemini/antigravity-ide/brain/675dfe70-0ccd-49e4-8adc-b0d1cd0f38e4/.system_generated/logs/transcript_full.jsonl'
workspace = '/Users/rasya2121/Documents/code/template-poin-siswa/'

latest_file_states = {}

if not os.path.exists(transcript_path):
    print("Transcript not found")
    exit()

with open(transcript_path, 'r') as f:
    for line in f:
        try:
            step = json.loads(line)
            if step.get('type') == 'PLANNER_RESPONSE':
                tool_calls = step.get('tool_calls', [])
                for tc in tool_calls:
                    name = tc.get('name', '')
                    if name in ['write_to_file', 'replace_file_content', 'multi_replace_file_content']:
                        args = tc.get('args', {})
                        path = args.get('TargetFile')
                        if path and path.startswith(workspace):
                            if name == 'write_to_file':
                                latest_file_states[path] = args.get('CodeContent', '')
                            elif name == 'replace_file_content':
                                # This is harder because it's a diff. But wait, I just want to know if it was modified.
                                # Let's just print that it was modified.
                                pass
        except Exception as e:
            pass

print("Files written entirely:")
for path in latest_file_states.keys():
    print(path[len(workspace):])
