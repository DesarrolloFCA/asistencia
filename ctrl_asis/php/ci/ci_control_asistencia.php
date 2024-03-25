<?php
class ci_control_asistencia extends ctrl_asis_ci
{
	protected $s__datos_filtro;
	protected $s__datos;

	protected $s__seleccion;
	protected $s__limite_envio_masivo = 5;

	//-----------------------------------------------------------------------------------
	//---- Configuraciones 2023-08-08 10:49 ---------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf()
	{
	}

	//---- Filtro -----------------------------------------------------------------------

	function conf__filtro(toba_ei_formulario $filtro)
	{
		if (isset($this->s__datos_filtro)) {
			
			$filtro->set_datos($this->s__datos_filtro);
		}else{
			$f['anio'] = date("Y");
			$f['mes']  = date("m");
			if(!empty($_SESSION['dependencia'])){ 
				$f['cod_depcia']  =   $_SESSION['dependencia'];
			}
			if(!empty($_SESSION['agente'])){ 
				$f['legajo']  = $_SESSION['agente'];
			}
			$filtro->set_datos($f);
			
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


	//---- Cuadro resumen -------------------------------------------------------------------

	function conf__cuadro_resumen(toba_ei_cuadro $cuadro)
	{
		
		//$limit = $cuadro->get_tamanio_pagina();
		//$offset = $limit * ($cuadro->get_pagina_actual() - 1);

		

		if (isset($this->s__datos_filtro)) {
			
			// ORiginal
			/* if (isset($this->s__datos_filtro['anio'])) {
				//  $y = $this->s__datos_filtro['anio'];
				// $m = $this->s__datos_filtro['mes'];
				//  $d = date("d",(mktime(0,0,0,$m+1,1,$y)-1));

				$this->s__datos_filtro['fecha_desde'] = $y."-".$m."-01";
				$this->s__datos_filtro['fecha_hasta'] = $y."-".$m."-".$d;
			}*/
			//Modificacion
			if(isset($this->s__datos_filtro['catedra'])){
			$id_catedra = $this->s__datos_filtro['catedra'];
			$sql = "SELECT count(*) cant from reloj.catedras_agentes
					WHERE id_catedra = $id_catedra";
			$cant_agente = toba::db('ctrl_asis')->consultar($sql);
			} else {
				$cant_agente[0]['cant'] = 1;
			}
			//ei_arbol($cant_agente);
			if($cant_agente[0]['cant'] > 0){
				if (isset($this->s__datos_filtro['fecha_inicio'])) {
					$fecha1 = $this->s__datos_filtro['fecha_inicio'];
					$fechaentera1 =strtotime($fecha1);
					$y =date("Y",$fechaentera1);
					$m =date("m",$fechaentera1);
					$d =date("d",$fechaentera1);
					$this->s__datos_filtro['fecha_desde'] = $y."-".$m."-".$d;
					$fecha2 = $this->s__datos_filtro['fecha_fin'];
					$fechaentera2 =strtotime($fecha2);
					$y2 =date("Y",$fechaentera2);
					$m2 =date("m",$fechaentera2);
					$d2 =date("d",$fechaentera2);
					//$a=$y2."-".$m2."-".$d2;
					$this->s__datos_filtro['fecha_hasta'] = $y2."-".$m2."-".$d2;
					
						//}

				}
				
			//En caso de que no venga ningun dato en el filtro de base de datos de marcas
			if(empty($this->s__datos_filtro['basedatos'])){
				$this->s__datos_filtro['basedatos'] = "access";
			}
			
			$_SESSION['fecha_desde'] = $this->s__datos_filtro['fecha_desde'];
			$_SESSION['fecha_hasta'] = $this->s__datos_filtro['fecha_hasta'];
			$_SESSION['basedatos']   = $this->s__datos_filtro['basedatos'];  
			

			if($_SESSION['dependencia']  == '64'){ // damsu

			//---------------------------------------------------------------------------------------------------
			
			$agentes_total =  $this->dep('damsu')->get_agentes_control_asistencia($this->s__datos_filtro);
			$total_registros = count($agentes_total);
			unset($agentes_total);

			$agentes =  $this->dep('damsu')->get_agentes_control_asistencia($this->s__datos_filtro, 'LIMIT '.$limit, 'OFFSET '.$offset);

			//---------------------------------------------------------------------------------------------------

			}else{
			//---------------------------------------------------------------------------------------------------
			
			

			$agentes_total =  $this->dep('mapuche')->get_agentes_control_asistencia($this->s__datos_filtro);
			$total_registros = count($agentes_total);
			
			
			unset($agentes_total);

			$agentes =  $this->dep('mapuche')->get_agentes_control_asistencia($this->s__datos_filtro, 'LIMIT '.$limit, 'OFFSET '.$offset);
			
			
			//---------------------------------------------------------------------------------------------------
			}
			
			$filtro['fecha_desde'] = $this->s__datos_filtro['fecha_desde'];
			$filtro['fecha_hasta'] = $this->s__datos_filtro['fecha_hasta'];
			$filtro['marcas']= $this->s__datos_filtro['marcas'];
			if (isset($this->s__datos_filtro['basedatos'])) {
			$filtro['basedatos'] = $this->s__datos_filtro['basedatos'];
			}
			$this->s__datos = $this->dep('access')->get_lista_resumen($agentes,$filtro);
		
			//ei_arbol($agentes);
			unset($agentes);

			$f = $this->s__datos;

			$total_registros = count($f);
			
			
		

			if ($this->s__datos_filtro ['marcas']== 1) {
				$this->s__datos = array_filter($this->s__datos, function ($f) {
				return $f['presentes'] > 0 ;});	
			} else if ($this->s__datos_filtro ['marcas']== 0){
				$this->s__datos = array_filter($this->s__datos, function ($f) {
				return $f['presentes'] == 0 ;});	
			} 
			
			unset($f);
			
			
			$e = $this->s__datos;
			
		
			
			if (isset($this->s__datos_filtro['catedra'])){
			$catedras = $this->dep('datos')->tabla('catedras')->get_catedra($this->s__datos_filtro['catedra']);
			
				if ($catedras[0]['id_departamento'] == 6 Or $catedras[0]['id_departamento'] == 10) {
				$this->s__datos_filtro ['agrup'] = 'paa';
				}else 
				{	
				$this->s__datos_filtro ['agrup'] = 'doc';
				}
				//$agentes_0 = true;
			/*}else {
				toba::notificacion()->agregar('No existen agentes en la catedra u oficina seleccionada', "info");
				//$agentes_0 = false;
				
			}*/
			}
			
			$tot = $e['total'];
			//ei_arbol($agentes_0);
			//if ($agentes_0){
			for($m = 0; $m<$tot;$m++){
				 if ($e[$m]['agrupamiento'] == 'CORF') {
			 	$e[$m]['agrupamiento'] = 'DOCE';
			 	$this->s__datos[$m]['agrupamiento'] ='DOCE';

			
				 }
			}
			
			if(isset($this ->s__datos_filtro['agrup'])){
				$tot = $e['total'];
				//for($m = 0; $m<$tot;$m++){
				if (($this ->s__datos_filtro['agrup']) == 'doc'){
			
				$this->s__datos = array_filter($this->s__datos, function ($e) {
				return $e['agrupamiento'] == 'DOCE';});
				//$this->s__datos['total'] =count($this->s__datos);
			
				}else { 
				$this->s__datos = array_filter($this->s__datos, function ($e) {
				return $e['agrupamiento'] <> 'DOCE';});    
				//$this->s__datos['total'] =count($this->s__datos);      
			
			}
				//}
			}

			$this->s__datos = $e; //para agregar catedras y parcelas
			unset($e);
			$todo =	array_values($this->s__datos);		
			//	ei_arbol($todo);
			$registros = count($todo); 	
			//$hasta = $this->s__datos['total'] +1;
			for ($i = 0;$i<$registros;$i++){
				$horas_esp = $this->dep('datos')->tabla('conf_jornada')->get_horas_diarias($todo[$i]['legajo']);
				//ei_arbol($horas_esp);
				if(isset($horas_esp[0]['horas'])){
					$horas_diarias = '0'.$horas_esp[0]['horas'].':00';
				
				} else {
				/*switch ($todo[$i]['cant_horas']){
					case 10 :  
					$horas_diarias= '01:24';
								break;	
					case 20 : 
					$horas_diarias= '02:48';
								break;	
					case 30 :
					$horas_diarias = '04:12';
							break;			
					case 40:
					$horas_diarias = '05:36';
						break;
					case 35:
					$horas_diarias = '06:00';
					break;	

				} */
				switch ($todo[$i]['cant_horas']){
					case 10 :  
					$horas_diarias= '01:24';
					//$requerido = '28:00';
					$todo[$i]['dedicacion'] = 'SIMPLE';
								break;	
					case 20 : 
					$horas_diarias= '02:48';
					//$requerido = '56:00';			
					
					$todo[$i]['dedicacion'] = 'SEMIEXCLUSIVA';
								break;	
					case 30 :
					$horas_diarias = '04:12';
					//$requerido = '84:00';
							break;			
					case 40:
					//$requerido = '112:00';
					$horas_diarias = '05:36';
					$todo[$i]['dedicacion'] = 'EXCLUSIVA';
						break;
					case 35:
					$horas_diarias = '06:00';
					//$requerido = '120:24';
					break;	

				} 
				}
				
				$tmp= 0;
						$dias_laborales = $todo[$i]['laborables'];
						$dias_trab = $todo[$i]['laborables'] - $todo[$i]['justificados'];
					//	ei_arbol($todo);
						//ei_arbol($dias_trab);
						// guardo horas diarias

						$horas_min = explode(":",$horas_diarias);
						$todo[$i]['h_min'] = $horas_min[0] +($horas_min[1]/60);

						//Horas totales ideales trabajadas
					//	ei_arbol($horas_min);
						//$horas= $dias_trab * $horas_min[0];
						$horas= $dias_laborales * $horas_min[0];
						// Calculos de minutos
						//$minutos = $dias_trab * $horas_min[1];
						$minutos = $dias_laborales * $horas_min[1];
						while ($minutos >= 60){
							$minutos = $minutos - 60;
							$tmp ++;
						}

						$horas = $horas + $tmp;
						
						if($minutos < 10 or $minutos == 0) {
							$minutos = '0'.$minutos;
						} 

						$requerido = $horas .':'.$minutos;
						//ei_arbol($requerido);
						
						$todo[$i]['horas_requeridas_prom']= $requerido;
			}
			//ei_arbol($todo);
			
			/*for ($h=0; $h <= $registros; $h++)
			{
				
				if ($h<>0) {
					$k = $h-1;
					$legajo_actual = ($todo[$h]['legajo']);

					if ($legajo_actual ==''){
						//unset($todo[$h]);	
					}else {
					$legajo_ant = ($todo[$k]['legajo']);
					//$requerido = $todo [$h]['cant_horas'];
					//$todo [$h]['horas_requeridas_prom'] =$requerido;
					//ei_arbol($todo);
			
						if ($legajo_ant ==$legajo_actual){ 
						$tmp = 0;
						//$requerido = ($todo [$k]['cant_horas'] + $todo [$h]['cant_horas']);
						$horas_1 =explode(":",$todo [$k]['horas_requeridas_prom']);
						$horas_2 = explode(":",$todo [$h]['horas_requeridas_prom']);
						$hora=$horas_1[0]+$horas_2[0];
						$min = $horas_1[1]+$horas_2[1];
						while ($min >= 60){
							$min = $min - 60;
							$tmp ++;
						}
						$hora=$hora+$tmp;
						if($minutos < 10) {
							$minutos = '0'.$minutos;
						}
						$requerido=$hora .':'.$min;
						//$requerido = ($todo [$k]['horas_requeridas_prom']) + ($todo [$h]['horas_requeridas_prom']);
						
						$todo [$k]['horas_requeridas_prom'] =$requerido;


						//unset($todo[$h]);
					}
					//ei_arbol($todo);
					// equivalencia dias

					//$requerido = $todo [$k]['horas_requeridas_prom'] /5 ;
					/*ei_arbol ($requerido);
						switch($requerido) {
							case 15 :
								$horas_diarias = '10:48';
								break;
							case 11:
								$horas_diarias= '08:00';
								break;
							case 10 :
								$horas_diarias = '05:36';
								break;
							case 9 :
								$horas_diarias= '06:48';
								break;
							case 8 :	
								$horas_diarias= '04:48';
								break;
							case 7 : 
								$horas_diarias= '06:00';
								break;
							case 6 : 
								$horas_diarias= '03:18';
								break;
							case 4 : 
								$horas_diarias= '02:00';
								break;	
							case 2 : 
								$horas_diarias= '00:48';
								break;	
							}
						$tmp= 0;
						$dias_trab = $todo[$k]['laborables'] -  $todo[$k]['feriados'] - $todo[$k]['justificados'];
						$horas_min = explode(':',$$horas_diarias);
						//Horas totales ideales trabajadas
						$horas= $dias_trab * $horas_min[0];
						// Calulos de minutos
						$minutos = $dias_trab * $horas_min[1];
						while ($minutos >= 60){
							$minutos = $minutos - 60;
							$tmp ++;
						}

						$horas = $horas + $tmp;
						if($minutos < 10) {
							$minutos = '0'.$minutos;
						}
						$requerido = $horas .':'.$minutos;
						ei_arbol ($requerido);
					}
				
				}
			}*/
					
			//	ei_arbol($todo);
			$todos =	array_values($todo);		
			$registros = count($todos)  ; 
			unset($todo);
			//ei_arbol($todos);
						
			for ($l = 0; $l < $registros; $l++){
				$leg = $todos [$l]['legajo'];
				$mail = $this->dep('datos')->tabla('agentes_mail')->get_legajo_mail($leg);
				$todos[$l]['email']=$mail[0]['email'];
			
					if ($leg <> null or $leg > 10000){
					$sql = "Select nombre_catedra from reloj.vw_catedra_agente a
					where legajo = $leg ;";
					$catedras = toba::db('ctrl_asis')->consultar($sql); 
					$sql = "SELECT email FROM reloj.agentes_mail
		      		 where legajo = $leg";
		      		$email= toba::db('ctrl_asis')->consultar($sql);
		      		 // ei_arbol($email);
		      		$todos[$l]['email']=$email[0]['email'];


				$cant_catedra = count($catedras);
			
						if ($cant_catedra == 1) {
						$todos[$l]['catedra'] = $catedras[0]['nombre_catedra'];
						}else
						{
							for ($m = 0; $m < $cant_catedra; $m++){
								if ($m == 0) {
								$area = $catedras[$m]['nombre_catedra'];
								} else
								{
								$area = $area.", ".$catedras[$m]['nombre_catedra'];
								}
							}
						$todos[$l]['catedra'] = $area;
						}
					}
						
					
			}
			//ei_arbol(round((memory_get_usage()/(1024*1024)),2));
			//ei_arbol($todos);
			$lim = count($todos);
			/*for ($l=0;$l<$lim;$l++){

				$tot=$todos[$l]['horas_totales'];
				$h_tot = explode(":",$tot);
				
// Ver dias Equivantes realizar calculos
				$req =$todos[$l]['horas_requeridas_prom'];
				$h_req =explode(":",$req);
				// Equivalencia Dias
				if ($todos[$l]['escalafon'] == 'DOCE'){
				//$ho_dia= explode(":",$horas_diarias);
				$ho_totales = $h_tot[0]+($h_tot[1]/60);
				
				$dias_eq = $ho_totales/$todos[$l]['h_min'];
				$todos[$l]['presentes'] = intval($ho_totales/$todos[$l]['h_min']);
				$trab = $todos[$l]['laborables'] - $todos[$l]['presentes'] ;
					if ($trab > 0) {
					$todos[$l]['ausentes'] =$trab;
					$todos[$l]['injustificados'] = $trab -( $todos[$l]['partes'] + $todos[$l]['partes_sanidad']);
					}else {
					$todos[$l]['ausentes'] = 0;
					$todos[$l]['injustificados'] = 0;
					}
				}
				if ($h_tot[0] < $h_req[0]) {
					$todos[$l]['horas_totales'] = $todos[$l]['horas_totales'];
				} else if ($h_tot[0] == $h_req[0]){
						if ($h_tot[1] < $h_req[1] ){
							$todos[$l]['horas_totales'] = $todos[$l]['horas_totales'];
						} 
				}				
				
				$todos[$l]['desviacion_horario'] = $this->restar_horas($todos[$l]['horas_requeridas_prom'],$todos[$l]['horas_totales']);
				if($todos[$l]['horas_requeridas_prom']> $todos[$l]['horas_totales']){
					$todos[$l]['desviacion_horario'] = '-'.$todos[$l]['desviacion_horario'] ;
				}
						
					
			}*/
			$this ->s__datos = $todos;
		//	ei_arbol($todos);
			unset($todos);
			$this->s__datos['total'] =count($this->s__datos); 
			
			
			/*if($this->s__datos_filtro['marcas']== 1) {
				$cuadro->set_datos($this->s__datos); 
				$e = $this->s__datos;
				$temp = array_filter( $this->s__datos, function( $e ) {
				return $e['presentes'] == 1;
			
				});
				ei_arbol('estoy dentro de 1 ');
				$cuadro-> set_datos($temp);  
				
				} elseif ($this->s__datos_filtro['marcas']== 0) {
				$cuadro->set_datos($this->s__datos); 
				//$h = $this->s__datos;
			
				
				$temp = array_filter( $this->s__datos, function( $h ) {
				return ( $h[24]['presentes']== 0);
				});
			
				$cuadro-> set_datos($temp);
				
				} else {
				$cuadro->set_total_registros($total_registros);
				$cuadro->set_datos($this->s__datos);  
				}*/
			//$total= count($this->s__datos);
			
			//$cuadro->set_total_registros($total_registros);
			//ei_arbol($this->s__datos);
			
			
			$cuadro->set_total_registros($this->s__datos['total']);
			$cuadro->set_datos($this->s__datos);
			list($y,$m,$d) = explode('-', $this->s__datos_filtro['fecha_desde']);
			$fecha_desde = "$d/$m/$y";
			list($y,$m,$d) = explode('-', $this->s__datos_filtro['fecha_hasta']);
			$fecha_hasta = "$d/$m/$y";  
			if (isset($this->s__datos_filtro['catedra'])){
				$catedras = $this->dep('datos')->tabla('catedras')->get_catedra($this->s__datos_filtro['catedra']);
				$catedra = $catedras[0]['nombre_catedra'];
				//ei_arbol ($catedra);
				$cuadro->set_titulo("Asistencia desde el ".$fecha_desde.", hasta el ".$fecha_hasta." de " . $catedra);
			} else {	
			$cuadro->set_titulo("Asistencia desde el ".$fecha_desde.", hasta el ".$fecha_hasta);
			}
			
			
		}
		unset($cuadro);
		} // End de $agentes_0
		}

	function evt__cuadro_resumen__multiple($seleccion)
	{
		$this->s__seleccion = $seleccion;
	}
	
	function evt__cuadro_resumen__enviar($datos)
	{
		$this->s__seleccion[0]['legajo'] = $datos['legajo'];
		$this->enviar_asistencia($this->s__seleccion);
	}

		function conf_evt__cuadro_resumen__enviar($evento, $fila)
		{
			if (empty($this->s__datos[$fila]['email'])) {  $evento->anular();   }
		}
		function conf_evt__cuadro_resumen__multiple($evento, $fila)
		{
			if (empty($this->s__datos[$fila]['email'])) {  $evento->anular();   }
		}  

	/*function evt__cuadro__seleccion($datos)
	{
			$this->dep('datos')->cargar($datos);
			$this->set_pantalla('pant_edicion');
	}*/

	//---- Cuadro resumen -------------------------------------------------------------------

	function conf__cuadro_imprimir(toba_ei_cuadro $cuadro)
	{
		if (isset($this->s__datos)) {
			$cuadro->set_datos($this->s__datos);
			

			list($y,$m,$d) = explode('-', $this->s__datos_filtro['fecha_desde']);
			$fecha_desde = "$d-$m-$y";
			list($y,$m,$d) = explode('-', $this->s__datos_filtro['fecha_hasta']);
			$fecha_hasta = "$d-$m-$y";  
			$cuadro->set_titulo("Asistencia desde el ".$fecha_desde.", hasta el ".$fecha_hasta);
			
		}
	}

	//---- FUNCIONES -------------------------------------------------------------------

	function resetear()
	{
		#$this->dep('datos')->resetear();
		unset($this->s__datos);
		unset($this->s__seleccion);
		$this->set_pantalla('pant_seleccion');
	}

	function vista_excel(toba_vista_excel $salida)
	{
		
		$excel = $salida->get_excel();
		$excel->setActiveSheetIndex(0);
		$excel->getActiveSheet()->setTitle('Control de asistencia');
		$this->dependencia('cuadro_imprimir')->vista_excel($salida);

	}
	function vista_pdf (toba_vista_pdf $salida)
	{
		$salida->set_papel_orientacion('landscape');
		$salida->inicializar();
		$pdf =$salida->get_pdf();
		
		
		
		$pdf ->ezText("<b>Control de Asistencia</b>", 11, array( 'justification' => 'center' ));
		$this->dependencia('cuadro_imprimir')->vista_pdf($salida);
	}

	function enviar_asistencia($seleccion)
	{
		require_once('3ros/phpmailer/class.phpmailer.php');

		
		if(count($seleccion)>0){
			
		foreach($seleccion as $s){

			foreach($this->s__datos as $d){

				//set dato a enviar
				if($s['legajo'] == $d['legajo']){
					$datos = $d;
					break;
				}
			}
		//	ei_arbol($datos);
			if(!empty($datos['email'])){
				//---------------------------------------------------------------------

				//Completamos parametros que se envian con la funcion de envio de mensajes por email -----------------    
				$email_destino =    $datos['email'];                         
				$parametros['correo_destino']           = $email_destino; 
				#$parametros['reply_email']              = $vendedor['email_contacto']; 
				#$parametros['reply_nombre']             = $vendedor['razon_vendedor']; 

				list($y,$m,$d) = explode('-', $this->s__datos_filtro['fecha_desde']);
				$fecha_desde = "$d-$m-$y";
				list($y,$m,$d) = explode('-', $this->s__datos_filtro['fecha_hasta']);
				$fecha_hasta = "$d-$m-$y";  
				$parametros['asunto']                   = $datos['nombre_completo'].' - Asistencia desde el '.$fecha_desde.', hasta el '.$fecha_hasta; 
				$parametros['contenido_mensaje']        = '<div>
										<p></p> 
										<p>DETALLE ASISTENCIA:</p>
										<p></p> 
							</div>';

				$parametros['encabezado_mensaje']      = 'Asistencia desde el '.$fecha_desde.', hasta el '.$fecha_hasta;
				$parametros['encabezado_mensaje_txt']  = strip_tags($parametros['encabezado_mensaje']);                                                                
				$parametros['contenido_mensaje_txt']   = strip_tags($parametros['contenido_mensaje']);

				#$parametros['adjunto1']                = $path.$nombre_fichero;
				#$parametros['correo_copia']           = $vendedor['email_contacto'];
				$parametros['correo_copia_oculta']     = $vendedor['email_contacto'];

				try {
					$this->enviar_mail($parametros);
					toba::notificacion()->agregar("El mensaje se ha enviado correctamente al correo ".$email_destino.".", "info");

				} catch (Exception $e) {

					$error = 'Excepción capturada: '.$e->getMessage();
					toba::notificacion()->agregar($error, "error");

					$error = "Problemas enviando correo electrónico.<br/>".$mail->ErrorInfo;
					toba::notificacion()->agregar($error, "error");
				}

				//---------------------------------------------------------------------------------------------------
			}

		}
		}else{
			toba::notificacion()->agregar('No hay selección', "info");

		}

	}

		function enviar_mail($parametros,$uso='predeterminado'){

		//Obtengo datos necesarios para mandar correo
		#$datos_correo =   toba::tabla('correo_envio')->get_correo_envio_por_uso($uso);
		/*
		correo_destino
		asunto
		encabezado_mensaje
		encabezado_mensaje_txt
		contenido_mensaje
		contenido_mensaje_txt
		reply_email y reply_nombre opcionales
		*/
		require('enviar_mail.php');
		//return true;
	}

	//---- EVENTOS CI -------------------------------------------------------------------

	function evt__envio_seleccion()
	{  
		$this->enviar_asistencia($this->s__seleccion);
	}

	function evt__envio_masivo()
	{

		if(count($this->s__datos)>0){
			unset($this->s__seleccion);
			$cont = 0;
			$limite = $this->s__limite_envio_masivo;
			foreach($this->s__datos as $dato){
				if( !empty($dato['email']) and $cont < $limite ){
					$this->s__seleccion[]['legajo'] = $dato['legajo'];
					$cont++;
				}
			}
		}

		if(count($this->s__seleccion)>0){
			$this->enviar_asistencia($this->s__seleccion);
		}else{
			toba::notificacion()->agregar("No hay datos para enviar.", "error");
		}
	}
	function restar_horas($hora1,$hora2)
	{
	
	$timei = explode(':',$hora1);
	$time1 = $timei[0]*3600 +$timei[1]*60;
	$timef = explode(':',$hora2);
	$time2 = $timef[0]*3600 +$timef[1]*60;
	//$time1 = strtotime($hora1);
    //$time2 = strtotime($hora2);
	//	$time1 = $hora1;
	//	$time2 = $hora2;

    $diff = $time1 - $time2;
    //ei_arbol(strtotime($time1),$time2);

    if ($diff >= 0) {
        $signo = "+";
        $horas = floor($diff / 3600);
        $minutos = floor(($diff % 3600) / 60);
    } else {
        $signo = "-";
        $horas = floor(abs($diff) / 3600);
        $minutos = floor((abs($diff) % 3600) / 60);
    }

    $resultado = sprintf("%s%02d:%02d", $signo, $horas, $minutos);

    return $resultado;
	}

	
	//-----------------------------------------------------------------------------------
	//---- cuadro_rectorado -------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro_rectorado(ctrl_asis_ei_cuadro $cuadro)
	{
		ei_arbol(s__datos);
	if (isset($this->s__datos)) {
			
			
			$cuadro->set_datos($this->s__datos);
			

			list($y,$m,$d) = explode('-', $this->s__datos_filtro['fecha_desde']);
			$fecha_desde = "$d-$m-$y";
			list($y,$m,$d) = explode('-', $this->s__datos_filtro['fecha_hasta']);
			$fecha_hasta = "$d-$m-$y";  
			$cuadro->set_titulo("Asistencia desde el ".$fecha_desde.", hasta el ".$fecha_hasta);
			
		}	
	}

}
?>