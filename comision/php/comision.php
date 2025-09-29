<?php
class comision extends toba_ci
{
	protected $s__datos;
	protected $s__agentes;
	//-----------------------------------------------------------------------------------
	//---- formulario -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__formulario(toba_ei_formulario $form)
	{

		include("usuario_logueado.php");
		$legajo = usuario_logueado::get_legajo(toba::usuario()->get_id());

		$this->$s__agentes = $legajo;
		$datos['legajo'] = $legajo[0]['legajo'];
		$datos['apellido'] = $legajo[0]['apellido'];
		$datos['nombre'] = $legajo[0]['nombre'];
		$form->set_datos($datos);
		if ($this->dep('datos')->esta_cargada()) {
			$form->set_solo_lectura('id_decreto');
			$form->set_solo_lectura('id_motivo');
			$form->set_solo_lectura('id_articulo');
		}
	}

	function evt__formulario__alta($datos)
	{
		
		if ($datos['fecha'] <= $datos['fecha_fin']) {
			// Preparar datos
			
			//$fecha = $datos['fecha'];
			//$fecha_fin = $datos['fecha_fin'];
			$fecha = new DateTime($datos['fecha']);
			$fecha_fin = new DateTime($datos['fecha_fin']);
		//	$fecha_fin->modify('+1 day');
			$dias_seleccionados = !empty($datos['dias']) ? $datos['dias'] : [1, 2, 3, 4, 5,6,7];
			$intervalo = new DateInterval('P1D');
			$fecha_fin_mod = (clone $fecha_fin)->add(new DateInterval('P1D'));
			$periodo = new DatePeriod($fecha, $intervalo, $fecha_fin_mod);
			
			$legajo = $datos['legajo'];
			$superior = $datos['superior'];
			$autoridad = $datos['autoridad'];
			$lugar = $datos['lugar'];
			$catedra = $datos['catedra'];
			$horario = $datos['horario'];
			$horario_fin = $datos['horario_fin'];
			$obs = $datos['observaciones'] . ' ';
			$motivo = $datos['motivo'];
			$fuera = $datos['fuera'];
			
			

			/*if ($datos['fuera'] == 1){
				$fuera = true;
			} else {
				$fuera = false;
				ei_arbol($fuera);
			}*/

			
			switch ($superior){
				case 1 : $superior = 20428;
				break;
				case 3 : $superior = 26629;
				break;
				case 4 : $superior = 26118;
				break;
				case 9 : $superior=29960;
				break;
				case 5 : $superior = 28840;
				break;
				case 6: $superior = 29956;
				break;
				case 7: $superior = 25153;
				break;
				case 8: $superior = 27443;
				break;
				default : $superior;
			}
			 	
			
			$fecha_stri = $fecha_str = $fecha->format('Y-m-d');
			$fecha_fin_stri = $fecha_fin->format('Y-m-d');
			
			// Obtener nombre de la cátedra
			$sql = "SELECT nombre_catedra FROM reloj.catedras WHERE id_catedra = $catedra";
			$a = toba::db('comision')->consultar($sql);
			$datos['catedra'] = $a[0]['nombre_catedra'];
			$comision_pedida=0;
			// Verificar si ya existe una comisión pedida
			$sql = "SELECT legajo, fecha, fecha_fin FROM reloj.comision
					WHERE legajo = $legajo
					AND fecha BETWEEN '$fecha_stri' AND '$fecha_fin_stri'
					and horario = '$horario'
					and horario_fin = '$horario_fin'
					AND catedra = $catedra
					AND (pasada IS NULL OR pasada = true)";
			$comision_pedida = count(toba::db('comision')->consultar($sql));

			$sql = "Select id_parte from reloj.parte
				where legajo = $legajo and fecha_inicio_licencia = '$fecha_stri' and id_motivo = 56";
			$comision_pedida = $comision_pedida +count(toba::db('comision')->consultar($sql));
	
			if ($comision_pedida == 0) {
				// Obtener correos electrónicos
				$correo_agente = !empty($datos['legajo']) ? $this->dep('datos')->tabla('agentes_mail')->get_correo($datos['legajo'])[0]['descripcion'] : null;
				$correo_sup = !empty($datos['superior']) ? $this->dep('datos')->tabla('agentes_mail')->get_correo($datos['superior'])[0]['descripcion'] : null;
		//		$correo_aut = !empty($datos['legajo_autoridad']) ? $this->dep('datos')->tabla('agentes_mail')->get_correo($datos['autoridad'])[0]['descripcion'] : null;
	
				// Obtener descripción del agente
				$agente = $this->dep('mapuche')->get_legajo_todos($legajo);
				$datos['descripcion'] = $agente[0]['descripcion'];
				$this->s__datos = $datos;
				
				
				
				// Insertar nueva comisión
				foreach ($periodo as $fecha_actual) {
    				$dia_semana = (int)$fecha_actual->format('N'); // 1 (lunes) a 7 (domingo)
    				if (in_array($dia_semana, $dias_seleccionados)) {
        				$fecha_str = $fecha_actual->format('Y-m-d');
						$resultado = false;
				
            		$sql = "INSERT INTO reloj.comision
        			    	(legajo, catedra, lugar, motivo, fecha, horario, observaciones, legajo_sup, legajo_aut, fecha_fin, horario_fin, fuera)
            				VALUES
            				($legajo, $catedra, '$lugar', '$motivo', '$fecha_str', '$horario', '$obs', $superior, $autoridad, '$fecha_str', '$horario_fin', $fuera)";
		
       					$resultado = toba::db('comision')->ejecutar($sql);
    				}
				}	
				if ($resultado) {
					// Enviar correos electrónicos
						if ($correo_agente) {
							$this->enviar_correos($correo_agente);
						}
						if ($correo_sup) {
							$this->enviar_correos_sup($correo_sup);
						}
							toba::notificacion()->agregar('Su solicitud ha sido ingresada.', 'info');
						if ($fuera) {
							toba::notificacion()->agregar('Si viaja fuera de la provincia de Mendoza diríjase a la oficina de Personal para tramitar su seguro', 'info');
						}
					} else {
						toba::notificacion()->agregar('Error al insertar la comisión en la base de datos', 'error');
					}
				
			} else {
				toba::notificacion()->agregar('Ud. ya ha solicitado una comisión para las fechas y horas consignadas', 'error');
			}
		 } else {
			toba::notificacion()->agregar('Coloque una fecha hasta mayor o igual que la fecha desde', 'error');
		}
	
	}

