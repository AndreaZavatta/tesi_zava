<?php
session_start();

// Connessione al database
$connection = new mysqli('localhost', 'root', 'ErZava01', 'prova', 3306);

// Controllo connessione
if ($connection->connect_error) {
    die("Connessione fallita: " . $connection->connect_error);
}

$errorMessage = '';

// Funzione per registrare un nuovo utente
function register($username, $password, $connection) {
    global $errorMessage;

    // Vincoli lato server per la password
    if (strlen($password) < 8 || !preg_match("/[A-Z]/", $password) || !preg_match("/[a-z]/", $password) || !preg_match("/[0-9]/", $password) || !preg_match("/[\W]/", $password)) {
        $errorMessage = "La password non soddisfa i requisiti di sicurezza!";
        return;
    }

    // Controlla se l'username esiste già
    $checkUserExists = $connection->prepare("SELECT * FROM admin WHERE username = ?");
    $checkUserExists->bind_param('s', $username);
    $checkUserExists->execute();
    $checkUserExists->store_result();

    if ($checkUserExists->num_rows > 0) {
        $errorMessage = "L'username è già in uso!";
        return;
    }

    // Hash della password
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Inserisci il nuovo utente
    $stmt = $connection->prepare("INSERT INTO admin (username, password_hash) VALUES (?, ?)");
    $stmt->bind_param('ss', $username, $passwordHash);

    if ($stmt->execute()) {
        // Reindirizza al login dopo una registrazione riuscita
        header('Location: login.php?message=success');
        exit();
    } else {
        $errorMessage = "Errore nella registrazione: " . $connection->error;
    }

    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    register($username, $password, $connection);
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrazione Utente</title>
    <link rel="stylesheet" href="style.css"> <!-- Collegamento al file CSS esterno -->
    <style>
        .error-message {
            color: red;
            font-size: 14px;
            margin-bottom: 15px;
            text-align: center;
            display: block;
            padding: 10px;
            background-color: #ffe5e5;
            border-radius: 5px;
        }
    </style>
    <script>
        function validatePassword() {
            const password = document.getElementById("password").value;
            const errorMessage = document.getElementById("password-error");

            // Vincoli per la password
            const regexLower = /[a-z]/;
            const regexUpper = /[A-Z]/;
            const regexNumber = /[0-9]/;
            const regexSpecial = /[\W]/;
            
            if (password.length < 8 || !regexLower.test(password) || !regexUpper.test(password) || !regexNumber.test(password) || !regexSpecial.test(password)) {
                errorMessage.style.display = "block";
                return false;
            } else {
                errorMessage.style.display = "none";
                return true;
            }
        }

        function validateForm() {
            return validatePassword();
        }
    </script>
</head>
<body>
    <div class="form-container">
        <h2>Registrazione Nuovo Utente</h2>
        
        <!-- Messaggio di errore se presente -->
        <?php if (!empty($errorMessage)) : ?>
            <div class="error-message"><?php echo $errorMessage; ?></div>
        <?php endif; ?>

        <form action="register.php" method="POST" onsubmit="return validateForm()">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" oninput="validatePassword()" required>
            <div id="password-error" class="error-message" style="display:none;">
                La password deve contenere almeno 8 caratteri, includere una lettera maiuscola, una minuscola, un numero e un carattere speciale.
            </div>

            <button type="submit">Registrati</button>
        </form>
        <div class="login-link">
            <p>Hai già un account? <a href="login.php">Accedi qui</a></p>
        </div>
    </div>
</body>
</html>
