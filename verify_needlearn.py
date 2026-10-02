import re

path = r'C:\Users\nokturno\Desktop\mehah-withupstream2\otclient\modules\gamelib\spells.lua'
text = open(path, 'r', encoding='utf-8').read()

# Server needlearn=1 spell IDs (from our earlier check)
server_needlearn = {4,8,11,12,13,14,20,23,29,30,31,48,49,51,54,57,61,65,68,71,76,77,78,85,89,92,111,113,114,134,135,136,152,154,157,160,166,168,175,178,180,182,183,189,190,193,194,203,221,291,292,293,294,295,296,297,298,299,300,301,302,303,304,305,306,307,308,309,310,504,505,506,507,508,509,510,511,512,513,514}

# Find all spell definitions in client
pattern = r"\['([^']+)'\] = \{(id = (\d+),.*?)\},?\s*$"
matches = re.findall(pattern, text, re.MULTILINE | re.DOTALL)

client_spells = {}
for name, body, sid in matches:
    client_spells[int(sid)] = {'name': name, 'needLearn': 'needLearn = true' in body}

print("=== Server needlearn=1 spells MISSING from client ===")
missing = sorted(server_needlearn - set(client_spells.keys()))
for sid in missing:
    print(f"  {sid}")

print("\n=== Server needlearn=1 spells in client but MISSING needLearn=true ===")
problems = []
for sid in sorted(server_needlearn & set(client_spells.keys())):
    if not client_spells[sid]['needLearn']:
        problems.append((sid, client_spells[sid]['name']))
        print(f"  {sid}: {client_spells[sid]['name']}")

if not problems:
    print("  None - all good!")

print(f"\nTotal server needlearn=1: {len(server_needlearn)}")
print(f"Total in client: {len(client_spells)}")
print(f"Missing from client: {len(missing)}")
print(f"Missing needLearn=true: {len(problems)}")
