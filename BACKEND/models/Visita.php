<?php

class Visita {
    function createVisitaSocio($conexion, $idSocio, $idUsuario) {
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

    function updateEstadoVisita($conexion, $estado, $idVisita, $idUsuario) {
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

        $filasModificadas = $stmt->rowCount();

        if ($filasModificadas === 0) {
            return false;
        }

        return true;
    }
}

?>