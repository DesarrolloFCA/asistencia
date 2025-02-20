<?php

require 'vendor/autoload.php'; // Asegúrate de tener FPDF y PHPMailer instalados

use setasign\Fpdi\Fpdi;
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
    $sql= "SELECT Distinct  fecha,hora_entrada,hora_salida,horas_trabajadas,horas_requeridad,descripcion,estado 
    from reloj.vm_detalle_pres
    where legajo = $legajo
    and fecha >= CURRENT_DATE - INTERVAL '30 days'";
    
    $presentismo = toba::db('comision')->consultar($sql);
    $sql1 = "SELECT 
        id_parte,
        estado,
        fecha_inicio_licencia,
        dias
        
    FROM
        sanidad.parte as t_p    
        
    where t_p.legajo = $legajo
    and fecha_inicio_licencia >= CURRENT_DATE - INTERVAL '30 days'
    and estado = 'C'

    ";
    
    $sanidad = toba::db('mapuche')->consultar($sql1);
    $sani = []; // Inicializar el arreglo $sani
    $j= count($sanidad);
    if ($j>0){
        for ($i=0;$i<$j;$i++){
            
            if ($sanidad[$i]['dias'] > 1) {
                $k=$sanidad[$i]['dias'];
                for ($h=0;$h<$k;$h++){
                    $fecha = new DateTime($sanidad[$i]['fecha_inicio_licencia']);
                    $fecha->modify('+'.$h.' day');
                    $sani[]=$fecha->format('Y-m-d');
                    
                }
                
            } else {
                $sani[] = $sanidad[$i]['fecha_inicio_licencia'];
            }
        }
    }
        
    $j = count($presentismo);
    $sql = "SELECT   CONCAT(FLOOR(EXTRACT(EPOCH FROM (b.h2 - b.h1)) / 3600),':',LPAD(EXTRACT(MINUTE FROM (b.h2 - b.h1))::TEXT, 2, '0') )
     AS horas_corregidas from reloj.agentes a
            left join reloj.conf_jornada b on a.legajo = b.legajo
            where EXTRACT(EPOCH FROM (b.h2 - b.h1)) / 3600 < 6
            AND a.legajo = $legajo
            and (fecha_fin >= CURRENT_DATE - INTERVAL '30 days' or fecha_fin is null);";	
    $jornada = 	toba::db('comision')->consultar($sql);
    if (count($jornada)>0){
        for($i=0;$i<$j;$i++){
            $presentismo[$i]['horas_requeridad']=$jornada[0]['horas_corregidas'];
        }
    }	
    
    for ($i=0;$i<$j;$i++)	{
        if(in_array($presentismo[$i]['fecha'],$sani )){
            $presentismo[$i]['estado'] = 'Ausente Justificado';
            $presentismo [$i]['descripcion'] = 'Parte Sanidad';

        }
        
    }
    return $presentismo;
}

function generar_pdf($presentismo, $legajo, $nombre, $apellido, $fecha_inicio, $fecha_fin) {
    $pdf = new \FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 10, "Informe de Horas Trabajadas - Legajo: $legajo - $nombre $apellido", 0, 1, 'C');
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
        $pdf->Cell(16, 6, $fila['horas_requeridad'], 1, 0,'C');
        $pdf->Cell(16, 6, !empty($fila['hora_entrada']) ? date('H:i', strtotime($fila['hora_entrada'])) : '', 1, 0,'C');
        $pdf->Cell(16, 6, !empty($fila['hora_salida']) ? date('H:i', strtotime($fila['hora_salida'])) : '', 1, 0,'C');
        $pdf->Cell(16, 6, $fila['horas_trabajadas'], 1, 0, 'C');
        $pdf->Cell(50, 6, $fila['descripcion'], 1);
        $pdf->Cell(50, 6, $fila['estado'], 1);
        $pdf->Ln();
    }

    $filename = "informe_mensual_legajo_$legajo.pdf";
    $pdf->Output('F', $filename);
    return $filename;
}

function enviar_email($email, $filename) {
    require_once('../mail/tobamail.php');
    
    // Contenido del correo
    //$mail->isHTML(true);
    $asunto = 'Informe de Horas Trabajadas';
    $cuerpo = 'ver adjunto';

    // Adjuntar el PDF
  

    // Enviamos el correo
    $mail = new TobaMail($email, $asunto, $cuerpo, 'lfontes@fca.uncu.edu.ar', '');
    //$mail->addAttachment('nombre_archivo.pdf', $filename);
    // Agregar un archivo adjunto
    $mail->agregarAdjunto('nombre_archivo.pdf', $filename);

    try {
        $mail->ejecutar();
        //echo "Correo enviado exitosamente a $correo.<br>";
    } catch (Exception $e) {
        echo "Error al enviar el correo a $correo: " . $e->getMessage();
    }
}

function obtener_legajos_agentes() {
    // Conexión a la base de datos
    $host = 'pg';
    $dbname = 'reloj';
    $user = 'postgres';
    $password = 'postgres';

    $dsn = "pgsql:host=$host;dbname=$dbname";
    try {
        $pdo = new PDO($dsn, $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo 'Connection failed: ' . $e->getMessage();
        return [];
    }

    // Consulta para obtener los legajos y correos de los agentes
    $sql = "SELECT legajo, email, nombre, apellido FROM reloj.agentes";
    $agentes = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

    return $agentes;
}

// Ejemplo de uso
$agentes = obtener_legajos_agentes();
$fecha_fin = date('Y-m-d');
$fecha_inicio = date('Y-m-d', strtotime('-30 days'));

// Filtrar solo algunos legajos específicos para pruebas
$legajos_prueba = [26010, 31096, 28168]; // Reemplaza estos valores con los legajos que deseas probar

foreach ($agentes as $agente) {
    if (in_array($agente['legajo'], $legajos_prueba)) {
        $legajo = $agente['legajo'];
        $email = $agente['email'];
        $nombre = trim($agente['nombre']);
        $apellido = $agente['apellido'];
        $datos = obtener_datos_mensuales($legajo);
        if (!empty($datos)) {
            $filename = generar_pdf($datos, $legajo, $nombre, $apellido, $fecha_inicio, $fecha_fin);
            enviar_email($email, $filename);
        } else {
            echo "No se encontraron datos para el legajo $legajo.<br>";
        }
    }
}