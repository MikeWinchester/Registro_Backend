<?php

require_once __DIR__ . '/../config/constants.php';

require_once __DIR__ . "/../helpers/PHPMailer/src/PHPMailer.php";
require_once __DIR__ . "/../helpers/PHPMailer/src/SMTP.php";
require_once __DIR__ . "/../helpers/PHPMailer/src/Exception.php";


function sendEmail($destino, $nombreDestino, $asunto, $mensaje) {
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    
    try {
        $mail->isSMTP();
        $mail->Host       = MAIL_HOST;
        $mail->SMTPAuth   = SMTP_AUTH;
        $mail->Username   = MAIL_USERNAME;
        $mail->Password   = MAIL_PASSWORD;
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Port       = MAIL_PORT;
        $mail->CharSet = SMTP_CHARSET; 
        $mail->Encoding = SMTP_ENCODING;

        $mail->setFrom(MAIL_USERNAME, MAIL_FROM_NAME);
        $mail->addAddress($destino, $nombreDestino);
        
        // Contenido
        $mail->isHTML(true);
        $mail->Subject = $asunto;
        $mail->Body    = $mensaje;
        $mail->AltBody = strip_tags($mensaje); // Versión texto plano

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Error al enviar email: " . $mail->ErrorInfo);
        return false;
    }
}