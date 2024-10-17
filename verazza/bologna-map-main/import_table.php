<?php
require "utilities.php";  // Assuming this file contains your database connection logic

// Establish a connection to the MySQL database
$connection = new mysqli('localhost', 'root', 'ErZava01', 'prova', 3306);
$connection->options(MYSQLI_OPT_LOCAL_INFILE, true);

// Check for connection errors
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Enable local_infile for this session
$connection->query("SET GLOBAL local_infile = 1");

// SQL query to create the table if it doesn't already exist, adding the new columns for time ranges
$create_table_query = "
    CREATE TABLE IF NOT EXISTS rilevazione_flusso_veicoli (
        ID_univoco_stazione_spira VARCHAR(255),
        data DATE,
        giorno_settimana VARCHAR(255),
        codice_spira VARCHAR(255),
        `00:00-01:00` INT,
        `01:00-02:00` INT,
        `02:00-03:00` INT,
        `03:00-04:00` INT,
        `04:00-05:00` INT,
        `05:00-06:00` INT,
        `06:00-07:00` INT,
        `07:00-08:00` INT,
        `08:00-09:00` INT,
        `09:00-10:00` INT,
        `10:00-11:00` INT,
        `11:00-12:00` INT,
        `12:00-13:00` INT,
        `13:00-14:00` INT,
        `14:00-15:00` INT,
        `15:00-16:00` INT,
        `16:00-17:00` INT,
        `17:00-18:00` INT,
        `18:00-19:00` INT,
        `19:00-20:00` INT,
        `20:00-21:00` INT,
        `21:00-22:00` INT,
        `22:00-23:00` INT,
        `23:00-24:00` INT,
        notte INT,
        mattina INT,
        pomeriggio INT,
        sera INT,
        id_uni VARCHAR(255),
        Livello VARCHAR(255),
        tipologia VARCHAR(255),
        codice VARCHAR(255),
        codice_arco VARCHAR(255),
        codice_via VARCHAR(255),
        Nome_via VARCHAR(255),
        Nodo_da VARCHAR(255),
        Nodo_a VARCHAR(255),
        ordinanza VARCHAR(255),
        stato VARCHAR(255),
        codimpsem VARCHAR(255),
        direzione VARCHAR(255),
        angolo VARCHAR(255),
        longitudine FLOAT,
        latitudine FLOAT,
        geopoint VARCHAR(255),
        comune VARCHAR(255) DEFAULT 'Bologna'
    )
";

// Execute the query to create the table
if ($connection->query($create_table_query) === TRUE) {
    echo "Table rilevazione_flusso_veicoli created successfully or already exists.<br>";
} else {
    echo "Error creating table: " . $connection->error . "<br>";
}

// Path to the CSV file
$csv_file_path = 'C:/xampp/htdocs/tesi_zava/verazza/bologna-map-main/csvFiles/rilevazione-flusso-veicoli-tramite-spire-anno-2024.csv';

// Check if the file exists
if (file_exists($csv_file_path)) {
    echo "File exists: $csv_file_path<br>";
} else {
    die("File not found: $csv_file_path<br>");
}

// Open the CSV file and process it in batch
$batch_size = 1000;  // Number of rows per batch
$rows = [];  // Buffer to store rows for batch inserts

