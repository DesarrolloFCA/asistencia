<?php
class ci_justificacion_con_certificado extends ctrl_asis_ci
{
	protected $s__datos;
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
		$fecha_cierre =date("Y-m-d H:i:s");
		//ei_arbol($datos);
		for($i=0;$i<$cant;$i++){
			$legajo=$datos[$i]['legajo'];
			$id = $datos[$i]['id_inasistencia'];
			$obs = $datos[$i]['observaciones'];
			$datos[$i]['fecha_inicio']=$this->s__datos[$i]['fecha_inicio'];
			if ($datos[$i]['auto_sup'] == 1){
				$aut_sup = 'true';
			}else{
				$aut_sup = 'false';
			}
			if ($datos[$i]['auto_aut'] == 1){
				$aut_aut = 'true';
			}else{
				$aut_aut = 'false';
			}

		//ei_arbol($datos[$i]['apex_ei_analisis_fila']);
			if ($datos[$i]['apex_ei_analisis_fila'] == 'M' ){
				if ($datos[$i]['estado']=='A') {
				// code...
				$sql = "UPDATE reloj.inasistencias
					Set auto_sup = $aut_sup, auto_aut = $aut_aut, observaciones = '$obs'
					where id_inasistencia = $id";
		//    ei_arbol($sql);
				toba::db('ctrl_asis')->ejecutar ($sql);    

				} else {
					if ($datos[$i]['auto_aut'] == 1) {
					//ei_arbol($legajo);
					$filtro['legajo'] = $legajo;

					$edad = $this->dep('mapuche')->get_edad($legajo, null);
					$direccion = $this->dep('mapuche')->get_datos_agente($filtro);
					$nombre= $direccion [0]['nombre'];
					$apellido = $direccion [0]['apellido'];
					$datos[$i]['nombre']=$nombre;
					$datos[$i]['apellido']=$apellido;
					$domicilio = $direccion [0]['calle'] ||' '|| $direccion[0]['numero'];
					$localidad= $direccion[0]['localidad'];
					$agrupamiento = $direccion[0]['escalafon'];
					$fecha_nacimiento = $direccion[0]['fecha_nacimiento'];
					$fecha_alta = $formula[$i]['fecha_alta'];
					$usuario_alta = $formula[$i]['usuario_alta'];
					$estado=$datos[$i]['estado'];
					$fechaentera1 =strtotime($fecha_inicio);
					//$january = new DateTime($datos[$i]['fecha_fin']);
					//$february = new DateTime($datos[$i]['fecha_fin']);
					$fecha_inicio = date_create(date("Y-m-d",$fechaentera1)); 
					$hoy=date_create(date("Y-m-d",strtotime($fecha_fin)));
					//$dia = $february->diff($january);
					$dia = date_diff($fecha_inicio , $hoy);
					$dias = $dia->format('%a') + 1;
					//ei_arbol($dias);
					$fecha_ini=$datos[$i]['fecha_inicio'];
					$estado_civil= $direccion[0]['estado_civil'];
					$id_decreto= $formula[$i]['id_decreto'];
					$id_motivo=$datos[$i]['id_motivo'];
					$id_articulo=$formula[$i]['id_articulo'];
					$sexo=$this->dep('mapuche')->get_tipo_sexo($legajo, null);
					
					$sql = "INSERT INTO reloj.parte(
						legajo, edad, fecha_alta, usuario_alta, estado, fecha_inicio_licencia, dias, cod_depcia, domicilio, localidad, agrupamiento, fecha_nacimiento,
						apellido, nombre, estado_civil, observaciones, id_decreto, id_motivo,  tipo_sexo,usuario_cierre,fecha_cierre)
						VALUES ($legajo, $edad, '$fecha_alta', $usuario_alta, '$estado', '$fecha_ini', $dias, '04', '$domicilio', '$localidad', '$agrupamiento', 
						'$fecha_nacimiento','$apellido', '$nombre',    '$estado_civil', '$observaciones', $id_decreto, $id_motivo,'$tipo_sexo','$usuario_cierre','$fecha_cierre');";
					toba::db('ctrl_asis')->ejecutar($sql);
					$sql = "SELECT max(id_parte) ultimo FROM reloj.parte";
					$a= toba::db('ctrl_asis')->consultar($sql);
					$ultimo = $a[0]['ultimo'];
					$ql = "INSERT INTO reloj.certificados(
							id_inasistencia, id_parte) VALUES ($id, $ultimo)";
					toba::db('ctrl_asis')->ejecutar($ql);			
					$this->enviar_correos($correo[0]['email'],$datos[$i]['auto_aut'] );
					$sql="DELETE from reloj.inasistencias
						WHERE id_inasistencia =$id";
					toba::db('ctrl_asis')->ejecutar($sql); 
					}else{
						$this->enviar_correos($correo[0]['email'],$datos[$i]['auto_aut'] );
					$sql = "UPDATE reloj.inasistencias
					SET estado='C', observaciones = '$observaciones' 
					WHERE id_inasistencia = $id";
					toba::db('ctrl_asis')->ejecutar($sql);
					}

				} 
			}
		} 
		//$this->dep('datos')->procesar_filas($datos);
	}

	function conf__formulario(toba_ei_formulario_ml $componente)
	{
		$sql = "SELECT * FROM reloj.inasistencias
			WHERE  estado = 'A'
			AND id_motivo not in (30,35,57)
			Order by fecha_inicio, fecha_fin, legajo";
		$datos = toba::db('ctrl_asis')->consultar($sql);
		$this ->s__datos = $datos;
		$ruta = 'certificados/';
		$lim = count($datos);
		for ($i=0;$i<$lim;$i++){
			$id_ina= $datos[$i]['id_inasistencia'];
			if ($datos[$i]['auto_sup'] == 1){
				$aut_sup = 'true';
			}else{
				$aut_sup = 'false';
			}
			if ($datos[$i]['auto_aut'] == 1){
				$aut_aut = 'true';
			}else{
				$aut_aut = 'false';
			}

			$archivo = $datos[$i]['id_inasistencia'].$datos[$i]['fecha_inicio'].'.pdf';
			$datos[$i]['certificado'] = '<a href='.$ruta.$archivo.' target="_blank">Descargar Certificado</a>';;
		}
		$this ->s__datos = $datos;
		$componente->set_datos($datos);
	}
