<?php
require_once '../models/Cuota.php';

class SocioModel {
    function getSocios($conexion) {
        $querySocios = "  SELECT s.id, s.nombre, s.apellido, s.dni, s.telefono, s.barrio, s.calle, s.altura, p.titulo
                FROM socio AS s 
                JOIN periodo AS p ON s.id_periodo = p.id AND s.eliminado IS NULL
                ORDER BY s.apellido ASC, s.nombre ASC  ";

        $stmt = $conexion->prepare($querySocios);

        $stmt->execute();

        $socios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$socios) {
            return null;
        }

        return $socios;
    }

    function getSociosCobranza($conexion) {
        $querySocios = "SELECT s.id, s.nombre, s.apellido, s.dni, s.telefono, s.barrio, s.calle, s.altura, 
                        c.id AS idCuota, c.estado AS estadoCuota FROM socio AS s
                    INNER JOIN cuota AS c ON c.id_socio = s.id
                    WHERE s.eliminado IS NULL
                    AND c.fecha_creacion <= CURDATE()
                    AND c.fecha_vencimiento >= CURDATE()
                    ORDER BY s.apellido ASC, s.nombre ASC;";

        $stmt = $conexion->prepare($querySocios);

        $stmt->execute();

        $socios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$socios) {
            return null;
        }

        return $socios;
    }

    function getSocioById($conexion, $idSocio) {
        $query = "SELECT * FROM socio WHERE id = :id";

        $stmt = $conexion->prepare($query);

        $stmt->execute([
            'id' => $idSocio
        ]);

        $socio = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$socio) {
            return null;
        }

        return $socio;
    }

    function getCantidadSocios($conexion, $busqueda) {
        if ($busqueda === 'cobranza') {
            $query = "SELECT COUNT(DISTINCT s.id) AS cantidad FROM socio AS s 
                    JOIN periodo AS pe ON s.id_periodo = pe.id 
                    JOIN cuota AS c ON c.id_socio = s.id 
                    AND s.eliminado IS NULL";
            
            $stmt = $conexion->prepare($query);

            $stmt->execute();

            $cantidad = $stmt->fetch(PDO::FETCH_ASSOC);

            return $cantidad;
        }
        else {
            $query = "SELECT COUNT(*) AS cantidad FROM socio";

            $stmt = $conexion->prepare($query);

            $stmt->execute();

            $cantidad = $stmt->fetch(PDO::FETCH_ASSOC);

            return $cantidad;
        }
    }

    function getSociosFiltro($conexion, $parametro) {
        $columnasPermitidas = [
            'id',
            'dni',
            'barrio',
            'calle'
        ];

        // ESTA FUNCION COMPRUEBA SI EL VALOR DE $parametro ESTA DENTRO DEL ARRAY $columnasPermitidas
        // LO NIEGO POR SI VALIDA Q EL VALOR ESTA DENTRO DEL ARRAY, NO ENTRE AL IF
        if (!in_array($parametro, $columnasPermitidas, true)) {
            $parametro = 'apellido ASC, nombre';
        }

        $query = "  SELECT s.id, s.nombre, s.apellido, s.dni, s.telefono, s.barrio, s.calle, s.altura, s.estado, p.titulo 
                    FROM socio AS s JOIN periodo AS p ON s.id_periodo = p.id 
                    ORDER BY $parametro ASC  ";

        $stmt = $conexion->prepare($query);

        $stmt->execute();

        $socios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$socios) {
            return null;
        }

        return $socios;
    }

    function createSocio($conexion, $datos, $idAdministrador) {
        $query = "INSERT INTO socio (nombre, apellido, dni, telefono, barrio, calle, altura, activo, id_periodo, created_by, created_at) 
                VALUES (:nombre, :apellido, :dni, :telefono, :barrio, :calle, :altura, :activo, :id_periodo, :created_by, :created_at)";

        $stmt = $conexion->prepare($query);

        $stmt->execute([
            'nombre'        => $datos['nombre'],
            'apellido'      => $datos['apellido'],
            'dni'           => $datos['dni'],
            'telefono'      => $datos['telefono'],
            'barrio'        => $datos['barrio'],
            'calle'         => $datos['calle'],
            'altura'        => $datos['altura'],
            'activo'        => 1,
            'id_periodo'    => 1,
            'created_by'    => $idAdministrador,
            'created_at'    => date('Y-m-d'),
        ]);

        $idSocioCreado = $conexion->lastInsertId();

        if (!$idSocioCreado) {
            return null;
        }

        return [
            'idSocioCreado' => $idSocioCreado,
            'id_periodo'    => 1
        ];
    }

    function updateSocio($conexion, $idUpdate, $datos, $idAdministrador) {
        $query = "UPDATE socio SET nombre = :nombre,
                        apellido = :apellido,
                        dni = :dni,
                        telefono = :telefono,
                        barrio = :barrio,
                        calle = :calle,
                        altura = :altura,
                        updated_by = :updated_by,
                        updated_at = :updated_at
                        WHERE id = :id";

        $stmt = $conexion->prepare($query);

        $stmt->execute([
            'id'           => $idUpdate,
            'nombre'       => $datos['nombre'],
            'apellido'     => $datos['apellido'],
            'dni'          => $datos['dni'],
            'telefono'     => $datos['telefono'],
            'barrio'       => $datos['barrio'],
            'calle'        => $datos['calle'],
            'altura'       => $datos['altura'],
            'updated_by'   => $idAdministrador,
            'updated_at'   => date('Y-m-d'),
        ]);

        $filasModificadas = $stmt->rowCount();

        if ($filasModificadas === 0) {
            return false;
        }

        return true;
    }

    function deleteSocio($conexion, $idEliminar, $idAdministrador) {
        $query = "UPDATE socio SET activo = :activo,
                    eliminado = :eliminado, 
                    deleted_by = :deleted_by, 
                    deleted_at = :deleted_at 
                    WHERE id = :id";

        $stmt = $conexion->prepare($query);

        $stmt->execute([
            'id'         => $idEliminar,
            'activo'     => 0,
            'eliminado'  => 1,
            'deleted_by' => $idAdministrador,
            'deleted_at' => date('Y-m-d')
        ]);

        $filasModificadas = $stmt->rowCount();

        if ($filasModificadas === 0) {
            return false;
        }

        return true;
    }
}

?>