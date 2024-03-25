<?php
class comision extends toba_ci
{
	protected $s__datos;
	//-----------------------------------------------------------------------------------
	//---- formulario -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__formulario(toba_ei_formulario $form)
	{
		
		include ("usuario_logueado.php");
		$legajo = usuario_logueado::get_legajo(toba::usuario()-> get_id());
			
		$this->$s__agentes = $legajo;
		$datos['legajo'] = $legajo[0]['legajo'];
		$datos['apellido']=$legajo[0]['apellido'];
		$datos['nombre'] = $legajo [0]['nombre'];
		$form ->set_datos($datos);
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
		if ($datos['fecha'] <= $datos['fecha_fin'])
		{
			$fecha=$datos['fecha'];    
			$fecha_fin=date('d/m/Y',strtotime($datos['fecha_fin']));
			$legajo=$datos['legajo'];
			$superior=$datos['superior'];
			$autoridad=$datos['autoridad'];
			$lugar=$datos['lugar'];
			$catedra=$datos['catedra'];
			$sql= "SELECT nombre_catedra FROM reloj.catedras 
				Where id_catedra =$catedra";
			$a = toba::db('comision')->consultar($sql);
			$datos['catedra']= $a[0]['nombre_catedra'];
			$horario=$datos['horario'];
			$obs=$datos['observaciones'].' ';
			$motivo= $datos['motivo'];
			$fuera = $datos['fuera'];
			//$datos['fecha_fin'] = $fecha_fin;

			
		
			if ($fuera == 1) 
			{
				
				$f = 'true';
			}else
			{
				$f = 'false';
			}
			
			$horario_fin=$datos['horario_fin'];
			
			//ei_arbol ($datos);
			if (!empty ($datos['legajo'])){
				//$correo_agente = $this->dep('mapuche')->get_legajos_email($datos['legajo']);
				$correo_agente=$this->dep('datos')->tabla('agentes_mail')->get_correo($datos['legajo']);
				$datos['agente']=$correo_agente[0]['descripcion'];
		//    ei_arbol ($correo_agente);
			}
			if (!empty ($datos['superior'])){
			//	$correo_sup = $this->dep('mapuche')->get_legajos_email($datos['superior']);
				$correo_sup=$this->dep('datos')->tabla('agentes_mail')->get_correo($datos['superior']);
				$datos['superior']=$correo_sup[0]['descripcion'];
			}
			if (!empty ($datos['legajo_autoridad'])){
		//		$correo_aut = $this->dep('mapuche')->get_legajos_email($datos['autoridad']);
				$correo_aut=$this->dep('datos')->tabla('agentes_mail')->get_correo($datos['autoridad']);
			$datos['autoridad']=$correo_aut[0]['descripcion'];
			}
			$agente= $this -> dep('mapuche')->get_legajo_todos($legajo); 
			$datos['descripcion']= $agente[0]['descripcion'];
			$this->s__datos = $datos;
			//ei_arbol($datos);
			//ei_arbol($correo_sup);
			if (!empty ($datos['legajo'])){
			$this->enviar_correos($datos['agente']);
			$this->enviar_correos_sup($datos['superior']);
		
			}
			ei_arbol($correo_sup);
			/*if (!empty ($datos['legajo_sup'])){
				
			}
			/*if (!empty ($datos['legajo_aut'])){
			$this->enviar_correos_sup($correo_aut[0]['email']);
			}*/
		
			$sql = "INSERT INTO reloj.comision
				(legajo, catedra, lugar, motivo, fecha, horario, observaciones, legajo_sup, legajo_aut,  fecha_fin, horario_fin, fuera) VALUES
					($legajo, $catedra, '$lugar', '$motivo','$fecha', '$horario', '$obs', $superior, $autoridad,'$fecha_fin','$horario_fin',$f);";
		
			toba::db('comision')->ejecutar($sql);
		
			if($datos['fuera'] == 1){
			toba::notificacion()->agregar('Si viaja fuera de la provincia de Mendoza diríjase a la oficina de Personal para tramitar su seguro', 'info');
			}
		} else
		{    
			toba::notificacion()->agregar('Coloqu&ecute una fecha hasta mayor o igual que la fecha desde', 'error');
		}
		
	}

	function evt__formulario__modificacion($datos)
	{
		
		

		//$this->dep('datos')->tabla('comision')->set($datos);
	}

