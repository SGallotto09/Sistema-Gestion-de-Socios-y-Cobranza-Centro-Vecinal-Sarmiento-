<?php

class VisitaModel {
    function getCantidadVisitasSocio($conexion, $idCuota) {
        $query = "SELECT COUNT(*) AS cantidad FROM visita WHERE id_cuota = :id_cuota";

        $stmt = $conexion->prepare($query);

        $stmt->execute([
            'id_cuota' => $idCuota
        ]);

        $visitas = $stmt->fetchColumn();

        return $visitas;
    }

    function createVisita($conexion, $idCuota, $idUsuario) {
        $query = "INSERT INTO visita (created_by, created_at, id_cuota) 
                    VALUES (:created_by, :created_at, :id_cuota)";

        $stmt = $conexion->prepare($query);

        $stmt->execute([
            'created_by' => $idUsuario,
            'created_at' => date('Y-m-d H:i:s'),
            'id_cuota'   => $idCuota
        ]);

        return true;
    }   
}

?>