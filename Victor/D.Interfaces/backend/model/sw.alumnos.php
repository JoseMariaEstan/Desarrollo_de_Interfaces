<?php 
include "Alumno.php";
//Indicar que es una respuesta JSON, es decir, para que la web reconozca el archivo
header("Content-Type: application/json; charset=UTF-8");

/*echo '{
    "nombre":"Juan",
    "apellido_1": "Perez",
    "localidad": "Madrid"    
}';
*/
// Obtener el contenido del cuerpo de la petición
$contenidoJSON = file_get_contents('php://input');
// Usa true como seguno parametro para obtener un array asociativo en lugar de un objeto
$request = json_decode($contenidoJSON, true);

$success = true;
$msg = "";
$data =[];
$arrSalida = [];

switch($request ["action"]){
    case "insert":
        $alumno["dni"] = $request["data"][0]["dni"];
        $alumno["nombre"] = $request["data"][0]["nombre"];
        $alumno["apellido_1"] = $request["data"][0]["apellido_1"];
        $alumno["localidad"] = $request["data"][0]["localidad"];

        $alumnoA=new Alumno($alumno["dni"], $alumno["nombre"], $alumno["apellido_1"], $alumno["localidad"]);

        $success = $alumnoA->insertAlm();
        $data = $alumno;
        $msg = $success ? "Alumno insertado correctamente" : "No se pudo insertar el alumno";
        break;

    case "get":
        //se le pasa directamente el dni para buscar por el como pk
        $alumnoA = new Alumno($request["data"][0]["dni"]);
        $resultado = $alumnoA->obtenerAlm();
        
        if($resultado !== false){
            $success = true;
            $data = $resultado;
            $msg="Alumnos obtenidos correctamente";
        }else{
            $success = false;
            $msg="Error al obtener los alumnos";
        }
        break;

    case "update":
        //Se le pasan todos los datos del formulario para modificar los campos segun el dni
        $alumno = $request["data"][0];
        $alumnoA = new Alumno(
            $alumno["dni"],
            $alumno["nombre"],
            $alumno["apellido_1"],
            $alumno["localidad"]
        );
        $success = $alumnoA->updateAlm();
        $data = $alumno;
        $msg = $success ? "Alumno actualizado correctamente" : "No se pudo actualizar el alumno";
        break;
    case "delete":
        $dni=$request["data"][0]["dni"];
        $alumnoA = new Alumno($dni);
        
        $success = $alumnoA->deleteAlm();
        $data = ["dni" => $dni];
        $msg = $success ? "Alumno borrado correctamente" : "No se pudo borrar el alumno";

    break;
    default:
        $success = false;
        $msg = "Accion no reconocida";
        break;
}

$arrSalida = [
    "data" => $data,
    "success" => $success,
    "msg" => $msg
];

echo json_encode($arrSalida);

//Hacer que cada pulsacion sea diferente//
//switch con los casos de cada pulsacion, y en cada caso, un array diferente.
//Sin usar el method