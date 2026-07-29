<?php

header('Content-Type: application/json');

include '../config/database.php';

$method = $_SERVER['REQUEST_METHOD'];

$input = json_decode(file_get_contents('php://input'), true);

if ($method === 'GET') {
    GetSocios($pdo);
}



?>