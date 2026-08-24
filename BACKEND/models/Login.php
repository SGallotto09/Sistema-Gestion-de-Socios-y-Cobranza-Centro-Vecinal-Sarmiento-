<?php

class LoginModel {
    function validarUsuario($conexion, $input) {

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

        try {
            $query = "SELECT * FROM usuario WHERE usuario = :usuario";

            $stmt = $conexion->prepare($query);

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

            session_start();
            session_regenerate_id(true);

            $_SESSION['id'] = $usuarioEncontrado['id'];
            $_SESSION['rol'] = $usuarioEncontrado['rol'];

            return true;
        }
        catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'message' => $e->getMessage()
            ]);

            return false;
        }
    }
}

?>