<?php
declare(strict_types=1);

$host = '127.0.0.1';
$port = '3309';
$dbname = 'touche_pas_klaxon';
$username = 'root';
$password = '';

$dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    error_log('Erreur de connexion a la base de donnees : ' . $e->getMessage());
    http_response_code(500);
    exit('Une erreur est survenue lors de la connexion a la base de donnees.');
}

return $pdo;
