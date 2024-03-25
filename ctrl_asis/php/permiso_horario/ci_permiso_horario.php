<?php
class ci_permiso_horario extends ctrl_asis_ci
{
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
			$a_aut=$datos[$i]['auto_aut'];
			$obs = $datos[$i]['observaciones'];
			
			$sql = "UPDATE reloj.permisos_horarios
					Set aut_sup = $a_sup, auto_aut = $a_aut, observaciones = '$obs'
					where id_permiso = $id";
			//ei_arbol($sql);		
			toba::db('ctrl_asis')->ejecutar ($sql);    
			
			/*	$id= $datos[$i]['id_comision'];
				$legajo = $datos[$i]['legajo'];
			//	ei_arbol ($datos[$i]['pasada']);
				if ($datos[$i]['procesado']  == 1){
					$estado = 'C';
				} else {
					$estado = 'A';
				}
				//ei_arbol ($estado, $i);
				
				$ayn= $this->dep('mapuche')->get_legajos_autoridad($datos[$i]['legajo']);
				$apellido=$ayn[0]['apellido'];
				$nombre= $ayn[0]['nombre']; 
				$fecha_inicio = $datos[$i]['fecha'];
				$fecha_fin = $datos[$i]['fecha_fin'];
				$hora_inicio= $datos[$i]['horario'];
				$hora_fin = $datos[$i]['horario_fin'];
				$lugar =$datos[$i]['lugar'];
				$motivo = $datos[$i]['motivo'];
				$autoriza_sup = $datos[$i]['aut_sup'];
				$autoriza_aut = $datos[$i]['auto_aut'];
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
			//	ei_arbol($datos[$i]['pasada'] );
				
				
			//	ei_arbol($estado);

				if ($estado=='C'&& (($autoriza_sup == 1 )|| ($autoriza_aut == 1))) {	 
						if ($autoriza_aut == 1){
							$auto_aut= true;
						} else {
							$auto_aut= false;
						}
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
						$dias = $dia->format('%a') +1 ;
					//ei_arbol($dias);
						$fecha_ini=$datos[$i]['fecha'];
					//	ei_arbol($fecha_ini);
						$estado_civil = $direccion[0]['estado_civil'];
						$id_decreto = 5;
						$id_articulo = 104;
						$id_motivo = 56;
						$sexo=$this->dep('mapuche')->get_tipo_sexo($legajo, null);
						
						$sql = "INSERT INTO reloj.parte(
							legajo, edad, fecha_alta, usuario_alta, estado, fecha_inicio_licencia, dias, cod_depcia, domicilio, localidad, agrupamiento, fecha_nacimiento,
							apellido, nombre, estado_civil, observaciones, id_decreto, id_motivo, id_articulo, tipo_sexo,usuario_cierre,fecha_cierre)
							VALUES ($legajo, $edad, '$fecha_alta', '$usuario_alta', '$estado', '$fecha_ini', $dias, '04', '$domicilio', '$localidad', '$agrupamiento', '$fecha_nacimiento',
							'$apellido', '$nombre',    '$estado_civil', '$observaciones', $id_decreto,  $id_motivo,	  $id_articulo,'$tipo_sexo','$usuario_cierre','$fecha_cierre');";
						toba::db('ctrl_asis')->ejecutar($sql);
						

						$this->enviar_correos($correo[0]['email'],true);
						} else  if ($estado =='C'&& (($autoriza_sup == 0 )&& ($autoriza_aut == 0 ))) {
						
						$this->enviar_correos($correo[0]['email'],false );			
					
						} 
					if ($estado == 'C') {
									
								$sql= "UPDATE reloj.comision
								SET observaciones = '$obs', procesado = true 
								WHERE id_comision = $id";
								toba::db('ctrl_asis')->ejecutar($sql);	
							}*/	
					}	
				 
				
			}	
			

	}

	function conf__formulario(toba_ei_formulario_ml $componente)
	{
		//$componente->set_datos($this->dep('datos')->get_filas());
		$sql = "SELECT  *  FROM reloj.permisos_horarios
					WHERE procesado is null
					";
		$listado = toba::db('ctrl_asis')->consultar($sql);
		$componente->set_datos($listado);
	}

}

?>