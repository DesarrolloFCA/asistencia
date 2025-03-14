<?php
class ci_comisiones_de_servicio extends ctrl_asis_ci
{
	
	protected $s__datos_filtro;
	protected $s__id_comision;
	protected $s__datos_correo;

	function ini__operacion()
	{
		$this->dep('datos')->cargar();
	}

	function evt__guardar()
	{
		$this->dep('datos')->sincronizar();
		$this->dep('datos')->resetear();
		$this->dep('datos')->cargar();
	}


	function conf__formulario(toba_ei_formulario_ml $componente)
	{
		//$componente->set_datos($this->dep('datos')->get_filas());
		$where = array();
		$filtro = $this->s__datos_filtro;

		if (isset($filtro['legajo']['valor'])){
			$legajo = $filtro['legajo']['valor'];
			$where[]= "legajo = $legajo";
		}
		if (isset($filtro['id_catedra']['valor'])){
			$catedras = $filtro['id_catedra']['valor'];
			$where[]= "catedra = $catedras";
		}
		if(isset($filtro['fuera']['valor'])){
			if($filtro['fuera']['valor'] == 1){
				$where[]="fuera= true";
			}else{
				$where[]= "fuera in (null,false)";
			}
		}
	
		
		$sql = "SELECT  *  FROM reloj.comision
					WHERE (pasada is null or pasada = false)
					order by fecha asc
					LIMIT 100";
		if (count($where)>0) {
			$sql = sql_concatenar_where($sql, $where);
		}	
	
			$listado = toba::db('ctrl_asis')->consultar($sql);		

			$componente->set_datos($listado);

	}

