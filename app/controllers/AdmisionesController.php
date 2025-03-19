<?php

require_once __DIR__ . "/../models/Admisiones.php";
require_once __DIR__ . "/../core/Cors.php"; 

class AdmisionesController {
    private $admision;

    public function __construct() {
        $this->admision = new Admisiones();
        header("Content-Type: application/json"); // Estandariza las respuestas como JSON
    }

    public function createAdmission() {
        // Verificación de campos obligatorios
        if (!isset($_POST['primerNombre'], $_POST['segundoNombre'], $_POST['primerApellido'], $_POST['segundoApellido'], 
                  $_POST['correo'], $_POST['identidad'], $_POST['telefono'], $_POST['carreraPrincipal'], 
                  $_POST['carreraSecundaria'], $_POST['centroRegional']) || 
            !isset($_FILES['certificado'])) {
            echo json_encode(['error' => 'Todos los campos son obligatorios']);
            return;
        }

        // Obtener los valores del formulario
        $primerNombre = $_POST['primerNombre'];
        $segundoNombre = $_POST['segundoNombre'];
        $primerApellido = $_POST['primerApellido'];
        $segundoApellido = $_POST['segundoApellido'];
        $correo = $_POST['correo'];
        $identidad = $_POST['identidad'];
        $telefono = $_POST['telefono'];
        $carreraPrincipal = $_POST['carreraPrincipal'];
        $carreraSecundaria = $_POST['carreraSecundaria'];
        $centroRegional = $_POST['centroRegional'];

        // Obtener el archivo de certificado
        $certificado = $_FILES['certificado'];

        // Definir la ruta de subida
        $uploadDir = __DIR__ . "/../uploads/certificados";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Obtener la extensión del archivo
        $fileExtension = pathinfo($certificado['name'], PATHINFO_EXTENSION);
        $fileName = uniqid('certificado_') . '.' . $fileExtension;
        $uploadPath = $uploadDir . '/' . $fileName;

        // Mover el archivo a la carpeta de destino
        if (!move_uploaded_file($certificado['tmp_name'], $uploadPath)) {
            echo json_encode(['error' => 'Hubo un problema al subir el certificado']);
            return;
        }

        // Crear un arreglo con los campos a insertar en la base de datos
        $fields = [
            'Primer_nombre' => $primerNombre,
            'Segundo_nombre' => $segundoNombre,
            'Primer_apellido' => $primerApellido,
            'Pegundo_apellido' => $segundoApellido,
            'Correo' => $correo,
            'Numero_identidad' => $identidad,
            'Numero_telefono' => $telefono,
            'CarreraID' => $carreraPrincipal,
            'CarreraAlternativaID' => $carreraSecundaria,
            'CentroRegionalID' => $centroRegional,
            'CertificadoSecundaria' => $fileName // Guardamos solo el nombre del archivo
        ];

        // Instanciar el modelo y realizar la creación
        $newAdmissionId = $this->admision->create($fields);

        if ($newAdmissionId) {
            // Recuperar la admisión recién creada usando el ID
            $newAdmission = $this->admision->getOne($newAdmissionId);
            
            // Responder con el registro recién creado
            http_response_code(200);
            echo json_encode([
                'success' => 'Admisión creada correctamente',
                'admision' => $newAdmission
            ]);
        } else {
            // En caso de error
            http_response_code(500);
            echo json_encode(['error' => 'Hubo un error al crear la admisión']);
        }
    }
}
