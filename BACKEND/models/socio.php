<?php
require_once '../models/Cuota.php';

class SocioModel {
    function getSocios($conexion) {
        try {
            $querySocios = "  SELECT s.id, s.nombre, s.apellido, s.dni, s.telefono, s.barrio, s.calle, s.altura, p.titulo
                    FROM socio AS s 
                    JOIN periodo AS p ON s.id_periodo = p.id AND s.eliminado IS NULL
                    ORDER BY s.apellido ASC, s.nombre ASC  ";

            $stmt = $conexion->prepare($querySocios);

            $stmt->execute();

            $socios = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $socios;
        }   
        catch (Exception $e) {
            http_response_code(404);

            echo json_encode([
                'message' => $e->getMessage()
            ]);
        } 
    }

    function getSociosCobranza($conexion) {
        try {
            $querySocios = "SELECT s.id, s.nombre, s.apellido, s.dni, s.telefono, s.barrio, s.calle, s.altura, pe.titulo, c.estado AS estadoCuota, pa.estado AS estadoPago FROM socio AS s 
                            JOIN periodo AS pe ON s.id_periodo = pe.id 
                            JOIN cuota AS c ON c.id_socio = s.id AND s.eliminado IS NULL
                            JOIN pago AS pa ON pa.id_cuota = c.id
                            ORDER BY s.apellido ASC, s.nombre ASC";

            $stmt = $conexion->prepare($querySocios);

            $stmt->execute();

            $socios = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $socios;
        }
        catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'message' => $e->getMessage()
            ]);

            return false;
        }
    }

    function getSocioPorNombre($conexion) {
        try {
            $busqueda = preg_replace('/\s+/', ' ', trim($_GET['buscar'] ?? ''));

            if (empty($busqueda)) {
                echo json_encode([]);
                return;
            }

            $query = "  SELECT s.id, s.nombre, s.apellido, s.dni, s.telefono, s.barrio, s.calle, s.altura, s.estado, p.titulo 
                        FROM socio AS s JOIN periodo AS p ON s.id_periodo = p.id 
                        WHERE nombre LIKE :busqueda 
                        OR apellido LIKE :busqueda
                        OR CONCAT(nombre, ' ', apellido) LIKE :busqueda
                        OR dni LIKE :busqueda 
                        ORDER BY s.apellido ASC, s.nombre ASC  ";

            $stmt = $conexion->prepare($query);

            $stmt->execute([
                ':busqueda' => "%$busqueda%"
            ]);

            $socios = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $socios;
        }
        catch (Excpetion $e) {
            http_resposne_code(404);

            echo json_encode([
                'message' => $e->getMessage()
            ]);
        }
    }

    function getSociosFiltro($conexion, $parametro) {
        try {
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

            return $socios;
        }
        catch (Exception $e) {
            http_response_code(400) ;
            
            echo json_encode([
                'message' => $e->getMessage()
            ]);
        }
    }

    function getCantidadSocios($conexion, $busqueda) {
        if ($busqueda === 'cobranza') {
            try {
                $query = "SELECT COUNT(DISTINCT s.id) AS cantidad FROM socio AS s 
                        JOIN periodo AS pe ON s.id_periodo = pe.id 
                        JOIN cuota AS c ON c.id_socio = s.id 
                        AND s.eliminado IS NULL
                        JOIN pago AS pa ON pa.id_cuota = c.id";
                
                $stmt = $conexion->prepare($query);

                $stmt->execute();

                $cantidad = $stmt->fetch(PDO::FETCH_ASSOC);

                return $cantidad;
            }
            catch (Exception $e) {
                http_response_code(404);

                echo json_encode([
                    'message' => $e->getMessage()
                ]);
            }
        }
        else {
            try {
                $query = "SELECT COUNT(*) AS cantidad FROM socio";

                $stmt = $conexion->prepare($query);

                $stmt->execute();

                $cantidad = $stmt->fetch(PDO::FETCH_ASSOC);

                return $cantidad;
            }
            catch (Exception $e) {
                http_response_code(404);

                echo json_encode([
                    'message' => $e->getMessage()
                ]);
            }
        }
    }

    function createSocio($conexion, $input, $idUsuario) {
        try {
            $datos = $this->validarCampos($input);

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
                'created_by'    => $idUsuario,
                'created_at'    => date('Y-m-d'),
            ]);

            $idSocioCreado = $conexion->lastInsertId();

            return [
                'idSocioCreado' => $idSocioCreado,
                'id_periodo'    => 1
            ];

        } catch (Exception $e) {
            http_response_code(400);

            echo json_encode([
                'message' => $e->getMessage()
            ]);
        }
    }

    function updateSocio($conexion, $input, $idUsuario) {
        try {
            $id = $this->validarId($input);
            $datos = $this->validarCampos($input);

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
                'id'           => $id,
                'nombre'       => $datos['nombre'],
                'apellido'     => $datos['apellido'],
                'dni'          => $datos['dni'],
                'telefono'     => $datos['telefono'],
                'barrio'       => $datos['barrio'],
                'calle'        => $datos['calle'],
                'altura'       => $datos['altura'],
                'updated_by'   => $idUsuario,
                'updated_at'   => date('Y-m-d'),
            ]);

            http_response_code(200);
            // SI NO SE AFECTO NINGUNA FILA ES PORQUE EL SOCIO NO EXISTE O PORQUE NO SE REALIZO NINGUN CAMBIO
            if ($stmt->rowCount() === 0) {
                echo json_encode(['message' => 'No se realizaron cambios.']);
                return;
            }

            echo json_encode(['message' => 'Socio actualizado correctamente.']);

        } catch (Exception $e) {
            http_response_code(400);

            echo json_encode([
                'message' => $e->getMessage()
            ]);
        }
    }

    function deleteSocio($conexion, $input, $idUsuario) {
        try {
            $idEliminar = $this->validarId($input);

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
                'deleted_by' => $idUsuario,
                'deleted_at' => date('Y-m-d')
            ]);

            http_response_code(200);

            echo json_encode([
                'message' => 'Socio eliminado correctamente!'
            ]);
        }
        catch (Exception $e) {
            http_response_code(404);
            echo json_encode([
                'message' => $e->getMessage()
            ]);
        }
    }

    function validarId($input) {

        // $input['id'] ?? => ?? significa que es algo nulo. Y la linea completa me sirve para usar el id que me viene del input o sino un texto vacio gracias a ??

        // FILTER_SANITIZE_NUMBER_INT => me sirve para filtrar solo por numeros enteros del 0 al 9
        $id = filter_var($input['id'] ?? '', FILTER_SANITIZE_NUMBER_INT);

        // VALIDO QUE SEA UN NUMERO ENTERO CON VALORES DEL 0 AL 9
        if (!ctype_digit($id)) {
            throw new Exception('ID inválido.');
        }

        return $id;
    }

    function validarCampos($input) {
        // SANITIZAR
        $apellido = preg_replace('/\s+/', ' ', trim($input['apellido'] ?? ''));
        $nombre = preg_replace('/\s+/', ' ', trim($input['nombre'] ?? ''));
        $dni = preg_replace('/\s+/', '', trim($input['dni'] ?? ''));
        $telefono = preg_replace('/\s+/', '', trim($input['telefono'] ?? ''));
        $barrio = preg_replace('/\s+/', ' ', trim($input['barrio'] ?? ''));
        $calle = preg_replace('/\s+/', ' ', trim($input['calle'] ?? ''));
        $altura = preg_replace('/\s+/', ' ', trim($input['altura'] ?? ''));

        // VALIDACIONES
        if ($apellido === '') {
            throw new Exception('El apellido es obligatorio.');
        }

        if (strlen($apellido) < 2 || strlen($apellido) > 50) {
            throw new Exception('El apellido debe tener entre 2 y 50 caracteres.');
        }

        if ($nombre === '') {
            throw new Exception('El nombre es obligatorio.');
        }

        if (strlen($nombre) < 2 || strlen($nombre) > 50) {
            throw new Exception('El nombre debe tener entre 2 y 50 caracteres.');
        }

        if ($dni === '') {
            throw new Exception('El DNI es obligatorio.');
        }

        if (!ctype_digit($dni) || strlen($dni) != 8) {
            throw new Exception('El DNI debe contener exactamente 8 números.');
        }

        if ($telefono === '') {
            throw new Exception('El teléfono es obligatorio.');
        }

        if (!ctype_digit($telefono) || strlen($telefono) < 8 || strlen($telefono) > 15) {
            throw new Exception('El teléfono es inválido.');
        }

        if ($barrio === '') {
            throw new Exception('El barrio es obligatorio.');
        }

        if (strlen($barrio) > 50) {
            throw new Exception('El barrio no puede superar los 50 caracteres.');
        }

        if ($calle === '') {
            throw new Exception('La calle es obligatoria.');
        }

        if (strlen($calle) > 100) {
            throw new Exception('La calle no puede superar los 100 caracteres.');
        }

        if ($altura === '') {
            throw new Exception('La altura es obligatoria.');
        }

        if (!ctype_digit($altura)) {
            throw new Exception('La altura debe ser un número válido.');
        }

        // RETORNO TODOS LOS VALORES DE MI INPUT YA SANITIZADOS Y VALIDADOS PARA EJECUTAR LA QUERY
        return [
            'nombre'     => $nombre,
            'apellido'   => $apellido,
            'dni'        => $dni,
            'telefono'   => $telefono,
            'barrio'     => $barrio,
            'calle'      => $calle,
            'altura'     => $altura,
        ];
    }
}

?>