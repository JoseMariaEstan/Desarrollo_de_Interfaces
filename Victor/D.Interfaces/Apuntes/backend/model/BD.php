<?php
class BD {
    private static $instance = null;
    private $conexion;

    private function __construct() {
        $host = "localhost";
        $db = "universidad";
        $user = "root";
        $pass = "";

        $this->conexion = new PDO(
            "mysql:host=$host;dbname=$db;charset=utf8",
            $user,
            $pass
        );
        $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getConexion() {
        return $this->conexion;
    }
}