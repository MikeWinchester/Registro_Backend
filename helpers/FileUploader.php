<?php
class FileUploader {
    public function subirArchivo($archivo, $destino, $mimesPermitidos = [], $reemplazar = true) {
        // Verificar errores de subida
        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Error al subir el archivo: código {$archivo['error']}");
        }

        // Verificar tipo MIME
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $archivo['tmp_name']);
        finfo_close($finfo);

        if (!empty($mimesPermitidos) && !in_array($mime, $mimesPermitidos)) {
            throw new Exception("Tipo de archivo no permitido: $mime");
        }

        // Crear directorio si no existe
        $directorio = dirname($destino);
        if (!file_exists($directorio)) {
            mkdir($directorio, 0777, true);
        }

        // Eliminar archivo existente si se permite reemplazo
        if ($reemplazar && file_exists($destino)) {
            unlink($destino);
        }

        // Mover el archivo
        if (!move_uploaded_file($archivo['tmp_name'], $destino)) {
            throw new Exception("No se pudo mover el archivo subido");
        }
    }

    public function eliminarArchivosLibro($uuid) {
        $libroDir = __DIR__ . '/../data/books/' . $uuid;
        
        if (file_exists($libroDir)) {
            // Eliminar archivos dentro del directorio
            array_map('unlink', glob("$libroDir/*"));
            // Eliminar el directorio
            rmdir($libroDir);
        }
    }
}
?>