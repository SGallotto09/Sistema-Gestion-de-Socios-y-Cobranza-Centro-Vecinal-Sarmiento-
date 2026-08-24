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
        catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'message' => $e->getMessage()
            ]);

            return false;
        }
    }

    function createPagoCuotaSocio($conexion, $idCuota, $idUsuario) {
        try {
            $query = "INSERT INTO pago (created_by, created_at, estado, id_cuota)
                    VALUES (:created_by, :created_at, :estado, :id_cuota)";
            
            $stmt = $conexion->prepare($query);

            $stmt->execute([
                'created_by' => $idUsuario,
                'created_at' => date('Y-m-d'),
                'estado'     => 1,
                'id_cuota'   => $idCuota
            ]);

            return true;
        }
        catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'message' => $e->getMessage()
            ]);

            return false;
        }
    }   

    function updatePagoCuotaSocio($conexion, $estadoPago, $idCuota, $idUsuario) {
        try {
            $query = "UPDATE pago SET estado = :estado,
                        updated_by = :updated_by,
                        updated_at = :updated_at
                        WHERE id_cuota = :id_cuota";

            $stmt = $conexion->prepare($query);

            $stmt->execute([
                'estado'     => $estadoPago,
                'id_cuota'   => $idCuota,
                'updated_by' => $idUsuario,
                'updated_at' => date('Y-m-d')
            ]);

            return true;
        }
        catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'message' => $e->getMessage()
            ]);

            return false;
        }
    }
}

?>