<?php

require_once __DIR__ . '/../database/database.php';
require_once __DIR__ . '/../models/Cuota.php';
require_once __DIR__ . '/../models/Socio.php';

$conexion = Conexion::getInstance()->getConexion();

try {
    $conexion->beginTransaction();

    $cuotaModel = new CuotaModel();
    $socioModel = new SocioModel();

    $sociosCon2CuotasImpagasSeguidas = $cuotaModel->getSociosConDosCuotasImpagas($conexion);

    if (empty($sociosCon2CuotasImpagasSeguidas)) {
        $conexion->commit();
        echo "No hay socios para dar de baja." . PHP_EOL;
        exit;
    }

    $cantidadBajas = 0;

    foreach ($sociosCon2CuotasImpagasSeguidas as $socio) {
        $idSocio = (int) $socio['id_socio'];
        $socioModel->darDeBajaSocio($conexion, $idSocio);

        $cantidadBajas++;
    }
    $conexion->commit();

    echo "Proceso realizado correctamente." . PHP_EOL;
    echo "Socios dados de baja: " . $cantidadBajas . PHP_EOL;

} catch (PDOException $e) {
    if ($conexion->inTransaction()) {
        $conexion->rollBack();
    }
    echo "Error de base de datos: " . $e->getMessage() . PHP_EOL;

    exit(1);
} catch (Exception $e) {
    if ($conexion->inTransaction()) {
        $conexion->rollBack();
    }
    echo "Error: " . $e->getMessage() . PHP_EOL;
    exit(1);
}