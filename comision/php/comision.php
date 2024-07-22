<?php
class comision extends toba_ci
{
	protected $s__datos;
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
			//$form->set_datos($this->dep('datos')->tabla('parte')->get());
		} // else {
		//    $this->pantalla()->eliminar_evento('eliminar');
		//}
	}

	function evt__formulario__alta($datos)
	{
		//ei_arbol($datos);
		if ($datos['fecha'] <= $datos['fecha_fin']) {
			$fecha = $datos['fecha'];
			//$fecha_fin=date('d/m/Y',strtotime($datos['fecha_fin']));
			$fecha_fin = $datos['fecha_fin'];
			$legajo = $datos['legajo'];
			$superior = $datos['superior'];
			$autoridad = $datos['autoridad'];
			$lugar = $datos['lugar'];
			$catedra = $datos['catedra'];
			$sql = "SELECT nombre_catedra FROM reloj.catedras 
				Where id_catedra =$catedra";
			$a = toba::db('comision')->consultar($sql);
			$datos['catedra'] = $a[0]['nombre_catedra'];
			
			$horario = $datos['horario'];
			$obs = $datos['observaciones'] . ' ';
			$motivo = $datos['motivo'];
			$fuera = $datos['fuera'];
			//$datos['fecha_fin'] = $fecha_fin;
			$sql="SELECT legajo, fecha,fecha_fin from reloj.comision
			where legajo = $legajo
			and fecha between '$fecha' and '$fecha_fin'
			and catedra <> $catedra
			and	(pasada is null or pasada = true)";
			$comision_pedida = toba::db('comision')->consultar($sql);


			if ($fuera == 1) {

				$f = 'true';
			} else {
				$f = 'false';
			}

			$horario_fin = $datos['horario_fin'];
			if (count($comision_pedida)>0){
			//ei_arbol ($datos);
			if (!empty($datos['legajo'])) {
				//$correo_agente = $this->dep('mapuche')->get_legajos_email($datos['legajo']);
				$correo_agente = $this->dep('datos')->tabla('agentes_mail')->get_correo($datos['legajo']);
				$datos['agente'] = $correo_agente[0]['descripcion'];
				//    ei_arbol ($correo_agente);
			}
			if (!empty($datos['superior'])) {
				//	$correo_sup = $this->dep('mapuche')->get_legajos_email($datos['superior']);
				$correo_sup = $this->dep('datos')->tabla('agentes_mail')->get_correo($datos['superior']);
				$datos['superior'] = $correo_sup[0]['descripcion'];
			}
			if (!empty($datos['legajo_autoridad'])) {
				//		$correo_aut = $this->dep('mapuche')->get_legajos_email($datos['autoridad']);
				$correo_aut = $this->dep('datos')->tabla('agentes_mail')->get_correo($datos['autoridad']);
				$datos['autoridad'] = $correo_aut[0]['descripcion'];
			}
			$agente = $this->dep('mapuche')->get_legajo_todos($legajo);
			$datos['descripcion'] = $agente[0]['descripcion'];
			$this->s__datos = $datos;
			//ei_arbol($datos);
			//ei_arbol($correo_sup);
			if (!empty($datos['legajo'])) {
				//$this->enviar_correos($datos['agente']);
				//$this->enviar_correos_sup($datos['superior']);
			}
			//ei_arbol($correo_sup);
			/*if (!empty ($datos['legajo_sup'])){
				
			}
			/*if (!empty ($datos['legajo_aut'])){
			$this->enviar_correos_sup($correo_aut[0]['email']);
			}*/

			$sql = "INSERT INTO reloj.comision
				(legajo, catedra, lugar, motivo, fecha, horario, observaciones, legajo_sup, legajo_aut,  fecha_fin, horario_fin, fuera) VALUES
					($legajo, $catedra, '$lugar', '$motivo','$fecha', '$horario', '$obs', $superior, $autoridad,'$fecha_fin','$horario_fin',$f);";

			toba::db('comision')->ejecutar($sql);
			}
			else 
			{
				toba::notificacion()->agregar('Ud. ya ha solicitado una comision para la fechas consignadas', 'error');
			}
			if ($datos['fuera'] == 1) {
				toba::notificacion()->agregar('Si viaja fuera de la provincia de Mendoza diríjase a la oficina de Personal para tramitar su seguro', 'info');
			}
		} else {
			toba::notificacion()->agregar('Coloqu&ecute una fecha hasta mayor o igual que la fecha desde', 'error');
		}
	}


	function enviar_correos($correo)
	{
		require_once('mail/tobamail.php');

		$datos = $this->s__datos;
		$hacia = $correo;
		$asunto = 'Formulario Comision de Servicio';
		$fecha = date('d/m/Y', strtotime($datos['fecha']));
		$fecha_fin = date('d/m/Y', strtotime($datos['fecha_fin']));

		$cuerpo = '<table>
						El/la agente  <b>' . $datos['descripcion'] . '</b> perteneciente a  <b>' . $datos['catedra'] . '</b>.<br/>
						Solicita <b>Comision de Servicio</b> a realizarse el dia ' . $fecha . ' hasta el dia ' . $fecha_fin . '
						en ' . $datos['lugar'] . ' a partir de la hora ' . $datos['horario'] . ' hasta la hora ' . $datos['horario_fin'] . ' con el siguiente motivo de: ' . $datos['motivo'] . '  observaciones: ' . $datos['observaciones'] . '
											
			</table>';
		
		//Enviamos el correo

		$mail = new TobaMail($hacia, $asunto, $cuerpo, $desde);

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

		$asunto = 'Formulario Comision de Servicio - Agente';
		$fecha = date('d/m/Y', strtotime($datos['fecha']));
		$fecha_fin = date('d/m/Y', strtotime($datos['fecha_fin']));

		$cuerpo = '<table>
						El/la agente  <b>' . $datos['descripcion'] . '</b> perteneciente a la catedra/oficina/ direccion <b>' . $datos['catedra'] . '</b>.<br/>
						Solicita <b>Comision de Servicio</b> con motivo de ' . $datos['motivo'] . ' a realizarse el dia ' . $fecha . ' hasta el dia' . $fecha_fin . '
						en ' . $datos['lugar'] . ' a partir de la hora ' . $datos['horario'] . ' hasta la hora ' . $datos['horario_fin'] . '. Teniendo en cuenta las siguientes Observaciones: ' . $datos['observaciones'] . '</br>
						Para aprobar/rechazar la solicitud ingresar a https://sistemas.fca.uncu.edu.ar/solicitudes, menu autorizaciones -> Comisiones </br>


											
			</table>'; //date("d/m/y",$fecha)

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
}
