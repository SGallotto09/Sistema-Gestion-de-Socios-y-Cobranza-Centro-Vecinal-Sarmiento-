<?php

$host = 'localhost';
$port = '3306';
$user = 'root';
$password = 'AguStin09';
$dbname = 'vecinal_sarmiento_bbdd';

try {
    $connectionString = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";

            // PDO = PHP Data Objects
    $pdo = new PDO($connectionString, $user, $password);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Conexion fallida: ' . $e->getMessage());
}

?>