function enviar_correos($correo,$aprobado)
	{
		require_once('3ros/phpmailer/class.phpmailer.php');
		$datos =$this->s__datos_correo;
		//$formula = $this->s__formula;    
	$fecha=date('d/m/Y',strtotime($datos['fecha_inicio'] ) );

	$hasta=date('d/m/Y',strtotime($datos['fecha_fin'] ) );

$datos ['agente_ayn'] = $datos['apellido']. ', '.$datos['nombre'];
//$catedra = $this->            

// ei_arbol ($datos);              
$mail = new phpmailer();
$mail->IsSMTP();

//Esto es para activar el modo depuraciÃ³n. En entorno de pruebas lo mejor es 2, en producciÃ³n siempre 0
// 0 = off (producciÃ³n)
// 1 = client messages
// 2 = client and server messages
$mail->SMTPDebug  = 0;
//Ahora definimos gmail como servidor que aloja nuestro SMTP
$mail->Host       = 'smtp.gmail.com';
//El puerto serÃ¡ el 587 ya que usamos encriptaciÃ³n TLS
$mail->Port       = 587;
//Definmos la seguridad como TLS
$mail->SMTPSecure = 'tls';
//Tenemos que usar gmail autenticados, asÃ­ que esto a TRUE
$mail->SMTPAuth   = true;
//Definimos la cuenta que vamos a usar. DirecciÃ³n completa de la misma
$mail->Username   = "formularios_asistencia@fca.uncu.edu.ar";
//Introducimos nuestra contraseÃ±a de gmail
$mail->Password   = "#1754OpD_;-?)(Fc4MtSKm*0-*#1=/Fz";
//Definimos el remitente (direcciÃ³n y, opcionalmente, nombre)
$mail->SetFrom('formularios_asistencia@fca.uncu.edu.ar', 'Formulario Personal');
//Esta lÃ­nea es por si querÃ©is enviar copia a alguien (direcciÃ³n y, opcionalmente, nombre)
//$mail->AddReplyTo('caifca@fca.uncu.edu.ar','El de la rÃ©plica');
//Y, ahora sÃ­, definimos el destinatario (direcciÃ³n y, opcionalmente, nombre)
$mail->AddAddress($correo, $datos['agente_ayn']);
//Definimos el tema del email
$mail->Subject = 'Solicitud de vacaciones';
//Para enviar un correo formateado en HTML lo cargamos con la siguiente funciÃ³n. Si no, puedes meterle directamente una cadena de texto.
//$mail->MsgHTML(file_get_contents('correomaquetado.html'), dirname(ruta_al_archivo));
//Y por si nos bloquean el contenido HTML (algunos correos lo hacen por seguridad) una versiÃ³n alternativa en texto plano (tambiÃ©n serÃ¡ vÃ¡lida para lectores de pantalla)
$mail->IsHTML(true); //el mail contiene html*/

	
// ei_arbol($datos);

	if ($aprobado == 1) {
		
		$mail->Subject = 'Solicitud Inasistencia Justificada con certificado ';    
		//$motivo = 'Razones Particulares con gose de haberes';
			$body = '<table>
						El/la agente  <b>'.$datos['agente_ayn'].'</b> perteneciente a la catedra/oficina/ direccion <b>'.$datos['catedra'].'</b>.<br/>
						La solicitud de Justificacion acompañada con certificado ha sido otorgada desde'.$fecha.' hasta '.$hasta. '
							Teniendo en cuenta las siguientes Observaciones: ' .$datos['observaciones']. '
											
				</table>';

		} else {
		//$motivo = 'Razones Particulares con gose de haberes';
			$mail->Subject = 'Solicitud Inasistencia Justificada con certificado';    
			$body = '<table>
						El/la agente  <b>'.$datos['agente_ayn'].'</b> perteneciente a la catedra/oficina/ direccion <b>'.$datos['catedra'].'</b>.<br/>
						La solicitud de Justificacion acompañada con certificado a partir del dia '.$fecha.' hasta '.$hasta. 'ha sido <b>rechazada</b>.
							Teniendo en cuenta las siguientes Observaciones: ' .$datos['observaciones']. '
											
				</table>';

		
		}

	; //date("d/m/y",$fecha)
	$mail->Body = $body;
	//Enviamos el correo
		if(!$mail->Send()) {
		echo "Error: " . $mail->ErrorInfo;
		} else {
			echo "Enviado!";
		}
	}

}

?>