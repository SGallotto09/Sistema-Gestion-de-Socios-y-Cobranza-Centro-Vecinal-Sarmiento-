<?php

session_start();

$method = $_SERVER['REQUEST_METHOD'];

$esAdministrador = isset($_SESSION['id']);

$esCobrador = isset($_SESSION['cobrador_autenticado']) &&
                $_SESSION['cobrador_autenticado'] === true &&
                isset($_SESSION['id_cobrador']);

if (!$esAdministrador && !$esCobrador) {
    http_response_code(401);
    echo json_encode([
        'message' => 'Usuario no autenticado.'
    ]);

    exit;
}

if ($method !== 'GET' && !$esAdministrador) {
    http_response_code(403);
    echo json_encode([
        'message' => 'No tiene permisos para realizar esta acción.'
    ]);

    exit;
}

header('Content-Type: application/json');
$input = json_decode(file_get_contents('php://input'), true);

require_once '../database/database.php';
require_once '../models/Socio.php';
require_once '../models/Cuota.php';

$cadenaConexion = Conexion::getInstance()->getConexion();

$idAdministrador = null;

if ($esAdministrador) {
    $idAdministrador = $_SESSION['id'];
}

$socio = new SocioModel();

try {
    match (($method)) {
        'GET'       => getSociosController($socio, $cadenaConexion),
        'POST'      => createSocioController($socio, $cadenaConexion, $input, $idAdministrador),
        'PUT'       => updateSocioCointroller($socio, $cadenaConexion, $input, $idAdministrador),
        'DELETE'    => deleteSocioController($socio, $cadenaConexion, $input, $idAdministrador),
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

function getSociosController($socio, $conexion) {
    $accion = $_GET['accion'] ?? null;

    $resultado = match ($accion) {
        'cantidadSocios'    => $socio->getCantidadSocios($conexion, null),
        'cantidadCobranza'  => $socio->getCantidadSocios($conexion, 'cobranza'),
        'nombreSocio'       => $socio->getSocioPorNombre($conexion),
        'cobranza'          => $socio->getSociosCobranza($conexion),
        'filtro'            => $socio->getSociosFiltro($conexion, $_GET['parametro'] ?? ''),
        default             => $socio->getSocios($conexion)
    };

    http_response_code(200);
    echo json_encode($resultado);
}

function createSocioController($_socio, $_cadenaConexion, $_input, $_idAdministrador) {
    if ($_idAdministrador === null) throw new Exception('El usuario no se encuentra autenticado.');

    $datos = validarCampos($_input);

    $socioCreado = $_socio->createSocio($_cadenaConexion, $datos, $_idAdministrador);

    if (empty($socioCreado['idSocioCreado'])) throw new Exception('No se pudo crear el socio.');

    $cuota = new CuotaModel();
    $cuotaCreada = $cuota->createCuotaSocio($_cadenaConexion, $socioCreado['idSocioCreado'], $socioCreado['id_periodo']);

    if (!$cuotaCreada) throw new Exception('No se pudo crear la cuota.');

    http_response_code(201);
    echo json_encode([
        'message' => 'Socio y cuota creados correctamente.'
    ]);
}

function updateSocioCointroller($_socio, $_cadenaConexion, $_input, $_idAdministrador) {
    if ($_idAdministrador === null) throw new Exception('El usuario no se encuentra autenticado.');

    $idUpdate = validarId($_input);
    $datos = validarCampos($_input);

    $socioUpdated = $_socio->updateSocio($_cadenaConexion, $idUpdate, $datos, $_idAdministrador);

    if (!$socioUpdated) throw new Exception('No se pudo modificar el socio.');

    http_response_code(200);
    echo json_encode([
        'message' => 'Socio modificado correctamente.'
    ]);
}

function deleteSocioController($_socio, $_cadenaConexion, $_input, $_idAdministrador) {
    if ($_idAdministrador === null) throw new Exception('El usuario no se encuentra autenticado.');

    $idEliminar = validarId($_input);

    $socioEliminado = $_socio->deleteSocio($_cadenaConexion, $idEliminar, $_idAdministrador);

    if (!$socioEliminado) throw new Exception('No se elimino ningun socio.');

    http_response_code(200);
    echo json_encode([
        'message' => 'Socio eliminado correctamente.'
    ]);
}

function validarCampos($input) {
    // SANITIZAR
    $apellido = preg_replace('/\s+/', ' ', trim($input['apellido'] ?? ''));
    $nombre = preg_replace('/\s+/', ' ', trim($input['nombre'] ?? ''));
    $dni = preg_replace('/\s+/', '', trim($input['dni'] ?? ''));
    $telefono = preg_replace('/\s+/', '', trim($input['telefono'] ?? ''));
    $barrio = preg_replace('/\s+/', ' ', trim($input['barrio'] ?? ''));
    $calle = preg_replace('/\s+/', ' ', trim($input['calle'] ?? ''));
    $altura = trim($input['altura'] ?? '');

    // VALIDACIONES
    if ($apellido === '') throw new Exception('El apellido es obligatorio.');
    if (strlen($apellido) < 2 || strlen($apellido) > 50) throw new Exception('El apellido debe tener entre 2 y 50 caracteres.');
    if ($nombre === '') throw new Exception('El nombre es obligatorio.');
    if (strlen($nombre) < 2 || strlen($nombre) > 50) throw new Exception('El nombre debe tener entre 2 y 50 caracteres.');
    if ($dni === '') throw new Exception('El DNI es obligatorio.');
    if (!ctype_digit($dni) || strlen($dni) != 8) throw new Exception('El DNI debe contener exactamente 8 números.');
    if ($telefono === '') throw new Exception('El teléfono es obligatorio.');
    if (!ctype_digit($telefono) || strlen($telefono) < 8 || strlen($telefono) > 15) throw new Exception('El teléfono es inválido.');
    if ($barrio === '') throw new Exception('El barrio es obligatorio.');
    if (strlen($barrio) > 50) throw new Exception('El barrio no puede superar los 50 caracteres.');
    if ($calle === '') throw new Exception('La calle es obligatoria.');
    if (strlen($calle) > 100) throw new Exception('La calle no puede superar los 100 caracteres.');
    if ($altura === '') throw new Exception('La altura es obligatoria.');
    if (!ctype_digit($altura)) throw new Exception('La altura debe ser un número válido.');

    // RETORNO TODOS LOS VALORES DE MI INPUT YA SANITIZADOS Y VALIDADOS PARA EJECUTAR LA QUERY
    return [
        'nombre'     => $nombre,
        'apellido'   => $apellido,
        'dni'        => $dni,
        'telefono'   => $telefono,
        'barrio'     => $barrio,
        'calle'      => $calle,
        'altura'     => $altura,
    ];
}

function validarId($input) {
    $id = filter_var($input['id'] ?? '', FILTER_SANITIZE_NUMBER_INT);
    if (!ctype_digit($id)) throw new Exception('ID inválido.');
    
    return $id;
}

?>