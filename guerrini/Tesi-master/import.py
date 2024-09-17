import json

# Load the JSON file
with open('presenze-e-votazioni.json', 'r', encoding='utf-8') as f:
    json_data = json.load(f)

# Generate SQL INSERT statements
sql_statements = []
numrecord = 0
for record in json_data:
    nominativo = record['nominativo'].replace("'", "''")  # Handle single quotes in names
    data_seduta = record['data_seduta']
    gruppo_politico = record['gruppo_politico'].replace("'", "''")  # Handle single quotes
    presenza = record['presenza']
    num_votazioni = record['num_votazioni']
    percentuale_presenza_alle_votazioni = record['percentuale_presenza_alle_votazioni']
    numrecord += 1
    # Construct SQL insert statement
    sql = f"INSERT INTO presenze_votazioni (nominativo, data_seduta, gruppo_politico, presenza, num_votazioni, percentuale_presenza_alle_votazioni) VALUES ('{nominativo}', '{data_seduta}', '{gruppo_politico}', '{presenza}', {num_votazioni}, {percentuale_presenza_alle_votazioni});"
    
    sql_statements.append(sql)

# Write the SQL statements to a file
with open('presenze_votazioni_insert.sql', 'w', encoding='utf-8') as f:
    f.write("\n".join(sql_statements))

print("SQL insert statements have been written to 'presenze_votazioni_insert.sql'")
print("numrecord: "+str(numrecord))