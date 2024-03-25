<?php
class ci_inasistencia extends ctrl_asis_ci
{
	protected $s__datos_filtro;


	//---- Filtro -----------------------------------------------------------------------

	function conf__filtro(toba_ei_formulario $filtro)
	{
		if (isset($this->s__datos_filtro)) {
			$filtro->set_datos($this->s__datos_filtro);
		}
	}

	function evt__filtro__filtrar($datos)
	{
		$this->s__datos_filtro = $datos;
	}

	function evt__filtro__cancelar()
	{
		unset($this->s__datos_filtro);
	}

	//---- Cuadro -----------------------------------------------------------------------

	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		if (isset($this->s__datos_filtro)) {
			$cuadro->set_datos($this->dep('datos')->tabla('parte')->get_listado_fca($this->s__datos_filtro));
		} else {
			$cuadro->set_datos($this->dep('datos')->tabla('parte')->get_listado_fca());
		}
	}

	function evt__cuadro__seleccion($datos)
	{
		$this->dep('datos')->cargar($datos);
		$this->set_pantalla('pant_edicion');
	}

	//---- Formulario -------------------------------------------------------------------

	function conf__formulario(toba_ei_formulario $form)
	{
		if ($this->dep('datos')->esta_cargada()) {
			$form->set_solo_lectura('id_decreto');
			$form->set_solo_lectura('id_motivo');
			$form->set_solo_lectura('id_articulo');
			$form->set_datos($this->dep('datos')->tabla('parte')->get());
		} else {
			$this->pantalla()->eliminar_evento('eliminar');
		}
	}

	function evt__formulario__modificacion($datos)
	{
		$legajo = $datos['legajo'];
		$mail = $this->dep('datos2')->tabla('agentes_mail')->get_legajo_mail($legajo);
		ei_arbol($mail);
		//$this->dep('datos')->tabla('parte')->set($datos);

	}

	function resetear()
	{
		$this->dep('datos')->resetear();
		$this->set_pantalla('pant_seleccion');
	}

	//---- EVENTOS CI -------------------------------------------------------------------

	function evt__agregar()
	{
		$this->set_pantalla('pant_edicion');
	}

	function evt__volver()
	{
		$this->resetear();
	}

	function evt__eliminar()
	{
		$this->dep('datos')->eliminar_todo();
		$this->resetear();
	}

	function evt__guardar()
	{
		$this->dep('datos')->sincronizar();
		$this->resetear();
	}
	function enviar_correos($correo)
	{
		require_once('3ros/phpmailer/class.phpmailer.php');

				$datos =$this-> s__datos;    
				
//ei_arbol ($this->s__datos);                
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
$mail->Username   = "mmolina@fca.uncu.edu.ar";
//Introducimos nuestra contraseña de gmail
$mail->Password   = "cebkeqtiuonnpipw";
//Definimos el remitente (dirección y, opcionalmente, nombre)
$mail->SetFrom('mmolina@fca.uncu.edu.ar', 'Martin Molina');
//Esta línea es por si queréis enviar copia a alguien (dirección y, opcionalmente, nombre)
$mail->AddReplyTo('caifca@fca.uncu.edu.ar','El de la réplica');
//Y, ahora sí, definimos el destinatario (dirección y, opcionalmente, nombre)
$mail->AddAddress('molina.martin@gmail.com', 'El Destinatario');
//Definimos el tema del email
$mail->Subject = 'Esto es un correo de prueba';
//Para enviar un correo formateado en HTML lo cargamos con la siguiente función. Si no, puedes meterle directamente una cadena de texto.
//$mail->MsgHTML(file_get_contents('correomaquetado.html'), dirname(ruta_al_archivo));
//Y por si nos bloquean el contenido HTML (algunos correos lo hacen por seguridad) una versión alternativa en texto plano (también será válida para lectores de pantalla)
$mail->IsHTML(true); //el mail contiene html
	$fecha=date('d/m/Y',strtotime($datos['fecha']) );
	
	$body = '<table>
						El/la agente  <b>'. $datos['agente'].'</b> perteneciente a la catedra/oficina/ direccion <b>'.$datos['catedra'].'</b>.<br/>
						Solicita comision de servicio con motivo de '.$datos['motivo'].' a realizarse el dia '.$fecha.' 
						en ' .$datos['lugar']. ' a partir de la hora ' .$datos['horario'].'. Teniendo en cuenta las siguientes Observaciones: ' .$datos['observaciones']. '
											
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
		$mail->Password = "Francisco_04";
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