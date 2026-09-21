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

    function getTotalVisitasSocios($conexion) {
        $query = "SELECT COUNT(DISTINCT s.id) FROM visita AS v 
            JOIN cuota AS c ON v.id_cuota = c.id 
            JOIN socio AS s ON c.id_socio = s.id WHERE s.activo = 1 AND c.fecha_vencimiento >= CURDATE()";

        $stmt = $conexion->prepare($query);

        $stmt->execute();

        $totalVisitas = $stmt->fetchColumn();

        return $totalVisitas;
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