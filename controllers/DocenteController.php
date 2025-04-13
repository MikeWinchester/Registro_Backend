<?php

require_once __DIR__ . "/../models/Docente.php";
require_once __DIR__ . "/BaseController.php";

require_once __DIR__ . "/../controllers/JefeController.php";

class DocenteController extends BaseController{
    private $docente;
    private $jefe;

    public function __construct() {
        parent::__construct();
        $this->docente = new Docente();
        $this->jefe = new JefeController();
        header("Content-Type: application/json"); 
    }

    
    public function createDocente() {
        $data = json_decode(file_get_contents("php://input"), true);
    
        $data["contrasenia"] = password_hash($data["contrasenia"], PASSWORD_DEFAULT);
    
        $data_usr = [
            "nombre_completo" => $data["nombre_completo"],
            "identidad" => $data["identidad"],
            "correo" => $data["correo"],
            "contrasenia" => $data["contrasenia"],
            "numero_cuenta" => $data["numero_cuenta"],
            "telefono" => isset($data["telefono"]) ? $data["telefono"] : null,
        ];
    
        $this->docente->crearDocente(array_values($data_usr));
    
        $usuario = $this->docente->obtenerUsuarioID($data['correo']);
    
        if (!$usuario || count($usuario) == 0) {
            http_response_code(500);
            echo json_encode(["error" => "Error al obtener UsuarioID"]);
            return;
        }
    
        $data_doc = [
            "usuario_id" => $usuario[0]['usuario_id'],
            "centro_regional_id" => $data["centro_regional_id"],
            "carrera_id" => $data["carrera_id"],
            "departamento_id" => $data["departamento_id"],
            "foto_perfil" => null
        ];
    
        // Insertar docente
        if ($this->docente->create($data_doc)) {
            echo json_encode(["message" => "Docente creado correctamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error al crear Docente"]);
        }
    }

    
    public function getDocente(){
        

        $header = getallheaders();

        if(!isset($header['docenteid'])){
            echo "clabe docenteid necesria";
            return;
        }

        $docenteid = $header['docenteid'];


        $result = $this->docente->obtenerPerfilDocente($docenteid);

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Docente obtenido", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Docente no obtenido"]);
        }
    }


    public function getAllDocentes(){

        $result = $this->docente->obtenerTodosDocentes();

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Docentes obtenidos", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Docentes no obtenidos"]);
        }
    }

    public function getDocentesBydepartment(){

        $header = getallheaders();

        $dep = $header['areaid'];
        $centro = $this->jefe->getCentroByJefe($header['jefeid']);

        $result = $this->docente->listaDocentes($dep, $centro);

        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Docentes obtenidos", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Docentes no obtenidos"]);
        }
    }

    public function getDocentesByHorario() {
        header('Content-Type: application/json');
        $header = getallheaders();
    
        $sec = $header['seccionid'];
        $dep = $header['areaid'];
        $centro = $this->jefe->getCentroByJefe($header['jefeid']);
        $periodo = $this->getPeriodo();
    
        $horario = $this->docente->obtenerHorarioBySeccion($sec);
    
        if (!$horario || empty($horario[0])) {
            http_response_code(400);
            echo json_encode(["error" => "No se encontró el horario de la sección."]);
            return;
        }
    
        $horarioData = $horario[0];
        list($inicio, $fin) = explode('-', $horarioData['horario']);
        $dias = explode(',', str_replace(' ', '', $horarioData['dias'])); // ["Lun", "Mar", ...]
    
        $diasCondiciones = implode(' OR ', array_fill(0, count($dias), "sc.dias LIKE ?"));
        $diasParams = array_map(fn($d) => "%$d%", $dias);
    
  
    
        $params = array_merge(
            [$periodo, $fin, $inicio],
            $diasParams,
            [$dep, $centro]
        );
    
        $result = $this->docente->listaDocentesDispo($diasCondiciones, $params);
    
        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Docentes disponibles obtenidos", "data" => $result]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "No se encontraron docentes disponibles"]);
        }
    }
    
    public function getUsuarioByDocente(){
        $header = getallheaders();


        $result = $this->docente->obtenerUsuarioByDoc($header['docenteid']);

        if($result){
            http_response_code(200);
            echo json_encode(['message' => 'usuario obtenido', "data" => $result]);
        }else{
            http_response_code(400);
            echo json_encode(['error' => 'usuario no obtenido']);
        }
    }


    public function uploadData() {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents("php://input"), true);
    
        $docenteId = $input['docente_id'] ?? 0;
        $descripcion = $input['descripcion'] ?? '';
        $base64 = $input['foto_perfil'] ?? null;
    
        $ruta = $this->guardarImagenBase64($base64, $docenteId);
    
        $data = [
            'foto_perfil' => $ruta,
            'descripcion' => $descripcion,
            'docente_id' => $docenteId
        ];
    
        if (strlen($ruta) > 255) {
            http_response_code(400);
            echo json_encode(['error' => "Excede el máximo de caracteres permitidos en la ruta"]);
            return;
        }

        $result = $this->docente->uploadData($data);
    
        if ($result) {
            http_response_code(200);
            echo json_encode(['message' => "Se han actualizado los datos"]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => "Ha ocurrido un error"]);
        }
    }
    
    private function guardarImagenBase64($base64, $docenteId) {
    
        if (!preg_match('/^data:image\/(\w+);base64,/', $base64, $type)) {
            http_response_code(400);
            echo json_encode(['error' => 'Formato base64 no válido']);
            return false;
        }
    
        $base64 = substr($base64, strpos($base64, ',') + 1);
        $type = strtolower($type[1]); 
    
        $decoded = base64_decode($base64);
    
        $carpetaDestino = "data/uploads/$docenteId/";
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

    public function uploadVideo(){
        $data = json_decode(file_get_contents("php://input"), true);

        $value = [$data['seccion_id'], $data['titulo'], $data['video'], $data['descripcion']];
        $result = $this->docente->uploadVideoSql($value);

        if($result){
            http_response_code(200);
            echo json_encode(['message' => 'Se ha guardado el video']);
        }else{
            http_response_code(400);
            echo json_encode(['error' => 'Ha ocurrido un error']);
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

    public function getId(){
        $header = getallheaders();

        $result = $this->docente->obtenerDocenteId([$header['id']]);

        if($result){
            http_response_code(200);
            echo json_encode(['data' => $result]);
        }else{
            http_response_code(400);
            echo json_encode(['error' => "No se pudo completa la accion"]);
        }
    }
}

?>
