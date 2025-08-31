<?php
header('Content-Type: application/json');

// Configuración SMTP (datos de Ferozo)
$smtpHost = "c2182006.ferozo.com";
$smtpUser = "contacto@estepaconsultores.ar";
$smtpPass = "kL6/7fC9gS";
$smtpPort = 465; // Puerto SSL

// Validar campos obligatorios
$nombre = $_POST['nombre'] ?? '';
$apellido = $_POST['apellido'] ?? '';
$email = $_POST['email'] ?? '';
$mensaje = $_POST['mensaje'] ?? '';

if (!$nombre || !$apellido || !$email || !$mensaje) {
    echo json_encode(["success" => false, "error" => "Faltan campos obligatorios"]);
    exit;
}

// (Descomentar cuando uses reCAPTCHA)
/*
$recaptchaResponse = $_POST['g-recaptcha-response'];
$secretKey = "TU_SECRET_KEY_AQUI";

$verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secretKey}&response={$recaptchaResponse}");
$responseData = json_decode($verify);

if (!$responseData->success) {
    echo json_encode(["success" => false, "error" => "Error en reCAPTCHA"]);
    exit;
}
*/

// Validar archivo adjunto si existe
$archivoAdjunto = null;
if (!empty($_FILES['cv']['name'])) {
    $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];
    $maxSize = 4 * 1024 * 1024; // 4 MB

    if (!in_array($_FILES['cv']['type'], $allowedTypes)) {
        echo json_encode(["success" => false, "error" => "Formato no permitido. Solo PDF, JPG o PNG"]);
        exit;
    }

    if ($_FILES['cv']['size'] > $maxSize) {
        echo json_encode(["success" => false, "error" => "El archivo supera los 2MB"]);
        exit;
    }

    $archivoAdjunto = $_FILES['cv']['tmp_name'];
    $nombreArchivo = $_FILES['cv']['name'];
}

// Incluir PHPMailer
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    // Configuración del servidor
    $mail->isSMTP();
    $mail->Host = $smtpHost;
    $mail->SMTPAuth = true;
    $mail->Username = $smtpUser;
    $mail->Password = $smtpPass;
    $mail->SMTPSecure = 'ssl'; // Porque usamos puerto 465
    $mail->Port = $smtpPort;

    // Remitente y destinatario
    $mail->setFrom($email, $nombre . ' ' . $apellido);
    $mail->addAddress('contacto@estepaconsultores.ar', 'Estepa Consultores');

    // Contenido del mail
    $mail->isHTML(true);
    $mail->Subject = 'Nuevo mensaje de contacto desde el sitio web';
    $mail->Body = "
        <h2>De: $nombre $apellido</h2>
        <h3>Email de contacto: $email</h3>
        <p>$mensaje</p>
    ";

    // Adjuntar archivo si existe
    if ($archivoAdjunto) {
        $mail->addAttachment($archivoAdjunto, $nombreArchivo);
    }

    $mail->send();
    echo json_encode(["success" => true]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => "Error al enviar: " . $mail->ErrorInfo]);
}
?>
