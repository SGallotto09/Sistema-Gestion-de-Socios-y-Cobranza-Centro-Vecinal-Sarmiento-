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
require_once '../models/Cuota.php';

$cadenaConexion = Conexion::getInstance()->getConexion();

$method = $_SERVER['REQUEST_METHOD'];
$idUsuario = $_SESSION['id'];
$pagoModel = new PagoModel();

$input = json_decode(file_get_contents('php://input'), true);

try {
    match ($method) {
        'GET'       => getCantidadPagos($pagoModel, $cadenaConexion),
        'POST'      => createOrUpdatePagoCuota($pagoModel, $cadenaConexion, $input['id_cuota'], $idUsuario),
        default     => throw new Exception('Método HTTP no permitido')
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

function getCantidadPagos($_pagoModel, $_cadenaConexion) {
    $cantidadPagos = $_pagoModel->getCantidadDePagos($_cadenaConexion);

    http_response_code(200);
    echo json_encode([
        'cantidadPagos' => $cantidadPagos
    ]);
}

function createOrUpdatePagoCuota($_pagoModel, $_cadenaConexion, $_idCuota, $_idUsuario) {
    if ($_idUsuario === null) throw new Exception('El usuario no se encuentra autenticado.');
    if ($_idCuota === null) throw new Exception('Se requiere un numero de cuota.');
    if (!filter_var($_idCuota, FILTER_VALIDATE_INT) || $_idCuota <= 0) throw new Exception('Numero de cuota invalido.');

    $cuota = new CuotaModel();
    $existeCuota = $cuota->getCuotaById($_cadenaConexion, $_idCuota);

    if (!$existeCuota) throw new Exception('Este socio no contiene cuota.');

    $cuotaPagada = $_pagoModel->getPagoPorCuota($_cadenaConexion, $_idCuota);

    if ($cuotaPagada !== null) {
        $estadoPago = (int)$cuotaPagada['estado'];

        if ($estadoPago === 0) {
            $estadoPago = 1;
        }
        elseif ($estadoPago === 1) {
            $estadoPago = 0;
        }

        $updateado = $_pagoModel->processEstadoPagoCuotaSocio($_cadenaConexion, $estadoPago, $_idCuota, $_idUsuario);

        if (!$updateado) throw new Exception('No se pudo modificar el estado del pago.');

        http_response_code(200);
        echo json_encode([
            'message' => 'Pago actualizado con exito!'
        ]);
    }
    else {
        $creado = $_pagoModel->registerPagoCuotaSocio($_cadenaConexion, $_idCuota, $_idUsuario);

        if (!$creado) throw new Exception('No se pudo ejecutar el pago de la cuota.');

        http_response_code(201);
        echo json_encode([
            'message' => 'Pago creado con exito!'
        ]);
    }
}
?>