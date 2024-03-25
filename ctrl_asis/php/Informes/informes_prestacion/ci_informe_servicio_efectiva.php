<?php
class ci_informe_servicio_efectiva extends ctrl_asis_ci
{
	protected $s__datos_filtro;
	protected $s__datos;

	protected $s__seleccion;
	//-----------------------------------------------------------------------------------
	//---- cuadro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro(ctrl_asis_ei_cuadro $cuadro)
	{
		if (isset($this->s__datos_filtro)) {
			if (isset($this->s__datos_filtro['fecha_inicio']['valor'])) {
					$fecha1 = $this->s__datos_filtro['fecha_inicio']['valor'];
			//	ei_arbol($fecha1);
					$fechaentera1 =strtotime($fecha1);
					$y =date("Y",$fechaentera1);
					$m =date("m",$fechaentera1);
					$d =date("d",$fechaentera1);
					$this->s__datos_filtro['fecha_desde'] = $y."-".$m."-".$d;
					$fecha2 = $this->s__datos_filtro['fecha_fin']['valor'];
					$fechaentera2 =strtotime($fecha2);
					$y2 =date("Y",$fechaentera2);
					$m2 =date("m",$fechaentera2);
					$d2 =date("d",$fechaentera2);
					$this->s__datos_filtro['fecha_hasta'] = $y2."-".$m2."-".$d2;
					
						
			}

			$this->s__datos_filtro['basedatos'] = "access";
			$_SESSION['fecha_desde'] = $this->s__datos_filtro['fecha_desde'];
			$_SESSION['fecha_hasta'] = $this->s__datos_filtro['fecha_hasta'];
			$_SESSION['basedatos']   = $this->s__datos_filtro['basedatos']; 
			$_SESSION['dependencia']  == '04';
			
			$filtro['fecha_desde'] = $this->s__datos_filtro['fecha_desde'];
			$filtro['fecha_hasta'] = $this->s__datos_filtro['fecha_hasta'];
			$this->s__datos_filtro['marcas']= 3;
			$this->s__datos_filtro['adscripcion']=1;
			$this->s__datos_filtro['cargos_todos'] = 0;
			$this->s__datos_filtro[0]['legajo']=28168;

			

			$agentes =  $this->dep('mapuche')->get_agentes_control_asistencia($this->s__datos_filtro);
			//ei_arbol($agentes);
			$this->s__datos = $this->dep('access')->get_lista_resumen($agentes,$filtro);
			//$f = $this->s__datos;
			//ei_arbol($f);
			$todo =	array_values($this->s__datos);		
			//	ei_arbol($todo);
			$registros = count($todo); 
		//	ei_arbol($registros);	
			//$hasta = $this->s__datos['total'] +1;
			for ($i = 0;$i<$registros;$i++){
				$horas_esp = $this->dep('datos')->tabla('conf_jornada')->get_horas_diarias($todo[$i]['legajo']);
				//ei_arbol($horas_esp);
				if(isset($horas_esp[0]['horas'])){
					$horas_diarias = '0'.$horas_esp[0]['horas'].':00';
				
				} else {
				switch ($todo[$i]['cant_horas']){
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

				} 
			}

		}
		$tmp= 0;
						//ei_arbol($todo[$i]['laborables'] );
						$dias_trab = $todo[$i]['laborables'] - $todo[$i]['justificados'];
						//ei_arbol($dias_trab);
						
						$horas_min = explode(":",$horas_diarias);
						//Horas totales ideales trabajadas
						
						$horas= $dias_trab * $horas_min[0];
						
						// Calculos de minutos
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
						//ei_arbol($requerido);
						
						$todo[$i]['horas_requeridas_prom']= $requerido;
				$registros = count($todo); 
	//		ei_arbol($registros);
			for ($h=0; $h < $registros; $h++)
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
						$requerido=$hora .':'.$min;
						//$requerido = ($todo [$k]['horas_requeridas_prom']) + ($todo [$h]['horas_requeridas_prom']);
						
						$todo [$k]['horas_requeridas_prom'] =$requerido;


						//unset($todo[$h]);
					}
				}
			}
		}
			$lim = count($todo) ;
			//ei_arbol($lim);
			for ($l=0;$l<$lim;$l++){

				$tot=$todo[$l]['horas_totales'];
				$h_tot = explode(":",$tot);
				

				$req =$todo[$l]['horas_requeridas_prom'];
				$h_req =explode(":",$req);
				// Equivalencia Dias
				if ($todos[$l]['escalafon'] == 'DOCE'){
				//$ho_dia= explode(":",$horas_diarias);
				$ho_totales = $h_tot[0]+($h_tot[1]/60);
				
				$dias_eq = $ho_totales/$todos[$l]['h_min'];
				$todo[$l]['presentes'] = intval($ho_totales/$todos[$l]['h_min']);
				$trab = $todos[$l]['laborables'] - $todos[$l]['presentes'] ;
					if ($trab > 0) {
					$todo[$l]['ausentes'] =$trab;
					$todo[$l]['injustificados'] = $trab -( $todos[$l]['partes'] + $todos[$l]['partes_sanidad']);
					}else {
					$todo[$l]['ausentes'] = 0;
					$todo[$l]['injustificados'] = 0;
					}
				}

				if ($h_tot[0] < $h_req[0]) {
					$todo[$l]['horas_totales'] = '<b><span style="color:#FF0000">'.$todo[$l]['horas_totales'].'</b></span>';
				} else if ($h_tot[0] == $h_req[0]){
						if ($h_tot[1] < $h_req[1] ){
							$todo[$l]['horas_totales'] = '<b><span style="color:#FF0000">'.$todo[$l]['horas_totales'].'</b></span>';
						} 
				}	

				
					
						
					
			}
			
			$reg = count($todo);
			for ($i=0; $i<$reg;$i++){
				if ($todo[$i]['legajo'] <> null) {
					$datos [$i]['cuil'] = $todo[$i]['cuil'];
					$datos [$i]['legajo'] = $todo[$i]['legajo'];
					$datos [$i]['nombre_completo'] = $todo[$i]['nombre_completo'];
					$datos [$i]['categoria'] = $todo[$i]['categoria'];
					$datos [$i]['dedicacion'] = $todo[$i]['dedicacion'];
					$datos [$i]['escalafon'] = $todo[$i]['escalafon'];
					$datos [$i]['caracter'] = $todo[$i]['caracter'];
					$datos [$i]['laborables'] = $todo[$i]['laborables'];
					$datos [$i]['ausentes'] = $todo[$i]['ausentes'];
					$datos [$i]['liquidados'] = $todo[$i]['liquidados'];
					$datos [$i]['observaciones'] = $todo[$i]['observaciones'];
				}
			}
			unset($todo);
			$this->s__datos = $datos;

			//ei_arbol($this->s__datos);
			$cuadro->set_datos($this->s__datos);		
			}
		//	ei_arbol($todo);
			

	}

	//-----------------------------------------------------------------------------------
	//---- filtro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__filtro(ctrl_asis_ei_filtro $filtro)
	{
		if (isset($this->s__datos_filtro)) {
			
			$filtro->set_datos($this->s__datos_filtro);
		}else{
			$f['anio'] = date("Y");
			$f['mes']  = date("m");
			if(!empty($_SESSION['dependencia'])){ 
				$f['cod_depcia']  =   $_SESSION['dependencia'];
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

}
?>