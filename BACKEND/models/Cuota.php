<?php

date_default_timezone_set('America/Argentina/Buenos_Aires');

class CuotaModel {
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