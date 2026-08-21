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
    'POST' => $socio->createSocio($cadenaConexion, $input, $idUsuario),
    'PUT' => $socio->updateSocio($cadenaConexion, $input, $idUsuario),
    'DELETE' => $socio->deleteSocio($cadenaConexion, $input, $idUsuario)
};

?>