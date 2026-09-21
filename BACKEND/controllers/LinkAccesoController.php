<?php

session_start();

header('Content-Type: application/json');
$input = json_decode(file_get_contents('php://input'), true);
$method = $_SERVER['REQUEST_METHOD'];

require_once '../database/database.php';
require_once '../models/LinkAcceso.php';

$cadenaConexion = Conexion::getInstance()->getConexion();
$linkAcceso = new LinkAcceso();

try {
    if ($method === 'POST') {

        if (!isset($_SESSION['id'])) {
            http_response_code(401);

            echo json_encode([
                'message' => 'Usuario no autenticado.'
            ]);

            exit;
        }

        $idAdministrador = $_SESSION['id'];

        createLinkController($linkAcceso, $cadenaConexion, $idAdministrador, $input['idCobrador'] ?? null, $input['duracionToken']) ?? null;
    }
    elseif ($method === 'GET') {
        validarLinkAccesoController($linkAcceso, $cadenaConexion, $_GET['token'] ?? null);
    }
    else {
        http_response_code(405);
        echo json_encode([
            'message' => 'Método no permitido.'
        ]);
    }
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

function createLinkController($_linkAcceso, $_cadenaConexion, $_idAdministrador, $_idCobrador, $_duracionToken) {
    if ($_idAdministrador === null) throw new Exception('Se requiere del ID del administrador');
    if (!filter_var($_idAdministrador, FILTER_VALIDATE_INT) || $_idAdministrador <= 0) throw new Exception('El ID del administrador no es válido.');

    if ($_idCobrador === null) throw new Exception('Se requiere del ID del cobrador');
    if (!filter_var($_idCobrador, FILTER_VALIDATE_INT) || $_idCobrador <= 0) throw new Exception('El ID del cobrador no es válido.');

    if ($_duracionToken === null) throw new Exception('Se requiere de un tiempo de duracion del token');
    if (!filter_var($_duracionToken, FILTER_VALIDATE_INT) || $_duracionToken <= 0) throw new Exception('La duración del token no es válida.');
    if ($_duracionToken > 24) throw new Exception('El tiempo maximo de duración del token es de 24 horas');

    $linkCreado = $_linkAcceso->createLinkAcceso($_cadenaConexion, $_idAdministrador, $_idCobrador, $_duracionToken);

    if (!$linkCreado) {
        throw new Exception('No se ha podido crear el link de acceso.');
    }

    $url = "http://localhost/Proyecto/BACKEND/controllers/LinkAccesoController.php?token=" . urlencode($linkCreado['token']);

    $linkCreado['url'] = $url;

    http_response_code(201);
    echo json_encode([
        'message' => 'Link de acceso generado con exito!',
        'linkAcceso' => $linkCreado
    ]);
}

function validarLinkAccesoController($_linkAcceso, $_cadenaConexion, $_token) {
    if (empty($_token)) throw new Exception('El link de acceso es invalido.');

    $linkValidado = $_linkAcceso->validarLinkAcceso($_cadenaConexion, $_token);

    if (!$linkValidado) throw new Exception('El link de acceso no es válido o ha vencido.');

    session_regenerate_id(true);

    $_SESSION['cobrador_autenticado'] = true;
    $_SESSION['id_cobrador'] = $linkValidado['destinado_a'];

    header("Location: /Proyecto/FRONTEND/PAGES/cobrador.php");
    exit;
}

?>