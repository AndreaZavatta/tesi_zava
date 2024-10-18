<?php
session_start();

// Controlla se è stato caricato un file CSV
if (isset($_FILES['csv_file']['tmp_name'])) {
    $csv_file_path = $_FILES['csv_file']['tmp_name'];

    // Controlla se il file esiste
    if (!file_exists($csv_file_path)) {
        die("File non trovato: " . $csv_file_path);
    }

    // Stabilire una connessione al database MySQL
    $connection = new mysqli('localhost', 'root', 'ErZava01', 'prova', 3306);
    if ($connection->connect_error) {
        die("Connessione fallita: " . $connection->connect_error);
    }

    // Creazione della tabella se non esiste
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

    if ($connection->query($create_table_query) !== TRUE) {
        die("Errore nella creazione della tabella: " . $connection->error . "<br>");
    }

    // Conta le righe totali nel file CSV
    $total_rows = count(file($csv_file_path)) - 1; // Escludi l'intestazione
    $successful_inserts = 0;
    $skipped_rows = 0;

    // Apri il file CSV
    if (($handle = fopen($csv_file_path, "r")) !== FALSE) {
        fgetcsv($handle); // Salta la prima riga (header)
        $rows = [];

        while (($data = fgetcsv($handle, 10000, ";")) !== FALSE) {
            // Validazione dei campi numerici
            $valid_data = true;
            for ($i = 4; $i <= 25; $i++) {
                if (!is_numeric($data[$i])) {
                    $skipped_rows++;
                    $valid_data = false;
                    break;
                }
            }

            if (!$valid_data) {
                continue;  // Salta le righe non valide
            }

            // Calcola i valori basati sul tempo
            $notte_values = [$data[2], $data[3], $data[4], $data[5], $data[6], $data[7]];
            $notte = array_sum(array_map('intval', $notte_values));

            $mattina_values = [$data[8], $data[9], $data[10], $data[11], $data[12], $data[13]];
            $mattina = array_sum(array_map('intval', $mattina_values));

            $pomeriggio_values = [$data[14], $data[15], $data[16], $data[17], $data[18], $data[19]];
            $pomeriggio = array_sum(array_map('intval', $pomeriggio_values));

            $sera_values = [$data[20], $data[21], $data[22], $data[23], $data[24], $data[25]];
            $sera = array_sum(array_map('intval', $sera_values));

            // Prepara i dati per l'inserimento
            $row = "('{$connection->real_escape_string($data[0])}', '{$connection->real_escape_string($data[1])}', '{$connection->real_escape_string($data[2])}', '{$connection->real_escape_string($data[3])}', '{$connection->real_escape_string($data[4])}', '{$connection->real_escape_string($data[5])}', '{$connection->real_escape_string($data[6])}', '{$connection->real_escape_string($data[7])}', '{$connection->real_escape_string($data[8])}', '{$connection->real_escape_string($data[9])}', '{$connection->real_escape_string($data[10])}', '{$connection->real_escape_string($data[11])}', '{$connection->real_escape_string($data[12])}', '{$connection->real_escape_string($data[13])}', '{$connection->real_escape_string($data[14])}', '{$connection->real_escape_string($data[15])}', '{$connection->real_escape_string($data[16])}', '{$connection->real_escape_string($data[17])}', '{$connection->real_escape_string($data[18])}', '{$connection->real_escape_string($data[19])}', '{$connection->real_escape_string($data[20])}', '{$connection->real_escape_string($data[21])}', '{$connection->real_escape_string($data[22])}', '{$connection->real_escape_string($data[23])}', '{$connection->real_escape_string($data[24])}', '{$connection->real_escape_string($data[25])}', '$notte', '$mattina', '$pomeriggio', '$sera', '{$connection->real_escape_string($data[26])}', '{$connection->real_escape_string($data[27])}', '{$connection->real_escape_string($data[28])}', '{$connection->real_escape_string($data[29])}', '{$connection->real_escape_string($data[30])}', '{$connection->real_escape_string($data[31])}', '{$connection->real_escape_string($data[32])}', '{$connection->real_escape_string($data[33])}', '{$connection->real_escape_string($data[34])}', '{$connection->real_escape_string($data[35])}', '{$connection->real_escape_string($data[36])}', '{$connection->real_escape_string($data[37])}', '{$connection->real_escape_string($data[38])}', '{$connection->real_escape_string($data[39])}', '{$connection->real_escape_string($data[40])}', '{$connection->real_escape_string($data[41])}', '{$connection->real_escape_string($data[42])}', '{$connection->real_escape_string($data[43])}', '{$connection->real_escape_string($data[44])}')";
            
            $rows[] = $row;

            // Inserimento batch
            if (count($rows) >= 500) {
                $query = "INSERT INTO rilevazione_flusso_veicoli (data, codice_spira, `00:00-01:00`, `01:00-02:00`, `02:00-03:00`, `03:00-04:00`, `04:00-05:00`, `05:00-06:00`, `06:00-07:00`, `07:00-08:00`, `08:00-09:00`, `09:00-10:00`, `10:00-11:00`, `11:00-12:00`, `12:00-13:00`, `13:00-14:00`, `14:00-15:00`, `15:00-16:00`, `16:00-17:00`, `17:00-18:00`, `18:00-19:00`, `19:00-20:00`, `20:00-21:00`, `21:00-22:00`, `22:00-23:00`, `23:00-24:00`, notte, mattina, pomeriggio, sera, id_uni, Livello, tipologia, codice, codice_arco, codice_via, Nome_via, Nodo_da, Nodo_a, ordinanza, stato, codimpsem, direzione, angolo, longitudine, latitudine, geopoint, ID_univoco_stazione_spira, giorno_settimana) VALUES " . implode(',', $rows);

                if ($connection->query($query) === TRUE) {
                    $successful_inserts += count($rows); // Aggiorna il conteggio delle righe inserite
                } else {
                    echo "Errore durante l'inserimento del batch: " . $connection->error . "<br>";
                }

                $rows = [];  // Svuota il buffer
            }
        }

        // Inserisci le righe rimanenti
        if (count($rows) > 0) {
            $query = "INSERT INTO rilevazione_flusso_veicoli (data, codice_spira, `00:00-01:00`, `01:00-02:00`, `02:00-03:00`, `03:00-04:00`, `04:00-05:00`, `05:00-06:00`, `06:00-07:00`, `07:00-08:00`, `08:00-09:00`, `09:00-10:00`, `10:00-11:00`, `11:00-12:00`, `12:00-13:00`, `13:00-14:00`, `14:00-15:00`, `15:00-16:00`, `16:00-17:00`, `17:00-18:00`, `18:00-19:00`, `19:00-20:00`, `20:00-21:00`, `21:00-22:00`, `22:00-23:00`, `23:00-24:00`, notte, mattina, pomeriggio, sera, id_uni, Livello, tipologia, codice, codice_arco, codice_via, Nome_via, Nodo_da, Nodo_a, ordinanza, stato, codimpsem, direzione, angolo, longitudine, latitudine, geopoint, ID_univoco_stazione_spira, giorno_settimana) VALUES " . implode(',', $rows);

            if ($connection->query($query) === TRUE) {
                $successful_inserts += count($rows); // Aggiorna il conteggio delle righe inserite
            } else {
                echo "Errore durante l'inserimento delle righe rimanenti: " . $connection->error . "<br>";
            }
        }

        // Chiudi il file CSV
        fclose($handle);

        // Restituisci un riepilogo delle righe elaborate
        echo json_encode([
            "message" => "Elaborazione completata!",
            "successful_inserts" => $successful_inserts,
            "skipped_rows" => $skipped_rows,
            "total_rows" => $total_rows
        ]);
    } else {
        echo "Errore nell'apertura del file CSV.";
    }

    // Chiudi la connessione al database
    $connection->close();
} else {
    echo "Nessun file CSV fornito.";
}
?>
