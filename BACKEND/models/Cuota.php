<?php

date_default_timezone_set('America/Argentina/Buenos_Aires');

class CuotaModel {
    function getCuotaById($conexion, $idCuota) {
        $query = "SELECT * FROM cuota WHERE id = :id";

        $stmt = $conexion->prepare($query);

        $stmt->execute([
            'id' => $idCuota
        ]);

        $cuota = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$cuota) {
            return false;
        }

        return $cuota;
    }

    function getUltimasCuotasSocios($conexion) {
        $queryCuotas = "SELECT c.*, s.id_periodo as idPS FROM cuota as c 
                        INNER JOIN(SELECT id_socio, MAX(id) AS ultima_cuota FROM cuota GROUP BY id_socio)
                        ultimas ON c.id = ultimas.ultima_cuota
                        INNER JOIN socio as s
                        ON c.id_socio = s.id WHERE s.activo = 1";

        $stmt = $conexion->prepare($queryCuotas);

        $stmt->execute();

        $cuotas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$cuotas) {
            return null;
        }

        return $cuotas;
    }

    function getCuotasSinPagar($conexion) {
        $query = "SELECT COUNT(*) FROM cuota AS c JOIN socio AS s ON c.id_socio = s.id 
        WHERE c.estado = 0 AND s.activo = 1 AND c.fecha_vencimiento >= :fecha_actual";

        $stmt = $conexion->prepare($query);

        $stmt->execute([
            'fecha_actual' => date('Y-m-d')
        ]);

        $cuotasSinPagar = $stmt->fetchColumn();

        return $cuotasSinPagar;
    }

    function createCuotaSocio($conexion, $idSocio, $idPeriodoSocio) {
        $query = "INSERT INTO cuota (fecha_creacion, fecha_vencimiento, estado, id_socio, id_periodo)
                    VALUES (:fecha_creacion, :fecha_vencimiento, :estado, :id_socio, :id_periodo)";

        $stmt = $conexion->prepare($query);

        $stmt->execute([
            'fecha_creacion'    => date('Y-m-d'),
            'fecha_vencimiento' => (new DateTime())->modify('+2 months')->format('Y-m-d'),
            'estado'            => 0,
            'id_socio'          => $idSocio,
            'id_periodo'        => $idPeriodoSocio,
        ]);

        return true;
    }
}

?>