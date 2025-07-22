<?php
$host = 'localhost';
$dbname = 'sunam220_sunamu_db'; // <- da creare se non esiste ancora
$user = 'sunam220';
$pass = 'Js091U(5rx_M'; // ← inserisci qui la password

$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    exit('Connessione fallita: ' . $e->getMessage());
}
