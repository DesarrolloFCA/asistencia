<?php
//clase para autorizar comisiones
class ci_comisiones extends comision_ci
{

	//-----------------------------------------------------------------------------------
	//---- formulario -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__formulario(comision_ei_formulario_ml $form_ml)
	{
		include("usuario_logueado.php");
		$legajo = usuario_logueado::get_legajo(toba::usuario()->get_id());
		$legajo = $legajo[0]['legajo'];
		if (usuario_logueado::get_jefe($legajo)) {
			$sql = "SELECT id_comision, legajo, comision.catedra, catedras.nombre_catedra, lugar, motivo, fecha, horario, observaciones, legajo_sup, legajo_aut, autoriza_sup, autoriza_aut, fecha_fin, horario_fin, fuera, pasada
				FROM reloj.comision
					join reloj.catedras ON comision.catedra = catedras.id_catedra
				WHERE (pasada is null or pasada = false)
				and catedra :: int in ((Select id_catedra from reloj.catedras_agentes a
										where legajo = $legajo and jefe = true)) 
				or id_departamento = ((Select id_departamento from reloj.departamento_director
				where legajo_dir = $legajo))						
				and legajo <> $legajo
		Order by catedra, fecha, legajo ";
			$datos = toba::db('comision')->consultar($sql);
			$tot = count($datos);

			for ($i = 0; $i < $tot; $i++) {
				$leg = usuario_logueado::get_agentes($datos[$i]['legajo']);
				$datos[$i]['ayn'] = $leg[0]['descripcion'];
				$datos[$i]['catedra'] = (int) $datos[$i]['catedra'];
			}

			$form_ml->set_datos($datos);
		} else {
			toba::notificacion()->agregar('Ud. no tiene personal a cargo', "info");
		}
	}

