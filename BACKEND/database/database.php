<?php

class Conexion {

    public static function obtenerConexion() {
        $host = 'localhost';
        $port = '3306';
        $user = 'root';
        $password = 'AguStin09';
        $dbname = 'vecinal_sarmiento_bbdd';

        try {
            $connectionString = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";

                    // PDO = PHP Data Objects
            $conexion = new PDO($connectionString, $user, $password);

            $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $conexion;
        } catch (PDOException $e) {
            die('Conexion fallida: ' . $e->getMessage());
        }
    } 

}

?>