if (($handle = fopen($csv_file_path, "r")) !== FALSE) {
    fgetcsv($handle); // Skip the first row (header)

    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
        // Validate: Ensure each field contains only numbers where expected
        $valid_data = true;
        for ($i = 4; $i <= 25; $i++) {
            if (!is_numeric($data[$i])) {
                echo "Invalid data detected in row. Skipping row: " . implode(', ', $data) . "<br>";
                $valid_data = false;
                break;
            }
        }

        if (!$valid_data) {
            continue;  // Skip this row if the data is not valid
        }

        // Calculate 'notte' (00:00-06:00)
        $notte_values = [$data[2], $data[3], $data[4], $data[5], $data[6], $data[7]];
        $notte = array_sum(array_map('intval', $notte_values));

        // Calculate 'mattina' (06:00-12:00)
        $mattina_values = [$data[8], $data[9], $data[10], $data[11], $data[12], $data[13]];
        $mattina = array_sum(array_map('intval', $mattina_values));

        // Calculate 'pomeriggio' (12:00-18:00)
        $pomeriggio_values = [$data[14], $data[15], $data[16], $data[17], $data[18], $data[19]];
        $pomeriggio = array_sum(array_map('intval', $pomeriggio_values));

        // Calculate 'sera' (18:00-24:00)
        $sera_values = [$data[20], $data[21], $data[22], $data[23], $data[24], $data[25]];
        $sera = array_sum(array_map('intval', $sera_values));


        $row = "('{$connection->real_escape_string($data[0])}', '{$connection->real_escape_string($data[1])}', '{$connection->real_escape_string($data[2])}', '{$connection->real_escape_string($data[3])}', '{$connection->real_escape_string($data[4])}', '{$connection->real_escape_string($data[5])}', '{$connection->real_escape_string($data[6])}', '{$connection->real_escape_string($data[7])}', '{$connection->real_escape_string($data[8])}', '{$connection->real_escape_string($data[9])}', '{$connection->real_escape_string($data[10])}', '{$connection->real_escape_string($data[11])}', '{$connection->real_escape_string($data[12])}', '{$connection->real_escape_string($data[13])}', '{$connection->real_escape_string($data[14])}', '{$connection->real_escape_string($data[15])}', '{$connection->real_escape_string($data[16])}', '{$connection->real_escape_string($data[17])}', '{$connection->real_escape_string($data[18])}', '{$connection->real_escape_string($data[19])}', '{$connection->real_escape_string($data[20])}', '{$connection->real_escape_string($data[21])}', '{$connection->real_escape_string($data[22])}', '{$connection->real_escape_string($data[23])}', '{$connection->real_escape_string($data[24])}', '{$connection->real_escape_string($data[25])}', '$notte', '$mattina', '$pomeriggio', '$sera', '{$connection->real_escape_string($data[26])}', '{$connection->real_escape_string($data[27])}', '{$connection->real_escape_string($data[28])}', '{$connection->real_escape_string($data[29])}', '{$connection->real_escape_string($data[30])}', '{$connection->real_escape_string($data[31])}', '{$connection->real_escape_string($data[32])}', '{$connection->real_escape_string($data[33])}', '{$connection->real_escape_string($data[34])}', '{$connection->real_escape_string($data[35])}', '{$connection->real_escape_string($data[36])}', '{$connection->real_escape_string($data[37])}', '{$connection->real_escape_string($data[38])}', '{$connection->real_escape_string($data[39])}', 
        '{$connection->real_escape_string($data[40])}', '{$connection->real_escape_string($data[41])}', 
        '{$connection->real_escape_string($data[42])}', '{$connection->real_escape_string($data[43])}', 
        '{$connection->real_escape_string($data[44])}')";

        $rows[] = $row;

        // Inserisci batch una volta raggiunto il batch size
        if (count($rows) >= $batch_size) {
            $query = "INSERT INTO rilevazione_flusso_veicoli (data, codice_spira, `00:00-01:00`, `01:00-02:00`, `02:00-03:00`, 
                `03:00-04:00`, `04:00-05:00`, `05:00-06:00`, `06:00-07:00`, `07:00-08:00`, `08:00-09:00`, `09:00-10:00`, 
                `10:00-11:00`, `11:00-12:00`, `12:00-13:00`, `13:00-14:00`, `14:00-15:00`, `15:00-16:00`, `16:00-17:00`, 
                `17:00-18:00`, `18:00-19:00`, `19:00-20:00`, `20:00-21:00`, `21:00-22:00`, `22:00-23:00`, `23:00-24:00`, 
                notte, mattina, pomeriggio, sera, id_uni, Livello, tipologia, codice, codice_arco, codice_via, Nome_via, 
                Nodo_da, Nodo_a, ordinanza, stato, codimpsem, direzione, angolo, longitudine, latitudine, geopoint, 
                ID_univoco_stazione_spira, giorno_settimana) VALUES " . implode(',', $rows);

            // Esegui l'inserimento batch
            if ($connection->query($query) === TRUE) {
                echo "Batch of data inserted successfully.<br>";
            } else {
                echo "Error inserting batch: " . $connection->error . "<br>";
            }

            // Svuota il buffer dopo l'inserimento
            $rows = [];
        }
    }

    // Inserisci le righe rimanenti non inserite nel batch precedente
    if (count($rows) > 0) {
        $query = "INSERT INTO rilevazione_flusso_veicoli (data, codice_spira, `00:00-01:00`, `01:00-02:00`, `02:00-03:00`, 
            `03:00-04:00`, `04:00-05:00`, `05:00-06:00`, `06:00-07:00`, `07:00-08:00`, `08:00-09:00`, `09:00-10:00`, 
            `10:00-11:00`, `11:00-12:00`, `12:00-13:00`, `13:00-14:00`, `14:00-15:00`, `15:00-16:00`, `16:00-17:00`, 
            `17:00-18:00`, `18:00-19:00`, `19:00-20:00`, `20:00-21:00`, `21:00-22:00`, `22:00-23:00`, `23:00-24:00`, 
            notte, mattina, pomeriggio, sera, id_uni, Livello, tipologia, codice, codice_arco, codice_via, Nome_via, 
            Nodo_da, Nodo_a, ordinanza, stato, codimpsem, direzione, angolo, longitudine, latitudine, geopoint, 
            ID_univoco_stazione_spira, giorno_settimana) VALUES " . implode(',', $rows);

        // Esegui l'inserimento delle righe rimanenti
        if ($connection->query($query) === TRUE) {
            echo "Remaining data inserted successfully.<br>";
        } else {
            echo "Error inserting remaining data: " . $connection->error . "<br>";
        }
    }

    // Chiudi il file CSV
    fclose($handle);
    echo "Data import completed successfully.<br>";
} else {
    echo "Error opening the CSV file.<br>";
}

// Chiudi la connessione al database
$connection->close();
?>
