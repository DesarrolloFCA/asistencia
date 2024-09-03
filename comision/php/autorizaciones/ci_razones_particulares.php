<?php

//clase para autorizar inasistencias por razones particulares
class ci_razones_particulares extends comision_ci
{
	protected $s__datos;
	protected $s__agentes;
	protected $s__datos_correo;
	function ini__operacion()
	{
		//$this->dep('datos')->cargar();
	}

	function evt__guardar()
	{
		$this->dep('datos')->sincronizar();
		$this->dep('datos')->resetear();
		$this->dep('datos')->cargar();
	}

	function evt__formulario__modificacion($datos)
	{
		$formula = $this->s__datos;
		$cant = count($datos);
		$fecha_cierre = date("Y-m-d H:i:s");
		
		for ($i = 0; $i < $cant; $i++) {
			$legajo = $datos[$i]['legajo'];
			$id = $datos[$i]['id_inasistencia'];
			$obs = $datos[$i]['obs'];
			$datos[$i]['fecha_inicio'] = $this->s__datos[$i]['fecha_inicio'];
			if ($datos[$i]['auto_sup'] == 1) {
				$aut_sup = 'true';
			} else {
				$aut_sup = 'false';
			}

			$correo = $this->dep('mapuche')->get_correos($legajo);
			//ei_arbol($datos[$i]['apex_ei_analisis_fila']);
			if ($datos[$i]['apex_ei_analisis_fila'] == 'M') {
				if ($datos[$i]['estado'] == 'A') {
					// code...
					$sql = "UPDATE reloj.inasistencias
					Set auto_sup = $aut_sup, observaciones = '$obs'
					where id_inasistencia = $id";
					//    ei_arbol($sql);
					toba::db('comision')->ejecutar($sql);
				} else {
					if ($datos[$i]['auto_sup'] == 1) {
						//ei_arbol($legajo);
						$filtro['legajo'] = $legajo;

						$edad = $this->dep('mapuche')->get_edad($legajo, null);
						$direccion = $this->dep('mapuche')->get_datos_agente($filtro);
						$nombre = $direccion[0]['nombre'];
						$apellido = $direccion[0]['apellido'];
						$datos[$i]['nombre'] = $nombre;
						$datos[$i]['apellido'] = $apellido;
						$domicilio = $direccion[0]['calle'] || ' ' || $direccion[0]['numero'];
						$localidad = $direccion[0]['localidad'];
						$agrupamiento = $direccion[0]['escalafon'];
						$fecha_nacimiento = $direccion[0]['fecha_nacimiento'];
						$fecha_alta = $formula[$i]['fecha_alta'];
						$usuario_alta = $formula[$i]['usuario_alta'];
						$estado = $datos[$i]['estado'];
						$fecha_ini = $datos[$i]['fecha_inicio'];
						$fecha_fin = $datos[$i]['fecha_fin'];
						$fechaentera1 = strtotime($fecha_ini);
						//$january = new DateTime($datos[$i]['fecha_fin']);
						//$february = new DateTime($datos[$i]['fecha_fin']);
						$fecha_inicio = date_create(date("Y-m-d", $fechaentera1));
						$hoy = date_create(date("Y-m-d", strtotime($fecha_fin)));
						//$dia = $february->diff($january);
						$dia = date_diff($fecha_inicio, $hoy);
						$dias = $dia->format('%a') + 1;
						//ei_arbol($dias);
						$datos[$i]['dias'] = $dias;
						$estado_civil = $direccion[0]['estado_civil'];
						$id_decreto = $formula[$i]['id_decreto'];
						$id_motivo = $datos[$i]['id_motivo'];
						$id_articulo = $formula[$i]['id_articulo'];
						$id_catedra = $datos[$i]['id_catedra'];
						$sexo = $this->dep('mapuche')->get_tipo_sexo($legajo, null);
						$datos_correo['legajo'] = $legajo;
						$datos_correo['apellido'] = $apellido;
						$datos_correo['nombre'] = $nombre;
						$datos_correo['fecha_inicio'] = $fecha_ini;
						$datos_correo['fecha_fin'] = $fecha_fin;
						$datos_correo['dias'] = $dias;
						$sql = "SELECT descripcion from reloj.motivo
						WHERE id_motivo = $id_motivo";
						$mot = toba::db('comision')->consultar_fila($sql);
						$sql = "SELECT nombre_catedra from reloj.catedras
						WHERE id_catedra = $id_catedra";
						$nom_cat = toba::db('comision')->consultar_fila($sql);
						$datos_correo['motivo'] = $mot['descripcion'];
						$datos_correo['catedra'] = $nom_cat['nombre_catedra'];
						$datos_correo['observaciones'] = $obs ;
						$this->s__datos_correo = $datos_correo;
						$sql = "INSERT INTO reloj.parte(
						legajo, edad, fecha_alta, usuario_alta, estado, fecha_inicio_licencia, dias, cod_depcia, domicilio, localidad, agrupamiento, fecha_nacimiento,
						apellido, nombre, estado_civil, observaciones, id_decreto, id_motivo,  tipo_sexo,usuario_cierre,fecha_cierre)
						VALUES ($legajo, $edad, '$fecha_alta', $usuario_alta, '$estado', '$fecha_ini', $dias, '04', '$domicilio', '$localidad', '$agrupamiento', 
						'$fecha_nacimiento','$apellido', '$nombre',    '$estado_civil', '$obs', $id_decreto, $id_motivo,'$tipo_sexo','$usuario_cierre','$fecha_cierre');";
						toba::db('comision')->ejecutar($sql);
						$sql = "SELECT max(id_parte) ultimo FROM reloj.parte";
						$a = toba::db('comision')->consultar($sql);
						$ultimo = $a[0]['ultimo'];
						$ql = "INSERT INTO reloj.certificados(
							id_inasistencia, id_parte) VALUES ($id, $ultimo)";
						toba::db('comision')->ejecutar($ql);
						$this->enviar_correos($correo['email'], $datos[$i]['auto_sup']);
						$sql = "DELETE from reloj.inasistencias
						WHERE id_inasistencia =$id";
						toba::db('comision')->ejecutar($sql);
					} else {
						$this->enviar_correos($correo['email'], $datos[$i]['auto_sup']);
						$sql = "UPDATE reloj.inasistencias
					SET estado='C', observaciones = '$obs' 
					WHERE id_inasistencia = $id";
						toba::db('comision')->ejecutar($sql);
					}
				}
			}
		}
		//$this->dep('datos')->procesar_filas($datos);
	}

	function conf__formulario(toba_ei_formulario_ml $componente)
	{
		include("usuario_logueado.php");
		$legajo = usuario_logueado::get_legajo(toba::usuario()->get_id());
		
		$legajo = $legajo[0]['legajo'];
		if (usuario_logueado::get_jefe($legajo)) {
			$sql = "SELECT * FROM reloj.inasistencias
			WHERE  estado = 'A'
			AND id_motivo not in (57,35)
			and leg_sup  = $legajo							
			and legajo <> $legajo
			Order by id_catedra, fecha_inicio,  legajo";
			$datos = toba::db('comision')->consultar($sql);
			$this->s__datos = $datos;
			$ruta = 'certificados/';
			$lim = count($datos);
			for ($i = 0; $i < $lim; $i++) {
				$id_ina = $datos[$i]['id_inasistencia'];
				$leg = usuario_logueado::get_agentes($datos[$i]['legajo']);
				$datos[$i]['ayn'] = $leg[0]['descripcion'];

				if ($datos[$i]['auto_sup'] == 1) {
					$aut_sup = 'true';
				} else {
					$aut_sup = 'false';
				}


				$archivo = $datos[$i]['id_inasistencia'] . $datos[$i]['fecha_inicio'] . '.pdf';
				$datos[$i]['certificado'] = '<a href=' . $ruta . $archivo . ' target="_blank">Descargar Certificado</a>';;
			}
			$this->s__datos = $datos;
			
			$componente->set_datos($datos);
		} else {
			toba::notificacion()->agregar('Ud. no tiene personal a cargo', "info");
		}
	}
	function enviar_correos($correo, $aprobado)
	{
		require_once('mail/tobamail.php');
		$datos = $this->s__datos_correo;
		//ei_arbol ($datos);  
		$fecha = date('d/m/Y', strtotime($datos['fecha_inicio']));
		$hasta = date('d/m/Y', strtotime($datos['fecha_fin']));
		$datos['agente_ayn'] = $datos['apellido'] . ', ' . $datos['nombre'];


		if ($aprobado == 1) {

			$asunto = 'Solicitud Inasistencia Justificada  ';
			//$motivo = 'Razones Particulares con gose de haberes';
			$cuerpo = '<table>
						El/la agente  <b>' . $datos['agente_ayn'] . '</b> perteneciente a la catedra/oficina/ direccion <b>' . $datos['catedra'] . '</b>.<br/>
						La solicitud de Justificacion de <b>'. $datos['motivo'] .'</b> ha sido otorgada desde ' . $fecha . ' hasta ' . $hasta . ' ('. $datos['dias'].' dias)
							Teniendo en cuenta las siguientes Observaciones: ' . $datos['observaciones'] . '
											
				</table>';
		} else {
			//$motivo = 'Razones Particulares con gose de haberes';
			
			//$ccopia='personal@fca.uncu.edu.ar';
			$asunto = 'Solicitud Inasistencia Justificada rechazada ';
			$cuerpo = '<table>
						El/la agente  <b>' . $datos['agente_ayn'] . '</b> perteneciente a la catedra/oficina/ direccion <b>' . $datos['catedra'] . '</b>.<br/>
						La solicitud de Justificacion de <b> '. $datos['motivo'] . '</b> a partir del dia ' . $fecha . ' hasta ' . $hasta . ' ('. $datos['dias'].' dias) ha sido <b>RECHAZADA</b>.
							Teniendo en cuenta las siguientes Observaciones: ' . $datos['observaciones'] . '
											
				</table>';
				
		} //date("d/m/y",$fecha)
		
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
}
