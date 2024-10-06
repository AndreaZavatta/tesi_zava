<?php
require "utilities.php";  // Assuming this file contains your database connection logic

// Establish a database connection with local_infile enabled
$connection = new mysqli('localhost', 'root', 'ErZava01', 'prova', 3306);

// Enable local_infile for this session
$connection->options(MYSQLI_OPT_LOCAL_INFILE, true);

// Step 1: Create the main table if it doesn't exist
$create_main_table_query = "
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

// Execute the main table creation query
if ($connection->query($create_main_table_query) === TRUE) {
    echo "Main table `rilevazione_flusso_veicoli` created successfully.<br>";
} else {
    echo "Error creating main table: " . $connection->error . "<br>";
}

// Step 2: Load data directly into the main table
$csv_file_path = 'C:/xampp/htdocs/tesi_zava/verazza/bologna-map-main/rilevazione-flusso-veicoli-tramite-spire-anno-2023.csv';

$load_data_query = "
    LOAD DATA LOCAL INFILE '$csv_file_path'
    INTO TABLE rilevazione_flusso_veicoli
    FIELDS TERMINATED BY ';'
    ENCLOSED BY '\"'
    LINES TERMINATED BY '\n'
    IGNORE 1 ROWS
    (ID_univoco_stazione_spira, @data_field, giorno_settimana, codice_spira, 
     `00:00-01:00`, `01:00-02:00`, `02:00-03:00`, `03:00-04:00`, `04:00-05:00`, 
     `05:00-06:00`, `06:00-07:00`, `07:00-08:00`, `08:00-09:00`, `09:00-10:00`, 
     `10:00-11:00`, `11:00-12:00`, `12:00-13:00`, `13:00-14:00`, `14:00-15:00`, 
     `15:00-16:00`, `16:00-17:00`, `17:00-18:00`, `18:00-19:00`, `19:00-20:00`, 
     `20:00-21:00`, `21:00-22:00`, `22:00-23:00`, `23:00-24:00`, 
     latitudine, longitudine, id_uni, Nodo_da, Nodo_a, direzione, stato, 
     codice_arco, codice_via, Nome_via, tipologia, Livello, codimpsem, geopoint)
    SET data = STR_TO_DATE(@data_field, '%d %M %Y');
";

// Execute the LOAD DATA query
if ($connection->query($load_data_query) === TRUE) {
    echo "Data loaded into main table.<br>";
} else {
    echo "Error loading data: " . $connection->error . "<br>";
}

// Close the database connection
$connection->close();
?>
