<?php
date_default_timezone_set('America/Argentina/Buenos_Aires');
require '../../vendor/autoload.php'; // Asegúrate de tener FPDF y PHPMailer instalados

use setasign\Fpdi\Fpdi;
use PHPMailer\PHPMailer\Exception;


function obtener_datos_mensuales($legajo, $fecha_inicio, $fecha_fin)
{

    // Consulta de datos del mes anterior
    $sql = "SELECT Distinct fecha, hora_entrada, hora_salida, horas_trabajadas, horas_requeridad, descripcion, estado 
        FROM reloj.vm_detalle_pres
        WHERE legajo = $legajo
        AND fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'";

    $presentismo = toba::db('comision')->consultar($sql);

    return $presentismo;
}

function generar_pdf($presentismo, $legajo, $nombre, $apellido, $fecha_inicio, $fecha_fin)
{
    $pdf = new \FPDF();

    $url = 'logo_grande.png';
    $pdf->AddPage('P', 'A4'); // Orientación horizontal
    $pdf->SetMargins(10, 10, 10); // Márgenes: izquierda, arriba, derecha

    $pdf->Image($url, 10, 10, 50); // Ajusta la posición y el tamaño de la imagen
    $pdf->SetXY(70, 10); // Ajusta la posición del título
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->MultiCell(0, 5, "Informe de Horas Trabajadas\nLegajo: $legajo\nNombre: $nombre $apellido", 0, 'L');
    $pdf->Ln(5);
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell(0, 10, "Periodo: $fecha_inicio a $fecha_fin", 0, 1, 'C');
    $pdf->Ln(10);

    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(15, 6, 'Fecha', 1, 0, 'C');
    $pdf->Cell(16, 6, 'Horas Req.', 1, 0, 'C');
    $pdf->Cell(16, 6, 'Hora Ent.', 1, 0, 'C');
    $pdf->Cell(16, 6, 'Hora Sal.', 1, 0, 'C');
    $pdf->Cell(16, 6, 'Horas Trab.', 1, 0, 'C');
    $pdf->Cell(50, 6, 'Descripcion', 1);
    $pdf->Cell(50, 6, 'Estado', 1);
    $pdf->Ln();

    $pdf->SetFont('Arial', '', 7);
    foreach ($presentismo as $fila) {
        $pdf->Cell(15, 6, date('d-m-Y', strtotime($fila['fecha'])), 1);
        $pdf->Cell(16, 6, $fila['horas_requeridad'], 1, 0, 'C');
        $pdf->Cell(16, 6, !empty($fila['hora_entrada']) ? date('H:i', strtotime($fila['hora_entrada'])) : '', 1, 0, 'C');
        $pdf->Cell(16, 6, !empty($fila['hora_salida']) ? date('H:i', strtotime($fila['hora_salida'])) : '', 1, 0, 'C');
        $pdf->Cell(16, 6, $fila['horas_trabajadas'], 1, 0, 'C');
        $pdf->Cell(50, 6, $fila['descripcion'], 1);
        $pdf->Cell(50, 6, $fila['estado'], 1);
        $pdf->Ln();
    }


    //Footer
    // Posición a 1.5 cm del final
    $pdf->SetY(-25);
    // Fuente Arial itálica 8
    $pdf->SetFont('Arial', 'I', 8);
    // Fecha y hora de generación
    $pdf->Cell(0, 0, 'Generado el ' . date('d-m-Y H:i:s'), 0, 0, 'C');

    $filename = "informe_mensual_legajo_$legajo.pdf";
    $pdf->Output('F', $filename);
    return $filename;
}

function enviar_email($email, $filename)
{
    require_once('../mail/tobamail.php');

    $asunto = 'Informe de Horas Trabajadas';
    $cuerpo = 'El informe adjunto contiene un resumen de sus horas trabajadas.<br>';
    $cuerpo .= 'Si tiene Comisiones de Servicio no autorizadas, consulte con su Jefe inmediato superior.<br>';
    $cuerpo .='Por otras consultas comunicarse con asistencia@fca.uncu.edu.ar <br><br>';
    $cuerpo .= 'Saludos cordiales.<br>';   
    $cuerpo .= 'Direccion de personal - Facultad de Ciencias Agrarias';

    $mail = new TobaMail($email, $asunto, $cuerpo, 'formularios_asistencia@fca.uncu.edu.ar', '');
    $mail->agregarAdjunto('nombre_archivo.pdf', $filename);

    try {
        $mail->ejecutar();
        echo "Correo enviado exitosamente a $email.<br>";
        // Eliminar el archivo PDF después de enviar el correo
        ////   if (file_exists($filename)) {
        //  //    unlink($filename);
       // //   }
    } catch (Exception $e) {
        echo "Error al enviar el correo a $email: " . $e->getMessage();
    }
}

function obtener_legajos_agentes()
{

    // Consulta para obtener los legajos y correos de los agentes
    $sql = "SELECT a.legajo,  apellido, nombre, agentes_mail.email as email
	            FROM reloj.agentes a
	            join reloj.agentes_mail on agentes_mail.legajo=a.legajo";
    $agentes = toba::db('comision')->consultar($sql);

    return $agentes;
}

// Ejemplo de uso
$agentes = obtener_legajos_agentes();
/* Un mes para atras*/
$fecha_fin = new DateTime('last day of previous month');
$fecha_inicio = new DateTime('first day of previous month');

/* Fechas personalizadas*/
//$fecha_inicio = new DateTime('2024-11-01');
//$fecha_fin = new DateTime('2025-02-20');

//Formatear fechas
$fecha_fin = $fecha_fin->format('Y-m-d');
$fecha_inicio = $fecha_inicio->format('Y-m-d');

// Filtrar solo algunos legajos específicos para pruebas
$legajos_prueba = [31831]; // Reemplaza estos valores con los legajos que deseas probar

foreach ($agentes as $agente) {
    //if (in_array($agente['legajo'], $legajos_prueba)) {
        $legajo = $agente['legajo'];
        $email = $agente['email'];
        $nombre = trim($agente['nombre']);
        $apellido = $agente['apellido'];
        $datos = obtener_datos_mensuales($legajo, $fecha_inicio, $fecha_fin);
        if (!empty($datos)) {
            $filename = generar_pdf($datos, $legajo, $nombre, $apellido, $fecha_inicio, $fecha_fin);
            enviar_email($email, $filename);
            date_default_timezone_set('America/Argentina/Buenos_Aires');
        } else {
            echo "No se encontraron datos para el legajo $legajo.<br>";
        }
   }
}
