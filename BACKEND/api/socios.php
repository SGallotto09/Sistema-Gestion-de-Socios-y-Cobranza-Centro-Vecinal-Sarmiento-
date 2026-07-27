<?php

header('Content-Type: application/json');

include '../config/database.php';

$method = $_SERVER['REQUEST_METHOD'];

$input = json_decode(file_get_contents('php://input'), true);

if ($method === 'GET') {
    if (isset($_GET['accion']) && $_GET['accion'] === 'cantidad') {
        GetCantidadSocios($pdo);
    } else {
        GetSocios($pdo);
    }

    return;
}

match (($method)) {
    'POST' => PostSocio($pdo, $input),
    'PUT' => PutSocio($pdo, $input),
    'DELETE' => DeleteSocio($pdo, $input),
};

function GetSocios($pdo) {
    $query = "SELECT * FROM socio";

    $stmt = $pdo->prepare($query);

    $stmt->execute();

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($result);
}

function GetCantidadSocios($pdo) {
    $query = "SELECT COUNT(*) AS cantidad FROM socio";

    $stmt = $pdo->prepare($query);

    $stmt->execute();

    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode($resultado);
}

function PostSocio($pdo, $input) {
    try {
        $datos = validarCampos($input);

        $query = "INSERT INTO socio (nombre, apellido, dni, telefono, barrio, calle, altura) 
                    VALUES (:nombre, :apellido, :dni, :telefono, :barrio, :calle, :altura)";

        $stmt = $pdo->prepare($query);

        $stmt->execute([
            'nombre'   => $datos['nombre'],
            'apellido' => $datos['apellido'],
            'dni'      => $datos['dni'],
            'telefono' => $datos['telefono'],
            'barrio'   => $datos['barrio'],
            'calle'    => $datos['calle'],
            'altura'   => $datos['altura'],
        ]);

        http_response_code(201);

        echo json_encode([
            'message' => 'Socio creado correctamente.'
        ]);

    } catch (Exception $e) {
        http_response_code(400);

        echo json_encode([
            'message' => $e->getMessage()
        ]);
    }
}

function PutSocio($pdo, $input) {
    $id = validarId($input);
    $datos = validarCampos($input);

    $query = "UPDATE socio SET nombre = :nombre,
                    apellido = :apellido,
                    dni = :dni,
                    telefono = :telefono,
                    barrio = :barrio,
                    calle = :calle,
                    altura = :altura WHERE id = :id";

    $stmt = $pdo->prepare($query);

    $stmt->execute([
        'id'       => $id,
        'nombre'   => $datos['nombre'],
        'apellido' => $datos['apellido'],
        'dni'      => $datos['dni'],
        'telefono' => $datos['telefono'],
        'barrio'   => $datos['barrio'],
        'calle'    => $datos['calle'],
        'altura'   => $datos['altura'],
    ]);

    // SI NO SE AFECTO NINGUNA FILA ES PORQUE EL SOCIO NO EXISTE O PORQUE NO SE REALIZO NINGUN CAMBIO
    if ($stmt->rowCount() === 0) {
        echo json_encode(['message' => 'No se realizaron cambios.']);
        return;
    }

    echo json_encode(['message' => 'Socio actualizado correctamente.']);
}

function DeleteSocio($pdo, $input) {
    $id = validarId($input);

    $query = "DELETE FROM socio WHERE id = :id";

    $stmt = $pdo->prepare($query);

    $stmt->execute([
        'id' => $id,
    ]);

    echo json_encode(['message' => 'Socio eliminado correctamente']);
}

function validarId($input) {

    // $input['id'] ?? => ?? significa que es algo nulo. Y la linea completa me sirve para usar el id que me viene del input o sino un texto vacio gracias a ??

    // FILTER_SANITIZE_NUMBER_INT => me sirve para filtrar solo por numeros enteros del 0 al 9
    $id = filter_var($input['id'] ?? '', FILTER_SANITIZE_NUMBER_INT);

    // VALIDO QUE SEA UN NUMERO ENTERO CON VALORES DEL 0 AL 9
    if (!ctype_digit($id)) {
        http_response_code(400);
        echo json_encode(['message' => 'ID inválido.']);
        return;
    }

    return $id;
}

function validarCampos($input) {
    // SANITIZAR
    $nombre = preg_replace('/\s+/', ' ', trim($input['nombre'] ?? ''));
    $apellido = preg_replace('/\s+/', ' ', trim($input['apellido'] ?? ''));
    $dni = preg_replace('/[^0-9]/', '', trim($input['dni'] ?? ''));
    $telefono = preg_replace('/[^0-9]/', '', trim($input['telefono'] ?? ''));
    $barrio = preg_replace('/\s+/', ' ', trim($input['barrio'] ?? ''));
    $calle = preg_replace('/\s+/', ' ', trim($input['calle'] ?? ''));
    $altura = preg_replace('/[^0-9]/', '', trim($input['altura'] ?? ''));

    // VALIDACIONES
    if (empty($nombre)) {
        throw new Exception('El nombre es obligatorio.');
    }

    if (strlen($nombre) < 2 || strlen($nombre) > 50) {
        throw new Exception('El nombre debe tener entre 2 y 50 caracteres.');
    }

    if (empty($apellido)) {
        throw new Exception('El apellido es obligatorio.');
    }

    if (strlen($apellido) < 2 || strlen($apellido) > 50) {
        throw new Exception('El apellido debe tener entre 2 y 50 caracteres.');
    }

    if (empty($dni)) {
        throw new Exception('El DNI es obligatorio.');
    }

    if (!ctype_digit($dni) || strlen($dni) != 8) {
        throw new Exception('El DNI debe contener exactamente 8 números.');
    }

    if (empty($telefono)) {
        throw new Exception('El teléfono es obligatorio.');
    }

    if (!ctype_digit($telefono) || strlen($telefono) < 8 || strlen($telefono) > 15) {
        throw new Exception('El teléfono es inválido.');
    }

    if (empty($barrio)) {
        throw new Exception('El barrio es obligatorio.');
    }

    if (strlen($barrio) > 50) {
        throw new Exception('El barrio no puede superar los 50 caracteres.');
    }

    if (empty($calle)) {
        throw new Exception('La calle es obligatoria.');
    }

    if (strlen($calle) > 100) {
        throw new Exception('La calle no puede superar los 100 caracteres.');
    }

    if (empty($altura)) {
        throw new Exception('La altura es obligatoria.');
    }

    if (!ctype_digit($altura)) {
        throw new Exception('La altura debe ser un número válido.');
    }

    // RETORNO TODOS LOS VALORES DE MI INPUT YA SANITIZADOS Y VALIDADOS PARA EJECUTAR LA QUERY
    return [
        'nombre'   => $nombre,
        'apellido' => $apellido,
        'dni'      => $dni,
        'telefono' => $telefono,
        'barrio'   => $barrio,
        'calle'    => $calle,
        'altura'   => $altura
    ];
}

?>