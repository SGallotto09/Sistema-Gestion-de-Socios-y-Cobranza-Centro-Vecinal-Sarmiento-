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

require_once '../database/database';
require_once '../models/Visita.php';

$cadenaConexion = Conexion::getInstance()->getConexion();

$method = $_SERVER['REQUEST_METHOD'];
$idUsuario = $_SESSION['id'];

$input = json_decode(file_get_contents('php://input'), true);

try {

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'message' => 'Ocurrio un error en el servidor.'
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'message' => $e->getMessage()
    ]);
}

function createOrUpdateVisitaSocio($_cadenaConexion, $_idSocio, $_idUsuario) {
    $visita = new Visita();
    
    // Chequear como hacer para que el cobrador pueda marcar mas de una vez que visito al mismo socio en el mismo dia 
}
?>