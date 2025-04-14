<?php
require_once __DIR__ . "/../models/Estudiante.php";
require_once __DIR__ . "/BaseController.php";

class EstudianteController extends BaseController{
    private $estudiante;
    

    public function __construct() {
        parent::__construct();
        $this->estudiante = new Estudiante();
    }


    public function getEspEstudiante(){
        $header = getallheaders();



        $estudiante = $header['estudianteid'];
        

        $result = $this->estudiante->obtenerEsperaEstudiante($estudiante);

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Secciones encontradas", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Secciones no disponibles"]);
        }
    }


    public function getEstudiante(){
        $header = getallheaders();

        $estudiante = $header['estudianteid'];
        
        $result = $this->estudiante->obtenerPerfilEstudiante($estudiante);

        $indice = $this->estudiante->obtenerIndiceGlobalById($estudiante)['indice_global'];

        if($indice == null){
            $indice = 0;
        }

        $result['indice_global'] = $indice;

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Secciones encontradas", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Secciones no disponibles"]);
        }
    }

    public function getEstudianteByCuenta(){
        $header = getallheaders();

        $estudiante = $header['cuenta'];
        
        $result = $this->estudiante->obtenerEstudianteByCuenta($estudiante);

        $result['indice_global'] = $this->getIndiceGlobal($estudiante);
        $result['indice_periodo'] = $this->getIndicePeriodo($estudiante);

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Estudiante encontrado", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Estudiante  no encontrado"]);
        }
    }

    private function getIndiceGlobal($numero_cuenta){
       

        $result = $this->estudiante->obtenerIndiceGlobal($numero_cuenta);

        if($result){
            return $result['indice_global'];
        }

        return 0;
    }

    private function getIndicePeriodo($numero_cuenta){
        

        $result = $this->estudiante->obtenerIndicePeriodo($numero_cuenta, $this->getPeriodo());

        if($result['indice_periodo'] != null){
            return $result['indice_periodo'];
        }

        $result = $this->estudiante->obtenerIndicePeriodo($numero_cuenta, $this->getPeriodoPasado());

        if($result['indice_periodo'] != null){
            return $result['indice_periodo'];
        }

        return 0;
    }


    public function getHistorial(){
        $header = getallheaders();

       
        $estudiante = $header['cuenta'];
        
        $result = $this->estudiante->obtenerHistorialByCuenta($estudiante);

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Historial encontrado", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Historial no encontrado"]);
        }
    }

    public function getUsuarioByEstu(){
        $header = getallheaders();


        $result = $this->estudiante->obtenerUsuarioByEstudiante($header['estudianteid']);

        if($result){
            http_response_code(200);
            echo json_encode(['message' => 'usuario obtenido', "data" => $result]);
        }else{
            http_response_code(400);
            echo json_encode(['error' => 'usuario no obtenido']);
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

    private function getPeriodoPasado() {
        
        $periodoActual = $this->getPeriodo();
        $anio = explode("-",$periodoActual)[0];
        $periodo = explode("-",$periodoActual)[1];

        if($periodo == 'I'){
            $anio = intval($anio) - 1;
            $periodo = 'III';
        }elseif($periodo == 'II'){
            $periodo = 'I';
        }elseif($periodo == 'III'){
            $periodo = 'II';
        }
    
        return "$anio-$periodo";
    }

    public function getId(){
        $header = getallheaders();

        error_log("ID recibido: " . $header['id']);

        $result = $this->estudiante->obtenerEstudianteId([$header['id']]);

        if($result){
            http_response_code(200);
            echo json_encode(['data' => $result]);
        }else{
            http_response_code(400);
            echo json_encode(['error' => "No se pudo completa la accion"]);
        }
    }

    public function getAll() {
        // Obtenemos parámetros desde la URL
        $busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : null;
        $carrera = isset($_GET['carrera']) ? $_GET['carrera'] : null;

    
        // Ejecutamos la consulta
        $result = $this->estudiante->obtenerHistorialEstudiante($busqueda, $carrera);
    
        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Historial encontrado", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "No se encontraron resultados."]);
        }
    }

    public function updateDescripcion(){
        $data = json_decode(file_get_contents("php://input"), true);

        $result = $this->estudiante->actualizarDescripcion($data);

        if($result){
            http_response_code(200);
            echo json_encode(['message' => 'Se ha actualizado la informacion']);
        }else{
            http_response_code(200);
            echo json_encode(['message' => 'No se ha actualizado la informacion']);
        }
    }

    public function uploadData() {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents("php://input"), true);
    
        $estudianteid = $input['estudiante_id'] ?? 0;
        $base64 = $input['foto_perfil'] ?? null;
    
        if (!$base64) {
            http_response_code(400);
            echo json_encode(['error' => 'La imagen de perfil es obligatoria']);
            return;
        }
    
        $carpetaDestino = "data/uploads/estudiante/$estudianteid/";

        $ruta = $this->guardarImagenBase64($base64, $carpetaDestino);
    
        if (!$ruta) {
            return;  // La función guardarImagenBase64 ya maneja la respuesta de error
        }
    
        $data = [
            'foto_perfil' => $ruta,
            'estudiante_id' => $estudianteid
        ];
    
        if (strlen($ruta) > 255) {
            http_response_code(400);
            echo json_encode(['error' => "Excede el máximo de caracteres permitidos en la ruta"]);
            return;
        }
    
        $value = [$data['foto_perfil'], $data['estudiante_id']];
        $result = $this->estudiante->uploadData($value);
    
        if ($result) {
            http_response_code(200);
            echo json_encode(['message' => "Se ha actualizado la foto de perfil"]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => "Ha ocurrido un error al actualizar la foto de perfil", 'data' => $data]);
        }
    }

    public function uploadGaleria(){
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents("php://input"), true);
    
        $estudianteid = $input['estudiante_id'] ?? 0;
        $base64 = $input['fotografia'] ?? null;
    
        if (!$base64) {
            http_response_code(400);
            echo json_encode(['error' => 'La imagen de perfil es obligatoria']);
            return;
        }
    
        $data = [
            'estudiante_id' => $estudianteid,
            'fotografia' => null
        ];

        $carpetaDestino = "data/uploads/estudiante/$estudianteid/galeria/";

        $value[] = $data['estudiante_id'];
        $result = $this->estudiante->validateGaleria($value);
        if($result['cantidad_fotos'] == 3){
            http_response_code(400);
            echo json_encode(['message' => "Excede la cantidad de fotos permitidas"]);
            return;
        }

        $ruta = $this->guardarImagenBase64($base64, $carpetaDestino);
        $data['fotografia'] = $ruta;
    
        if (strlen($ruta) > 255) {
            http_response_code(400);
            echo json_encode(['error' => "Excede el máximo de caracteres permitidos en la ruta"]);
            return;
        }

        $value[] = $data['fotografia']; 

        $result = $this->estudiante->uploadGaleria($value);
    
        if ($result) {
            http_response_code(200);
            echo json_encode(['message' => "Se ha subido la fotografia"]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => "Ha ocurrido un error al subir la foto"]);
        }
    }

    public function getGaleriaEstu(){
        $header = getallheaders();

        $usuario = $header['id'];

        $result = $this->estudiante->getRouteGaleria([$usuario]);

        if($result){
            http_response_code(200);
            echo json_encode(['message' => "Se ha obtenido la galeria", 'data' => $result]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => "Ha ocurrido un error "]);
        }
    }
    
    private function guardarImagenBase64($base64, $carpetaDestino) {
        if (!preg_match('/^data:image\/(\w+);base64,/', $base64, $type)) {
            http_response_code(400);
            echo json_encode(['error' => 'Formato base64 no válido']);
            return false;
        }
    
        $base64 = substr($base64, strpos($base64, ',') + 1);
        $type = strtolower($type[1]); 
    
        $decoded = base64_decode($base64);
    
        if (!is_dir($carpetaDestino)) {
            mkdir($carpetaDestino, 0755, true);
        }
    
        $nombreArchivo = uniqid() . "." . $type;
        $rutaDestino = $carpetaDestino . $nombreArchivo;
    
        if (file_put_contents($rutaDestino, $decoded)) {
            return $rutaDestino;
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'No se pudo guardar la imagen']);
            return false;
        }
    }

    public function deleteFotoGal(){
        $data = json_decode(file_get_contents("php://input"), true);

        $filepath = $data['ruta'] ?? null;

        if ($filepath && file_exists($filepath)) {
            if (unlink($filepath)) {
                $result = $this->estudiante->eliminarFoto([$data['ruta']]);
                if($result){
                    http_response_code(200);
                    echo json_encode(["message" => "Se ha eliminado la fotografia"]);
                }else{
                    http_response_code(600);
                    echo json_encode(["error" => "No se pudo eliminar el archivo"]);
                }
            } else {
                http_response_code(500);
                echo json_encode(["error" => "No se pudo eliminar el archivo"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Ruta no válida o archivo no encontrado"]);
        }

    }
    

}
?>