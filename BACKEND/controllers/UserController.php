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
$input = json_decode(file_get_contents('php://input'), true);
$method = $_SERVER['REQUEST_METHOD'];

require_once '../database/database.php';
require_once '../models/User.php';

$cadenaConexion = Conexion::getInstance()->getConexion();

try {
    $usuarios = match ($method) {
        'POST' => getUsuariosController($cadenaConexion, $input['rol'])
    };

    echo json_encode($usuarios);
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

function getUsuariosController($_cadenaConexion, $_rol) {
    if ($_rol === null) throw new Exception('Se requiere de un tipo de rol.');
    $usuariosPermitidos = ['Administrador', 'Cobrador'];
    if (!in_array($_rol, $usuariosPermitidos, true)) throw new Exception('Valor de rol no valido.'); 

    $userModel = new UserModel();
    $usuarios = null;

    if ($_rol === 'Administrador') {
        $usuarios = $userModel->getAdministradores($_cadenaConexion);
    } elseif ($_rol === 'Cobrador') {
        $usuarios = $userModel->getCobradores($_cadenaConexion);
    }

    if ($usuarios === null) {
        throw new Exception('No se encontro ningun ' . $_rol);
    }

    return $usuarios;
}

?>