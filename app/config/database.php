<?php

define('DB_HOST', 'localhost');
define('DB_NAME', 'etatcivil');
define('DB_USER', 'root');
define('DB_PASS', '');

$db;

try {

    $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Si une erreur de connexion survient, on attrape l'exception PDOException
    echo "Échec de la connexion à la base de données : " . $e->getMessage();
}
?>