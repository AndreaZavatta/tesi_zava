<?php
require "utilities.php";  // Assuming this file contains your database connection logic

// Check if a CSV file path is passed to this script
if (isset($_FILES['csv_file']['tmp_name'])) {
    $csv_file_path = $_FILES['csv_file']['tmp_name'];

    // Establish a connection to the MySQL database
    $connection = new mysqli('localhost', 'root', 'ErZava01', 'prova', 3306);
    $connection->options(MYSQLI_OPT_LOCAL_INFILE, true);

    // Check for connection errors
    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }

    // SQL query to create the table if it doesn't already exist
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
    if ($connection->query($create_table_query) !== TRUE) {
        die("Error creating table: " . $connection->error . "<br>");
    }

    // Initialize counters for feedback
    $total_rows = 0;
    $successful_inserts = 0;
    $skipped_rows = 0;

    // Open the CSV file
    if (($handle = fopen($csv_file_path, "r")) !== FALSE) {
        fgetcsv($handle); // Skip the first row (header)

        // Batch processing
        $batch_size = 1000;
        $rows = [];

        while (($data = fgetcsv($handle, 10000, ";")) !== FALSE) {
            $total_rows++;  // Count total rows
            
            // Validate numeric fields (adjust based on your file)
            $valid_data = true;
            for ($i = 4; $i <= 25; $i++) {
                if (!is_numeric($data[$i])) {
                    $skipped_rows++;  // Count skipped rows
                    $valid_data = false;
                    break;
                }
            }

            if (!$valid_data) {
                continue;  // Skip invalid rows
            }

            // Calculate time-based values (e.g., 'notte', 'mattina', etc.)
            $notte_values = [$data[2], $data[3], $data[4], $data[5], $data[6], $data[7]];
            $notte = array_sum(array_map('intval', $notte_values));

            $mattina_values = [$data[8], $data[9], $data[10], $data[11], $data[12], $data[13]];
            $mattina = array_sum(array_map('intval', $mattina_values));

            $pomeriggio_values = [$data[14], $data[15], $data[16], $data[17], $data[18], $data[19]];
            $pomeriggio = array_sum(array_map('intval', $pomeriggio_values));

            $sera_values = [$data[20], $data[21], $data[22], $data[23], $data[24], $data[25]];
            $sera = array_sum(array_map('intval', $sera_values));

            // Prepare data for insertion
            $row = "('{$connection->real_escape_string($data[0])}', '{$connection->real_escape_string($data[1])}', '{$connection->real_escape_string($data[2])}', '{$connection->real_escape_string($data[3])}', '{$connection->real_escape_string($data[4])}', '{$connection->real_escape_string($data[5])}', '{$connection->real_escape_string($data[6])}', '{$connection->real_escape_string($data[7])}', '{$connection->real_escape_string($data[8])}', '{$connection->real_escape_string($data[9])}', '{$connection->real_escape_string($data[10])}', '{$connection->real_escape_string($data[11])}', '{$connection->real_escape_string($data[12])}', '{$connection->real_escape_string($data[13])}', '{$connection->real_escape_string($data[14])}', '{$connection->real_escape_string($data[15])}', '{$connection->real_escape_string($data[16])}', '{$connection->real_escape_string($data[17])}', '{$connection->real_escape_string($data[18])}', '{$connection->real_escape_string($data[19])}', '{$connection->real_escape_string($data[20])}', '{$connection->real_escape_string($data[21])}', '{$connection->real_escape_string($data[22])}', '{$connection->real_escape_string($data[23])}', '{$connection->real_escape_string($data[24])}', '{$connection->real_escape_string($data[25])}', '$notte', '$mattina', '$pomeriggio', '$sera', '{$connection->real_escape_string($data[26])}', '{$connection->real_escape_string($data[27])}', '{$connection->real_escape_string($data[28])}', '{$connection->real_escape_string($data[29])}', '{$connection->real_escape_string($data[30])}', '{$connection->real_escape_string($data[31])}', '{$connection->real_escape_string($data[32])}', '{$connection->real_escape_string($data[33])}', '{$connection->real_escape_string($data[34])}', '{$connection->real_escape_string($data[35])}', '{$connection->real_escape_string($data[36])}', '{$connection->real_escape_string($data[37])}', '{$connection->real_escape_string($data[38])}', '{$connection->real_escape_string($data[39])}', '{$connection->real_escape_string($data[40])}', '{$connection->real_escape_string($data[41])}', '{$connection->real_escape_string($data[42])}', '{$connection->real_escape_string($data[43])}', '{$connection->real_escape_string($data[44])}')";

            $rows[] = $row;

            // Batch insertion
            if (count($rows) >= $batch_size) {
                $query = "INSERT INTO rilevazione_flusso_veicoli (data, codice_spira, `00:00-01:00`, `01:00-02:00`, `02:00-03:00`, `03:00-04:00`, `04:00-05:00`, `05:00-06:00`, `06:00-07:00`, `07:00-08:00`, `08:00-09:00`, `09:00-10:00`, `10:00-11:00`, `11:00-12:00`, `12:00-13:00`, `13:00-14:00`, `14:00-15:00`, `15:00-16:00`, `16:00-17:00`, `17:00-18:00`, `18:00-19:00`, `19:00-20:00`, `20:00-21:00`, `21:00-22:00`, `22:00-23:00`, `23:00-24:00`, notte, mattina, pomeriggio, sera, id_uni, Livello, tipologia, codice, codice_arco, codice_via, Nome_via, Nodo_da, Nodo_a, ordinanza, stato, codimpsem, direzione, angolo, longitudine, latitudine, geopoint, ID_univoco_stazione_spira, giorno_settimana) VALUES " . implode(',', $rows);

                if ($connection->query($query) === TRUE) {
                    $successful_inserts += count($rows);  // Track successful inserts
                } else {
                    echo "Error inserting batch: " . $connection->error . "<br>";
                }

                $rows = [];  // Clear the buffer
            }
        }

        // Insert remaining rows
        if (count($rows) > 0) {
            $query = "INSERT INTO rilevazione_flusso_veicoli (data, codice_spira, `00:00-01:00`, `01:00-02:00`, `02:00-03:00`, `03:00-04:00`, `04:00-05:00`, `05:00-06:00`, `06:00-07:00`, `07:00-08:00`, `08:00-09:00`, `09:00-10:00`, `10:00-11:00`, `11:00-12:00`, `12:00-13:00`, `13:00-14:00`, `14:00-15:00`, `15:00-16:00`, `16:00-17:00`, `17:00-18:00`, `18:00-19:00`, `19:00-20:00`, `20:00-21:00`, `21:00-22:00`, `22:00-23:00`, `23:00-24:00`, notte, mattina, pomeriggio, sera, id_uni, Livello, tipologia, codice, codice_arco, codice_via, Nome_via, Nodo_da, Nodo_a, ordinanza, stato, codimpsem, direzione, angolo, longitudine, latitudine, geopoint, ID_univoco_stazione_spira, giorno_settimana) VALUES " . implode(',', $rows);

            if ($connection->query($query) === TRUE) {
                $successful_inserts += count($rows);
            } else {
                echo "Error inserting remaining data: " . $connection->error . "<br>";
            }
        }

        // Close the CSV file
        fclose($handle);

        echo "CSV processing completed.<br>";
        echo "Total rows processed: $total_rows<br>";
        echo "Rows inserted successfully: $successful_inserts<br>";
        echo "Rows skipped: $skipped_rows<br>";
    } else {
        echo "Error opening the CSV file.<br>";
    }

    // Close the database connection
    $connection->close();
} else {
    echo "No CSV file path provided or file does not exist.<br>";
}
?>
