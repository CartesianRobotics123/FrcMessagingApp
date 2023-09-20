<?php
// config.php

try {
    // Database connection using PDO
    $host = "localhost";
$username = "username";
$password = "password";
$dbname = "efe_mesajlasma";

    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 echo $username;   
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>