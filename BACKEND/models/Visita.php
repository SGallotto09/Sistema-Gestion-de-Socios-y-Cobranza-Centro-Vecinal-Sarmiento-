<?php

class Visita {
    function registerVisitaSocio($conexion, $idSocio, $idUsuario) {
        try {
            $query = "INSERT INTO visita (created_by, created_at, estado, id_socio) 
                        VALUES (:created_by, :created_at, :estado, :id_socio)";

            $stmt = $conexion->prepare($query);

            $stmt->execute([
                'created_by' => $idUsuario,
                'created_at' => date('Y-m-d'),
                'estado'     => 1,
                'id_socio'   => $idSocio
            ]);

            return true;
        }
        catch (PDOException $e) {
            throw $e;
        }
    }   

    function processEstadoVisita($conexion, $estado, $idVisita, $idUsuario) {
        try {
            $query = "UPDATE visita SET estado = :estado, 
                        updated_by = :updated_by, 
                        updated_at = :updated_at 
                        WHERE id = :id";

            $stmt = $conexion->prepare($query);

            $stmt->execute([
                'estado' => $estado,
                'updated_by' => $idUsuario,
                'updated_at' => date('Y-m-d')
            ]);

            return true;
        }
        catch (PDOException $e) {
            throw $e;
        }
    }
}

?>