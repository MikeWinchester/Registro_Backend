<?php
require_once __DIR__ . "/../models/Clase.php";
require_once __DIR__ . "/../controllers/CareerController.php";
require_once __DIR__ . "/BaseController.php";

class ClaseController extends BaseController{
    private $clase;
    private $carrera;

    public function __construct() {
        parent::__construct();
        $this->clase = new Clase();
        $this->carrera = new CareerController();
    }


    /**
     * Obtiene todos las clases por Area
     *
     * @version 0.1.1
     */
    public function getClasesByArea($request){

        $depID = $request->getRouteParam(0);

        $result = $this->clase->obtenerClasesPorDep($depID);

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "clases del departamento obtenidas", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Clases no han sido obtenidas"]);
        }
    }

    public function getClasesByAreaEstu($request){

        $depID = $request->getRouteParam(0);
        $est = $request->getRouteParam(1);

        $carID = $this->carrera->getCarrera($est)['carrera'];

        $result = $this->clase->obtenerClasesPendientes([$est, $this->getPeriodo(), $est, $est,$this->getPeriodo(), $depID, $carID]);

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "clases para estudiante obtenidas", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Clases no han sido obtenidas"]);
        }
    }
   
    /**
     * Crea una clase
     *
     * @version 0.1.0
     */
    public function createClases(){

        $data = json_decode(file_get_contents("php://input"), true);
        
        if ($this->clase->create($data)) {
            http_response_code(200);
            echo json_encode(["message" => "clase creada", "data" => $data]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Clase no creada"]);
        }
    }

    /**
     * retorna el id de un edificio por una clase
     * 
     * @version 0.1.0
     */
    public function getEdidByClass($request){

        $claseID = $request->getRouteParam(0);

        $result = $this->clase->obtenerEdificioPorClase($claseID);

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "clases obtenidas", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Clases no obtenidas"]);
        }

    }

    /**
     * Retorna las clases asignada al docente
     * 
     * @version 0.1.0
     */
    public function getClasesAsigDoc($request){
    
        $claseID = $request->getRouteParam(0);

        $result = $this->clase->obtenerClasesAsignadasDoc($claseID, $this->getPeriodo());

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "clases obtenidas", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Clases no obtenidas"]);
        }
    }
 
    /**
     * Funcion para obtener el periodo acadmico actual
     *
     * @return "anio-trimestre" ejemplo: "2021-1"
     * 
     * @version 0.1.1
     */
    private function getPeriodo() {
        $year = date("Y");
        $mon = date("n");
    
        if ($mon >= 1 && $mon <= 4) {
            $trimestre = "I";
        } elseif ($mon >= 5 && $mon <= 8) {
            $trimestre = "II";
        } else {
            $trimestre = "III";
        }
    
        return "$year-$trimestre";
    }


}

?>
