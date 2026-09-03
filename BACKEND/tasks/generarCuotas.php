<?php

require_once '../database/database.php';
require_once '../models/Cuota.php';

$cadenaConexion = Conexion::getInstance()->getConexion();

try {
    $cuotaModel = new CuotaModel();
    $cuotas = $cuotaModel->getUltimasCuotasSocios($cadenaConexion);

    if ($cuotas === null) {
        throw new Exception('No se encontraron cuotas.');
    }

    $hoy = date('Y-m-d');

    foreach ($cuotas as $cuota) {
        if ($cuota['fecha_vencimiento'] < $hoy) {
            $cuotaModel->createCuotaSocio($cadenaConexion, $cuota['id_socio'], $cuota['idPS']);
        }
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'message' => 'Ocurrio un error en el servidor.'
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'message' => $e->getMessage()
    ]);
}

?>