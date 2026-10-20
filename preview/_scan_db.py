import sqlite3

p = r"C:\Users\Skydrake\Documents\gamtech-electronic\wp-content\database\.ht.sqlite"
c = sqlite3.connect(p)
cur = c.cursor()
print("TABLES:")
for r in cur.execute("SELECT name FROM sqlite_master WHERE type='table' ORDER BY 1"):
    print(r[0])

print("\nPOST TYPES:")
try:
    for r in cur.execute("SELECT post_type, COUNT(*) FROM wp_posts GROUP BY post_type"):
        print(r)
except Exception as e:
    print("posts err", e)
    for r in cur.execute("SELECT name FROM sqlite_master WHERE name LIKE '%post%'"):
        print("like", r)
