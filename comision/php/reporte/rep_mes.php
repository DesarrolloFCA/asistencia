<?php

require 'vendor/autoload.php'; // Asegúrate de tener FPDF y PHPMailer instalados

use FPDF\FPDF;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\OAuth;
use League\OAuth2\Client\Provider\Google;

function obtener_datos_mensuales($legajo) {
    // Conexión a la base de datos
    $host = 'pg';
    $dbname = 'reloj';
    $user = 'postgres';
    $password = 'postgres';

    $dsn = "pgsql:host=$host;dbname=$dbname";
    try {
        $pdo = new PDO($dsn, $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        //echo 'Connection exito ';
        //echo date('Y-m-d H:i:s') . "\n";
    } catch (PDOException $e) {
        echo 'Connection failed: ' . $e->getMessage();
        return [];
    }

    // Calcular fechas de inicio y fin
    $fecha_fin = date('Y-m-d');
    $fecha_inicio = date('Y-m-d', strtotime('-30 days'));

    // Consulta de datos del mes anterior
   $sql= "SELECT Distinct fecha, hora_entrada, hora_salida, horas_trabajadas, horas_requeridad, descripcion, estado 
           FROM reloj.vm_detalle_pres
           WHERE legajo = :legajo
           AND fecha >= :fecha_inicio
           AND fecha <= :fecha_fin";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['legajo' => $legajo, 'fecha_inicio' => $fecha_inicio, 'fecha_fin' => $fecha_fin]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function generar_pdf($datos, $legajo, $fecha_inicio, $fecha_fin) {
    $pdf = new \FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, "Informe Mensual de Horas Trabajadas - Legajo: $legajo", 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 10, "Periodo: $fecha_inicio a $fecha_fin", 0, 1, 'C');
    $pdf->Ln(10);

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(20, 10, 'Fecha', 1);
    $pdf->Cell(30, 10, 'Horas Requeridas', 1);
    $pdf->Cell(30, 10, 'Hora Entrada', 1);
    $pdf->Cell(30, 10, 'Hora Salida', 1);
    $pdf->Cell(30, 10, 'Horas Trabajadas', 1);
    $pdf->Cell(50, 10, 'Descripcion', 1);
    $pdf->Cell(30, 10, 'Estado', 1);
    $pdf->Ln();

    $pdf->SetFont('Arial', '', 10);
    foreach ($datos as $fila) {
        $pdf->Cell(20, 10, $fila['fecha'], 1);
        $pdf->Cell(30, 10, $fila['horas_requeridad'], 1);
        $pdf->Cell(30, 10, $fila['hora_entrada'], 1);
        $pdf->Cell(30, 10, $fila['hora_salida'], 1);
        $pdf->Cell(30, 10, $fila['horas_trabajadas'], 1);
        $pdf->Cell(50, 10, $fila['descripcion'], 1);
        $pdf->Cell(30, 10, $fila['estado'], 1);
        $pdf->Ln();
    }

    $filename = "informe_mensual_legajo_$legajo.pdf";
    $pdf->Output('F', $filename);
    return $filename;
}

function enviar_email($email, $filename) {
           require_once('../mail/tobamail.php');
           
           
            $correo = $email;
       
        // Contenido del correo
        //$mail->isHTML(true);
        $asunto = 'Formulario de Justificacion de Inasistencia por Excesos de Inasistencia (SIN GOCE)';
		$cuerpo = 'ver adjunto';

        // Adjuntar el PDF
       // $mail->addAttachment($filename);


       //Enviamos el correo
		$mail = new TobaMail($correo, $asunto, $cuerpo,$desde , '');

		// Agregar un archivo adjunto
		$mail->agregarAdjunto('nombre_archivo.pdf', $filename);

		try {
			$mail->ejecutar();
			echo "Correo enviado exitosamente.<br>";
		} catch (Exception $e) {
			echo "Error al enviar el correo: " . $e->getMessage();
		}
	}


// Ejemplo de uso
echo "Ingrese el número de legajo: ";
$legajo = trim(fgets(STDIN));
$fecha_fin = date('Y-m-d');
$fecha_inicio = date('Y-m-d', strtotime('-30 days'));
$datos = obtener_datos_mensuales($legajo);
if (!empty($datos)) {
    $filename = generar_pdf($datos, $legajo, $fecha_inicio, $fecha_fin);
    echo "Ingrese el email del destinatario: ";
    $email = trim(fgets(STDIN));
    enviar_email($email, $filename);
} else {
    echo "No se encontraron datos para el legajo ingresado.";
}