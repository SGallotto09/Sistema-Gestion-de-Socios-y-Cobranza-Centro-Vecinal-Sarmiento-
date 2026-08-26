<?php

class PagoModel {
    function getPagoPorCuota($conexion, $idCuota) {
        try {
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
        catch (PDOException $e) {
            http_response_code(400);

            throw $e;
        }
    }

    function registerPagoCuotaSocio($conexion, $idCuota, $idUsuario) {
        try {
            $conexion->beginTransaction();
            
            $queryEstadoPago = "INSERT INTO pago (created_by, created_at, estado, id_cuota)
                    VALUES (:created_by, :created_at, :estado, :id_cuota)";
            
            $stmt = $conexion->prepare($query);

            $stmt->execute([
                'created_by' => $idUsuario,
                'created_at' => date('Y-m-d'),
                'estado'     => 1,
                'id_cuota'   => $idCuota
            ]);

            $queryEstadoCuota = "UPDATE cuota SET estado = :estado,
                                updated_by = :updated_by,
                                updated_at = :updated_at
                                WHERE id = :id";

            $stmt = $conexion->prepare($queryEstadoCuota);

            $stmt->execute([
                'estado'     => 1,
                'id_cuota'   => $idCuota,
                'updated_by' => $idUsuario,
                'updated_at' => date('Y-m-d')
            ]);
            $conexion->commit();
            return true;
        }
        catch (PDOException $e) {
            http_response_code(400);
            $conexion->rollBack();

            throw $e;
        }
    }   

    function processEstadoPagoCuotaSocio($conexion, $estado, $idCuota, $idUsuario) {
        try {
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
        catch (PDOException $e) {
            http_response_code(400);
            $conexion->rollBack();

            throw $e;
        }
    }
}

?>