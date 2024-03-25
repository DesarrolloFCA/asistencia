<?php
ini_set('memory_limit','512M');
class dt_marca extends toba_datos_tabla
{
	function get_listado($filtro=array())
	{
		$where = array();
		#if (isset($filtro['agente_id'])) {
		#    $where[] = "t_m.agente_id = ".quote($filtro['agente_id']);
		#}
		if (isset($filtro['fecha'])) {
			$where[] = "t_m.fecha = ".quote($filtro['fecha']);
		}
		if (isset($filtro['estado_marca_id'])) {
			$where[] = "t_m.estado_marca_id = ".quote($filtro['estado_marca_id']);
		}
		if (isset($filtro['fecha_desde'])) {
				list($y,$m,$d)=explode("-",$filtro['fecha_desde']); //2011-03-31
				$fecha_desde = $y."-".$m."-".$d;
				$where[] = "t_m.fecha >= ".quote($fecha_desde);
		}
		if (isset($filtro['fecha_hasta'])) {
				list($y,$m,$d)=explode("-",$filtro['fecha_hasta']); //2011-03-31
				$fecha_hasta = $y."-".$m."-".$d." 23:59:59";
				$where[] = "t_m.fecha <= ".quote($fecha_hasta);
		}        
		#if (isset($filtro['dependencia_id'])) {
		#    $where[] = "t_a.dependencia_id = ".quote($filtro['dependencia_id']);
		#}
		if (isset($filtro['legajo'])) {
			$where[] = "t_m.legajo = ".quote($filtro['legajo']);
		}        
			
		$sql = "SELECT
					t_m.marca_id,
					t_m.agente_id,
					t_m.fecha,
					t_m.entrada,
					t_m.salida,
					t_m.estado_marca_id,
					t_m.observaciones,
					t_m.reloj,
					t_m.legajo
				FROM marca as t_m
				ORDER BY t_m.fecha ASC";

				/*
				,
					t_a.dependencia_id,
					t_a.area_id,
					t_a.cuil,
					t_cem.estado_marca,
					t_a.agente_id as agente_id_nombre,
					t_cem.estado_marca as estado_marca_id_nombre
				*/

		if (count($where)>0) {
			$sql = sql_concatenar_where($sql, $where);
		}

		$datos = toba::db('ctrl_asis')->consultar($sql);

		if(isset($filtro['calcular_horas']) and count($datos) > 0){

			foreach($datos as $key=>$dato){
				$datos[$key]['horas']     = $this->restar_horas($dato['entrada'],$dato['salida']);
				$datos[$key]['suma']      = $this->sumar_horas ($dato['entrada'],$dato['salida']);
				$suma_acum = $this->sumar_horas ($datos[$key]['suma'],$suma_acum);
				$datos[$key]['suma_acum'] = $suma_acum;
			}
		}

		return $datos;
	}

	function get_lista_fecha($filtro){
			
			$sql = "SELECT 
					t_m.marca_id,
					t_m.legajo,
					t_m.fecha,
					t_m.entrada,
					t_m.salida,
					t_m.estado_marca_id,
					t_m.observaciones,
					t_m.reloj 
				FROM reloj.marca as t_m
				WHERE t_m.fecha = '".$filtro['fecha']."'
					AND t_m.legajo = '".$filtro['legajo']."' 
					AND t_m.estado_marca_id = 2 ";

		$datos = toba::db('ctrl_asis')->consultar($sql);

		if(isset($filtro['calcular_horas']) and count($datos) > 0){

			foreach($datos as $key=>$dato){
				$datos[$key]['horas']     = $this->restar_horas($dato['entrada'],$dato['salida']);
				#$datos[$key]['suma']      = $this->sumar_horas ($dato['entrada'],$dato['salida']);
				#$suma_acum = $this->sumar_horas ($datos[$key]['suma'],$suma_acum);
				#$datos[$key]['suma_acum'] = $suma_acum;
			}
		}

		return $datos;
	}

