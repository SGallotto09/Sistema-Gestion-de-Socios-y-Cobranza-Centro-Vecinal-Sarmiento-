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
require_once '../models/LinkAcceso.php';

$cadenaConexion = Conexion::getInstance()->getConexion();
$idAdministrador = $_SESSION['id'];

try {
    match ($method) {
        'POST' => createLinkController($cadenaConexion, $idAdministrador, $input['idCobrador'], $input['duracionToken'])
    };
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'message' => 'Ocurrion un error en el servidor.'
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'message' => $e->getMessage()
    ]);
}

function createLinkController($_cadenaConexion, $_idAdministrador, $_idCobrador, $_duracionToken) {
    if ($_idAdministrador === null) throw new Exception('Se requiere del ID del administrador');
    if (!filter_var($_idAdministrador, FILTER_VALIDATE_INT) || $_idAdministrador <= 0) throw new Exception('El ID del administrador no es válido.');

    if ($_idCobrador === null) throw new Exception('Se requiere del ID del cobrador');
    if (!filter_var($_idCobrador, FILTER_VALIDATE_INT) || $_idCobrador <= 0) throw new Exception('El ID del cobrador no es válido.');

    if ($_duracionToken === null) throw new Exception('Se requiere de un tiempo de duracion del token');
    if (!filter_var($_duracionToken, FILTER_VALIDATE_INT) || $_duracionToken <= 0) throw new Exception('La duración del token no es válida.');
    if ($_duracionToken > 24) throw new Exception('El tiempo maximo de duración del token es de 24 horas');

    $linkAcceso = new LinkAcceso();
    $linkCreado = $linkAcceso->createLinkAcceso($_cadenaConexion, $_idAdministrador, $_idCobrador, $_duracionToken);

    if (!$linkCreado) {
        throw new Exception('No se ha podido crear el link de acceso.');
    }

    http_response_code(201);
    echo json_encode([
        'message' => 'Link de acceso generado con exito!',
        'linkAcceso' => $linkCreado
    ]);
}

?>