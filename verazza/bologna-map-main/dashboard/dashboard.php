<?php
session_start();

// Verifica se l'utente è loggato
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Connessione al database
$connection = new mysqli('localhost', 'root', 'ErZava01', 'prova', 3306);

// Controllo connessione
if ($connection->connect_error) {
    die("Connessione fallita: " . $connection->connect_error);
}

$successMessage = "";
$passwordError = "";

// Caricamento del file CSV
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    $fileName = $_FILES['csv_file']['tmp_name'];
    
    if ($_FILES['csv_file']['size'] > 0) {
        // Check if the file is a CSV file
        if (pathinfo($_FILES['csv_file']['name'], PATHINFO_EXTENSION) !== 'csv') {
            $passwordError = "Seleziona un file CSV valido.";
        } else {
            // Include the import script to process the CSV file
            require "../import_table.php";
        }
    } else {
        $passwordError = "Seleziona un file CSV valido.";
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="dashboard_style.css"> <!-- Collegamento al file CSS -->
    <script src="dashboard.js" defer></script> <!-- Collegamento al file JS separato -->
</head>
<body>
    <div class="container">
        <h2>Dashboard</h2>

        <!-- Display success or error message -->
        <?php if (!empty($successMessage)): ?>
            <div class="success-message"><?php echo $successMessage; ?></div>
        <?php elseif (!empty($passwordError)): ?>
            <div class="error-message"><?php echo $passwordError; ?></div>
        <?php endif; ?>

        <!-- Menu Tabs -->
        <div class="tab-container">
            <span class="tab active" onclick="showTab(0)">Carica CSV</span>
            <span class="tab" onclick="showTab(1)">Profilo</span>
            <span class="tab" onclick="showTab(2)">Cambia Password</span>
        </div>

        <!-- Tab contenuto: Caricamento CSV -->
        <div class="tab-content active">
            <h3>Carica file CSV</h3>
            <form action="dashboard.php" method="POST" enctype="multipart/form-data">
                <label for="csv_file">Seleziona il file CSV:</label>
                <input type="file" name="csv_file" accept=".csv" required>
                <button type="submit">Carica File</button>
            </form>
        </div>

        <!-- Tab contenuto: Profilo -->
        <div class="tab-content">
            <h3>Il tuo profilo</h3>
            <p>Username: 
                <?php
                if (isset($_SESSION['admin_id'])) {
                    $adminId = $_SESSION['admin_id'];
                    $result = $connection->query("SELECT username FROM admin WHERE id = $adminId");
                    if ($row = $result->fetch_assoc()) {
                        echo htmlspecialchars($row['username']);
                    } else {
                        echo "Errore nel recupero del profilo.";
                    }
                }
                ?>
            </p>
        </div>

        <!-- Tab contenuto: Modifica password -->
        <div class="tab-content">
            <h3>Cambia la tua password</h3>
            <form action="dashboard.php" method="POST" onsubmit="return validatePasswordForm()">
                <label for="old_password">Vecchia Password:</label>
                <input type="password" id="old_password" name="old_password" required>

                <label for="new_password">Nuova Password:</label>
                <input type="password" id="new_password" name="new_password" required>

                <label for="confirm_new_password">Conferma Nuova Password:</label>
                <input type="password" id="confirm_new_password" name="confirm_new_password" required>

                <button type="submit" name="update_password">Aggiorna Password</button>
            </form>

            <div id="password-error" class="error-message" style="display: none;"></div>
        </div>

        <!-- Logout -->
        <p><a href="logout.php">Logout</a></p>
    </div>
</body>
</html>
