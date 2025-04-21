<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Admission.php';
require_once __DIR__ . '/../models/Document.php';
require_once __DIR__ . '/../models/Center.php';
require_once __DIR__ . '/../models/Career.php';
require_once __DIR__ . '/../models/Application.php';
require_once __DIR__ . '/../core/Mail.php';
require_once __DIR__ . '/../helpers/FileUploader.php';

class AdmissionController extends BaseController {
    private $admisionModel;
    private $documentoModel;
    private $centroRegionalModel;
    private $carreraModel;
    private $solicitudModel;
    private $fileUploader;

    public function __construct() {
        parent::__construct();
        $this->admisionModel = new Admission();
        $this->documentoModel = new Document();
        $this->centroRegionalModel = new Center();
        $this->carreraModel = new Career();
        $this->solicitudModel = new Application();
        $this->fileUploader = new FileUploader();
    }

    public function create($request) {
        //try {
            // 1. Validación básica de campos requeridos
            $requiredFields = [
                'primerNombre', 'primerApellido', 'email', 'tipoDocumento', 'numeroDocumento', 'codigoPais', 'telefono',
                'carreraPrincipal', 'carreraSecundaria', 'centroRegional'
            ];
            $data = $request->getBody();
            $this->validateRequiredFields($data, $requiredFields);

            // 2. Validar archivo adjunto
            if (!$request->hasFile('certificadoSecundaria')) {
                throw new Exception('El certificado de secundaria es obligatorio');
            }

            // 3. Validaciones específicas
            $this->validateDocumentNumber($data['tipoDocumento'], $data['numeroDocumento']);
            $this->validateEmail($data['email']);
            $this->validatePhoneNumber($data['telefono'], $data['codigoPais']);

            // 4. Validar referencias en base de datos
            $references = $this->validateReferences($data);

            // 5. Validar y subir archivo
            $fileName = $this->uploadCertificate($request);

            // 6. Crear documento si no existe
            $documentoId = $this->createDocumentIfNotExists($data);
            // 7. Crear admisión con solicitud (transacción)
            $admissionResult = $this->createAdmission($data, $fileName, $documentoId, $references);
            
            $codigo = $this->getGeneratedCode($admissionResult['admision_id']['admision_id']);

            // 9. Enviar correo de confirmación
            $this->sendConfirmationEmail($data, $codigo);

            // 10. Responder con éxito
            $this->sendSuccessResponse($data, $admissionResult['admision_id']['admision_id'], $codigo);

        //} catch (Exception $e) {
          //  $this->handleException($e);
        //}
    }

    /* Métodos de validación */

    private function validateDocumentNumber(string $tipo, string $numero): void {
        $numeroLimpio = preg_replace('/[^A-Z0-9]/', '', strtoupper($numero));
        
        if ($tipo === 'identidad') {
            if (!preg_match('/^\d{13}$/', $numeroLimpio)) {
                throw new Exception('El número de identidad debe tener 13 dígitos');
            }
            
            $mes = (int) substr($numeroLimpio, 0, 2);
            $dia = (int) substr($numeroLimpio, 2, 2);
            
            if ($mes < 1 || $mes > 12 || $dia < 1 || $dia > 31) {
                throw new Exception('Los primeros 4 dígitos del documento deben ser una fecha válida (MMDD)');
            }
        } elseif ($tipo === 'pasaporte') {
            if (!preg_match('/^[A-Z]{2}\d{6}$/', $numeroLimpio)) {
                throw new Exception('El pasaporte debe tener formato AB123456 (2 letras + 6 números)');
            }
        } else {
            throw new Exception('Tipo de documento no válido');
        }
    }

    private function validateEmail(string $email): void {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Formato de correo electrónico no válido');
        }

