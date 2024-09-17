import json
import pymysql

# Connect to the MySQL database
conn = pymysql.connect(host='localhost', user='root', password='ErZava01', db='prova')
cursor = conn.cursor()

# Load the JSON file
with open('presenze-e-votazioni.json', 'r', encoding='utf-8') as f:
    json_data = json.load(f)

# Check existing records in the database in a single query
cursor.execute("SELECT nominativo, data_seduta, gruppo_politico FROM presenze_votazioni")
existing_records = set(cursor.fetchall())

# Prepare batch insert for missing records
missing_records = []
for record in json_data:
    nominativo = record['nominativo']
    data_seduta = record['data_seduta']
    gruppo_politico = record['gruppo_politico']
    
    # Check if record exists
    if (nominativo, data_seduta, gruppo_politico) not in existing_records:
        presenza = record['presenza']
        num_votazioni = record['num_votazioni']
        percentuale_presenza_alle_votazioni = record['percentuale_presenza_alle_votazioni']
        
        # Add missing record to the list
        missing_records.append((
            nominativo.replace("'", "''"),
            data_seduta,
            gruppo_politico.replace("'", "''"),
            presenza,
            num_votazioni,
            percentuale_presenza_alle_votazioni
        ))

# Batch insert missing records
if missing_records:
    sql = """
    INSERT INTO presenze_votazioni 
    (nominativo, data_seduta, gruppo_politico, presenza, num_votazioni, percentuale_presenza_alle_votazioni)
    VALUES (%s, %s, %s, %s, %s, %s)
    """
    cursor.executemany(sql, missing_records)
    conn.commit()

# Close the database connection
conn.close()

print(f"Inserted {len(missing_records)} missing records.")
