<?php
class ci_permiso_horario extends comision_ci
{
	protected $s__datos;
	//-----------------------------------------------------------------------------------
	//---- formulario -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__formulario__alta($datos)
	{
		//ei_arbol ($datos);
		//$this->dep('datos')->nueva_fila($datos);

		$anio= date('Y');
		$legajo = $datos['legajo'];
		$escalafon = $this -> dep('mapuche')->get_legajo_escalafon($legajo);
		$agente= $this -> dep('mapuche')->get_legajo_todos($legajo); 
		$datos['descripcion']= $agente[0]['descripcion'];

		$j = count($escalafon);
		 for ($i=0; $i<=$j; $i++){
		 	if ($escalafon [$i]['escalafon'] == 'NODO'){
		 		$sql = "SELECT legajo , a.id_catedra 
						FROM reloj.catedras a
						INNER JOIN reloj.catedras_agentes c ON a.id_catedra = c.id_catedra
						WHERE legajo = $legajo ";
				$cat_leg = toba::db('comision')->consultar($sql);
				
				$catedra_nodo= $cat_leg [0]['id_catedra'];
		 	}
		 }
		
		
		if ($datos['id_catedra'] == $catedra_nodo and isset($catedra_nodo)  ){

			//ei_arbol($datos);
		
		$sql = "SELECT count(*) cantidad 
				FROM reloj.permisos_horarios
				WHERE legajo = $legajo
				and extract(year FROM fecha) = $anio ;";
		$a = toba::db('comision')->consultar($sql);
		$cantidad = $a [0]['cantidad'];
		
		if ($cantidad <= 5 ) {

		$this->dep('datos')->tabla('permiso_horarios')->nueva_fila($datos);
		$this->dep('datos')->sincronizar();
		$this->dep('datos')->resetear();
		$catedra=$datos['id_catedra'];
			$sql= "SELECT nombre_catedra FROM reloj.catedras 
				Where id_catedra =$catedra";
			$a = toba::db('comision')->consultar($sql);
			$datos['n_catedra']= $a[0]['nombre_catedra'];
		if (!empty ($datos['legajo'])){
				//$correo_agente = $this->dep('mapuche')->get_legajos_email($datos['legajo']);
				$correo_agente=$this->dep('datos')->tabla('agentes_mail')->get_correo($datos['legajo']);
				$datos['agente']=$correo_agente[0]['descripcion'];
		
			}
			if (!empty ($datos['leg_sup'])){
				//$correo_sup = $this->dep('mapuche')->get_legajos_email($datos['leg_sup']);
				$correo_sup=$this->dep('datos')->tabla('agentes_mail')->get_correo($datos['leg_sup']);
				$datos['superior']=$correo_sup[0]['descripcion'];
			}
			//ei_arbol ($datos);
			$agente= $this -> dep('mapuche')->get_legajo_todos($legajo); 
					$datos['descripcion']= $agente[0]['descripcion'];
			$this->s__datos = $datos;
			if (!empty ($datos['legajo'])){
			$this->enviar_correos($datos['agente']);
			toba::notificacion()->agregar('Su pedido de Permiso Horario sera tramitado a la brevedad', 'info');
		
			}
			

		} else 
		{
			toba::notificacion()->agregar('Ud. ha excedido la cantidad de permisos excepcionales que se otorgan por a&ntilde;o', 'info');
		}	
			if (!empty ($datos['leg_sup'])){
				$this->enviar_correos($datos['superior']);
			}
		} else {
			toba::notificacion()->agregar('Esta licencia es aplicable solamente a Personal de Apoyo Acad&eacute;mico', 'info');
		}	

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
$mail -> AddAddress($correo, $datos['descripcion']);
//$mail->AddAddress($correo, 'El Destinatario'); //Descomentar linea cuando pase a produccion
//Definimos el tema del email
$mail->Subject = 'Formulario Permiso Horario';
//Para enviar un correo formateado en HTML lo cargamos con la siguiente función. Si no, puedes meterle directamente una cadena de texto.
//$mail->MsgHTML(file_get_contents('correomaquetado.html'), dirname(ruta_al_archivo));
//Y por si nos bloquean el contenido HTML (algunos correos lo hacen por seguridad) una versión alternativa en texto plano (también será válida para lectores de pantalla)
$mail->IsHTML(true); //el mail contiene html
	$fecha=date('d/m/Y',strtotime($datos['fecha']) );
	$fecha_fin =date('d/m/Y',strtotime($datos['fecha_fin']));
	
	$body = '<table>
						El/la agente  <b>'. $datos['descripcion'].'</b> perteneciente a la <b>'.$datos['n_catedra'].'</b>.<br/>
						Solicita <b>permiso horario</b>  para el d&iacute;a '.$fecha.' a partir de la hora ' .$datos['horario_incio'].' hasta la hora '.$datos['horario_fin'].'<br/> 
						Motivo de la solicitud: '.$datos['razon'].'<br/>
						Observaciones: ' .$datos['observaciones']. ' -
											
			</table>'; //date("d/m/y",$fecha)
$mail->Body = $body;
//Enviamos el correo
if(!$mail->Send()) {
	echo "Error: " . $mail->ErrorInfo;
} else {
	echo "Enviado!";
}
	 }
/*	 function enviar_correos_sup($correo) 
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
$mail->Username   = "formularios_personal@fca.uncu.edu.ar";
//Introducimos nuestra contraseña de gmail
$mail->Password   = "djxgidwlytoydsow";
//Definimos el remitente (dirección y, opcionalmente, nombre)
$mail->SetFrom('formularios_personal@fca.uncu.edu.ar', 'Formulario Personal');
//Esta línea es por si queréis enviar copia a alguien (dirección y, opcionalmente, nombre)

//$mail->AddReplyTo('caifca@fca.uncu.edu.ar','El de la réplica');
//Y, ahora sí, definimos el destinatario (dirección y, opcionalmente, nombre)
$mail -> AddAddress($correo, 'Tester');
//$mail->AddAddress($correo, 'El Destinatario'); //Descomentar linea cuando pase a produccion
//Definimos el tema del email
$mail->Subject = 'Formulario Permiso Horario';
//Para enviar un correo formateado en HTML lo cargamos con la siguiente función. Si no, puedes meterle directamente una cadena de texto.
//$mail->MsgHTML(file_get_contents('correomaquetado.html'), dirname(ruta_al_archivo));
//Y por si nos bloquean el contenido HTML (algunos correos lo hacen por seguridad) una versión alternativa en texto plano (también será válida para lectores de pantalla)
$mail->IsHTML(true); //el mail contiene html
	$fecha=date('d/m/Y',strtotime($datos['fecha']) );
	$fecha_fin =date('d/m/Y',strtotime($datos['fecha_fin']));
	
	$body = '<table>
						El/la agente  <b>'. $datos['agente'].'</b> perteneciente a la catedra/oficina/ direcci&oacute;n <b>'.$datos['n_catedra'].'</b>.<br/>
						Solicita permiso horario con motivo de '.$datos['razon'].' a realizarse el d&iacute;a '.$fecha.' a partir de la hora ' .$datos['horario_incio'].' hasta la hora '.$datos['horario_fin'].'. Teniendo en cuenta las siguientes Observaciones: ' .$datos['observaciones']. '
											
			</table>'; //date("d/m/y",$fecha)
$mail->Body = $body;
//Enviamos el correo
if(!$mail->Send()) {
	echo "Error: " . $mail->ErrorInfo;
} else {
	echo "Enviado!";
}
	 }*/

	function conf__formulario(comision_ei_formulario $form)
	{
		include ("usuario_logueado.php");
		$legajo = usuario_logueado::get_legajo(toba::usuario()-> get_id());
		
		$this->$s__agentes = $legajo;
		$datos['legajo'] = $legajo[0]['legajo'];
		$datos['apellido']=$legajo[0]['apellido'];
		$datos['nombre'] = $legajo [0]['nombre'];
		$form ->set_datos($datos);
	}

}
?>