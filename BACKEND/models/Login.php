<?php

class LoginModel {
    function validarUsuario($conexion, $usuario, $contrasenia) {
        $query = "SELECT * FROM usuario WHERE usuario = :usuario";

        $stmt = $conexion->prepare($query);

        $stmt->execute([
            'usuario' => $usuario,
        ]);

        $usuarioEncontrado = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuarioEncontrado) return false;

        if (!password_verify($contrasenia, $usuarioEncontrado['contrasenia'])) return false;

        session_start();
        session_regenerate_id(true);

        $_SESSION['id'] = $usuarioEncontrado['id'];
        $_SESSION['rol'] = $usuarioEncontrado['rol'];
        $_SESSION["token_pestania"] = bin2hex(random_bytes(32));

        return $usuarioEncontrado;
    }
}

?>