<?php
require "utilities.php";  // Assuming this file contains your database connection logic

// Function to map Italian month names to their numeric equivalents
function convertItalianDateToMySQLDate($italianDate) {
    $months = [
        'gennaio' => '01',
        'febbraio' => '02',
        'marzo' => '03',
        'aprile' => '04',
        'maggio' => '05',
        'giugno' => '06',
        'luglio' => '07',
        'agosto' => '08',
        'settembre' => '09',
        'ottobre' => '10',
        'novembre' => '11',
        'dicembre' => '12'
    ];

    // Split the Italian date into parts (e.g., '31 dicembre 2023' becomes [31, dicembre, 2023])
    $parts = explode(' ', $italianDate);

    if (count($parts) == 3) {
        $day = $parts[0];
        $month = strtolower($parts[1]);  // Convert month to lowercase for consistency
        $year = $parts[2];

        // Replace the Italian month with the corresponding numeric value
        if (array_key_exists($month, $months)) {
            $month_number = $months[$month];
            // Return the formatted date as YYYY-MM-DD
            return "$year-$month_number-$day";
        }
    }
    
    // Return null if the date is not in a valid format
    return null;
}

// Establish a database connection with local_infile enabled
$connection = new mysqli('localhost', 'root', 'ErZava01', 'prova', 3306);

// Enable local_infile for this session
$connection->options(MYSQLI_OPT_LOCAL_INFILE, true);

// SQL query to create the table if it doesn't exist
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
        latitudine FLOAT,
        longitudine FLOAT,
        id_uni VARCHAR(255),
        Nodo_da VARCHAR(255),
        Nodo_a VARCHAR(255),
        direzione VARCHAR(255),
        stato VARCHAR(255),
        codice_arco VARCHAR(255),
        codice_via VARCHAR(255),
        Nome_via VARCHAR(255),
        tipologia VARCHAR(255),
        Livello VARCHAR(255),
        codimpsem VARCHAR(255),
        geopoint VARCHAR(255)
    )
";

// Execute the table creation query
if ($connection->query($create_table_query) === TRUE) {
    echo "Table rilevazione_flusso_veicoli created successfully or already exists.<br>";
} else {
    echo "Error creating table: " . $connection->error . "<br>";
}

// Set the path to your CSV file
$csv_file_path = 'C:/xampp/htdocs/tesi_zava/verazza/bologna-map-main/rilevazione-flusso-veicoli-tramite-spire-anno-2023.csv';

// Check if the file exists
if (file_exists($csv_file_path)) {
    echo "File exists: $csv_file_path<br>";
} else {
    die("File not found: $csv_file_path<br>");
}

// Open the CSV file and read each line
if (($handle = fopen($csv_file_path, "r")) !== FALSE) {
    fgetcsv($handle); // Skip the first row (header row)

    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
        // Extract data from the CSV
        $ID_univoco_stazione_spira = $connection->real_escape_string($data[0]);
        $italian_date = $data[1];  // Date in Italian format
        $giorno_settimana = $connection->real_escape_string($data[2]);
        $codice_spira = $connection->real_escape_string($data[3]);
        $latitudine = $connection->real_escape_string($data[30]);
        $longitudine = $connection->real_escape_string($data[31]);
        $id_uni = $connection->real_escape_string($data[32]);
        $Nodo_da = $connection->real_escape_string($data[33]);
        $Nodo_a = $connection->real_escape_string($data[34]);
        $direzione = $connection->real_escape_string($data[35]);
        $stato = $connection->real_escape_string($data[36]);
        $codice_arco = $connection->real_escape_string($data[37]);
        $codice_via = $connection->real_escape_string($data[38]);
        $Nome_via = $connection->real_escape_string($data[39]);
        $tipologia = $connection->real_escape_string($data[40]);
        $Livello = $connection->real_escape_string($data[41]);
        $codimpsem = $connection->real_escape_string($data[42]);
        $geopoint = $connection->real_escape_string($data[43]);

        // Hourly columns (00:00-24:00)
        $hourly_data = [];
        for ($i = 4; $i <= 29; $i++) {
            $hourly_data[] = (int)$data[$i];
        }

        // Convert the Italian date to MySQL format
        $mysql_date = convertItalianDateToMySQLDate($italian_date);

        // SQL query to insert data into the table
        $query = "
            INSERT INTO rilevazione_flusso_veicoli (ID_univoco_stazione_spira, data, giorno_settimana, codice_spira, 
                `00:00-01:00`, `01:00-02:00`, `02:00-03:00`, `03:00-04:00`, `04:00-05:00`, `05:00-06:00`, `06:00-07:00`, `07:00-08:00`,
                `08:00-09:00`, `09:00-10:00`, `10:00-11:00`, `11:00-12:00`, `12:00-13:00`, `13:00-14:00`, `14:00-15:00`, `15:00-16:00`, 
                `16:00-17:00`, `17:00-18:00`, `18:00-19:00`, `19:00-20:00`, `20:00-21:00`, `21:00-22:00`, `22:00-23:00`, `23:00-24:00`,
                latitudine, longitudine, id_uni, Nodo_da, Nodo_a, direzione, stato, codice_arco, codice_via, Nome_via, tipologia, Livello, codimpsem, geopoint)
            VALUES ('$ID_univoco_stazione_spira', '$mysql_date', '$giorno_settimana', '$codice_spira', 
                {$hourly_data[0]}, {$hourly_data[1]}, {$hourly_data[2]}, {$hourly_data[3]}, {$hourly_data[4]}, {$hourly_data[5]}, {$hourly_data[6]}, {$hourly_data[7]},
                {$hourly_data[8]}, {$hourly_data[9]}, {$hourly_data[10]}, {$hourly_data[11]}, {$hourly_data[12]}, {$hourly_data[13]}, {$hourly_data[14]}, {$hourly_data[15]},
                {$hourly_data[16]}, {$hourly_data[17]}, {$hourly_data[18]}, {$hourly_data[19]}, {$hourly_data[20]}, {$hourly_data[21]}, {$hourly_data[22]}, {$hourly_data[23]},
                '$latitudine', '$longitudine', '$id_uni', '$Nodo_da','$Nodo_a', '$direzione', '$stato', '$codice_arco', '$codice_via', '$Nome_via', '$tipologia', '$Livello', '$codimpsem', '$geopoint')
        ";

        // Execute the query
        if ($connection->query($query) === TRUE) {
            echo "Row inserted successfully.<br>";
        } else {
            echo "Error inserting row: " . $connection->error . "<br>";
        }
    }

    fclose($handle);  // Close the CSV file
} else {
    echo "Error opening the file.<br>";
}

// Close the database connection
$connection->close();
?>
