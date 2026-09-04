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
        'GET'  => getUsuariosController($cadenaConexion, $_GET['rol'] ?? null),
        'POST' => getUsuarioController($cadenaConexion, $input['rol'] ?? null, $input['id'] ?? null)
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

function getUsuarioController($_cadenaConexion, $_rol, $_idUsuario) {
    if ($_rol === null) throw new Exception('Se requiere de un tipo de rol.');
    $usuariosPermitidos = ['Administrador', 'Cobrador'];
    if (!in_array($_rol, $usuariosPermitidos, true)) throw new Exception('Valor de rol no valido.'); 
    if ($_idUsuario === null) throw new Exception('Se requiere de un usuario');
    if (!filter_var($_idUsuario, FILTER_VALIDATE_INT) || $_idUsuario <= 0) throw new Exception('El ID del usuario no es válido.');

    $userModel = new UserModel();
    $usuario = null;

    if ($_rol === 'Administrador') {
        $usuario = $userModel->getAdministradorById($_cadenaConexion, $_idUsuario);
    } elseif ($_rol === 'Cobrador') {
        $usuario = $userModel->getCobradorById($_cadenaConexion, $_idUsuario);
    }

    if ($usuario === null) {
        throw new Exception('No se encontro ningun ' . $_rol);
    }

    return $usuario;
}

?>