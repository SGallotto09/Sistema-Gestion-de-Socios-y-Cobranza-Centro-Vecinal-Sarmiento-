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

$conexion = new Conexion();
$cadenaConexion = $conexion->obtenerConexion();

$socio = new SocioModel();
$idUsuario = $_SESSION['id'];

$method = $_SERVER['REQUEST_METHOD'];

$input = json_decode(file_get_contents('php://input'), true);

if ($method === 'GET') {
    if (isset($_GET['accion']) && $_GET['accion'] === 'cantidad') {
        $socio->getCantidadSocios($cadenaConexion);

    } else if (isset($_GET['accion']) && $_GET['accion'] === 'nombreSocio') {
        $socio->getSocioPorNombre($cadenaConexion);

    } else if (isset($_GET['parametro'])) {
        $socio->getSociosFiltro($cadenaConexion, $_GET['parametro']);

    } else {
        $socio->getSocios($cadenaConexion);
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
        $idSocioCreado = $_socio->createSocio($_cadenaConexion, $_input, $_idUsuario);

        if (!empty($idSocioCreado['idSocioCreado'])) {
            $cuota = new CuotaModel();
            $cuotaCreada = $cuota->createCuotaSocio($_cadenaConexion, $idSocioCreado['idSocioCreado']);

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