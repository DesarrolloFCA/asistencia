<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';
require_once 'mail/tobamail.php';
$hacia = 'l_fontes@yahoo.com';
$asunto = 'Asunto del correo';
$cuerpo = 'Este es el cuerpo del correo en texto plano o HTML';
$desde = 'lfontes@fca.uncu.edu.ar'; // Opcional

// Ruta absoluta al archivo de configuración JSON
//$config_file = '/usr/local/proyectos/comision/php/mail/config_smtp.json';

$mail = new TobaMail($hacia, $asunto, $cuerpo, $desde, ['fontesleonardo@gmail.com']);

$mail->setHtml(true); // Si el cuerpo es HTML
//$mail->setCc(['cc1@example.com', 'cc2@example.com']); // Direcciones CC
//$mail->setBcc(['bcc1@example.com']); // Direcciones BCC
//$mail->setReply('replyto@example.com'); // Dirección de respuesta
//$mail->setRemitente('Nombre Remitente'); // Nombre del remitente
//$mail->setConfirmacion('confirm@example.com'); // Dirección de confirmación

// Agregar un archivo adjunto
$mail->agregarAdjunto('informe_mensual_legajo_26010.pdf', '/usr/local/proyectos/comision/php/reporte/informe_mensual_legajo_26010.pdf');

try {
    $mail->ejecutar();
    echo "Correo enviado exitosamente.";
} catch (Exception $e) {
    echo "Error al enviar el correo: " . $e->getMessage();
}
