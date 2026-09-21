<?php

require_once __DIR__ . '/../database/database.php';

try {
    $conexion = Conexion::getInstance()->getConexion();

    $sql = "UPDATE link_acceso SET activo = 0 WHERE fecha_vencimiento <= NOW() AND activo = 1";

    $stmt = $conexion->prepare($sql);
    $stmt->execute();

    echo "Links vencidos desactivados correctamente.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

?>