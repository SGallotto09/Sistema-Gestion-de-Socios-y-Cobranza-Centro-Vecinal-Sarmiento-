<?php

header('Content-Type: application/json');

include '../config/database.php';

$method = $_SERVER['REQUEST_METHOD'];

// ESTA LINEA ME LEE EL CUERPO DE LA REQUEST CONVIRTIENDOLA A FOTMATO PHP 
$input = json_decode(file_get_contents('php://input'), true);

match($method) {
    'GET' => handleGet($pdo),
    'POST' => handlePost($pdo, $input),
    'PUT' => handlePut($pdo, $input),
    'DELETE' => handleDelete($pdo, $input)
};

function handleGet($pdo) {
    $query = "SELECT * FROM usuarios";

    $stmt = $pdo->prepare($query);

    $stmt->execute();

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($result);
}

function handlePost($pdo, $input) {

    // ESTAS SON LAS VARIABLES QUE ME DEVUELVE LA LECTURA DEL CUERPO DEL INPUT
    //         ESTA LINEA DE CODIGO ME SACA LOS ESPACIOS INTERNOS Y EXTERNOS AL MISMO TIEMPO
    $usuario = preg_replace('/\s+/', ' ', trim($input['usuario'] ?? ''));
    $contrasenia = trim($input['contrasenia'] ?? ''); 

    $query = "SELECT * FROM usuarios WHERE usuario = :usuario";

    $stmt = $pdo->prepare($query);

    $stmt->execute([
        'usuario' => $usuario,
    ]);

    $usuarioEncontrado = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuarioEncontrado) {
        echo json_encode(['message' => 'Usuario o contraseña incorrectos']);
        return;
    } 

    if (!password_verify($contrasenia, $usuarioEncontrado['contrasenia'])) {
        echo json_encode(['message' => 'Usuario o contraseña incorrectos']);
        return;
    }

    echo json_encode([
        'message' => 'Acceso exitoso',
        'rol' => $usuarioEncontrado['rol']
    ]);
}

function handlePut($pdo, $input) {
    $query = "UPDATE usuarios SET usuario = :usuario, contrasenia = :contrasenia WHERE id = :id";

    $stmt = $pdo->prepare($query);

    $stmt->execute([
        'id' => $input['id'],
        'usuario' => $input['usuario'],
        'contrasenia' => $input['contrasenia'],
    ]);

    echo json_encode(['message' => 'Usuario actualizado exitosamente!']);
}

function handleDelete($pdo, $input) {
    $query = "DELETE FROM usuarios WHERE id = :id";

    $stmt = $pdo->prepare($query);

    $stmt->execute([
        'id' => $input['id'],
    ]);

    echo json_encode(['message' => 'Usuario eliminado exitosamente!']);
}

?>