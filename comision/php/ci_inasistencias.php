<?php
class ci_inasistencias extends ctrl_asis_ci
{
	protected $s__formula;
	protected $s__datos_correo;
	protected $s__datos_filtro;

	function ini__operacion()
	{

		$this->dep('datos')->cargar();
	}

	function evt__guardar()
	{
		$this->dep('datos')->sincronizar();
		$this->dep('datos')->resetear();
		$sql = "SELECT * from reloj.inasistencias
		where  estado ='A' 
		order by id_inasistencia";
		//$this->dep('datos')->cargar();
	}

	function evt__formulario__modificacion($datos)
	{



		$formula = $this->s__formula;
		//ei_arbol($formula);
		//ei_arbol($datos);
		$usuario_cierre   =  toba::usuario()->get_id();
		$fecha_cierre = date("Y-m-d H:i:s");
		$cant = count($datos);
		for ($i = 0; $i < $cant; $i++) {

			if ($datos[$i]['apex_ei_analisis_fila'] == 'M') {

				$id_inasistencia = $formula[$i]['id_inasistencia'];
				//ei_arbol($formula[$i]['inasistencia']);
				$observaciones = $datos[$i]['observaciones'];
				$legajo = $datos[$i]['legajo'];
				$ayn = $this->dep('mapuche')->get_legajos_autoridad($legajo);
				$apellido = $ayn[0]['apellido'];
				$nombre = $ayn[0]['nombre'];
				$fecha_inicio = $datos[$i]['fecha_inicio'];
				$fecha_fin = $datos[$i]['fecha_fin'];

				$datos_correo['anio'] = $formula[$i]['anio'];
				$datos_correo['apellido'] = $apellido;
				$datos_correo['nombre'] = $nombre;
				$datos_correo['fecha_inicio'] = $fecha_inicio;
				$datos_correo['fecha_fin'] = $fecha_fin;
				$this->s__datos_correo = $datos_correo;
				$sql = "SELECT email from reloj.agentes_mail
				where legajo=$legajo";
				$correo = toba::db('ctrl_asis')->consultar($sql);

				if ($datos[$i]['estado'] == 'C') {

					if ($datos[$i]['aprobado'] == 1) {
						
						$filtro['legajo'] = $legajo;
						$edad = $this->dep('mapuche')->get_edad($legajo, null);
						$direccion = $this->dep('mapuche')->get_datos_agente($filtro);
						$domicilio = $direccion[0]['calle'] || ' ' || $direccion[0]['numero'];
						$localidad = $direccion[0]['localidad'];
						$agrupamiento = $direccion[0]['escalafon'];
						$fecha_nacimiento = $direccion[0]['fecha_nacimiento'];
						$fecha_alta = $formula[$i]['fecha_alta'];
						$usuario_alta = $formula[$i]['usuario_alta'];
						$estado = $datos[$i]['estado'];
						$fechaentera1 = strtotime($fecha_inicio);
						$fecha_inicio = date_create(date("Y-m-d", $fechaentera1));
						$hoy = date_create(date("Y-m-d", $fecha_fin));
						$dia = date_diff($fecha_inicio, $hoy);
						$dias = $dia->format('%a');
						$fecha_ini = $datos[$i]['fecha_inicio'];
						$estado_civil = $direccion[0]['estado_civil'];
						$id_decreto = $formula[$i]['id_decreto'];
						$id_motivo = $datos[$i]['id_motivo'];
						$id_articulo = $formula[$i]['id_articulo'];
						$sexo = $this->dep('mapuche')->get_tipo_sexo($legajo, null);
						$existe = $this->dep('datos')->tabla('parte')->get_duplicado_inasistencia($legajo,$fecha_ini,$id_motivo);
						if ($existe) {
							toba::notificacion()->agregar('Este pedido fue anteriormente ingresado', "info");
							break;
						}else {
						$sql = "INSERT INTO reloj.parte(
						 legajo, edad, fecha_alta, usuario_alta, estado, fecha_inicio_licencia, dias, cod_depcia, domicilio, localidad, agrupamiento, fecha_nacimiento,
	  					apellido, nombre, estado_civil, observaciones, id_decreto, id_motivo, id_articulo, tipo_sexo,usuario_cierre,fecha_cierre)
						VALUES ($legajo, $edad, '$fecha_alta', $usuario_alta, '$estado', '$fecha_ini', $dias, '04', '$domicilio', '$localidad', '$agrupamiento', 
							'$fecha_nacimiento',				'$apellido', '$nombre',	'$estado_civil', '$observaciones', $id_decreto, $id_motivo,$id_articulo,'$sexo','$usuario_cierre','$fecha_cierre');";
						toba::db('ctrl_asis')->ejecutar($sql);
						$this->enviar_correos($correo[0]['email'], $datos[$i]['aprobado']);
						$sql = "DELETE from reloj.inasistencias
					  WHERE id_inasistencia =$id_inasistencia";
						toba::db('ctrl_asis')->ejecutar($sql);
					}
					} else if ($datos[$i]['aprobado'] == 0) {

						$this->enviar_correos($correo[0]['email'], $datos[$i]['aprobado']);
						$sql = "UPDATE reloj.inasistencias
					SET estado='C', observaciones = '$observaciones' 
					WHERE id_inasistencia = $id_inasistencia";

						toba::db('ctrl_asis')->ejecutar($sql);
					}
				}
			}
		}