	function evt__formulario__modificacion($datos)
	{
		$cant = count($datos);
		$fecha_cierre = date("Y-m-d H:i:s");
		//ei_arbol($datos); 

		for ($i = 0; $i < $cant; $i++) {
			if ($datos[$i]['apex_ei_analisis_fila'] == 'M') {
				/*$sql = "UPDATE reloj.comision
					Set autoriza_sup = $a_sup, autoriza_aut = $a_aut, observaciones = '$obs'
					where id_comision = $id";
			toba::db('ctrl_asis')->ejecutar ($sql);    */

				$id = $datos[$i]['id_comision'];
				$legajo = $datos[$i]['legajo'];
				//	ei_arbol ($datos[$i]['pasada']);
				if ($datos[$i]['pasada']  == 1) {
					$estado = 'C';
				} else {
					$estado = 'A';
				}
				//ei_arbol ($estado, $i);
				$a_sup = $datos[$i]['autoriza_sup'];
				$a_aut = $datos[$i]['autoriza_aut'];
				$obs = $datos[$i]['observaciones'];
				$ayn = $this->dep('mapuche')->get_legajos_autoridad($datos[$i]['legajo']);
				$apellido = $ayn[0]['apellido'];
				$nombre = $ayn[0]['nombre'];
				$fecha_inicio = $datos[$i]['fecha'];
				$fecha_fin = $datos[$i]['fecha_fin'];
				$hora_inicio = $datos[$i]['horario'];
				$hora_fin = $datos[$i]['horario_fin'];
				$lugar = $datos[$i]['lugar'];
				$motivo = $datos[$i]['motivo'];
				$autoriza_sup = $datos[$i]['autoriza_sup'];
				$autoriza_aut = $datos[$i]['autoriza_aut'];
				$datos_correo['legajo'] = $legajo;
				$datos_correo['apellido'] = $apellido;
				$datos_correo['nombre'] = $nombre;
				$datos_correo['fecha_inicio'] = $fecha_inicio;
				$datos_correo['fecha_fin'] = $fecha_fin;
				$datos_correo['hora_inicio'] = $hora_inicio;
				$datos_correo['horario_fin'] = $hora_fin;
				$datos_correo['lugar'] = $lugar;
				$datos_correo['motivo'] = $motivo;

				$this->s__datos_correo = $datos_correo;
				$sql = "SELECT email from reloj.agentes_mail
				where legajo=$legajo";
				$correo = toba::db('comision')->consultar($sql);
				//	ei_arbol($datos[$i]['pasada'] );


				//	ei_arbol($estado);

				if ($estado == 'C' && ($autoriza_sup == 1)) {

					if ($autoriza_sup == 1) {
						$superior_aut = true;
					} else {
						$superior_aut = false;
					}
					$edad = $this->dep('mapuche')->get_edad($legajo, null);
					$direccion = $this->dep('mapuche')->get_datos_agente($filtro);
					$domicilio = $direccion[0]['calle'] || ' ' || $direccion[0]['numero'];
					$localidad = $direccion[0]['localidad'];
					$agrupamiento = $direccion[0]['escalafon'];
					$fecha_nacimiento = $direccion[0]['fecha_nacimiento'];
					$usuario_alta = toba::usuario()->get_id();
					$fecha_alta    = date("Y-m-d H:i:s");
					$fechaentera1 = strtotime($fecha_inicio);
					//$january = new DateTime($datos[$i]['fecha_fin']);
					//$february = new DateTime($datos[$i]['fecha_fin']);
					$fecha_inicio1 = date_create(date("Y-m-d", $fechaentera1));
					$hoy = date_create(date("Y-m-d", strtotime($fecha_fin)));
					//$dia = $february->diff($january);
					$dia = date_diff($fecha_inicio1, $hoy);
					$dias = $dia->format('%a') + 1;
					//ei_arbol($dias);
					$fecha_ini = $datos[$i]['fecha'];
					//	ei_arbol($fecha_ini);
					$estado_civil = $direccion[0]['estado_civil'];
					if ($agrupamiento == 'DOCE') {
						$id_motivo = 56;
						$id_decreto = 2;
						$id_articulo = 104;
					} else {
						$id_decreto = 5;
						$id_articulo = 104;
						$id_motivo = 56;
					}
					$sexo = $this->dep('mapuche')->get_tipo_sexo($legajo, null);
					$sql= "UPDATE reloj.comision
						SET observaciones = '$obs', pasada = true ,autoriza_sup = true , autoriza_aut = true
						WHERE id_comision = $id";
						toba::db('ctrl_asis')->ejecutar($sql);	

					$sql = "INSERT INTO reloj.parte(
							legajo, edad, fecha_alta, usuario_alta, estado, fecha_inicio_licencia, dias, cod_depcia, domicilio, localidad, agrupamiento, fecha_nacimiento,
							apellido, nombre, estado_civil, observaciones, id_decreto, id_motivo, id_articulo, tipo_sexo,usuario_cierre,fecha_cierre)
							VALUES ($legajo, $edad, '$fecha_alta', '$usuario_alta', '$estado', '$fecha_ini', $dias, '04', '$domicilio', '$localidad', '$agrupamiento', '$fecha_nacimiento',
							'$apellido', '$nombre',    '$estado_civil', '$observaciones', $id_decreto,  $id_motivo,	  $id_articulo,'$tipo_sexo','$usuario_cierre','$fecha_cierre');";
					toba::db('comision')->ejecutar($sql);


					$this->enviar_correos($correo[0]['email'], true);
				} else  if ($estado == 'C' && $autoriza_sup == 0) {

					$this->enviar_correos($correo[0]['email'], false);
				}
				if ($estado == 'C') {

					$sql = "UPDATE reloj.comision
								SET observaciones = '$obs', pasada = true 
								WHERE id_comision = $id";
					toba::db('comision')->ejecutar($sql);
				}
			}
		}
	}


	//---------------------------------------------------------
	function enviar_correos($correo, $aprobado)
	{
		require_once('mail/tobamail.php');
		$datos = $this->s__datos_correo;
		//$formula = $this->s__formula;    
		$fecha = date('d/m/Y', strtotime($datos['fecha_inicio']));

		$hasta = date('d/m/Y', strtotime($datos['fecha_fin']));
		$datos['agente_ayn'] = $datos['apellido'] . ', ' . $datos['nombre'];

		//Definimos el tema del email
		$asunto = 'Solicitud de Comision de Servicio';
		
		if ($aprobado) {

			$asunto = 'Solicitud de Comision de Servicio';
			$cuerpo = '<table>
						Al agente  <b>' . $datos['agente_ayn'] . '</b> se aprueba la Solicitud de Comision de Servicio </b> <br/>
						con motivo de' . $datos['motivo'] . ' en ' . $datos['lugar'] . ' iniciando el dia ' . $datos['fecha_inicio'] . ' en el horario ' . $datos['hora_inicio'] . ' y finalizando el dia' . $datos['fecha_fin'] . ' en el horario ' . $datos['hora_fin'] . ' <br/> 
						Saluda atte Direccion de Personal.
											
				</table>';
		} else {
			//Esta línea es por si queréis enviar copia a alguien (dirección y, opcionalmente, nombre)
			$ccopia('personal@fca.uncu.edu.ar', 'Comision rechazada');
			$asunto = 'Solicitud de Comision de Servicio RECHAZADA';
			$cuerpo = '<table>
						Al agente  <b>' . $datos['agente_ayn'] . '</b> le ha sido rechazada  la Solicitud de Comision de Servicio </b>.<br/>
						con motivo de' . $datos['motivo'] . ' en ' . $datos['lugar'] . ' iniciando el dia ' . $datos['fecha_inicio'] . ' en el horario ' . $datos['hora_inicio'] . ' y finalizando el dia' . $datos['fecha_fin'] . ' en el horario ' . $datos['hora_fin'] . ' <br/> 
						Saluda atte Direccion de Personal.
											
				</table>';
		}; //date("d/m/y",$fecha)

		//Enviamos el correo
		$mail = new TobaMail($correo, $asunto, $cuerpo, $desde, $ccopia);

		// Agregar un archivo adjunto
		//$mail->agregarAdjunto('nombre_archivo.pdf', '/ruta/al/archivo/nombre_archivo.pdf');

		try {
			$mail->ejecutar();
			echo "Correo enviado exitosamente.<br>";
		} catch (Exception $e) {
			echo "Error al enviar el correo: " . $e->getMessage();
		}
	}

	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__procesar()
	{
		$this->dep('datos')->sincronizar();
		$this->dep('datos')->resetear();
		$this->dep('datos')->cargar();
	}

	function evt__cancelar()
	{
	}
}