	function get_lista_resumen($filtro=array())
	{

		$fecha_desde = $filtro['fecha_desde'];
		$fecha_hasta = $filtro['fecha_hasta'];

		$where = array();

		if (isset($filtro['legajo'])) {
				$where[] = "legajo = '".$filtro['legajo']."'";
		}        
		if (isset($filtro['desc_nombre'])) {
				$where[] = "desc_nombre ILIKE ".quote("%{$filtro['desc_nombre']}%");
		}

		if (isset($filtro['cod_depcia'])) {
				$where[] = "cod_depcia = '".$filtro['cod_depcia']."'";
		}
		if (isset($filtro['agrupamiento'])) {
				$where[] = "agrupamiento = '".$filtro['agrupamiento']."'";
		}        

		$sql = "SELECT legajo, apellido, nombre, fec_nacim, dni, fec_ingreso, estado_civil, 
							caracter, categoria, agrupamiento, escalafon, cod_depcia, cuil
						FROM uncu.legajo ORDER BY legajo";

		
		if (count($where)>0) {
				$sql = sql_concatenar_where($sql, $where);
		}

		$agentes =  toba::db('mapuche')->consultar($sql); 

		if(count($agentes)>0){
			/*
			Bucle por agente, para calcular presentismo, y razones de los ausentes
			*/
			foreach($agentes as $key=>$agente){

				//seteamos valores en cero
				if(file_exists('fotos/'.$agentes[$key]['legajo'].'.png')){
					$agentes[$key]['foto']         = '<img style="width: 50px; height: 50px; border-radius: 100px;    -moz-border-radius: 100px; -webkit-border-radius: 100px; -khtml-border-radius: 100px;" src="fotos/'.$agentes[$key]['legajo'].'.png">';
				}else{
					$agentes[$key]['foto']         = '<img style="width: 50px; height: 50px; border-radius: 100px;    -moz-border-radius: 100px; -webkit-border-radius: 100px; -khtml-border-radius: 100px;" src="fotos/unnamed.png">';    
				}

				$agentes[$key]['nombre_completo'] = $agente['apellido'].', '.$agente['nombre'];

				//seteamos dias de vacaciones por antiguedad
				if(!empty($agente['fec_ingreso'])){
					$antiguedad = toba::tabla('vacaciones_antiguedad')->get_array_antiguedad($agente['fec_ingreso']);
					$agentes[$key]['dias_vac_antiguedad']     = utf8_decode($antiguedad['dias'].' días');
					$agentes[$key]['antiguedad']             = utf8_decode($antiguedad['antiguedad'].' años');
				}
					

				$agentes[$key]['fecha_ini']         = null;
				$agentes[$key]['fecha_desde']         = null;

				$agentes[$key]['laborables']         = 0;
				$agentes[$key]['feriados']             = 0;
				$agentes[$key]['presentes']         = 0;
				$agentes[$key]['ausentes']             = 0;
				$agentes[$key]['justificados']         = 0;
				$agentes[$key]['injustificados']     = 0;
				$agentes[$key]['partes']             = 0;
				$agentes[$key]['partes_sanidad']    = 0;
				$agentes[$key]['vacaciones']         = 0;

				$agentes[$key]['horas_totales']     = 0;
				$agentes[$key]['horas_promedio']       = 0;

				$horas_totales = 0;

				$jornada = toba::tabla('conf_jornada')->get_jornada_agente($agente['legajo']);

				$filtro_marca['calcular_horas']     = true;

				if (isset($jornada['fecha_ini'])) { //tiene jornada, seguimos el calculo

					//--------------------------------------------------------------------------------------------
					//--------------------------------------------------------------------------------------------
					//reviso fecha desde 
					if($fecha_desde < $jornada['fecha_ini']){
						$fecha_desde = $jornada['fecha_ini'];
					}
					//reviso fecha hasta 
					if(!empty($jornada['fecha_fin']) and $fecha_hasta > $jornada['fecha_fin']){
						$fecha_hasta = $jornada['fecha_fin'];
					}                    

					//recorremos todos los dias entre fecha_desde y fecha_hasta
					$fechaInicio = strtotime($fecha_desde);
					$fechaFin    = strtotime($fecha_hasta);
			
					for($i=$fechaInicio; $i<=$fechaFin; $i+=86400){

						$dia = date("Y-m-d", $i);

						if(toba::tabla('conf_feriados')->hay_feriado($dia)){ //revisamos el día solo si no es feriado

							$agentes[$key]['feriados']++;

						}else{
	
							$datos_dia = getdate($i);

							switch ($datos_dia['wday']) { //0 (para Domingo) hasta 6 (para Sábado)
								
								case 1: //lunes

									//-------------------------------------------------------------------------
									if($jornada['normal']==1 or $jornada['lunes']==1 ) {

										$agentes[$key]['laborables']++;

										$filtro_marca['legajo'] = $agente['legajo'];  
										$filtro_marca['fecha']     = $dia;

										$marcas = $this->get_lista_fecha($filtro_marca);
										if(count($marcas)>0){
											
											foreach($marcas as $marca){
												$horas_totales = $this->sumar_horas ($marca['horas'],$horas_totales);
												#$agentes[$key]['horas_totales']  = $horas_totales;
											}

											$agentes[$key]['presentes']++;

										}else{
											$agentes[$key]['ausentes']++;

											//verificar justificacion ----------------------
											$justificado = false;
				
											$id_parte = toba::tabla('parte')->tiene_parte($agente['legajo'], $dia);
											$id_parte_sanidad = toba::tabla('parte')->tiene_parte_sanidad($agente['legajo'], $dia);
											if($id_parte > 0){ 
												$agentes[$key]['partes']++; 
												$justificado = true;
											}elseif($id_parte_sanidad > 0){  
												$agentes[$key]['partes_sanidad']++; 
												$justificado = true;
											}

											if($justificado){
												$agentes[$key]['justificados']++;
											}else{
												$agentes[$key]['injustificados']++;
											}
											//-----------------------------------------------

										}
									}
									//-------------------------------------------------------------------------
									break;

								case 2: //martes

									//-------------------------------------------------------------------------
									if($jornada['normal']==1 or $jornada['martes']==1 ) {
										
										$agentes[$key]['laborables']++;
										
										$filtro_marca['legajo'] = $agente['legajo'];
										$filtro_marca['fecha']     = $dia;

										$marcas = $this->get_lista_fecha($filtro_marca);
										if(count($marcas)>0){
											
											foreach($marcas as $marca){
												$horas_totales = $this->sumar_horas ($marca['horas'],$horas_totales);
												#$agentes[$key]['horas_totales']  = $horas_totales;
											}

											$agentes[$key]['presentes']++;


										}else{
											$agentes[$key]['ausentes']++;

											//verificar justificacion ----------------------
											$justificado = false;
				
											$id_parte = toba::tabla('parte')->tiene_parte($agente['legajo'], $dia);
											$id_parte_sanidad = toba::tabla('parte')->tiene_parte_sanidad($agente['legajo'], $dia);
											if($id_parte > 0){ 
												$agentes[$key]['partes']++; 
												$justificado = true;
											}elseif($id_parte_sanidad > 0){  
												$agentes[$key]['partes_sanidad']++; 
												$justificado = true;
											}

											if($justificado){
												$agentes[$key]['justificados']++;
											}else{
												$agentes[$key]['injustificados']++;
											}
											//-----------------------------------------------
										}
									}
									//-------------------------------------------------------------------------
									break;

								case 3: //miercoles

									//-------------------------------------------------------------------------
									if($jornada['normal']==1 or $jornada['miercoles']==1 ) {
										
										$agentes[$key]['laborables']++;
										
										$filtro_marca['legajo'] = $agente['legajo'];
										$filtro_marca['fecha']     = $dia;

										$marcas = $this->get_lista_fecha($filtro_marca);
										if(count($marcas)>0){
											
											foreach($marcas as $marca){
												$horas_totales = $this->sumar_horas ($marca['horas'],$horas_totales);
												#$agentes[$key]['horas_totales']  = $horas_totales;
											}

											$agentes[$key]['presentes']++;


										}else{
											$agentes[$key]['ausentes']++;

											//verificar justificacion ----------------------
											$justificado = false;
				
											$id_parte = toba::tabla('parte')->tiene_parte($agente['legajo'], $dia);
											$id_parte_sanidad = toba::tabla('parte')->tiene_parte_sanidad($agente['legajo'], $dia);
											if($id_parte > 0){ 
												$agentes[$key]['partes']++; 
												$justificado = true;
											}elseif($id_parte_sanidad > 0){  
												$agentes[$key]['partes_sanidad']++; 
												$justificado = true;
											}

											if($justificado){
												$agentes[$key]['justificados']++;
											}else{
												$agentes[$key]['injustificados']++;
											}
											//-----------------------------------------------
										}
									}
									//-------------------------------------------------------------------------
									break;

								case 4: //jueves

									//-------------------------------------------------------------------------
									if($jornada['normal']==1 or $jornada['jueves']==1 ) {
										
										$agentes[$key]['laborables']++;
										
										$filtro_marca['legajo'] = $agente['legajo']; 
										$filtro_marca['fecha']     = $dia;

										$marcas = $this->get_lista_fecha($filtro_marca);
										if(count($marcas)>0){
											
											foreach($marcas as $marca){
												$horas_totales = $this->sumar_horas ($marca['horas'],$horas_totales);
												#$agentes[$key]['horas_totales']  = $horas_totales;
											}

											$agentes[$key]['presentes']++;


										}else{
											$agentes[$key]['ausentes']++;

											//verificar justificacion ----------------------
											$justificado = false;
				
											$id_parte = toba::tabla('parte')->tiene_parte($agente['legajo'], $dia);
											$id_parte_sanidad = toba::tabla('parte')->tiene_parte_sanidad($agente['legajo'], $dia);
											if($id_parte > 0){ 
												$agentes[$key]['partes']++; 
												$justificado = true;
											}elseif($id_parte_sanidad > 0){  
												$agentes[$key]['partes_sanidad']++; 
												$justificado = true;
											}

											if($justificado){
												$agentes[$key]['justificados']++;
											}else{
												$agentes[$key]['injustificados']++;
											}
											//-----------------------------------------------
										}
									}
									//-------------------------------------------------------------------------
									break;

								case 5: //viernes

									//-------------------------------------------------------------------------
									if($jornada['normal']==1 or $jornada['viernes']==1 ) {
										
										$agentes[$key]['laborables']++;
										
										$filtro_marca['legajo'] = $agente['legajo'];
										$filtro_marca['fecha']     = $dia;

										$marcas = $this->get_lista_fecha($filtro_marca);
										if(count($marcas)>0){
									

											foreach($marcas as $marca){
												$horas_totales = $this->sumar_horas ($marca['horas'],$horas_totales);
												#$agentes[$key]['horas_totales']  = $horas_totales;
											}

											$agentes[$key]['presentes']++;


										}else{
											$agentes[$key]['ausentes']++;

											//verificar justificacion ----------------------
											$justificado = false;
				
											$id_parte = toba::tabla('parte')->tiene_parte($agente['legajo'], $dia);
											$id_parte_sanidad = toba::tabla('parte')->tiene_parte_sanidad($agente['legajo'], $dia);
											if($id_parte > 0){ 
												$agentes[$key]['partes']++; 
												$justificado = true;
											}elseif($id_parte_sanidad > 0){  
												$agentes[$key]['partes_sanidad']++; 
												$justificado = true;
											}

											if($justificado){
												$agentes[$key]['justificados']++;
											}else{
												$agentes[$key]['injustificados']++;
											}
											//-----------------------------------------------
										}
									}
									//-------------------------------------------------------------------------
									break;

								case 6: //sabado

									//-------------------------------------------------------------------------
									if($jornada['sabado']==1) {
										
										$agentes[$key]['laborables']++;
										
										$filtro_marca['legajo'] = $agente['legajo']; 
										$filtro_marca['fecha']     = $dia;

										$marcas = $this->get_lista_fecha($filtro_marca);
										if(count($marcas)>0){
											
											foreach($marcas as $marca){
												$horas_totales = $this->sumar_horas ($marca['horas'],$horas_totales);
												#$agentes[$key]['horas_totales']  = $horas_totales;
											}

											$agentes[$key]['presentes']++;


										}else{
											$agentes[$key]['ausentes']++;

											//verificar justificacion ----------------------
											$justificado = false;
				
											$id_parte = toba::tabla('parte')->tiene_parte($agente['legajo'], $dia);
											$id_parte_sanidad = toba::tabla('parte')->tiene_parte_sanidad($agente['legajo'], $dia);
											if($id_parte > 0){ 
												$agentes[$key]['partes']++; 
												$justificado = true;
											}elseif($id_parte_sanidad > 0){  
												$agentes[$key]['partes_sanidad']++; 
												$justificado = true;
											}

											if($justificado){
												$agentes[$key]['justificados']++;
											}else{
												$agentes[$key]['injustificados']++;
											}
											//-----------------------------------------------
										}
									}
									//-------------------------------------------------------------------------
									break;

								case 0: //domingo

									//-------------------------------------------------------------------------
									if($jornada['domingo']==1) {
										
										$agentes[$key]['laborables']++;
										
										$filtro_marca['legajo'] = $agente['legajo'];  
										$filtro_marca['fecha']     = $dia;

										$marcas = $this->get_lista_fecha($filtro_marca);
										if(count($marcas)>0){
											
											foreach($marcas as $marca){
												$horas_totales = $this->sumar_horas ($marca['horas'],$horas_totales);
												#$agentes[$key]['horas_totales']  = $horas_totales;
											}

											$agentes[$key]['presentes']++;


										}else{
											$agentes[$key]['ausentes']++;

											//verificar justificacion ----------------------
											$justificado = false;
				
											$id_parte = toba::tabla('parte')->tiene_parte($agente['legajo'], $dia);
											$id_parte_sanidad = toba::tabla('parte')->tiene_parte_sanidad($agente['legajo'], $dia);
											if($id_parte > 0){ 
												$agentes[$key]['partes']++; 
												$justificado = true;
											}elseif($id_parte_sanidad > 0){  
												$agentes[$key]['partes_sanidad']++; 
												$justificado = true;
											}

											if($justificado){
												$agentes[$key]['justificados']++;
											}else{
												$agentes[$key]['injustificados']++;
											}
											//-----------------------------------------------
										}
									}
									//-------------------------------------------------------------------------
									break;
							}//fin switch

						}//fin no es feriado

					}//fin recorremos todos los dias entre fecha_desde y fecha_hasta

					$agentes[$key]['fecha_desde']         = $fecha_desde;
					$agentes[$key]['fecha_hasta']         = $fecha_hasta;

					

					if($agentes[$key]['laborables'] > 0){
						$agentes[$key]['horas_totales']       = $horas_totales;
						#$agentes[$key]['horas_promedio']       = round( ( $agentes[$key]['horas_totales'] / $agentes[$key]['laborables'] ) , 2);
						$contador_marcas = $agentes[$key]['laborables'] - $agentes[$key]['justificados'];
						$agentes[$key]['horas_promedio']       = $this->dividir_horas($agentes[$key]['horas_totales'],$contador_marcas);
					}else{
						$agentes[$key]['horas_promedio']       = '0:00:00';
					}

				
					//--------------------------------------------------------------------------------------------
					//--------------------------------------------------------------------------------------------

				}

			}//fin bucle agentes

		}

		return $agentes;
	}

