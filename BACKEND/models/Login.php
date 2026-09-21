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

        return $usuarioEncontrado;
    }
}

?>