		//$this->dep('datos')->procesar_filas($datos);
	}

	function conf__formulario(toba_ei_formulario_ml $componente)
	{

		$sql = "SELECT * from reloj.inasistencias
		where  estado ='A' 
		order by id_inasistencia";
		$datos = toba::db('ctrl_asis')->consultar($sql);
		//$datos = $this->dep('datos')->get_filas();
		$cant = count($datos);
		for ($i = 0; $i < $cant; $i++) {
			$agente = $this->dep('mapuche')->get_legajos_jefes_fca($datos[$i]['legajo']);

			$datos[$i]['ayn'] = $agente[0]['descripcion'];
			//$datos[$i]['aprobado']= $componente->
		}
		//ei_arbol($datos);
		$this->s__formula = $datos;
		$componente->set_datos($datos);
	}

	function enviar_correos($correo, $aprobado)
	{
		require_once('mail/tobamail.php');
		$datos = $this->s__datos_correo;
		//$formula = $this->s__formula;    
		$fecha = date('d/m/Y', strtotime($datos['fecha_inicio']));

		$hasta = date('d/m/Y', strtotime($datos['fecha_fin']));


		$asunto = 'Esto es un correo de prueba';
		
		
		if ($aprobado == 1) {
			if ($datos['id_motivo'] == 30) {
				//$motivo = 'Razones Particulares con gose de haberes';
				$cuerpo = '<table>
						El/la agente  <b>' . $datos['agente_ayn'] . '</b> perteneciente a la catedra/oficina/ direccion <b>' . $datos['catedra'] . '</b>.<br/>
						Solicita Justificacion de Inasistencia por Razones Particulares a partir del dia ' . $fecha . ' hasta ' . $hasta . '.
						 Teniendo en cuenta las siguientes Observaciones: ' . $datos['observaciones'] . '
											
				</table>';
			} else {

				//$motivo = 'Vacaciones'.$datos['anio'];
				$cuerpo = '<table>
						Sr/a <b>' . $datos['nombre'] . ' ' . $datos['apellido'] . '</b>:
						Su solicitud de vacaciones correspondiente al anio ' . $datos['anio'] . ' ha sido otorgada la cual sera efectiva entre ' . $fecha . ' y ' . $hasta . ' .<br/>
						Esperamos que disfrute sus vacaciones											
			</table>';
			}
		} else if ($aprobado == 0) {

			if ($datos['id_motivo'] == 30) {
				//$motivo = 'Razones Particulares con gose de haberes';
				$cuerpo = '<table>
						El/la agente  <b>' . $datos['agente_ayn'] . '</b> perteneciente a la catedra/oficina/ direccion <b>' . $datos['catedra'] . '</b>.<br/>
						Solicita Justificacion de Inasistencia por Razones Particulares a partir del dia ' . $fecha . ' hasta ' . $hasta . '.
						 Teniendo en cuenta las siguientes Observaciones: ' . $datos['observaciones'] . '
											
				</table>';
			} else {

				//$motivo = 'Vacaciones'.$datos['anio'];
				$cuerpo = '<table>
						Sr/a <b>' . $datos['nombre'] . ' ' . $datos['apellido'] . '</b>:
						Su solicitud de vacaciones correspondiente al anio' . $datos['anio'] . 'ha sido rechazada de acuerdo a las siguientes observaciones ' . $datos['observaciones'] . '.
			</table>';
			}
		}; //date("d/m/y",$fecha)
		
		//Enviamos el correo
		$mail = new TobaMail($correo, $asunto, $cuerpo, $desde,'');

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
