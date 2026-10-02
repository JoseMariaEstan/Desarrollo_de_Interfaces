<?php
require_once __DIR__ . "/BD.php";

class Alumno {
    private $dni;
    private $nombre;
    private $apellido_1;
    private $localidad;
        //iniciar las variables con valor null, nos permite usar la funcion sin necesidad de pasarle paramentros
    public function __construct($dni = null, $nombre = null, $apellido_1 = null, $localidad = null) {
        $this->dni = $dni;
        $this->nombre = $nombre;
        $this->apellido_1 = $apellido_1;
        $this->localidad = $localidad;
    }

    public function insertAlm() {
        try {
            $conexion = BD::getInstance()->getConexion();
            $sql = "INSERT INTO alumno (dni, nombre, apellido_1, localidad) VALUES (:dni, :nombre, :apellido_1, :localidad)";
            $stmt = $conexion->prepare($sql);

            return $stmt->execute([
                ':dni'       => $this->dni,
                ':nombre'    => $this->nombre,
                ':apellido_1' => $this->apellido_1,
                ':localidad' => $this->localidad
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function obtenerAlm() {
        try {
            $conexion = BD::getInstance()->getConexion();
            $sql = "SELECT dni, nombre, apellido_1, localidad FROM alumno where dni = :dni";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([':dni' => $this-> dni]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }
    public function updateAlm(){
        try{
            $conexion = BD::getInstance()->getConexion();
            $sql="UPDATE alumno SET nombre = :nombre, apellido_1 = :apellido_1, localidad = :localidad WHERE dni = :dni";
            $stmt= $conexion->prepare($sql);
            return $stmt->execute([
                ':dni'       => $this->dni,
                ':nombre'    => $this->nombre,
                ':apellido_1' => $this->apellido_1,
                ':localidad' => $this->localidad
            ]);
        } catch (PDOException $e){
            return false;
        }
    }
    public function deleteAlm(){
        try{
            $conexion = BD::getInstance()->getConexion();
            $sql = "DELETE FROM alumno WHERE dni = :dni";
            $stmt= $conexion->prepare($sql);
            return $stmt->execute([':dni' => $this->dni]);
        }
        catch(PDOException $e){
        return false;
        }
    }
}