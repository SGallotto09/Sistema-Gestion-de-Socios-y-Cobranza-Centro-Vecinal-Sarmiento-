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
        'POST'      => userLoged($cadenaConexion, $input['usuario'], $input['contrasenia']),
        default     => throw new Exception('Método HTTP no permitido')
    };
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'message' => 'Error interno del servidor.'
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'message' => $e->getMessage()
    ]);
}


function userLoged($_cadenaConexion, $_usuario, $_contrasenia) {
    // ESTAS SON LAS VARIABLES QUE ME DEVUELVE LA LECTURA DEL CUERPO DEL INPUT
    //         ESTA LINEA DE CODIGO ME SACA LOS ESPACIOS INTERNOS Y EXTERNOS AL MISMO TIEMPO
    $usuario = preg_replace('/\s+/', ' ', trim($_usuario ?? ''));
    $contrasenia = trim($_contrasenia ?? ''); 

    if (empty($usuario)) throw new Exception('El usuario es obligatorio.');
    if (empty($contrasenia)) throw new Exception('La contraseña es obligatoria.');

    $login = new LoginModel();

    $logueado = $login->validarUsuario($_cadenaConexion, $_usuario, $_contrasenia);

    if (!$logueado) throw new Exception('No se pudo iniciar sesion. Credenciales incorrectas.');

    http_response_code(200);
    echo json_encode([
        'message'           => 'Bienvenido al sistema',
        'usuarioEncontrado' => $logueado,
        'token'             => $_SESSION['token_pestania']
    ]);
}

?>