<?php

header('Content-Type: application/json');

include '../config/database.php';

$method = $_SERVER['REQUEST_METHOD'];

$input = json_decode(file_get_contents('php://input'), true);

if ($method === 'GET') {
    GetSocios($pdo);
}

function GetSocios($pdo) {
    $query = "  SELECT s.id, s.apellido, s.nombre, s.dni, s.telefono, c.estado FROM socio AS s 
                JOIN cuota AS c ON s.id = c.id_socio";

    $stmt = $pdo->prepare($query);

    $stmt->execute();

    $socios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($socios);
}

?>