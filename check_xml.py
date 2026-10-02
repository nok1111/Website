import re

path = r'C:\Users\nokturno\Desktop\1.4 konakon\TFS-1.4.2-Compatible-Aura-Effect-Wings-Shader-MEHAH\data\spells\spells.xml'
text = open(path, 'r', encoding='utf-8').read()

# Find all spell tags with their attributes
pattern = r'<instant[^>]*spellid="(\d+)"[^>]*name="([^"]+)"[^>]*>'
matches = re.findall(pattern, text)

learn1 = []
learn0 = []
no_attr = []

for sid, name in matches:
    full_match = re.search(r'<instant[^>]*spellid="' + sid + r'"[^>]*>', text)
    if not full_match:
        continue
    tag = full_match.group(0)
    if 'needlearn="1"' in tag:
        learn1.append((sid, name))
    elif 'needlearn="0"' in tag:
        learn0.append((sid, name))
    else:
        no_attr.append((sid, name))

print("=== Server needlearn=1 (talent spells, should have needLearn=true in client) ===")
for sid, name in sorted(learn1, key=lambda x: int(x[0])):
    print(f"  {sid}: {name}")

print(f"\n=== Server needlearn=0 ({len(learn0)}) ===")
for sid, name in sorted(learn0, key=lambda x: int(x[0])):
    print(f"  {sid}: {name}")

print(f"\n=== Server WITHOUT needlearn attr ({len(no_attr)}) ===")
for sid, name in sorted(no_attr, key=lambda x: int(x[0])):
    print(f"  {sid}: {name}")
