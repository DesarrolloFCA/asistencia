<?php
class ci_vacaciones extends ctrl_asis_ci
{
	protected $s__formula;
	protected $s__datos_correo;
	protected $s__datos_filtro;
	function ini__operacion()
	{
		$sql = "SELECT * from reloj.inasistencias
		where  estado ='A'
		and id_motivo in (30,35,57) 
		order by id_inasistencia";
		//$this->dep('datos')->cargar();
	}

	function evt__guardar()
	{
		$this->dep('datos')->sincronizar();
		$this->dep('datos')->resetear();
		$sql = "SELECT * from reloj.inasistencias
		where  estado ='A' 
		and id_motivo in (30,35,57) 
		order by id_inasistencia";
		//$this->dep('datos')->cargar();
	}

	function evt__formulario__modificacion($datos)
	{
		$formula = $this->s__formula;
		//ei_arbol($formula);
		
		$usuario_cierre   =  toba::usuario()->get_id();
		$fecha_cierre = date("Y-m-d H:i:s");
		$cant = count($datos);

		for ($i = 0; $i < $cant; $i++) {
			
			if ($datos[$i]['legajo']<>null  ){
				
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
				$auto_aut = $datos[$i]['auto_aut'];
				$datos_correo['observaciones'] = $datos[$i]['observaciones'];
				$datos_correo['anio'] = $formula[$i]['anio'];
				$datos_correo['apellido'] = $apellido;
				$datos_correo['nombre'] = $nombre;
				$datos_correo['fecha_inicio'] = $fecha_inicio;
				$datos_correo['fecha_fin'] = $fecha_fin;
				$datos_correo['auto_aut'] = $auto_aut;
				$datos_correo['id_motivo'] = $datos[$i]['id_motivo'];
				$this->s__datos_correo = $datos_correo;
				$sql = "SELECT email from reloj.agentes_mail
				where legajo=$legajo";
				$correo = toba::db('ctrl_asis')->consultar($sql);

				if ($datos[$i]['estado'] == 'C') {

					if ($datos[$i]['aprobado'] == 1) {
						if ($auto_aut == 1) {
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
							//$january = new DateTime($datos[$i]['fecha_fin']);
							//$february = new DateTime($datos[$i]['fecha_fin']);
							$fecha_inicio = date_create(date("Y-m-d", $fechaentera1));
							$hoy = date_create(date("Y-m-d", strtotime($fecha_fin)));
							//$dia = $february->diff($january);
							$dia = date_diff($fecha_inicio, $hoy);
							$dias = $dia->format('%a') + 1;
							//ei_arbol($dias);
							$fecha_ini = $datos[$i]['fecha_inicio'];
							$estado_civil = $direccion[0]['estado_civil'];
							$id_decreto = $formula[$i]['id_decreto'];
							$id_motivo = $datos[$i]['id_motivo'];
							$id_articulo = $formula[$i]['id_articulo'];
							$sexo = $this->dep('mapuche')->get_tipo_sexo($legajo, null);
							
							if ($id_motivo != 30) {
								
								$dato_antiguedad = toba::tabla('antiguedad')->get_antiguedad($legajo);
								$bandera = 1;
			//ei_arbol($dato_antiguedad);
								if(!empty($dato_antiguedad['fecha_ingreso'])){
									$agente['fec_ingreso'] = $dato_antiguedad['fecha_ingreso'];
								}else{
						
									$sql = "SELECT fec_ingreso FROM reloj.agentes WHERE legajo = '$legajo' and agrupamiento = '$agrupamiento' ";
									$agente =  toba::db('ctrl_asis')->consultar_fila($sql); 
								}    
								if(!empty($agente['fec_ingreso'])){

									//obtenemos dias por antiguedad ------------------------------
									$antiguedad = toba::tabla('vacaciones_antiguedad')->get_array_antiguedad($agente['fec_ingreso'],$agrupamiento, $anio);
									$dias_tomados = 0;
									list($anio_lic,$mes_lic,$dia_lic) = explode('-', $fecha_inicio_licencia);
									$filtro['legajo']      = $legajo;
									$filtro['id_motivo']   = $id_motivo;
									$filtro['agrupamiento']= $agrupamiento;
									$filtro['parte_anio']  = $datos_correo['anio']; //ano seleccionado por vacaciones
									$filtro['anio']        = $datos_correo['anio'];
									$anio=$datos_correo['anio'];
									$partes = toba::tabla('parte')->get_listado_vaca($filtro);
									$dias_tomados =0;
									if ($partes[0]['sum']>0){
										$dias_tomados = $partes[0]['sum'];
									}
									$vacaciones_restantes = toba::tabla('vacaciones_restantes')->get_dias($legajo, $datos_correo['anio'], $agrupamiento);
										
									if (is_null($vacaciones_restantes)){

										$dias_disponibles = $dato_antiguedad['dias'] - $dias_tomados ; //$antiguedad['dias'] - $dias_tomados;
										$dias_restantes = $dias_disponibles - $dias;
										if ($dias_restantes >0){
											$sql = "INSERT INTO reloj.vacaciones_restantes(
											legajo, cod_depcia, agrupamiento, anio, dias)
											VALUES ($legajo, 4, '$agrupamiento', $anio, $dias_restantes);";

										}
									}else{
										$dias_disponibles = $vacaciones_restantes - $dias_tomados;
										$dias_restantes = $dias_disponibles - $dias;
										if ($dias_restantes >0){
											$sql = "UPDATE reloj.vacaciones_restantes
													SET  dias=$dias_restantes
													WHERE legajo = $legajo and agrupamiento = '$agrupamiento' and anio = $anio ;";
										} else {
											$sql = "DELETE FROM reloj.vacaciones_restantes
													WHERE legajo = $legajo and agrupamiento = '$agrupamiento' and anio = $anio ; ";
										}
									}
									
									toba::db('ctrl_asis')->ejecutar($sql); 
									$sql = "INSERT INTO reloj.parte(
							legajo, edad, fecha_alta, usuario_alta, estado, fecha_inicio_licencia, dias, cod_depcia, domicilio, localidad, agrupamiento, fecha_nacimiento,
							apellido, nombre, estado_civil, observaciones, id_decreto, id_motivo, id_articulo, tipo_sexo,usuario_cierre,fecha_cierre)
							VALUES ($legajo, $edad, '$fecha_alta', $usuario_alta, '$estado', '$fecha_ini', $dias, '04', '$domicilio', '$localidad', '$agrupamiento', 
							'$fecha_nacimiento','$apellido', '$nombre',    '$estado_civil', '$observaciones', $id_decreto, $id_motivo,$id_articulo,'$sexo','$usuario_cierre','$fecha_cierre');";
								
							} }else {
								$sql = "INSERT INTO reloj.parte(
							legajo, edad, fecha_alta, usuario_alta, estado, fecha_inicio_licencia, dias, cod_depcia, domicilio, localidad, agrupamiento, fecha_nacimiento,
							apellido, nombre, estado_civil, observaciones, id_decreto, id_motivo,  tipo_sexo,usuario_cierre,fecha_cierre)
						VALUES ($legajo, $edad, '$fecha_alta', $usuario_alta, '$estado', '$fecha_ini', $dias, '04', '$domicilio', '$localidad', '$agrupamiento', 
						'$fecha_nacimiento','$apellido', '$nombre',    '$estado_civil', '$observaciones', $id_decreto, $id_motivo,'$sexo','$usuario_cierre','$fecha_cierre');";
							
						}	
						
							toba::db('ctrl_asis')->ejecutar($sql);




							
							$sql = "DELETE from reloj.inasistencias
						WHERE id_inasistencia =$id_inasistencia";
							toba::db('ctrl_asis')->ejecutar($sql);
							toba::notificacion()->agregar('Inasistencias Procesada');
							$this->enviar_correos($correo[0]['email'], $datos[$i]['aprobado']);	
						} else {
							toba::notificacion()->agregar('Avise a la autoridad que falta su aprobacion, si no estan aprobadas las vacaciones coloque cerrado y no marque aprobado', "info");
						}
					} else if ($datos[$i]['aprobado'] == 0) {

						
						$sql = "UPDATE reloj.inasistencias
					SET estado='C', observaciones = '$observaciones' 
					WHERE id_inasistencia = $id_inasistencia";

						toba::db('ctrl_asis')->ejecutar($sql);

						
						$this->enviar_correos($correo[0]['email'], $datos[$i]['aprobado']);	
					}
				}
			}
				}
				}
		//$this->dep('datos')->procesar_filas($datos);
	}

	function conf__formulario(toba_ei_formulario_ml $componente)
	{

		$filtro = $this->s__datos_filtro;
		if (isset($filtro['id_catedra']['valor'])) {
			$id_catedra = $filtro['id_catedra']['valor'];
			if (isset($filtro['id_motivo']['valor'])) {
				$id_motivo = $filtro['id_motivo']['valor'];
				$sql = "SELECT * from reloj.inasistencias
				where  estado ='A'
				and id_catedra =$id_catedra 
				and id_motivo in (30,35,57) 
				and id_motivo = $id_motivo
				order by id_inasistencia";
			} else {

				$sql = "SELECT * from reloj.inasistencias
				where  estado ='A'
				and id_catedra =$id_catedra 
				and id_motivo in (30,35,57)
				order by id_inasistencia";
			}
		} else {

			if (isset($filtro['id_motivo']['valor'])) {
				$id_motivo = $filtro['id_motivo']['valor'];
				$sql = "SELECT * from reloj.inasistencias
				where  estado ='A'
				and id_motivo in (30,35,57) 
				and id_motivo = $id_motivo
				order by id_inasistencia";
			} else {
				$sql = "SELECT * from reloj.inasistencias
				where  estado ='A' 
				and id_motivo in (30,35,57)
				order by id_inasistencia";
			}
		}
		$datos = toba::db('ctrl_asis')->consultar($sql);
		$cant = count($datos);
		for ($i = 0; $i < $cant; $i++) {

			$agente = $this->dep('mapuche')->get_legajos_jefes_fca($datos[$i]['legajo']);
			//ei_arbol($agente);
			$datos[$i]['ayn'] = str_replace(' ', '', $agente[0]['descripcion']);
			//$datos[$i]['aprobado']= $componente->
		}
		$this->s__formula = $datos;
		//$componente->set_datos($this->dep('datos')->get_filas());
		$componente->set_datos($datos);
	}

	function enviar_correos($correo, $aprobado)
	{
		require_once('3ros/phpmailer/class.phpmailer.php');
		$datos = $this->s__datos_correo;
		//$formula = $this->s__formula;    
		$fecha = date('d/m/Y', strtotime($datos['fecha_inicio']));

		$hasta = date('d/m/Y', strtotime($datos['fecha_fin']));

		$datos['agente_ayn'] = $datos['apellido'] . ', ' . $datos['nombre'];
		//$catedra = $this->            

		// ei_arbol ($aprobado);              
		$mail = new phpmailer();
		$mail->IsSMTP();

		//Esto es para activar el modo depuración. En entorno de pruebas lo mejor es 2, en producción siempre 0
		// 0 = off (producción)
		// 1 = client messages
		// 2 = client and server messages
		$mail->SMTPDebug  = 0;
		//Ahora definimos gmail como servidor que aloja nuestro SMTP
		$mail->Host       = 'smtp.gmail.com';
		//El puerto será el 587 ya que usamos encriptación TLS
		$mail->Port       = 587;
		//Definmos la seguridad como TLS
		$mail->SMTPSecure = 'tls';
		//Tenemos que usar gmail autenticados, así que esto a TRUE
		$mail->SMTPAuth   = true;
		//Definimos la cuenta que vamos a usar. Dirección completa de la misma
		$mail->Username   = "formularios_asistencia@fca.uncu.edu.ar";
		//Introducimos nuestra contraseña de gmail
		$mail->Password   = "#1754OpD_;-?)(Fc4MtSKm*0-*#1=/Fz";
		//Definimos el remitente (dirección y, opcionalmente, nombre)
		$mail->SetFrom('formularios_asistencia@fca.uncu.edu.ar', 'Formulario Personal');
		//Esta línea es por si queréis enviar copia a alguien (dirección y, opcionalmente, nombre)
		//$mail->AddReplyTo('caifca@fca.uncu.edu.ar','El de la réplica');
		//Y, ahora sí, definimos el destinatario (dirección y, opcionalmente, nombre)
		$mail->AddAddress($correo, $datos['agente_ayn']);
		//Definimos el tema del email
		$mail->Subject = 'Solicitud de vacaciones';
		//Para enviar un correo formateado en HTML lo cargamos con la siguiente función. Si no, puedes meterle directamente una cadena de texto.
		//$mail->MsgHTML(file_get_contents('correomaquetado.html'), dirname(ruta_al_archivo));
		//Y por si nos bloquean el contenido HTML (algunos correos lo hacen por seguridad) una versión alternativa en texto plano (también será válida para lectores de pantalla)
		$mail->IsHTML(true); //el mail contiene html*/


		// ei_arbol($datos);

		if ($aprobado == 1) {
			if ($datos['id_motivo'] == 30) {
				$mail->Subject = 'Solicitud de Razones Particulares';
				//$motivo = 'Razones Particulares con gose de haberes';
				$body = '<table>
						El/la agente  <b>' . $datos['agente_ayn'] . '</b> perteneciente a la catedra/oficina/ direccion <b>' . $datos['catedra'] . '</b>.<br/>
						Solicita Justificacion de Inasistencia por Razones Particulares a partir del dia ' . $fecha . ' hasta ' . $hasta . 'ha sido otorgada.
							Teniendo en cuenta las siguientes Observaciones: ' . $datos['observaciones'] . '
											
				</table>';
			} else {

				//$motivo = 'Vacaciones'.$datos['anio'];
				$mail->Subject = 'Solicitud de vacaciones';
				$body = '<table>
						Sr/a <b>' . $datos['nombre'] . ' ' . $datos['apellido'] . '</b>:
						Su solicitud de vacaciones correspondientes al a�o ' . $datos['anio'] . ' ha sido otorgada, la cual sera efectiva entre ' . $fecha . ' y el ' . $hasta . ' .<br/>
						Debiendo reintegrase el siguiente dia habil correspondiente. <br/>
						
			</table>';
			}
		} else if ($aprobado == 0) {

			if ($datos['id_motivo'] == 30) {
				//$motivo = 'Razones Particulares con gose de haberes';
				$mail->Subject = 'Solicitud de Razones Particulares';
				$body = '<table>
						El/la agente  <b>' . $datos['agente_ayn'] . '</b> perteneciente a la catedra/oficina/ direccion <b>' . $datos['catedra'] . '</b>.<br/>
						La Solicitud de Justificacion de Inasistencia por Razones Particulares a partir del dia ' . $fecha . ' hasta ' . $hasta . 'ha sido <b>rechazada</b>.
							Teniendo en cuenta las siguientes Observaciones: ' . $datos['observaciones'] . '
											
				</table>';
			} else {

				//$motivo = 'Vacaciones'.$datos['anio'];
				$mail->Subject = 'Solicitud de vacaciones';
				$body = '<table>
						Sr/a <b>' . $datos['nombre'] . ' ' . $datos['apellido'] . '</b>:
						Su solicitud de vacaciones correspondiente al a�o ' . $datos['anio'] . 'ha sido rechazada de acuerdo a las siguientes observaciones ' . $datos['observaciones'] . '.
			</table>';
			}
		}; //date("d/m/y",$fecha)
		$mail->Body = $body;
		//Enviamos el correo
		if (!$mail->Send()) {
			echo "Error: " . $mail->ErrorInfo;
		} else {
			echo "Enviado!";
		}
	}


	//-----------------------------------------------------------------------------------
	//---- filtro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__filtro(ctrl_asis_ei_filtro $filtro) {}

	function evt__filtro__filtrar($datos)
	{
		$this->s__datos_filtro = $datos;
	}

	function evt__filtro__cancelar()
	{
		unset($this->s__datos_filtro);
	}
}
