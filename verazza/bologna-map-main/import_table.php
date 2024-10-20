<?php
require "./db_connection.php";
session_start();

// Funzione per creare le tabelle se non esistono
function createTables($connection) {
    $queries = [
        "CREATE TABLE IF NOT EXISTS comuni (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(255) NOT NULL UNIQUE,
            descrizione TEXT
        )",
        "CREATE TABLE IF NOT EXISTS spira (
            id INT AUTO_INCREMENT PRIMARY KEY,
            codice_spira VARCHAR(255) NOT NULL,
            livello VARCHAR(255),
            tipologia VARCHAR(255),
            codice_arco VARCHAR(255),
            codice_via VARCHAR(255),
            nodo_da VARCHAR(255),
            nodo_a VARCHAR(255),
            longitudine VARCHAR(255),
            latitudine VARCHAR(255),
            comune_id INT, 
            FOREIGN KEY (comune_id) REFERENCES comuni(id)
        )",
        "CREATE TABLE IF NOT EXISTS rilevazione_flusso_veicoli (
        id INT AUTO_INCREMENT PRIMARY KEY,
        spira_id INT,
        `data` DATE,
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
        FOREIGN KEY (spira_id) REFERENCES spira(id)
    )"
    ];

    foreach ($queries as $query) {
        if ($connection->query($query) !== TRUE) {
            echo "Errore nella creazione della tabella: " . $connection->error . "<br>";
        }
    }
}

// Funzione per importare dati nelle tabelle
// Your importData function
function importData($connection) {
    $successful_inserts = 0;
    $skipped_rows = 0;

    if (isset($_FILES['csv_file']['tmp_name'])) {
        $csv_file_path = $_FILES['csv_file']['tmp_name'];

        if (($handle = fopen($csv_file_path, "r")) !== FALSE) {
            fgetcsv($handle); // Skip header

            $nome_comune = 'Bologna'; // Assign 'Bologna' to a variable
            $query_comuni_check = "SELECT id FROM comuni WHERE nome = ?";
            $stmt_comuni_check = $connection->prepare($query_comuni_check);
            $stmt_comuni_check->bind_param('s', $nome_comune); // Pass the variable, not a string literal
            $stmt_comuni_check->execute();
            $result_comuni = $stmt_comuni_check->get_result();


                if ($result_comuni->num_rows > 0) {
                    // Comune exists, fetch its ID
                    $comune_id = $result_comuni->fetch_assoc()['id'];
                } else {
                    // Comune does not exist, insert it
                    $nome_comune = 'Bologna';
                    $descrizione_comune = 'Prova'; 

                    $query_comuni = "INSERT INTO comuni (nome, descrizione) VALUES (?, ?)";
                    $stmt_comuni = $connection->prepare($query_comuni);
                    $stmt_comuni->bind_param('ss', $nome_comune, $descrizione_comune); // Bind variables instead of literals
                    $stmt_comuni->execute();


                    $comune_id = $connection->insert_id; // Fetch the newly inserted comune's ID
                }

            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                // Extract data from CSV
                $data_rilevazione = $data[0];
                $codice_spira = $data[1];
                $livello = $data[27];
                $tipologia = $data[28];
                $codice_arco = $data[30];
                $codice_via = $data[31];
                $nodo_da = $data[33];
                $nodo_a = $data[34];
                $longitudine = $data[40];
                $latitudine = $data[41];
                $giorno_settimana = $data[44];

                // Station does not exist, insert it
                $query_spira = "INSERT INTO spira (codice_spira, livello, tipologia, codice_arco, codice_via, nodo_da, nodo_a, longitudine, latitudine) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt_spira = $connection->prepare($query_spira);
                $stmt_spira->bind_param('sssssssss', $codice_spira, $livello, $tipologia, $codice_arco, $codice_via, $nodo_da, $nodo_a, $longitudine, $latitudine);
                $stmt_spira->execute();
                $spira_id = $connection->insert_id; // Fetch the newly inserted station's ID

                // Prepare the hourly data for insertion
                $hourly_data = array_map('intval', array_slice($data, 5, 24));

                // Calculate nighttime, morning, afternoon, evening
                $notte = array_sum(array_slice($hourly_data, 0, 6));
                $mattina = array_sum(array_slice($hourly_data, 6, 6));
                $pomeriggio = array_sum(array_slice($hourly_data, 12, 6));
                $sera = array_sum(array_slice($hourly_data, 18, 6));

                // Insert data into 'rilevazione_flusso_veicoli' table
                $query_flusso = "INSERT INTO rilevazione_flusso_veicoli (
                    spira_id, `data`, giorno_settimana, codice_spira, `00:00-01:00`, `01:00-02:00`, 
                    `02:00-03:00`, `03:00-04:00`, `04:00-05:00`, `05:00-06:00`, `06:00-07:00`, 
                    `07:00-08:00`, `08:00-09:00`, `09:00-10:00`, `10:00-11:00`, `11:00-12:00`, 
                    `12:00-13:00`, `13:00-14:00`, `14:00-15:00`, `15:00-16:00`, `16:00-17:00`, 
                    `17:00-18:00`, `18:00-19:00`, `19:00-20:00`, `20:00-21:00`, `21:00-22:00`, 
                    `22:00-23:00`, `23:00-24:00`, notte, mattina, pomeriggio, sera
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                $stmt_flusso = $connection->prepare($query_flusso);

                // Bind the parameters for the query
                $params = array_merge(
                    [$spira_id, $data_rilevazione, $giorno_settimana, $data[1]], // Adjust the index for `codice_spira`
                    $hourly_data,
                    [$notte, $mattina, $pomeriggio, $sera]
                );

                // Create the types string
                $types = 'isss' . str_repeat('i', 24) . 'iiii';

                // Bind the parameters and execute
                $stmt_flusso->bind_param($types, ...$params);

                if ($stmt_flusso->execute()) {
                    $successful_inserts++;
                } else {
                    $skipped_rows++;
                }
            }

            fclose($handle);
        } else {
            echo "Errore nell'apertura del file CSV.";
        }
    } else {
        echo "Nessun file CSV fornito.";
    }

    return [
        "successful_inserts" => $successful_inserts,
        "skipped_rows" => $skipped_rows
    ];
}








// Creare le tabelle
createTables($connection);

// Importare i dati e ottenere il riepilogo
importData($connection);

// Chiudere la connessione
$connection->close();
?>