	function enviar_correos($correo)
	{
		require_once('mail/tobamail.php');

		$datos = $this->s__datos;

		$hacia = $correo;
		$asunto = 'Formulario Comision de Servicio';
		//$fecha = date('d/m/Y', strtotime($datos['fecha']));
		//$fecha_fin = date('d/m/Y', strtotime($datos['fecha_fin']));
		$fecha_inicio_raw = $datos['fecha'];
		$fecha_fin_raw = $datos['fecha_fin'];
		$fecha = date('d/m/Y', strtotime($fecha_inicio_raw));
		$fecha_fin = date('d/m/Y', strtotime($fecha_fin_raw));
		$texto_dias = '';
		$es_mismo_dia = ($fecha_inicio_raw === $fecha_fin_raw);
		$fecha_inicio = new DateTime($datos['fecha']);
		$fecha_fin = new DateTime($datos['fecha_fin']);

		$fecha = $fecha_inicio->format('d/m/Y');
		$fecha_fin_str = $fecha_fin->format('d/m/Y');
		if (!$es_mismo_dia) {
	    	if (!empty($datos['dias']) && is_array($datos['dias'])) {
    	    	$dias_texto = $this->obtener_nombres_dias($datos['dias']);
        		$texto_dias = ' en los días seleccionados: <b>' . $dias_texto . '</b>';
    		} else {
        		$texto_dias = ' en días hábiles (lunes a viernes)';
    		}
		}
		
		
		$cuerpo = '<table>
    				El/la agente <b>' . $datos['descripcion'] . '</b> perteneciente a <b>' . $datos['catedra'] . '</b>.<br/>
    				Solicita <b>Comisión de Servicio</b> a realizarse ';

					if ($es_mismo_dia) {
    					$cuerpo .= 'el día <b>' . $fecha . '</b>';
					} else {
    				$cuerpo .= 'desde el día <b>' . $fecha . '</b> hasta el día <b>' . $fecha_fin_str . '</b>' . $texto_dias;
					}

					$cuerpo .= ',<br/>en <b>' . $datos['lugar'] . '</b> a partir de la hora <b>' . $datos['horario'] . '</b> hasta la hora <b>' . $datos['horario_fin'] . '</b>,<br/>
					con el siguiente motivo: <b>' . $datos['motivo'] . '</b>.<br/>Observaciones: ' . $datos['observaciones'] . '
				</table>';

		//Enviamos el correo

		$mail = new TobaMail($hacia, $asunto, $cuerpo, $desde, ['asistencia@fca.uncu.edu.ar']);

		// Agregar un archivo adjunto
		//$mail->agregarAdjunto('nombre_archivo.pdf', '/ruta/al/archivo/nombre_archivo.pdf');

		try {
			$mail->ejecutar();
			echo "Correo enviado exitosamente.<br>";
		} catch (Exception $e) {
			echo "Error al enviar el correo: " . $e->getMessage();
		}
	}



