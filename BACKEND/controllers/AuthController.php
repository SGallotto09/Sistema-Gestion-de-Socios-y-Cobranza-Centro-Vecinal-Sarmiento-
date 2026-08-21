<?php

header('Content-Type: application/json');

require_once '../database/database.php';
require_once '../models/Login.php';

$conexion = new Conexion();
$cadenaConexion = $conexion->obtenerConexion();

$login = new LoginModel();

$method = $_SERVER['REQUEST_METHOD'];

// ESTA LINEA ME LEE EL CUERPO DE LA REQUEST CONVIRTIENDOLA A FOTMATO PHP 
$input = json_decode(file_get_contents('php://input'), true);

match($method) {
    'POST' => $login->validarUsuario($cadenaConexion, $input),
};

?>