	function evt__formulario__modificacion($datos)
	{
		//$this->dep('datos')->procesar_filas($datos);
		$cant = count($datos);
		$fecha_cierre =date("Y-m-d H:i:s");
		
		
		for($i=0;$i<$cant;$i++){
			if ($datos[$i]['apex_ei_analisis_fila'] == 'M' ){
						
				$id= $datos[$i]['id_comision'];
				$legajo = $datos[$i]['legajo'];
		
				if ($datos[$i]['pasada']  == 1){
					$estado = 'C';
				} else {
					$estado = 'A';
				}
				$id_comision = $datos[$i]['id_comision'];
				$obs = $datos[$i]['observaciones'];
				$ayn= $this->dep('mapuche')->get_legajos_autoridad($datos[$i]['legajo']);
				$apellido=$ayn[0]['apellido'];
				$nombre= $ayn[0]['nombre']; 
				$fecha_inicio = $datos[$i]['fecha'];
				$fecha_fin = $datos[$i]['fecha_fin'];
				$hora_inicio= $datos[$i]['horario'];
				$hora_fin = $datos[$i]['horario_fin'];
				$lugar =$datos[$i]['lugar'];
				$motivo = $datos[$i]['motivo'];
				$autoriza_sup = $datos[$i]['autoriza_sup'];
				$autoriza_aut = $datos[$i]['autoriza_aut'];
				$datos_correo ['legajo'] = $legajo;
				$datos_correo ['apellido'] = $apellido;
				$datos_correo ['nombre'] = $nombre;
				$datos_correo ['fecha_inicio'] = $fecha_inicio;
				$datos_correo ['fecha_fin'] = $fecha_fin;
				$datos_correo ['hora_inicio'] = $hora_inicio;
				$datos_correo ['horario_fin'] = $hora_fin;
				$datos_correo ['lugar'] = $lugar;
				$datos_correo ['motivo'] = $motivo;
				
				$this->s__datos_correo=$datos_correo;
				$sql= "SELECT email from reloj.agentes_mail
				where legajo=$legajo";
				$correo = toba::db('ctrl_asis')->consultar($sql);
		
				
				
		

				if ($estado=='C'&& (($autoriza_sup === 1 )|| ($autoriza_aut === 1))) {	 
						if ($autoriza_aut === 1){
							$autoridad_aut= true;
						} else {
							$autoridad_aut= false;
						}
						if ($autoriza_sup === 1) {
							$superior_aut =true;
						} else {
							$superior_aut =false;
						}
						$edad = $this->dep('mapuche')->get_edad($legajo, null);
						$direccion = $this->dep('mapuche')->get_datos_agente($filtro);
						$domicilio = $direccion [0]['calle'] ||' '|| $direccion[0]['numero'];
						$localidad= $direccion[0]['localidad'];
						$agrupamiento = $direccion[0]['escalafon'];
						$fecha_nacimiento = $direccion[0]['fecha_nacimiento'];
						$usuario_alta = $legajo;
						$usuario_cierre= toba::usuario()->get_id();
						$fecha_alta    = date("Y-m-d H:i:s");
						$fechaentera1 =strtotime($fecha_inicio);
					//$january = new DateTime($datos[$i]['fecha_fin']);
					//$february = new DateTime($datos[$i]['fecha_fin']);
						$fecha_inicio1 = date_create(date("Y-m-d",$fechaentera1)); 
						$hoy=date_create(date("Y-m-d",strtotime($fecha_fin)));
					//$dia = $february->diff($january);
						$dia = date_diff($fecha_inicio1 , $hoy);
						$dias = $dia->format('%a') +1 ;
		
						$fecha_ini=$datos[$i]['fecha'];
		
						$estado_civil = $direccion[0]['estado_civil'];
						if ($agrupamiento == 'DOCE') {
							$id_motivo = 56;
							$id_decreto = 2 ;
							$id_articulo = 104;
						} else {
							$id_decreto = 5;
							$id_articulo = 104;
							$id_motivo = 56;
						}
						$sexo=$this->dep('mapuche')->get_tipo_sexo($legajo, null);
						$sql= "UPDATE reloj.comision
						SET observaciones = '$obs', pasada = true ,autoriza_sup = $superior_aut , autoriza_aut = $autoridad_aut
						WHERE id_comision = $id";
						toba::db('ctrl_asis')->ejecutar($sql);	
						
						$sql = "INSERT INTO reloj.parte(
							legajo, edad,id_comision, fecha_alta, usuario_alta, estado, fecha_inicio_licencia, dias, cod_depcia, domicilio, localidad, agrupamiento, fecha_nacimiento,
							apellido, nombre, estado_civil, observaciones, id_decreto, id_motivo, id_articulo, tipo_sexo,usuario_cierre,fecha_cierre)
							VALUES ($legajo, $edad,$id_comision, '$fecha_alta', '$usuario_alta', '$estado', '$fecha_ini', $dias, '04', '$domicilio', '$localidad', '$agrupamiento', '$fecha_nacimiento',
							'$apellido', '$nombre',    '$estado_civil', '$obs', $id_decreto,  $id_motivo,	  $id_articulo,'$sexo','$usuario_cierre','$fecha_cierre');";
						toba::db('ctrl_asis')->ejecutar($sql);
						

						$this->enviar_correos($correo[0]['email'],true);
						} else  if ($estado =='C'&& (($autoriza_sup == 0 )&& ($autoriza_aut == 0 ))) {
						
						$this->enviar_correos($correo[0]['email'],false );			
					
						} 
					if ($estado == 'C') {
									
									$sql= "UPDATE reloj.comision
								SET observaciones = '$obs', pasada = true 
								WHERE id_comision = $id";
								toba::db('ctrl_asis')->ejecutar($sql);	
							}	
					}	
				 
				
				}


			 
			
			
		
	}
		

	//-----------------------------------------------------------------------------------
	//---- filtro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__filtro(ctrl_asis_ei_filtro $filtro)
	{
	}
	function evt__filtro__filtrar($datos)
	{
		$this->s__datos_filtro = $datos;

	}

	function evt__filtro__cancelar()
	{
		unset($this->s__datos_filtro);
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
$mail->AddReplyTo('caifca@fca.uncu.edu.ar','El de la réplica');
//Y, ahora sí, definimos el destinatario (dirección y, opcionalmente, nombre)
$mail->AddAddress($correo, $datos['agente_ayn']);
//Definimos el tema del email
$mail->Subject = 'Solicitud de Comision de Servicio';
//Para enviar un correo formateado en HTML lo cargamos con la siguiente función. Si no, puedes meterle directamente una cadena de texto.
//$mail->MsgHTML(file_get_contents('correomaquetado.html'), dirname(ruta_al_archivo));
//Y por si nos bloquean el contenido HTML (algunos correos lo hacen por seguridad) una versión alternativa en texto plano (también será válida para lectores de pantalla)
$mail->IsHTML(true); //el mail contiene html*/

	
//     ei_arbol($fecha,$hasta);
//

	if ($aprobado) {
		
		
			$body = '<table>
						Al agente  <b>'.$datos['agente_ayn'].'</b> se aprueba la Solicitud de Comision de Servicio </b> <br/>
						con motivo de' . $datos['motivo'].' en '. $datos['lugar']. ' iniciando el dia ' .$datos['fecha_inicio']. ' en el horario '. $datos['hora_inicio']. ' y finalizando el dia' .$datos['fecha_fin']. ' en el horario '.$datos['hora_fin']. ' <br/> 
						Saluda atte Direccion de Personal.
											
				</table>';

		} else
		{
			$body = '<table>
						Al agente  <b>'.$datos['agente_ayn'].'</b> le ha sido rechazada  la Solicitud de Comision de Servicio </b>.<br/>
						con motivo de' . $datos['motivo'].' en '. $datos['lugar']. ' iniciando el dia ' .$datos['fecha_inicio']. ' en el horario '. $datos['hora_inicio']. ' y finalizando el dia' .$datos['fecha_fin']. ' en el horario '.$datos['hora_fin']. ' <br/> 
						Saluda atte Direccion de Personal.
											
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