	function evt__formulario__cancelar()
	{
	}
	function enviar_correos($correo)
	{
		require_once('3ros/phpmailer/class.phpmailer.php');

				$datos =$this-> s__datos;    
				
//ei_arbol ($correo);                
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
//Leo: cambiamos de cuenta porque la hackearon esta esta contraseña para aplicaciones
$mail->Username   = "formularios_asistencia@fca.uncu.edu.ar";
//Introducimos nuestra contraseña de gmail
$mail->Password   = "gvcghltncpblkjbl";
//Definimos el remitente (dirección y, opcionalmente, nombre)
$mail->SetFrom('formularios_asistencia@fca.uncu.edu.ar', 'Formulario Personal');
//Esta línea es por si queréis enviar copia a alguien (dirección y, opcionalmente, nombre)

//$mail->AddReplyTo('caifca@fca.uncu.edu.ar','El de la réplica');
//Y, ahora sí, definimos el destinatario (dirección y, opcionalmente, nombre)
//$mail -> AddAddress('ebermejillo@fca.uncu.edu.ar', 'Tester');
$mail->AddAddress($correo, 'El Destinatario'); //Descomentar linea cuando pase a produccion
//Definimos el tema del email
$mail->Subject = 'Formulario Comision de Servicio';
//Para enviar un correo formateado en HTML lo cargamos con la siguiente función. Si no, puedes meterle directamente una cadena de texto.
//$mail->MsgHTML(file_get_contents('correomaquetado.html'), dirname(ruta_al_archivo));
//Y por si nos bloquean el contenido HTML (algunos correos lo hacen por seguridad) una versión alternativa en texto plano (también será válida para lectores de pantalla)
$mail->IsHTML(true); //el mail contiene html
	$fecha=date('d/m/Y',strtotime($datos['fecha']) );
	$fecha_fin =date('d/m/Y',strtotime($datos['fecha_fin']));
	
	$body = '<table>
						El/la agente  <b>'. $datos['descripcion'].'</b> perteneciente a  <b>'.$datos['catedra'].'</b>.<br/>
						Solicita <b>Comision de Servicio</b> a realizarse el dia '.$fecha.' hasta el dia ' .$fecha_fin. '
						en ' .$datos['lugar']. ' a partir de la hora ' .$datos['horario'].' hasta la hora '.$datos['horario_fin'].' con el siguiente motivo de: '.$datos['motivo'].'  observaciones: ' .$datos['observaciones']. '
											
			</table>'; 
$mail->Body = $body;
//ei_arbol($body);
//Enviamos el correo
 
	if(!$mail->Send()) {
	echo "Error: " . $mail->ErrorInfo;
	} else {
	echo "Enviado!";
	}
}
	function enviar_correos_sup($correo)
	{
		require_once('3ros/phpmailer/class.phpmailer.php');

				$datos =$this-> s__datos;    
				
//ei_arbol ($correo);                
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
//Leo: cambiamos de cuenta porque la hackearon esta esta contraseña para aplicaciones
$mail->Username   = "formularios_asistencia@fca.uncu.edu.ar";
//Introducimos nuestra contraseña de gmail
$mail->Password   = "gvcghltncpblkjbl";
//Definimos el remitente (dirección y, opcionalmente, nombre)
$mail->SetFrom('formularios_asistencia@fca.uncu.edu.ar', 'Formulario Personal');
//Esta línea es por si queréis enviar copia a alguien (dirección y, opcionalmente, nombre)

//$mail->AddReplyTo('caifca@fca.uncu.edu.ar','El de la réplica');
//Y, ahora sí, definimos el destinatario (dirección y, opcionalmente, nombre)
$mail -> AddAddress($correo, 'Superior');
//$mail->AddAddress($correo, 'El Destinatario'); //Descomentar linea cuando pase a produccion
//Definimos el tema del email
$mail->Subject = 'Formulario Comision de Servicio - Agente';
//Para enviar un correo formateado en HTML lo cargamos con la siguiente función. Si no, puedes meterle directamente una cadena de texto.
//$mail->MsgHTML(file_get_contents('correomaquetado.html'), dirname(ruta_al_archivo));
//Y por si nos bloquean el contenido HTML (algunos correos lo hacen por seguridad) una versión alternativa en texto plano (también será válida para lectores de pantalla)
$mail->IsHTML(true); //el mail contiene html
	$fecha=date('d/m/Y',strtotime($datos['fecha']) );
	$fecha_fin =date('d/m/Y',strtotime($datos['fecha_fin']));
	
	$body = '<table>
						El/la agente  <b>'. $datos['descripcion'] .'</b> perteneciente a la catedra/oficina/ direccion <b>'.$datos['catedra'].'</b>.<br/>
						Solicita <b>Comision de Servicio</b> con motivo de '.$datos['motivo'].' a realizarse el dia '.$fecha.' hasta el dia' .$fecha_fin. '
						en ' .$datos['lugar']. ' a partir de la hora ' .$datos['horario'].' hasta la hora '.$datos['horario_fin'].'. Teniendo en cuenta las siguientes Observaciones: ' .$datos['observaciones']. '</br>
						En caso de rechazar la solicitud del agente, debera enviar un correo a la siguiente direccion: asistencia@fca.uncu.edu.ar </br>


											
			</table>'; //date("d/m/y",$fecha)
$mail->Body = $body;
//Enviamos el correo
if(!$mail->Send()) {
	echo "Error: " . $mail->ErrorInfo;
} else {
	echo "Enviado!";
}
		/*$mail = new phpmailer();
		$mail->Mailer = "smtp";
		$mail->Host = "smtp.gmail.com";
		$mail->SMTPAuth = false;
		$mail->Username = "mmolina@fca.uncu.edu.ar";
		$mail->Password = "***";
		$mail->From = "Personal";
		$mail->FromName = "mmolina@fca.uncu.edu.ar";
		$mail->Timeout = 30;
		$mail->IsHTML(true);
		$mail->security = "tls";
		$mail->port = 587;
			$mail->AddAddress($correo['email']);
			$body = '<style type="text/css">
					.tg  {border-collapse:collapse;border-spacing:0;border-color:#9ABAD9;}
					.tg td{font-family:Arial, sans-serif;font-size:14px;padding:10px 5px;border-style:solid;border-width:0px;overflow:hidden;word-break:normal;border-top-width:1px;border-bottom-width:1px;border-color:#9ABAD9;color:#444;background-color:#EBF5FF;}
					.tg th{font-family:Arial, sans-serif;font-size:14px;font-weight:normal;padding:10px 5px;border-style:solid;border-width:0px;overflow:hidden;word-break:normal;border-top-width:1px;border-bottom-width:1px;border-color:#9ABAD9;color:#fff;background-color:#409cff;}
					.tg .tg-1wig{font-weight:bold;text-align:left;vertical-align:top}
					.tg .tg-by3v{font-weight:bold;font-size:14px;text-align:center}
					.tg .tg-md4w{background-color:#D2E4FC;text-align:left}
					.tg .tg-5ua9{font-weight:bold;text-align:left}
					.tg .tg-j92e{background-color:#D2E4FC;font-size:15px;text-align:left;vertical-align:top}
					</style>
					<table class="tg">
						<tr>
						<th class="tg-5ua9">' . $correo['descripcion'].' </th> 
						</tr>
						
					</table>';
		//$mail->PluginDir = "";
//Asignamos asunto y cuerpo del mensaje
		$mail->Subject = "Comisión de Servicio - FCA";
		$mail->Body = $body;
//Definimos AtBody por si el destinatario del correo no admite email con formato html
		$mail->AltBody = $body;
//se envia el mensaje, si no ha habido problemas
//la variable $exito tendra el valor true
		$exito = $mail->Send();
		$intentos = 1;
		while ((!$exito) && ($intentos < 3)) {
			sleep(1);
			toba::notificacion()->agregar("El servidor de correo informó un error<br>" . $mail->ErrorInfo);
			$exito = $mail->Send();
			$intentos = $intentos + 1;
		}
		if (!$exito) {
			toba::notificacion()->agregar("El servidor de correo informó un error<br>" . $mail->ErrorInfo);
		} else {

//toba::notificacion()->agregar("Correo enviado correctamente","info");
		}    
			//---------------------------------------------------------------------

				//Completamos parametros que se envian con la funcion de envio de mensajes por email -----------------    
			/*    $email_destino =  'mmolina@fca.uncu.edu.ar';  //$datos['email'];                         
				$parametros['correo_destino']           = $email_destino; 
				#$parametros['reply_email']              = $vendedor['email_contacto']; 
				#$parametros['reply_nombre']             = $vendedor['razon_vendedor']; 

				
				$parametros['asunto']                   = $datos['nombre_completo'].' - Comisión de Servicio'; 
				$parametros['contenido_mensaje']        = '<div>
										<p></p> 
										<p>DETALLE ASISTENCIA:</p>
										<p></p> 
							</div>';

				$parametros['encabezado_mensaje']      = 'Asistencia desde el '.$fecha_desde.', hasta el '.$fecha_hasta;
				/*$parametros['encabezado_mensaje_txt']  = strip_tags($parametros['encabezado_mensaje']);                                                                
				$parametros['contenido_mensaje_txt']   = strip_tags($parametros['contenido_mensaje']);

				#$parametros['adjunto1']                = $path.$nombre_fichero;
				#$parametros['correo_copia']           = $vendedor['email_contacto'];
				$parametros['correo_copia_oculta']     = $vendedor['email_contacto'];*/

				/*try {
					//$this->enviar_mail($parametros);
					toba::notificacion()->agregar("El mensaje se ha enviado correctamente al correo ".$email_destino.".", "info");

				} catch (Exception $e) {

					$error = 'Excepción capturada: '.$e->getMessage();
					toba::notificacion()->agregar($error, "error");

					$error = "Problemas enviando correo electrónico.<br/>".$mail->ErrorInfo;
					toba::notificacion()->agregar($error, "error");
				}*/

				//---------------------------------------------------------------------------------------------------
			

		
		

	}

	

}
?>