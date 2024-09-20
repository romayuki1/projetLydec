<?php
$dsn = 'mysql:host=localhost;dbname=encaissement_db';
$user = 'root';
$mdp = '';

try {
    // Create a new PDO instance
    $pdo = new PDO($dsn, $user, $mdp);
    
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if the connection was successful
    echo "Connected successfully";
} catch (PDOException $e) {
    // Display the error message if connection fails
    echo "Connection failed: " . $e->getMessage();
}
?>
