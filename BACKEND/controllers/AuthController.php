<?php

header('Content-Type: application/json');

require_once '../database/database.php';
require_once '../models/Login.php';

$cadenaConexion = Conexion::getInstance()->getConexion();

$method = $_SERVER['REQUEST_METHOD'];

// ESTA LINEA ME LEE EL CUERPO DE LA REQUEST CONVIRTIENDOLA A FOTMATO PHP 
$input = json_decode(file_get_contents('php://input'), true);

try {
    match($method) {
        'POST'      => userLoged($cadenaConexion, $input),
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