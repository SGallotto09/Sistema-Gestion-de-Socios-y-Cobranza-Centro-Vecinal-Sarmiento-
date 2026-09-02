<?php

class Conexion {

    private static $instance = null;

    private $conexion;

    private function __construct() {
        $host = 'localhost';
        $port = '3306';
        $user = 'root';
        $password = 'AguStin09';
        $dbname = 'vecinal_sarmiento_bbdd';

        $connectionString = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";

                // PDO = PHP Data Objects
        $this->conexion = new PDO($connectionString, $user, $password);

        $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Conexion();
        }

        return self::$instance;
    } 

    public function getConexion() {
        return $this->conexion;
    }

}

?>