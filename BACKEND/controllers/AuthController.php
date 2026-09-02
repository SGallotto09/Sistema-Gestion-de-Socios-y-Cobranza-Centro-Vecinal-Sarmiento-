<?php

header('Content-Type: application/json');

require_once '../database/database.php';
require_once '../models/Login.php';

$cadenaConexion = Conexion::getInstance()->getConexion();

$method = $_SERVER['REQUEST_METHOD'];

// ESTA LINEA ME LEE EL CUERPO DE LA REQUEST CONVIRTIENDOLA A FOTMATO PHP 
$input = json_decode(file_get_contents('php://input'), true);

match($method) {
    'POST' => userLoged($cadenaConexion, $input),
};

function userLoged($_cadenaConexion, $_input) {
    $login = new LoginModel();
    $logueado = false;

    $logueado = $login->validarUsuario($_cadenaConexion, $_input);

    if (!$logueado) {
        http_response_code(404);
        echo json_encode([
            'message' => 'No se pudo loguear. Credenciales incorrectas.'
        ]);
    }

    http_response_code(200);
    echo json_encode([
        'message' => 'Bienvenido al sistema!'
    ]);
}

?>