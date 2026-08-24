<?php

require_once '../database/database.php';
require_once '../models/Cuota.php';

$conexion = new Conexion();
$cadenaConexion = $conexion->obtenerConexion();

$cuotaModel = new CuotaModel();
$cuotas = $cuotaModel->getUltimasCuotasSocios($cadenaConexion);

$hoy = date('Y-m-d');

foreach ($cuotas as $cuota) {
    if ($cuota['fecha_vencimiento'] < $hoy) {
        $cuotaModel->createCuotaSocio($cadenaConexion, $cuota['id_socio'], $cuota['idPS']);
    }
}

?>