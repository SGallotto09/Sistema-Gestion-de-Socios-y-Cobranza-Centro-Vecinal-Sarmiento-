<?php

session_start();

if (!isset($_SESSION['id'])) {
    http_response_code(401);

    echo json_encode([
        'message' => 'Usuario no autenticado.'
    ]);

    exit;
}

header('Content-Type: application/json');

require_once '../database/database.php';
require_once '../models/Cuota.php';

$cadenaConexion = Conexion::getInstance()->getConexion();

$method = $_SERVER['REQUEST_METHOD'];
$idUsuario = $_SESSION['id'];

$input = json_decode(file_get_contents('php://input'), true);

try {
    if ($method === 'GET') {
        getCuotasSinPagarController($cadenaConexion);
    }
}
catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'message' => 'Ocurrio un error en el servidor.'
    ]);
}
catch (Exception $e) {
    http_response_code(400);
    echo json_enocde([
        'message' => $e->getMessage()
    ]);
}

function getCuotasSinPagarController($_cadenaConexion) {
    $cuotaModel = new CuotaModel();
    $cuotasSinPagar = $cuotaModel->getCuotasSinPagar($_cadenaConexion);

    http_response_code(200);
    echo json_encode([
        'cuotasSinPagar' => $cuotasSinPagar
    ]);
}
?>