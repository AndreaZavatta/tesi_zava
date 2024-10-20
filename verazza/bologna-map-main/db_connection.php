<?php
// Create a connection to the MySQL server
$connection = new mysqli('localhost', 'root', 'ErZava01', '', 3306);

// Check for connection errors
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Database name
$dbName = 'prova';

// Check if the database exists
$dbCheck = $connection->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$dbName'");

if ($dbCheck->num_rows == 0) {
    // If database doesn't exist, create it
    $createDB = "CREATE DATABASE $dbName";
    if ($connection->query($createDB) === TRUE) {
        echo "Database created successfully<br>";
    } else {
        die("Error creating database: " . $connection->error);
    }
}

// Select the database
$connection->select_db($dbName);
?>