        // Validar dominio
        $domain = explode('@', $email)[1] ?? '';
        if (!checkdnsrr($domain, 'MX')) {
            throw new Exception('El dominio del correo electrónico no es válido');
        }
    }

    private function validatePhoneNumber(string $telefono, string $codigoPais): void {
        $telefonoLimpio = preg_replace('/[^0-9]/', '', $telefono);
        
        if ($codigoPais === '+504') {
            if (!preg_match('/^[2-9]\d{7}$/', $telefonoLimpio)) {
                throw new Exception('El teléfono hondureño debe tener 8 dígitos y comenzar con un número entre 2-9');
            }
        } else {
            if (!preg_match('/^\d{10}$/', $telefonoLimpio)) {
                throw new Exception('El teléfono internacional debe tener 10 dígitos');
            }
        }
    }

    private function validateReferences(array $data): array {
        $center = $this->centroRegionalModel->getByCode($data['centroRegional']);
        if (!$center) {
            throw new Exception('El centro regional seleccionado no existe');
        }

        $career = $this->carreraModel->getByCode($data['carreraPrincipal']);
        if (!$career) {
            throw new Exception('La carrera principal seleccionada no existe');
        }

        $secCareer = $this->carreraModel->getByCode($data['carreraSecundaria']);
        if (!$secCareer) {
            throw new Exception('La carrera secundaria seleccionada no existe');
        }

        // Verificar que las carreras pertenezcan al centro
        $careerInCenter = $this->carreraModel->existsInCenter(
            $career['carrera_id'], 
            $center['centro_regional_id']
        );
        
        if (!$careerInCenter) {
            throw new Exception('La carrera principal no está disponible en el centro seleccionado');
        }

        $secCareerInCenter = $this->carreraModel->existsInCenter(
            $secCareer['carrera_id'], 
            $center['centro_regional_id']
        );
        
        if (!$secCareerInCenter) {
            throw new Exception('La carrera secundaria no está disponible en el centro seleccionado');
        }

        return [
            'center_id' => $center['centro_regional_id'],
            'career_id' => $career['carrera_id'],
            'sec_career_id' => $secCareer['carrera_id']
        ];
    }

    private function validateUploadedFile(array $file): void {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Error al subir el archivo: ' . $this->getUploadError($file['error']));
        }

        if ($file['size'] > 5 * 1024 * 1024) {
            throw new Exception('El archivo no puede exceder los 5MB');
        }

        $allowedMimeTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.oasis.opendocument.text',
            'image/jpeg',
            'image/png',
            'image/bmp',
            'image/tiff'
        ];

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedMimeTypes)) {
            throw new Exception('Tipo de archivo no permitido. Formatos aceptados: PDF, DOC, DOCX, JPG, JPEG, PNG, BMP, TIFF, ODT');
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['pdf', 'doc', 'docx', 'odt', 'jpg', 'jpeg', 'png', 'bmp', 'tiff'];
        
        if (!in_array($extension, $allowedExtensions)) {
            throw new Exception('Extensión de archivo no permitida');
        }

        // Validación adicional para imágenes
        if (strpos($mimeType, 'image/') === 0) {
            $imageInfo = getimagesize($file['tmp_name']);
            if (!$imageInfo) {
                throw new Exception('El archivo no es una imagen válida');
            }
            
            list($width, $height) = $imageInfo;
            if ($width < 800 || $height < 600) {
                throw new Exception('La imagen debe tener al menos 800x600 píxeles');
            }
        }
    }

    /* Métodos de operaciones */

    private function uploadCertificate($request): string {
        $file = $request->getUploadedFile('certificadoSecundaria');
        $this->validateUploadedFile($file);

        $uploadDir = __DIR__ . '/../data/uploads/certificados/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = uniqid('certificado_') . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
        $uploadPath = $uploadDir . $fileName;
        
        $this->fileUploader->subirArchivo($file, $uploadPath);
        
        return $fileName;
    }

    private function createDocumentIfNotExists(array $data): int {
        // Eliminar guiones y espacios del número de documento
        $numeroDocumentoLimpio = preg_replace('/[^A-Z0-9]/', '', strtoupper($data['numeroDocumento']));
        
        $documento = $this->documentoModel->getByNumber($numeroDocumentoLimpio);
        
        if (!$documento) {
            $tipoDocumentoId = ($data['tipoDocumento'] == "identidad") ? 1 : 2;
            $documentoId = $this->documentoModel->createDocumento(
                $numeroDocumentoLimpio, // Usamos el número limpio
                $tipoDocumentoId
            );
            
            if (!$documentoId) {
                throw new Exception("Error al registrar documento");
            }
            return $documentoId;
        }
        
        return $documento['documento_id'];
    }

    private function createAdmission(array $data, string $fileName, int $documentoId, array $references): array {
        $this->admisionModel->beginTransaction();
        
        try {
            $admissionData = [
                'primer_nombre' => $data['primerNombre'],
                'segundo_nombre' => $data['segundoNombre'],
                'primer_apellido' => $data['primerApellido'],
                'segundo_apellido' => $data['segundoApellido'],
                'correo' => $data['email'],
                'numero_telefono' => $data['codigoPais'] . preg_replace('/[^0-9]/', '', $data['telefono']),
                'documento_id' => $documentoId,
                'centro_regional_id' => $references['center_id'],
                'carrera_id' => $references['career_id'],
                'carrera_secundaria_id' => $references['sec_career_id'],
                'certificado_secundaria' => $fileName
            ];

            $admisionId = $this->admisionModel->createAdmision($admissionData);
            if (!$admisionId) {
                throw new Exception("Error al crear el registro de admisión");
            }

            $this->admisionModel->commit();
            
            return ['admision_id' => $admisionId];
        } catch (Exception $e) {
            $this->admisionModel->rollback();
            throw $e;
        }
    }

    private function getGeneratedCode(int $admisionId): string {
        $solicitud = $this->solicitudModel->getByAdmisionId($admisionId);
        
        if (!$solicitud || empty($solicitud['codigo'])) {
            throw new Exception("No se pudo obtener el código de solicitud generado");
        }

        return $solicitud['codigo'];
    }

    /* Métodos de respuesta */

    private function sendConfirmationEmail(array $data, string $codigo): void {
        $emailContent = <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #f8f8f8; padding: 15px; text-align: center; border-bottom: 1px solid #e0e0e0; }
                .content { padding: 20px; }
                .highlight { font-weight: bold; color: #2c3e50; background-color: #f0f7ff; padding: 8px 12px; border-radius: 4px; display: inline-block; }
                .footer { margin-top: 20px; font-size: 12px; color: #777; text-align: center; }
                a { color: #3498db; text-decoration: none; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h2>Confirmación de Solicitud - Admisiones</h2>
            </div>
            
            <div class='content'>
                <p>Estimado/a {$data['primerNombre']},</p>
                
                <p>Hemos recibido tu solicitud correctamente. A continuación, encontrarás los detalles importantes:</p>
                
                <p><strong>Número de solicitud:</strong> <span class='highlight'>{$codigo}</span></p>
                
                <p>Utiliza este número en el apartado de <strong>Solicitud en Admisiones</strong> para realizar el seguimiento de tu proceso.</p>
                
                <p style='text-align: center; margin: 25px 0;'>
                    <a href='' style='background-color: #3498db; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none;'>Verificar Solicitud</a>
                </p>
                        
                <p>Gracias por elegirnos.</p>
            </div>
            
            <div class='footer'>
                <p>© 2025 Universidad Nacional Autónoma de Honduras | Todos los derechos reservados.</p>
            </div>
        </body>
        </html>
        HTML;

        sendEmail(
            $data['email'],
            $data['primerNombre'] . ' ' . $data['primerApellido'],
            "Número de Solicitud: Proceso de Admisión UNAH",
            $emailContent
        );
    }

    private function sendSuccessResponse(array $data, int $admisionId, string $codigo): void {
        $this->jsonResponse([
            'success' => 'Admisión creada correctamente',
            'title' => '¡Solicitud enviada con éxito!',
            'subtitle' => "¡Gracias por tu interés por estudiar en UNAH, {$data['primerNombre']}!",
            'message' => "Hemos enviado un correo a <strong>{$data['email']}</strong> con tu número de solicitud. Úsalo para verificar el estado de solicitud.",
            'data' => [
                'codigo' => $codigo,
                'id' => $admisionId
            ]
        ], 201);
    }

    private function getUploadError(int $errorCode): string {
        $errors = [
            UPLOAD_ERR_INI_SIZE => 'El archivo excede el tamaño máximo permitido',
            UPLOAD_ERR_FORM_SIZE => 'El archivo excede el tamaño máximo permitido',
            UPLOAD_ERR_PARTIAL => 'El archivo solo se subió parcialmente',
            UPLOAD_ERR_NO_FILE => 'No se seleccionó ningún archivo',
            UPLOAD_ERR_NO_TMP_DIR => 'Falta la carpeta temporal',
            UPLOAD_ERR_CANT_WRITE => 'No se pudo escribir el archivo en el disco',
            UPLOAD_ERR_EXTENSION => 'Una extensión de PHP detuvo la subida del archivo',
        ];
        
        return $errors[$errorCode] ?? 'Error desconocido al subir el archivo';
    }
}