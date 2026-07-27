<?php

header('Content-Type: application/json');

include '../config/database.php';

$method = $_SERVER['REQUEST_METHOD'];

// ESTA LINEA ME LEE EL CUERPO DE LA REQUEST CONVIRTIENDOLA A FOTMATO PHP 
$input = json_decode(file_get_contents('php://input'), true);

match($method) {
    'GET' => GetAdministradores($pdo),
    'POST' => PostAdministrador($pdo, $input),
    'PUT' => PutAdministrador($pdo, $input),
    'DELETE' => DeleteAdministrador($pdo, $input)
};

function GetAdministradores($pdo) {
    $query = "SELECT * FROM administrador";

    $stmt = $pdo->prepare($query);

    $stmt->execute();

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($result);
}

function PostAdministrador($pdo, $input) {

    // ESTAS SON LAS VARIABLES QUE ME DEVUELVE LA LECTURA DEL CUERPO DEL INPUT
    //         ESTA LINEA DE CODIGO ME SACA LOS ESPACIOS INTERNOS Y EXTERNOS AL MISMO TIEMPO
    $usuario = preg_replace('/\s+/', ' ', trim($input['usuario'] ?? ''));
    $contrasenia = trim($input['contrasenia'] ?? ''); 

    if (empty($usuario)) {
        echo json_encode(['message' => 'El usuario es obligatorio.']);
        return;
    }

    if (empty($contrasenia)) {
        echo json_encode(['message' => 'La contraseña es obligatoria.']);
        return;
    }

    $query = "SELECT * FROM administrador WHERE usuario = :usuario";

    $stmt = $pdo->prepare($query);

    $stmt->execute([
        'usuario' => $usuario,
    ]);

    $usuarioEncontrado = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuarioEncontrado) {
        echo json_encode(['message' => 'Usuario o contraseña incorrectos.']);
        return;
    } 

    if (!password_verify($contrasenia, $usuarioEncontrado['contrasenia'])) {
        echo json_encode(['message' => 'Usuario o contraseña incorrectos.']);
        return;
    }

    echo json_encode([
        'message' => true,
        'rol' => $usuarioEncontrado['rol']
    ]);
}

function  PutAdministrador($pdo, $input) {
    $query = "UPDATE usuarios SET usuario = :usuario, contrasenia = :contrasenia WHERE id = :id";

    $stmt = $pdo->prepare($query);

    $stmt->execute([
        'id' => $input['id'],
        'usuario' => $input['usuario'],
        'contrasenia' => $input['contrasenia'],
    ]);

    echo json_encode(['message' => 'Usuario actualizado exitosamente!']);
}

function DeleteAdministrador($pdo, $input) {
    $query = "DELETE FROM usuarios WHERE id = :id";

    $stmt = $pdo->prepare($query);

    $stmt->execute([
        'id' => $input['id'],
    ]);

    echo json_encode(['message' => 'Usuario eliminado exitosamente!']);
}

?>