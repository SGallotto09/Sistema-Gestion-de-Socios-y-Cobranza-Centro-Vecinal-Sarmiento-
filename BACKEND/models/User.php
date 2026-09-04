<?php

class UserModel {
    function getCobradores($conexion) {
        $query = "SELECT * FROM usuario WHERE rol = :rol";

        $stmt = $conexion->prepare($query);

        $stmt->execute([
            'rol' => 'Cobrador'
        ]);

        $cobradores = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$cobradores) {
            return null;
        }

        return $cobradores;
    }

    function getCobradorById($conexion, $idCobrador) {
        $query = "SELECT * FROM usuario WHERE id = :id AND rol = :rol";

        $stmt = $conexion->prepare($query);

        $stmt->execute([
            'id'  => $idCobrador,
            'rol' => 'Cobrador'
        ]);

        $cobrador = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$cobrador) {
            return null;
        }

        return $cobrador;
    }

    function getAdministradores($conexion) {
        $query = "SELECT * FROM usuario WHERE rol = :rol";

        $stmt = $conexion->prepare($query);

        $stmt->execute([
            'rol' => 'Administrador'
        ]);

        $administradores = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$administradores) {
            return null;
        }

        return $administradores;
    }

    function getAdministradorById($conexion, $idAdministrador) {
        $query = "SELECT * FROM usuario WHERE id = :id AND rol = :rol";

        $stmt = $conexion->prepare($query);

        $stmt->execute([
            'id' => $idCobrador,
            'rol' => 'Administrador'
        ]);

        $administrador = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$administrador) {
            return null;
        }

        return $administrador;
    }
}

?>