<?php
class ci_permisos_horarios extends comision_ci
{
	protected $s__datos;
	protected $s__agentes;
	
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
		//$this->dep('datos')->procesar_filas($datos);
		$cant = count($datos);
		$fecha_cierre =date("Y-m-d H:i:s");
		//ei_arbol($datos); 
		
		for($i=0;$i<$cant;$i++){
			if ($datos[$i]['apex_ei_analisis_fila'] == 'M' ){
				$id = $datos[$i]['id_permiso'];
				$a_sup=$datos[$i]['aut_sup'];
				$obs = $datos[$i]['observaciones'];
				$id= $datos[$i]['id_permiso'];
				$legajo = $datos[$i]['legajo'];
				if ($datos[$i]['procesado']  == 1){
					$estado = 'C';
				} else {
					$estado = 'A';
				}
				$ayn= $this->dep('mapuche')->get_legajos_autoridad($datos[$i]['legajo']);
				$apellido=$ayn[0]['apellido'];
				$nombre= $ayn[0]['nombre']; 
				$fecha_inicio = $datos[$i]['fecha'];
				$fecha_fin = $datos[$i]['fecha_fin'];
				$hora_inicio= $datos[$i]['horario_incio'];
				$hora_fin = $datos[$i]['horario_fin'];
				$lugar =$datos[$i]['lugar'];
				$motivo = $datos[$i]['motivo'];
				$autoriza_sup = $datos[$i]['aut_sup'];
				$datos_correo ['legajo'] = $legajo;
				$datos_correo ['apellido'] = $apellido;
				$datos_correo ['nombre'] = $nombre;
				$datos_correo ['fecha_inicio'] = $fecha_inicio;
				$datos_correo ['hora_inicio'] = $hora_inicio;
				$datos_correo ['horario_fin'] = $hora_fin;
				$datos_correo ['lugar'] = $lugar;
				$datos_correo ['motivo'] = $motivo;
				
				$this->s__datos_correo=$datos_correo;
				$sql= "SELECT email from reloj.agentes_mail
				where legajo=$legajo";
				$correo = toba::db('comision')->consultar($sql);
			

				if ($estado=='C'&& $autoriza_sup == 1 ) {     
						if ($autoriza_sup == 1) {
							$aut_sup =true;
						} else {
							$aut_sup =false;
						}
						$edad = $this->dep('mapuche')->get_edad($legajo, null);
						$direccion = $this->dep('mapuche')->get_datos_agente($filtro);
						$domicilio = $direccion [0]['calle'] ||' '|| $direccion[0]['numero'];
						$localidad= $direccion[0]['localidad'];
						$agrupamiento = $direccion[0]['escalafon'];
						$fecha_nacimiento = $direccion[0]['fecha_nacimiento'];
						$usuario_alta = toba::usuario()->get_id();
						$fecha_alta    = date("Y-m-d H:i:s");
						$fechaentera1 =strtotime($fecha_inicio);
					//$january = new DateTime($datos[$i]['fecha_fin']);
					//$february = new DateTime($datos[$i]['fecha_fin']);
						$fecha_inicio1 = date_create(date("Y-m-d",$fechaentera1)); 
						$hoy=date_create(date("Y-m-d",strtotime($fecha_fin)));
					//$dia = $february->diff($january);
						$dia = date_diff($fecha_inicio1 , $hoy);
						$dias = 0.5;
					//ei_arbol($dias);
						$fecha_ini=$datos[$i]['fecha'];
					//    ei_arbol($fecha_ini);
						$estado_civil = $direccion[0]['estado_civil'];
						$id_decreto = 5;
						$id_articulo = 41;
						$id_motivo = 58;
						$obs = 'Nº Permiso:'.$id .'-' .$obs; 
						$sexo=$this->dep('mapuche')->get_tipo_sexo($legajo, null);
						
						$sql = "INSERT INTO reloj.parte(
							legajo, edad, fecha_alta, usuario_alta, estado, fecha_inicio_licencia, dias, cod_depcia, domicilio, localidad, agrupamiento, fecha_nacimiento,
							apellido, nombre, estado_civil, observaciones, id_decreto, id_motivo, id_articulo, tipo_sexo,usuario_cierre,fecha_cierre)
							VALUES ($legajo, $edad, '$fecha_alta', '$usuario_alta', '$estado', '$fecha_ini', $dias, '04', '$domicilio', '$localidad', '$agrupamiento', '$fecha_nacimiento',
							'$apellido', '$nombre',    '$estado_civil', '$obs', $id_decreto,  $id_motivo,      $id_articulo,'$tipo_sexo','$usuario_cierre','$fecha_cierre');";
						toba::db('comision')->ejecutar($sql);
						

						$this->enviar_correos($correo[0]['email'],true);
						} else  if ($estado =='C'&& $autoriza_sup == 0 ) {
						
						$this->enviar_correos($correo[0]['email'],false );            
					
						} 
					if ($estado == 'C') {
									
								$sql = "UPDATE reloj.permisos_horarios
								Set  observaciones = '$obs' , procesado = true
								where id_permiso = $id";
								toba::db('comision')->ejecutar($sql);    
							}    
					}    
					
				
			}    
			

	
	}

	function conf__formulario(toba_ei_formulario_ml $componente)
	{
		//$componente->set_datos($this->dep('datos')->get_filas());
		include ("usuario_logueado.php");
		$legajo = usuario_logueado::get_legajo(toba::usuario()-> get_id());
		$legajo = $legajo[0]['legajo'];
		if (usuario_logueado::get_jefe($legajo)){
		$sql = "SELECT  *  FROM reloj.permisos_horarios
						join reloj.catedras ON catedras.id_catedra = permisos_horarios.id_catedra
					WHERE procesado is null
					and permisos_horarios.id_catedra in ((Select id_catedra from reloj.catedras_agentes
										where legajo = $legajo
										and jefe = true))
					and legajo <> $legajo
					order by permisos_horarios.id_catedra, fecha  ,legajo ";
		$listado = toba::db('comision')->consultar($sql);
		$tot = count($listado);
		for($i=0;$i<$tot;$i++){
			$leg = usuario_logueado::get_agentes($listado[$i]['legajo']); 
			$listado [$i]['ayn'] = $leg[0]['descripcion'];
		}
		
		$componente->set_datos($listado);
		} else{
			toba::notificacion()->agregar('Ud. no tiene personal a cargo' , "info");
		}
	}

	function enviar_correos($correo,$aprobado)
	{
		require_once('phpmailer/class.phpmailer.php');
		$datos =$this->s__datos_correo;
		//$formula = $this->s__formula;    
	$fecha=date('d/m/Y',strtotime($datos['fecha_inicio'] ) );

	//$hasta=date('d/m/Y',strtotime($datos['fecha_fin'] ) );
	$datos ['agente_ayn'] = $datos['apellido']. ', '.$datos['nombre'];


//$catedra = $this->            

//ei_arbol ($datos);              
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
$mail->Password   = "Elitou01";
//Definimos el remitente (dirección y, opcionalmente, nombre)
$mail->SetFrom('formularios_asistencia@fca.uncu.edu.ar', 'Formulario Personal');
//Y, ahora sí, definimos el destinatario (dirección y, opcionalmente, nombre)
$mail->AddAddress($correo, $datos['agente_ayn']);
//Definimos el tema del email
$mail->Subject = 'Solicitud de Permiso Horario';
//Para enviar un correo formateado en HTML lo cargamos con la siguiente función. Si no, puedes meterle directamente una cadena de texto.
//$mail->MsgHTML(file_get_contents('correomaquetado.html'), dirname(ruta_al_archivo));
//Y por si nos bloquean el contenido HTML (algunos correos lo hacen por seguridad) una versión alternativa en texto plano (también será válida para lectores de pantalla)
$mail->IsHTML(true); //el mail contiene html*/

	


	if ($aprobado==1) {
		
		
			$body = '<table>
						Al agente  <b>'.$datos['agente_ayn'].'</b> se aprueba la Solicitud de Permiso Horario </b> <br/>
						con motivo de' . $datos['motivo'].' el dia ' .$fecha. ' en el horario '. $datos['hora_inicio']. '  hasta '.$datos['hora_fin']. ' <br/>
											
				</table>';

		} else
		{
			//Esta línea es por si queréis enviar copia a alguien (dirección y, opcionalmente, nombre)
			$mail->AddReplyTo('personal@fca.uncu.edu.ar','Personal FCA - Rechazos');
			$body = '<table>
						Al agente  <b>'.$datos['agente_ayn'].'</b> le ha sido rechazada  la Solicitud de Permiso Horario </b>.<br/>
						con motivo de' . $datos['motivo'].'  el dia ' .$datos['fecha_inicio']. '  <br/>
											
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