<?php
class ci_permiso_horario extends comision_ci
{
	protected $s__datos;
	//-----------------------------------------------------------------------------------
	//---- formulario -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__formulario__alta($datos)
	{


		$anio = date('Y');
		$legajo = $datos['legajo'];
		$escalafon = $this->dep('mapuche')->get_legajo_escalafon($legajo);
		$agente = $this->dep('mapuche')->get_legajo_todos($legajo);
		$datos['descripcion'] = $agente[0]['descripcion'];
		$fecha_ini = $datos['fecha'];

		$j = count($escalafon);
		$es_nodo = 0;
		for ($i = 0; $i <= $j; $i++) {
			if ($escalafon[$i]['escalafon'] == 'NODO') {
				$es_nodo = 1;
			}
		}


		if ($es_nodo == 1) {

			//ei_arbol($datos);

			$sql = "SELECT count(*) cantidad 
				FROM reloj.permisos_horarios
				WHERE legajo = $legajo
				and extract(year FROM fecha) = $anio ;";
			$a = toba::db('comision')->consultar($sql);
			$cantidad = $a[0]['cantidad'];

			if ($cantidad < 5) {
				$sql = "SELECT * from reloj.permisos_horarios
				where legajo = $legajo
				and fecha = '$fecha_ini'";
				$pedido = count(toba::db('comision')->consultar($sql));
				if ($pedido > 0) {
					toba::notificacion()->agregar('Ud. ya ha solicitado un permiso horario para las fechas consignadas', 'error');
				} else {

					$this->dep('datos')->tabla('permiso_horarios')->nueva_fila($datos);
					$this->dep('datos')->sincronizar();
					$this->dep('datos')->resetear();
					$catedra = $datos['id_catedra'];
					$sql = "SELECT nombre_catedra FROM reloj.catedras 
					Where id_catedra =$catedra";
					$a = toba::db('comision')->consultar($sql);
					$datos['n_catedra'] = $a[0]['nombre_catedra'];
					if (!empty($datos['legajo'])) {
						//$correo_agente = $this->dep('mapuche')->get_legajos_email($datos['legajo']);
						$correo_agente = $this->dep('datos')->tabla('agentes_mail')->get_correo($datos['legajo']);
						$datos['agente'] = $correo_agente[0]['descripcion'];
					}
					if (!empty($datos['leg_sup'])) {
						//$correo_sup = $this->dep('mapuche')->get_legajos_email($datos['leg_sup']);
						$correo_sup = $this->dep('datos')->tabla('agentes_mail')->get_correo($datos['leg_sup']);
						$datos['superior'] = $correo_sup[0]['descripcion'];
					}
					//ei_arbol ($datos);
					$agente = $this->dep('mapuche')->get_legajo_todos($legajo);
					$datos['descripcion'] = $agente[0]['descripcion'];
					$this->s__datos = $datos;
					if (!empty($datos['legajo'])) {
						$this->enviar_correos($datos['agente']);
						toba::notificacion()->agregar('Su pedido de Permiso Horario sera tramitado a la brevedad', 'info');
					}
				}
			} else {
				toba::notificacion()->agregar('Ud. ha excedido la cantidad de permisos excepcionales que se otorgan por a&ntilde;o', 'info');
			}
			if (!empty($datos['leg_sup'])) {
				$this->enviar_correos($datos['superior']);
			}
		} else {
			toba::notificacion()->agregar('Esta licencia es aplicable solamente a Personal de Apoyo Acad&eacute;mico', 'info');
		}
	}

	function evt__formulario__cancelar() {}
	function enviar_correos($correo)
	{
		require_once('mail/tobamail.php');

		$datos = $this->s__datos;
		$asunto = 'Formulario Permiso Horario';

		//Formateamos el cuerpo del mensaje
		$fecha = date('d/m/Y', strtotime($datos['fecha']));
		$fecha_fin = date('d/m/Y', strtotime($datos['fecha_fin']));
		//$mail->setHtml(true); // Si el cuerpo es HTML
		$cuerpo = '<table>
						El/la agente  <b>' . $datos['descripcion'] . '</b> perteneciente a la <b>' . $datos['n_catedra'] . '</b>.<br/>
						Solicita <b>permiso horario</b>  para el d&iacute;a ' . $fecha . ' a partir de la hora ' . $datos['horario_incio'] . ' hasta la hora ' . $datos['horario_fin'] . '<br/> 
						Motivo de la solicitud: ' . $datos['razon'] . '<br/>
						Observaciones: ' . $datos['observaciones'] . ' -
											
				</table>';


		$mail = new TobaMail($correo, $asunto, $cuerpo, $desde, ['asistencia@fca.uncu.edu.ar']);

		// Agregar un archivo adjunto
		//$mail->agregarAdjunto('nombre_archivo.pdf', '/ruta/al/archivo/nombre_archivo.pdf');

		try {
			$mail->ejecutar();
			echo "Correo enviado exitosamente.<br>";
		} catch (Exception $e) {
			echo "Error al enviar el correo: " . $e->getMessage();
		}
	}


	function conf__formulario(comision_ei_formulario $form)
	{
		include("usuario_logueado.php");
		$legajo = usuario_logueado::get_legajo(toba::usuario()->get_id());

		$this->$s__agentes = $legajo;
		$datos['legajo'] = $legajo[0]['legajo'];
		$datos['apellido'] = $legajo[0]['apellido'];
		$datos['nombre'] = $legajo[0]['nombre'];
		$form->set_datos($datos);
	}
}
