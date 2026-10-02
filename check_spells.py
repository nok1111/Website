import re

path = r'C:\Users\nokturno\Desktop\mehah-withupstream2\otclient\modules\gamelib\spells.lua'
text = open(path, 'r', encoding='utf-8').read()

# Find all spell definitions: ['Name'] = {id = X, ...}
# We need to match the full block including nested braces
pattern = r"\['([^']+)'\] = \{(id = \d+,.*?)\},?\s*$"
matches = re.findall(pattern, text, re.MULTILINE | re.DOTALL)

has_learn_true = []
has_learn_false = []
no_learn = []

for name, body in matches:
    sid_match = re.search(r'id = (\d+)', body)
    if not sid_match:
        continue
    sid = sid_match.group(1)
    
    if 'needLearn = true' in body:
        has_learn_true.append((sid, name))
    elif 'needLearn = false' in body:
        has_learn_false.append((sid, name))
    elif "needLearn = false" in body:
        has_learn_false.append((sid, name))
    else:
        no_learn.append((sid, name, body[:100]))

print("=== needLearn = true ===")
for sid, name in sorted(has_learn_true, key=lambda x: int(x[0])):
    print(f"  {sid}: {name}")

print(f"\n=== needLearn = false ({len(has_learn_false)}) ===")
for sid, name in sorted(has_learn_false, key=lambda x: int(x[0])):
    print(f"  {sid}: {name}")

print(f"\n=== NO needLearn property ({len(no_learn)}) ===")
for sid, name, body in sorted(no_learn, key=lambda x: int(x[0])):
    print(f"  {sid}: {name}")
