<?php
// Connessione al database
$connection = new mysqli('localhost', 'root', 'ErZava01', 'prova', 3306);

// Controllo connessione
if ($connection->connect_error) {
    die("Connessione fallita: " . $connection->connect_error);
}

// Creazione della tabella 'admin' nel database esistente
$createTable = "CREATE TABLE IF NOT EXISTS admin (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(30) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($connection->query($createTable) === TRUE) {
    echo "Tabella 'admin' creata con successo<br>";
} else {
    echo "Errore nella creazione della tabella: " . $connection->error;
}

// Aggiunta di un admin predefinito solo se non esiste già
$defaultUsername = 'admin';
$defaultPassword = 'admin123'; // Cambia questa password
$passwordHash = password_hash($defaultPassword, PASSWORD_DEFAULT);

// Controlla se l'utente admin esiste già
$checkAdminExists = $connection->prepare("SELECT * FROM admin WHERE username = ?");
$checkAdminExists->bind_param('s', $defaultUsername);
$checkAdminExists->execute();
$checkAdminExists->store_result();

if ($checkAdminExists->num_rows === 0) {
    // Inserisci l'admin predefinito se non esiste
    $stmt = $connection->prepare("INSERT INTO admin (username, password_hash) VALUES (?, ?)");
    $stmt->bind_param('ss', $defaultUsername, $passwordHash);
    
    if ($stmt->execute()) {
        echo "Admin predefinito creato con successo<br>";
    } else {
        echo "Errore nell'inserimento dell'admin: " . $connection->error;
    }
    $stmt->close();
} else {
    echo "L'admin predefinito esiste già<br>";
}

$checkAdminExists->close();
$connection->close();
?>