	function enviar_correos_sup($correo)
	{
		require_once('mail/tobamail.php');

		$datos = $this->s__datos;

		$asunto = 'Formulario Comisión de Servicio - Agente';
		//$fecha = date('d/m/Y', strtotime($datos['fecha']));
		//$fecha_fin = date('d/m/Y', strtotime($datos['fecha_fin']));
		$fecha_inicio_raw = $datos['fecha'];
		$fecha_fin_raw = $datos['fecha_fin'];
		$fecha = date('d/m/Y', strtotime($fecha_inicio_raw));
		$fecha_fin = date('d/m/Y', strtotime($fecha_fin_raw));
		$texto_dias = '';
		$es_mismo_dia = ($fecha_inicio_raw === $fecha_fin_raw);
		$fecha_inicio = new DateTime($datos['fecha']);
		$fecha_fin = new DateTime($datos['fecha_fin']);

		$fecha = $fecha_inicio->format('d/m/Y');
		$fecha_fin_str = $fecha_fin->format('d/m/Y');
		if (!$es_mismo_dia) {
	    	if (!empty($datos['dias']) && is_array($datos['dias'])) {
    	    	$dias_texto = $this->obtener_nombres_dias($datos['dias']);
        		$texto_dias = ' en los días seleccionados: <b>' . $dias_texto . '</b>';
    		} else {
        		$texto_dias = ' en días hábiles (lunes a viernes)';
    		}
		}
		$cuerpo = '<table>
    		El/la agente <b>' . $datos['descripcion'] . '</b> perteneciente a la catedra/oficina/dirección <b>' . $datos['catedra'] . '</b>.<br/>
    		Solicita <b>Comisión de Servicio</b> con motivo de <b>' . $datos['motivo'] . '</b> a realizarse ';

		if ($es_mismo_dia) {
			$cuerpo .= 'el día <b>' . $fecha . '</b>';
		} else {
			$cuerpo .= 'desde el día <b>' . $fecha . '</b> hasta el día <b>' . $fecha_fin_str . '</b>' . $texto_dias;
		}

		$cuerpo .= ',<br/>en <b>' . $datos['lugar'] . '</b> a partir de la hora <b>' . $datos['horario'] . '</b> hasta la hora <b>' . $datos['horario_fin'] . '</b>.<br/>
    			Teniendo en cuenta las siguientes Observaciones: ' . $datos['observaciones'] . '<br/>
    			Para aprobar/rechazar la solicitud ingresar a <a href="https://sistemas.fca.uncu.edu.ar/solicitudes" target="_blank">https://sistemas.fca.uncu.edu.ar/solicitudes</a>, menú autorizaciones -> Comisiones.<br/>
			</table>';

		//Enviamos el correo
		$mail = new TobaMail($correo, $asunto, $cuerpo, $desde, '');

		// Agregar un archivo adjunto
		//$mail->agregarAdjunto('nombre_archivo.pdf', '/ruta/al/archivo/nombre_archivo.pdf');

		try {
			$mail->ejecutar();
			echo "Correo enviado exitosamente.<br>";
		} catch (Exception $e) {
			echo "Error al enviar el correo: " . $e->getMessage();
		}
	}
	function obtener_nombres_dias($dias_numeros) {
    $nombres = [
        1 => 'lunes',
        2 => 'martes',
        3 => 'miércoles',
        4 => 'jueves',
        5 => 'viernes',
        6 => 'sábado',
        7 => 'domingo'
    ];
    // Ordenar de lunes a domingo
    sort($dias_numeros);
    $nombres_dias = [];
    foreach ($dias_numeros as $d) {
        if (isset($nombres[$d])) {
            $nombres_dias[] = $nombres[$d];
        }
    }
    return implode(', ', $nombres_dias);
}
}
