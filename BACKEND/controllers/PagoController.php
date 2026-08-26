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
require_once '../models/Pago.php';

$conexion = new Conexion();
$cadenaConexion = $conexion->obtenerConexion();

$method = $_SERVER['REQUEST_METHOD'];
$idUsuario = $_SESSION['id'];

$input = json_decode(file_get_contents('php://input'), true);

match ($method) {
    'POST' => createOrUpdatePagoCuota($cadenaConexion, $input['id_cuota'], $idUsuario),
};

function createOrUpdatePagoCuota($_cadenaConexion, $idCuota, $idUsuario) {
    $pagoModel = new PagoModel();
    $cuotaPagada = $pagoModel->getPagoPorCuota($_cadenaConexion, $idCuota);

    if ($cuotaPagada !== null) {
        $estadoPago = (int)$cuotaPagada['estado'];

        if ($cuotaPagada['estado'] === 0) {
            $estadoPago = 1;
        }
        elseif ($cuotaPagada['estado'] === 1) {
            $estadoPago = 0;
        }

        $updateado = $pagoModel->processEstadoPagoCuotaSocio($_cadenaConexion, $estadoPago, $idCuota, $idUsuario);

        if (!$updateado) {
            http_response_code(400);
            echo json_encode([
                'message' => 'Ocurrio un error.'
            ]);

            return;
        }

        http_response_code(200);
        echo json_encode([
            'message' => 'Pago actualizado con exito!'
        ]);
    }
    else {
        $creado = $pagoModel->registerPagoCuotaSocio($_cadenaConexion, $idCuota, $idUsuario);

        if (!$creado) {
            http_response_code(400);
            echo json_encode([
                'message' => 'Ocurrio un error.'
            ]);

            return;
        }

        http_response_code(200);
        echo json_encode([
            'message' => 'Pago creado con exito!'
        ]);
    }
}
?>