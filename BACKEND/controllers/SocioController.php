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
require_once '../models/Socio.php';
require_once '../models/Cuota.php';

$conexion = new Conexion();
$cadenaConexion = $conexion->obtenerConexion();

$socio = new SocioModel();
$idUsuario = 1;

$method = $_SERVER['REQUEST_METHOD'];

$input = json_decode(file_get_contents('php://input'), true);

if ($method === 'GET') {
    $socios = null;
    $cantidadSocios = null;

    if (isset($_GET['accion']) && $_GET['accion'] === 'cantidadSocios') {
        $cantidadSocios = $socio->getCantidadSocios($cadenaConexion, null);

    } else if (isset($_GET['accion']) && $_GET['accion'] === 'cantidadCobranza') {
        $cantidadSocios = $socio->getCantidadSocios($cadenaConexion, 'cobranza');

    } else if (isset($_GET['accion']) && $_GET['accion'] === 'nombreSocio') {
        $socios = $socio->getSocioPorNombre($cadenaConexion);

    } else if (isset($_GET['accion']) && $_GET['accion'] === 'cobranza') {
        $socios = $socio->getSociosCobranza($cadenaConexion);

    } else if (isset($_GET['parametro'])) {
        $socios =$socio->getSociosFiltro($cadenaConexion, $_GET['parametro']);

    } else {
        $socios = $socio->getSocios($cadenaConexion);
    }

    if ($socios !== null) {
        http_response_code(200);
        echo json_encode($socios);
    }

    if ($cantidadSocios !== null) {
        http_response_code(200);
        echo json_encode($cantidadSocios);
    }

    return;
}

match (($method)) {
    'POST' => createSocioController($socio, $cadenaConexion, $input, $idUsuario),
    'PUT' => $socio->updateSocio($cadenaConexion, $input, $idUsuario),
    'DELETE' => $socio->deleteSocio($cadenaConexion, $input, $idUsuario)
};

function createSocioController($_socio, $_cadenaConexion, $_input, $_idUsuario) {
    try {
        $socioCreado = $_socio->createSocio($_cadenaConexion, $_input, $_idUsuario);

        if (!empty($socioCreado['idSocioCreado'])) {
            $cuota = new CuotaModel();
            $cuotaCreada = $cuota->createCuotaSocio(
                $_cadenaConexion, $socioCreado['idSocioCreado'], $socioCreado['id_periodo']
            );

            if (!$cuotaCreada) {
                http_response_code(500);
                echo json_encode([
                    'message' => 'No se pudo crear la cuota.'
                ]);

                return;
            }
            
            http_response_code(201);
            echo json_encode([
                'message' => 'Socio y cuota creados correctamente.'
            ]);
        }
    }
    catch (PDOException $e) {
        http_response_code(500);

        echo json_encode([
            'message' => 'Ocurrió un error al crear el socio y la cuota.'
        ]);
    }
}

?>