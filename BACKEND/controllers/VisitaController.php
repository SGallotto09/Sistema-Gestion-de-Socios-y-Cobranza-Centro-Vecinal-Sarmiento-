<?php
/*
session_start();

if (!isset($_SESSION['id'])) {
    http_response_code(401);

    echo json_encode([
        'message' => 'Usuario no autenticado.'
    ]);

    exit;
}
*/
header('Content-Type: application/json');

require_once '../database/database.php';
require_once '../models/Visita.php';
require_once '../models/Cuota.php';

$cadenaConexion = Conexion::getInstance()->getConexion();

$method = $_SERVER['REQUEST_METHOD'];
$idUsuario = 1;
$visitaModel = new VisitaModel();

$input = json_decode(file_get_contents('php://input'), true);

try {
    match ($method) {
        'GET'  => match ($_GET['accion'] ?? null) {
            'visitasSocio' => getVisitasSocio($visitaModel, $cadenaConexion, $_GET['idCuota'] ?? null),
            'totalVisitas' => getTotalVisitas($visitaModel, $cadenaConexion),
            default        => throw new Exception('Acción GET no válida.')
        },
        'POST' => createVisitaSocio($visitaModel, $cadenaConexion, $input['idCuota'], $idUsuario)
    };
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

function getVisitasSocio($_visitaModel, $_cadenaConexion, $_idCuota) {
    if ($_idCuota === null) throw new Exception('Se requiere un numero de cuota.');
    if (!filter_var($_idCuota, FILTER_VALIDATE_INT) || $_idCuota <= 0) throw new Exception('Numero de cuota invalido.');

    $cantidadVisitas = $_visitaModel->getCantidadVisitasSocio($_cadenaConexion, $_idCuota);

    http_response_code(200);
    echo json_encode($cantidadVisitas);
}

function getTotalVisitas($_visitaModel, $_cadenaConexion) {
    $totalVisitas = $_visitaModel->getTotalVisitasSocios($_cadenaConexion);

    http_response_code(200);
    echo json_encode([
        'totalVisitas' => $totalVisitas
    ]);
}

function createVisitaSocio($_visitaModel, $_cadenaConexion, $_idCuota, $_idUsuario) {
    if ($_idUsuario === null) throw new Exception('El usuario no se encuentra autenticado.');
    if ($_idCuota === null) throw new Exception('Se requiere un numero de cuota.');
    if (!filter_var($_idCuota, FILTER_VALIDATE_INT) || $_idCuota <= 0) throw new Exception('Numero de cuota invalido.');

    $cuota = new CuotaModel();
    $existeCuota = $cuota->getCuotaById($_cadenaConexion, $_idCuota);

    if (!$existeCuota) throw new Exception('La cuota que se intenta cobrar no está registrada.');

    $visitado = $_visitaModel->createVisita($_cadenaConexion, $_idCuota, $_idUsuario);

    if (!$visitado) throw new Exception('Ocurrio un error al guardar el registro.');

    http_response_code(201);
    echo json_encode([
        'message' => 'Visita registrada con exito.'
    ]);
}
?>