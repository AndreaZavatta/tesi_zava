<?php
session_start();

// Connessione al database
$connection = new mysqli('localhost', 'root', 'ErZava01', 'prova', 3306);

// Controllo connessione
if ($connection->connect_error) {
    die("Connessione fallita: " . $connection->connect_error);
}

$errorMessage = '';

// Funzione per verificare il login
function login($username, $password, $connection) {
    global $errorMessage; // Per gestire il messaggio di errore all'interno della funzione

    $stmt = $connection->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();

    if ($admin && password_verify($password, $admin['password_hash'])) {
        // Imposta la sessione per l'admin autenticato
        $_SESSION['admin_id'] = $admin['id'];
        header('Location: ../dashboard/dashboard.php');
        exit();
    } else {
        $errorMessage = "Username o password errati!";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    login($username, $password, $connection);
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>
    <link rel="stylesheet" href="style.css"> <!-- Collegamento al file CSS esterno -->
</head>
<body>
    <div class="form-container">
        <h2>Login Admin</h2>
        <!-- Mostra messaggio di successo se viene dalla registrazione -->
        <?php if (isset($_GET['message']) && $_GET['message'] === 'success') : ?>
            <div class="success-message">Registrazione completata! Ora puoi accedere.</div>
        <?php endif; ?>

        <!-- Mostra messaggio di errore se presente -->
        <?php if (!empty($errorMessage)) : ?>
            <div class="message"><?php echo $errorMessage; ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Login</button>
        </form>

        <!-- Link per andare alla registrazione -->
        <div class="login-link">
            <p>Non hai un account? <a href="register.php">Registrati qui</a></p>
        </div>
    </div>
</body>
</html>