	/*private function mostrar_minutos($hora){
		if(strpos($hora, '.')){
			list($h,$decimal) = explode('.', $hora);

			$m = round($decimal * 60 / 100);

			if($m < 10){
				$m
			}

			return "$h:$m";
		}else{
			return $hora.":00";//"0:00";
		}
	}*/

	function sumar_horas($hora1,$hora2)
	{
		list($h1,$m1,$s1)  = explode(":", $hora1);
		list($h2,$m2,$s2)  = explode(":", $hora2);

		$horas = $h1 + $h2;
		$minutos = $m1 + $m2;

		if($minutos >= 60){ 
			$horas = $horas + 1;
			$minutos = $minutos - 60;
		}

		if($minutos < 10){ 
			$minutos = "0".$minutos;
		}

		return "$horas:$minutos";
	}

	function restar_horas($horaini,$horafin)
	{
		$horai=substr($horaini,0,2);
		$mini=substr($horaini,3,2);
		$segi=substr($horaini,6,2);
		
		$horaf=substr($horafin,0,2);
		$minf=substr($horafin,3,2);
		$segf=substr($horafin,6,2);
		
		$ini=((($horai*60)*60)+($mini*60)+$segi);
		$fin=((($horaf*60)*60)+($minf*60)+$segf);
		
		$dif=$fin-$ini;
		
		$difh=floor($dif/3600);
		$difm=floor(($dif-($difh*3600))/60);
		$difs=$dif-($difm*60)-($difh*3600);
		#return date("H:i:s",mktime($difh,$difm,$difs));
		return date("H:i",mktime($difh,$difm,$difs));
	}

	function dividir_horas($horas_dividendo,$divisor)
	{
		
		/*$hora=substr($horas_dividendo,0,2);
		$min=substr($horas_dividendo,3,2);
		$seg=substr($horas_dividendo,6,2);*/

		list($hora,$min,$seg)  = explode(":", $horas_dividendo);
		$seg = 0;

		$dividendo=((($hora*60)*60)+($min*60)+$seg); //a segundos

		$resultado = $dividendo / $divisor; //res en segundos

		$resultadoh=floor($resultado/3600);
		$resultadom=floor(($resultado-($resultadoh*3600))/60);
		$resultados=$resultado-($resultadom*60)-($resultadoh*3600);

		return date("H:i",mktime($resultadoh,$resultadom,$resultados));

	}

}
?>