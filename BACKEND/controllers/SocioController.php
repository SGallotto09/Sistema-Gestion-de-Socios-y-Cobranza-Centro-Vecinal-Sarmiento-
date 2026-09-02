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
require_once '../models/Socio.php';
require_once '../models/Cuota.php';

$cadenaConexion = Conexion::getInstance()->getConexion();

$socio = new SocioModel();
$idUsuario = $_SESSION['id'];

$method = $_SERVER['REQUEST_METHOD'];

$input = json_decode(file_get_contents('php://input'), true);

try {
    match (($method)) {
        'GET'       => getSociosController($socio, $cadenaConexion),
        'POST'      => createSocioController($socio, $cadenaConexion, $input, $idUsuario),
        'PUT'       => updateSocioCointroller($cadenaConexion, $input, $idUsuario),
        'DELETE'    => deleteSocioController($cadenaConexion, $input, $idUsuario),
        default     => throw new Exception('Método HTTP no permitido')
    };
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'message' => 'Error interno del servidor.'
    ]);

} catch (Exception $e) {
    htpp_response_code(400);
    echo json_encode([
        'message' => $e->getMessage()
    ]);
}

function getSociosController($socio, $conexion) {
    $accion = $_GET['accion'] ?? null;

    $resultado = match ($accion) {
        'cantidadSocios'    => $socio->getCantidadSocios($conexion, null),
        'cantidadCobranza'  => $socio->getCantidadSocios($conexion, 'cobranza'),
        'nombreSocio'       => $socio->getSocioPorNombre($conexion),
        'cobranza'          => $socio->getSociosCobranza($conexion),
        'filtro'            => $socio->getSociosFiltro($conexion, $_GET['parametro'] ?? ''),
        default             => $socio->getSocios($conexion)
    };

    http_response_code(200);
    echo json_encode($resultado);
}

function createSocioController($_socio, $_cadenaConexion, $_input, $_idUsuario) {
    $socioCreado = $_socio->createSocio($_cadenaConexion, $_input, $_idUsuario);

    if (empty($socioCreado['idSocioCreado'])) {
        throw new Exception('No se pudo crear el socio.');
    }

    $cuota = new CuotaModel();
    $cuotaCreada = $cuota->createCuotaSocio(
        $_cadenaConexion, $socioCreado['idSocioCreado'], $socioCreado['id_periodo']
    );

    if (!$cuotaCreada) {
        throw new Exception('No se pudo crear la cuota.');
    }

    http_response_code(201);
    echo json_encode([
        'message' => 'Socio y cuota creados correctamente.'
    ]);
}

function updateSocioCointroller($_socio, $_cadenaConexion, $_input, $_idUsuario) {
    $socioUpdated = $_socio->updatSocio($_cadenaConexion, $_input, $_idUsuario);

    if (!$socioUpdated) {
        throw new Exception('No se pudo modificar el socio.');
    }

    htpp_response_code(200);
    echo json_encode([
        'message' => 'Socio modificado correctamente.'
    ]);
}

function deleteSocioController($_socio, $_cadenaConexion, $_input, $_idUsuario) {
    $socioEliminado = $_socio->deleteSocio($_cadenaConexion, $_input, $_idUsuario);

    if (!$socioEliminado) {
        throw new Exception('No se elimino ningun socio.');
    }

    http_response_code(200);
    echo json_encode([
        'message' => 'Socio eliminado correctamente.'
    ]);
}

?>