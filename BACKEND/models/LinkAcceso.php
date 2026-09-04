<?php

date_default_timezone_set('America/Argentina/Buenos_Aires');

class LinkAcceso {
    function createLinkAcceso($conexion, $idAdministrador, $idCobrador, $duracionToken) {
        $token = bin2hex(random_bytes(32));
        $fechaVencimiento = date('Y-m-d H:i:s', strtotime("+{$duracionToken} hours"));

        $query = "INSERT INTO link_acceso (token, activo, created_by, destinado_a, fecha_creacion, fecha_vencimiento)
                    VALUES (:token, :activo, :created_by, :destinado_a, :fecha_creacion, :fecha_vencimiento)";
        
        $stmt = $conexion->prepare($query);

        $stmt->execute([
            'token' => $token,
            'activo' => 1,
            'created_by' => $idAdministrador,
            'destinado_a' => $idCobrador,
            'fecha_creacion' => date('Y-m-d H:i:s'),
            'fecha_vencimiento' => $fechaVencimiento
        ]);

        $idCreado = $conexion->lastInsertId();

        if ($idCreado === '0') {
            return false;
        }

        return [
            'token' => $token,
            'destinado_a' => $idCobrador,
            'fecha_vencimiento' => $fechaVencimiento
        ];
    }
}

?>