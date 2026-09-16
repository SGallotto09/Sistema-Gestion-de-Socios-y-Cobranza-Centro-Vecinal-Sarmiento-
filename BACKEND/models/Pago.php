<?php

class PagoModel {
    function getPagoPorCuota($conexion, $idCuota) {
        $query = "SELECT * FROM pago WHERE id_cuota = :id_cuota";

        $stmt = $conexion->prepare($query);

        $stmt->execute([
            'id_cuota' => $idCuota
        ]);

        $cuota = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cuota === false) {
            return null;
        }

        return $cuota;
    }

    function getCantidadDePagos($conexion) {
        $query = "SELECT COUNT(*) FROM pago AS p 
                JOIN cuota AS c ON p.id_cuota = c.id AND c.estado = 1
                JOIN socio AS s ON c.id_socio = s.id WHERE c.fecha_vencimiento >= :fecha_actual AND s.activo = 1";

        $stmt = $conexion->prepare($query);

        $stmt->execute([
            'fecha_actual' => date('Y-m-d')
        ]);

        $cantidadPagos = $stmt->fetchColumn();

        return $cantidadPagos;
    }

    function registerPagoCuotaSocio($conexion, $idCuota, $idUsuario) {
        $conexion->beginTransaction();
        
        $queryEstadoPago = "INSERT INTO pago (created_by, created_at, estado, id_cuota)
                VALUES (:created_by, :created_at, :estado, :id_cuota)";
        
        $stmt = $conexion->prepare($queryEstadoPago);

        $stmt->execute([
            'created_by' => $idUsuario,
            'created_at' => date('Y-m-d'),
            'estado'     => 1,
            'id_cuota'   => $idCuota
        ]);

        $queryEstadoCuota = "UPDATE cuota SET estado = :estado
                            WHERE id = :id";

        $stmt = $conexion->prepare($queryEstadoCuota);

        $stmt->execute([
            'estado'     => 1,
            'id'   => $idCuota
        ]);

        $conexion->commit();
        return true;
    }   

    function processEstadoPagoCuotaSocio($conexion, $estado, $idCuota, $idUsuario) {
        $conexion->beginTransaction();

        $queryEstadoPago = "UPDATE pago SET estado = :estado,
                            updated_by = :updated_by,
                            updated_at = :updated_at
                            WHERE id_cuota = :id_cuota";

        $stmt = $conexion->prepare($queryEstadoPago);

        $stmt->execute([
            'estado'     => $estado,
            'id_cuota'   => $idCuota,
            'updated_by' => $idUsuario,
            'updated_at' => date('Y-m-d')
        ]);

        $queryEstadoCuota = "UPDATE cuota SET estado = :estado
                            WHERE id = :id";

        $stmt = $conexion->prepare($queryEstadoCuota);

        $stmt->execute([
            'estado'     => $estado,
            'id'         => $idCuota
        ]);
        
        $conexion->commit();
        return true;
    }
}

?>