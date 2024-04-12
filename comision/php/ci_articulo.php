<?php
class ci_articulo extends comision_ci
{
	protected $s__datos;
	protected $s__agentes;
	//-----------------------------------------------------------------------------------
	//---- formulario -------------------------------------------------------------------
	//////-----------------------------------------------------------------------------------

	
	function evt__formulario__alta($datos)
	{
		
		$dias_totales = 0;
		$datos['anio']= date('Y');

		$bandera_nodo = true;   
		$temp = $this->$s__agentes;
		$datos['legajo']=$temp[0]['legajo'];
		ei_arbol($datos);
		$legajo =$datos['legajo'];
		$id_catedra = $datos['catedra'];
		$anio=$datos['anio'];
		$id_motivo = $datos['id_motivo'] ;
		//ei_arbol($datos);
		if ($datos['dias'] > 0) {
			
		//ei_arbol($id_motivo);
		
		if ($id_motivo == 35 or $id_motivo == 57 or $id_motivo == 55) {
		$sql =  "SELECT count(*) cantidad from reloj.inasistencias
			WHERE legajo = $legajo
			AND id_catedra=$id_catedra
			AND anio =$anio
			AND id_motivo = 35 ";
		$hay_vacaciones =  toba::db('comision')->consultar($sql); 
		$ya_tomo = $hay_vacaciones[0]['cantidad'];
			
			$sql = "SELECT nombre_catedra, id_departamento FROM reloj.catedras
				WHERE id_catedra = $id_catedra";

			$depto = toba::db('comision')->consultar($sql); 

			

			if ($depto[0]['id_departamento']<10 or ($depto[0]['id_departamento'] == 12) or ($depto[0]['id_departamento'] == 11)){
			$sql = "SELECT t_l.legajo, t_l.apellido, t_l.nombre, t_l.fec_nacim, t_l.dni, t_l.fecha_ingreso, t_l.estado_civil, 
						t_l.caracter, t_l.categoria, t_l.agrupamiento, t_l.escalafon, 
						t_l.fec_nacim as fecha_nacimiento, t_l.cuil,
						t_d.pais, t_d.provincia, t_d.codigo_postal, t_d.localidad, t_d.manzana, 
						t_d.zona_paraje_barrio, t_d.calle, t_d.numero, t_d.piso, t_d.dpto_oficina, t_d.telefono, t_l.tipo_sexo,
						t_d.telefono_celular
						FROM reloj.agentes  as t_l LEFT JOIN reloj.domicilio as t_d
						ON t_l.legajo = t_d.legajo
						WHERE t_l.legajo = $legajo
						--AND cod_depcia = '04'
						AND escalafon<> 'NODO'";    
			} else {
				$sql = "SELECT t_l.legajo, t_l.apellido, t_l.nombre, t_l.fec_nacim, t_l.dni, t_l.fecha_ingreso, t_l.estado_civil, 
						t_l.caracter, t_l.categoria, t_l.agrupamiento, t_l.escalafon, 
						t_l.fec_nacim as fecha_nacimiento, t_l.cuil,
						t_d.pais, t_d.provincia, t_d.codigo_postal, t_d.localidad, t_d.manzana, 
						t_d.zona_paraje_barrio, t_d.calle, t_d.numero, t_d.piso, t_d.dpto_oficina, t_d.telefono, t_l.tipo_sexo,
						t_d.telefono_celular
						FROM reloj.agentes  as t_l LEFT JOIN reloj.domicilio as t_d
						ON t_l.legajo = t_d.legajo
						WHERE t_l.legajo = $legajo
					--	AND cod_depcia = '04'
						AND escalafon in ('NODO','AUTO')  ";
			}


		
		} else {

			$ya_tomo= 0;
			$sql = "SELECT nombre_catedra, id_departamento FROM reloj.catedras
				WHERE id_catedra = $id_catedra";
			$depto = toba::db('comision')->consultar($sql); 

			//ei_arbol($depto);
			if ($depto[0]['id_departamento']<10 or ($depto[0]['id_departamento'] == 12)or ($depto[0]['id_departamento'] == 11)){
			$sql = "SELECT t_l.legajo, t_l.apellido, t_l.nombre, t_l.fec_nacim, t_l.dni, t_l.fecha_ingreso, t_l.estado_civil, 
						t_l.caracter, t_l.categoria, t_l.agrupamiento, t_l.escalafon, 
						t_l.fec_nacim as fecha_nacimiento, t_l.cuil,
						t_d.pais, t_d.provincia, t_d.codigo_postal, t_d.localidad, t_d.manzana, 
						t_d.zona_paraje_barrio, t_d.calle, t_d.numero, t_d.piso, t_d.dpto_oficina, t_d.telefono, t_l.tipo_sexo,
						t_d.telefono_celular
						FROM reloj.agentes  as t_l LEFT JOIN reloj.domicilio as t_d
						ON t_l.legajo = t_d.legajo
						WHERE t_l.legajo = $legajo
					--	AND cod_depcia = '04'
						AND escalafon<> 'NODO'";    
			} else {
				$sql = "SELECT t_l.legajo, t_l.apellido, t_l.nombre, t_l.fec_nacim, t_l.dni, t_l.fecha_ingreso, t_l.estado_civil, 
						t_l.caracter, t_l.categoria, t_l.agrupamiento, t_l.escalafon, 
						t_l.fec_nacim as fecha_nacimiento, t_l.cuil,
						t_d.pais, t_d.provincia, t_d.codigo_postal, t_d.localidad, t_d.manzana, 
						t_d.zona_paraje_barrio, t_d.calle, t_d.numero, t_d.piso, t_d.dpto_oficina, t_d.telefono, t_l.tipo_sexo,
						t_d.telefono_celular
						FROM reloj.agentes  as t_l LEFT JOIN reloj.domicilio as t_d
						ON t_l.legajo = t_d.legajo
						WHERE t_l.legajo = $legajo
						--AND cod_depcia = '04'
						AND escalafon in ('NODO','AUTO')";
			}
			

		} 

		if ($legajo == 26010 or $legajo==20738 or $legajo == 18615 or $legajo == 34394 or $legajo == 33672 or $legajo == 18176 or $legajo == 25549 or $legajo == 20738)

		{
			$sql = "SELECT t_l.legajo, t_l.apellido, t_l.nombre, t_l.fec_nacim, t_l.dni, t_l.fecha_ingreso, t_l.estado_civil, 
						t_l.caracter, t_l.categoria, t_l.agrupamiento, t_l.escalafon, 
						t_l.fec_nacim as fecha_nacimiento, t_l.cuil,
						t_d.pais, t_d.provincia, t_d.codigo_postal, t_d.localidad, t_d.manzana, 
						t_d.zona_paraje_barrio, t_d.calle, t_d.numero, t_d.piso, t_d.dpto_oficina, t_d.telefono, t_l.tipo_sexo,
						t_d.telefono_celular
						FROM reloj.agentes  as t_l LEFT JOIN reloj.domicilio as t_d
						ON t_l.legajo = t_d.legajo
						WHERE t_l.legajo = $legajo";
		}
		

		$agente = toba::db('comision')->consultar($sql);          
		
		$cant = count($agente);
		//ei_arbol($sql);

		/*$sql = "SELECT MIN(fecha_ingreso) fecha from reloj.agentes

		where legajo = $legajo";
		$fec_ingreso = toba::db('comision')->consultar($sql);
		$res =     $fec_ingreso[0]['fecha'];
		list($y,$m,$d)=explode("-",$res); //2011-03-31
				$fecha = $d."-".$m."-".$y;
				$dias = explode('-', $fecha, 3);
				$dias = mktime(0,0,0,$dias[1],$dias[0],$dias[2]);
				$antiguedad = ((time()-$dias)/31556926 );
				//(int)((time()-$dias)/31556926 );
				//ei_arbol($antiguedad);
				if ($antiguedad == 0){
					$prop_vaca= (int)((time()-$dias)/1729147);
				}
			$sql ="SELECT fecha_ingreso from reloj.antiguedad
			WHERE legajo = $legajo";
			$fec_ingreso = toba::db('comision')->consultar($sql);
		// ei_arbol($sql);
			if (isset($fec_ingreso[0]['fecha_ingreso'])) {
			$fec=$fec_ingreso[0]['fecha_ingreso'];
			list($y,$m,$d)=explode("-",$fec); //2011-03-31
				$fecha = $d."-".$m."-".$y;
				$dias = explode('-', $fecha, 3);
				$dias = mktime(0,0,0,$dias[1],$dias[0],$dias[2]);
				$antiguedad = ((time()-$dias)/31556926 );
				// (int)((time()-$dias)/31556926 );
			}*/
			// ei_arbol($antiguedad);

			$dias= $datos['dias'];
			$anio= $datos['anio'];
			if ($id_motivo== 57) {
				$sql = "SELECT sum(dias) dias_restantes FROM reloj.vacaciones_restantes
				WHERE legajo = $legajo
				AND anio <= $anio";
			} else if ($id_motivo== 35) {
			$sql = "SELECT sum(dias) dias_restantes FROM reloj.vacaciones_restantes
			WHERE legajo = $legajo
			AND anio < $anio";
		}
		$temp = toba::db('comision')->consultar($sql);
		/*$sql = "SELECT dias_adelanto FROM reloj.vacaciones_adelantadas
		where legajo = $legajo and anio = $anio; ";
	$ade = toba::db('comision')->consultar($sql);    
	$adelanto = $ade[0]['dias_adelanto'];*/
		// ei_arbol($temp);
		if (isset($temp)){
			$dias_restantes= $temp['dias_restantes'];
		}else
		{
			$dias_restantes= 0;
		}
		$fecha_inicio_licencia = $datos['fecha_inicio_licencia'];
		$fechaentera1 =strtotime($fecha_inicio_licencia);
	$fecha_inicio = date_create(date("Y-m-d",$fechaentera1)); 
	$hoy=date_create(date("Y-m-d"));
	$diferencia = date_diff($fecha_inicio , $hoy);
	$y =date("Y",$fechaentera1);
	$m =date("m",$fechaentera1);

	$agrupamiento = $agente [0]['escalafon'];
	$agrego = 0;    
	$sql = "SELECT sum(dias) dias_restantes FROM reloj.vacaciones_restantes
		WHERE legajo = $legajo
		AND anio = $anio";
		$vac_pen = toba::db('comision')->consultar($sql);
		$insertadas = count($vac_pen);
		//  ei_arbol($insertadas);

		for ($i=0; $i < $cant; $i++)
		{ 
			
			if ($agente [$i]['escalafon'] == 'NODO'){ //No docente
				
				
				
				if ($id_motivo == 30) { //Razones Particulares
					
						if (date("Y") == $anio){    
							if($dias<=2){
							$agente [$i]['articulo'] = null;
							$agente[$i]['id_decreto'] = 4;
							

							$sql = "SELECT SUM(dias) dias_restantes 
									FROM reloj.parte
							WHERE legajo = $legajo    AND id_motivo = 30    AND  DATE_PART('month', fecha_inicio_licencia) = $m
							and DATE_PART('year', fecha_inicio_licencia) = $anio";
							$parte = toba::db('comision')->consultar($sql);
							//ei_arbol($parte);
							$sql = "SELECT fecha_fin - fecha_inicio +1 dias_no_pasados
								FROM reloj.inasistencias
								Where legajo = $legajo AND id_motivo=30 AND extract (month from fecha_inicio)=$m And extract(year from fecha_inicio) = $anio";

							$pendiente = toba::db('comision')->consultar($sql);
							//ei_arbol($pendiente);
								$lim = count($pendiente);
								$dias_tomados = 0;
								for ($i=0; $i<$lim; $i++){
									$dias_tomados = $pendientes[$i]['dias_no_pasados']+$dias_tomados  ;
								/*	$fecha_inicio = $pendiente[$i]['fecha_inicio'];
										$fechaentera1 =strtotime($fecha_inicio);
								$fecha_inicio = date_create(date("Y-m-d",$fechaentera1)); 
								$fecha_fin = $pendiente[$i]['fecha_fin'];
										$fechaentera1 =strtotime($fecha_fin);
								$fecha_fin = date_create(date("Y-m-d",$fechaentera1)); 
								$diferencia = date_diff($fecha_inicio , $fecha_fin);
								$dias_tomados = $dias_tomados + $diferencia;*/
								//ei_arbol($dias_tomados);
								}

							if ($parte[0]['dias_restantes'] == null ) {
								$parte[0]['dias_restantes'] = 2;
							}

							$temp[0]['dias_restantes'] = $parte[0]['dias_restantes']- $dias_tomados - $dias;    
							//ei_arbol($temp[0]['dias_restantes'] < 0);                        
								if(!is_null($temp)&&($temp[0]['dias_restantes'] >= 0 && $temp[0]['dias_restantes'] <= 2 )){
									$sql="SELECT -SUM(dias) +6 dias_restantes 
									FROM reloj.parte
									WHERE legajo = $legajo
									AND id_motivo = 30
									AND extract (month from fecha_inicio_licencia)=$m
									AND  DATE_PART('year', fecha_inicio_licencia) = $y";
									$temp = toba::db('comision')->consultar($sql);    
									$bandera = false;

									//ei_arbol($temp);
									if (is_null($temp[0]['dias_restantes'])|| ($temp[0]['dias_restantes'] >= 0  && $temp[0]['dias_restantes'] <= 6 )){
										$lim = count($agente);
										for ($i = 0; $i<$lim; $i++){
										$agente [$i]['articulo'] = 40;
										}
									$bandera= true;
									//ei_arbol($agente);

									} else {
										//if(!is_null($temp[0]['dias_restantes'])&&!($temp[0]['dias_restantes']< 0 &&$temp[0]['dias_restantes'] > 6) ){
										toba::notificacion()->agregar('Ud ha excedido la cantidad anual de razones particulares este a&ntilde;o cuenta con '.$temp[0]['dias_restantes'] .' d&iacute;as', "info");
										$bandera= false;
										}    
								} else 
								{
								
								//ei_arbol($agente);    
									$temp[0]['dias_restantes'] = $parte[0]['dias_restantes'] + $dias_tomados -2;
									if ($temp[0]['dias_restantes'] >= 0 ) {
										$temp[0]['dias_restantes'] = 0;
									} else {
										$temp[0]['dias_restantes'] =abs($temp[0]['dias_restantes']);
									}

								toba::notificacion()->agregar('Ud ha excedido la cantidad mensual de razones particulares este mes cuenta con '.$temp[0]['dias_restantes'] .' d&iacute;as', "info");

									$bandera_nodo = false;

								} 

							} 
							/*else
							{
								toba::notificacion()->agregar('Ud ha excedido la cantidad de dias recuerde que las razones particulares son entre 1 y 2 d&iacute;as' , "info");
								$bandera_nodo = false;    
							}*/
					//ei_arbol($agente);
					} else {
						toba::notificacion()->agregar('Introduzca el corriente a&ntilde;o. Gracias ', "info");

									$bandera_nodo = false;

					}            
								
				} elseif ($id_motivo == 35) { //Vacaciones
							
					//ei_arbol($agente);
							$agente[$i]['articulo'] = 55;
							$agente[$i]['id_decreto'] = 4;
							$dias_restantes = 0;
							//ei_arbol ($agente);
							/*$sql= "SELECT count(*) tiene from reloj.vacaciones_restantes
									WHERE legajo = $legajo AND anio= $anio;";
							$resto = toba::db('comision')->consultar($sql);*/
							$sql = "SELECT dias FROM reloj.vacaciones_restantes
									WHERE legajo = $legajo";
							$dias_vp = toba::db('comision')->consultar($sql);
							
							if(count($dias_vp)>0){
								$dias_restantes=$dias_vp[0]['dias'];
							}
							if ($resto[0]['tiene'] > 0) {
								$hay_cargadas = 1;
							} else {
								$hay_cargadas = 0;
							}
						/*	if ($antiguedad > 20){
								$dias_totales = 40 + $dias_restantes - $adelanto;
							//     ei_arbol ($dias);
								if ($dias >$dias_totales ) {
									toba::notificacion()->agregar('Los días de vacaciones tienen que ser menores o iguales a '.$dias_totales .' d&iacute;as', "info");
									break;
								}
								if ($dias < 30) { 
									toba::notificacion()->agregar('Los días de vacaciones no pueden ser menores de 30 d&iacute;as', "info");
									break;
								} else {

									if ($agrego == 0 and $insertadas > 0 and $hay_cargadas == 0){
									$dias_restantes = $dias_totales - $dias;
									//ei_arbol ($dias_restantes);
									
									if ($dias_restantes <> 0) {



									$sql ="INSERT INTO reloj.vacaciones_restantes(
										legajo, cod_depcia, agrupamiento, anio, dias)
										VALUES ($legajo,4, '$agrupamiento', $anio, $dias_restantes);
										DELETE FROM reloj.vacaciones_restantes
										where legajo=$legajo
										AND anio < $anio;";
									toba::db('comision')->ejecutar($sql);
									
									$agrego=1;
									}
									}

								}
								
							} elseif ($antiguedad > 15 && $antiguedad <=20)
								{
								$dias_totales = 35 + $dias_restantes -$adelanto;

								if ($dias >$dias_totales ) {
									toba::notificacion()->agregar('Los días de vacaciones tienen que ser menores o iguales a '.$dias_totales .' d&iacute;as', "info");
									break;
								}
								if ($dias < 30) { 
									toba::notificacion()->agregar('Los días de vacaciones no pueden ser menores de 30 d&iacute;as', "info");
									$bandera_nodo = false;
									break;

								}else {
									if ($agrego == 0 and $insertadas > 0 and $hay_cargadas == 0){
									$dias_restantes = $dias_totales - $dias;
									if ($dias_restantes <> 0){
									$sql ="INSERT INTO reloj.vacaciones_restantes(
										legajo, cod_depcia, agrupamiento, anio, dias)
										VALUES ($legajo,4, '$agrupamiento', $anio, $dias_restantes);
										DELETE FROM reloj.vacaciones_restantes
										where legajo=$legajo
										AND anio < $anio;";
									toba::db('comision')->ejecutar($sql);
									$agrego=1;
									}
									}

								}

								} elseif($antiguedad > 10 && $antiguedad <=15)
									{
									$dias_totales = 30 + $dias_restantes -$adelanto;    
									if ($dias >$dias_totales ) {
									toba::notificacion()->agregar('Los días de vacaciones tienen que ser menores o iguales a '.$dias_totales .' d&iacute;as', "info");
									break;
									}
									if ($dias < 30) { 
									toba::notificacion()->agregar('Los días de vacaciones no pueden ser menores de 30 d&iacute;as', "info");
									$bandera_nodo = false;
									break;
									} else {
										$dias_restantes = 0;
										if ($agrego == 0 and $insertadas > 0 and $hay_cargadas == 0){
										$dias_restantes = $dias_totales - $dias;
										if ($dias_restantes > 0){
											$sql ="INSERT INTO reloj.vacaciones_restantes(
											legajo, cod_depcia, agrupamiento, anio, dias)
											VALUES ($legajo,4, '$agrupamiento', $anio, $dias_restantes);
											DELETE FROM reloj.vacaciones_restantes
											where legajo=$legajo
											AND anio < $anio;";
										toba::db('comision')->ejecutar($sql);
										$agrego=1;
											}
										}
										}
								}elseif ($antiguedad > 5 && $antiguedad <=10)
								{
									$dias_totales = 25 + $dias_restantes -$adelanto;
									$dias_restantes = 0;
								}elseif ($antiguedad > 0.5 && $antiguedad <=5)
								{
								$dias_totales = 20 + $dias_restantes -$adelanto;
								$dias_restantes = 0;
								}else{
								$dias_totales = $prop_vaca;
								$dias_restantes = 0;
								}

					if ($dias >$dias_totales ) {
									toba::notificacion()->agregar('Los días de vacaciones tienen que ser menores o iguales a '.$dias_totales .' d&iacute;as.', "info");

									$bandera_nodo=false;
							
								} else {
							$agente[$i]['articulo'] = 55;*/
						/*if (date("Y-m-d") >= '2023-12-08'){ //Quitar comentario para limite de dias
							toba::notificacion()->agregar('Se ha pasado la fecha limite de carga de Licencia Anual', "info");

									$bandera_nodo=false;
						} else {*/	
							$bandera= true;
							//} //Quitar comentario para limite de dias

				/// Vacaciones Pendientes no docente
				
				} else if ($id_motivo==57){
					$agente[$i]['articulo'] = null;
							$agente[$i]['id_decreto'] = 4;
							$datos['anio'] = $anio -1;
							//ei_arbol ($agente);
							$sql= "SELECT sum(dias)  dias_rest from reloj.vacaciones_restantes
									WHERE legajo = $legajo AND anio <= $anio;";
									

							$resto = toba::db('comision')->consultar($sql);
							$dias_pendientes = $resto[0]['dias_rest'];
							//ei_arbol($resto);
								if ($dias <= $dias_pendientes){
									/*toba::notificacion()->agregar('Usted cuenta con  '.$dias_pendientes .' días pendientes del año'.$anio. ' coloque una cantidad de dias validos.', "info");*/
							$agente[$i]['articulo'] = 106;
							$bandera= true;    

							} else {
								toba::notificacion()->agregar('Usted cuenta con  '.$dias_pendientes .' d&iacute;as, coloque una cantidad de d&iacute;as validos.', "info");
							$bandera = false;    
							}

				
				}else if($id_motivo==12){ // Donación de Sangre
					if ($dias==1) {
						$agente[$i]['articulo'] = null;
						$sql = "SELECT count(*) cant FROM reloj.parte 
								where id_motivo = $id_motivo
								and legajo = $legajo
								and Date_part('year',fecha_inicio_licencia) = $anio";
						$tomo=toba::db('comision')->consultar($sql);
						if ($tomo[0]['cant'] <= 0)	{

							if ($datos['certificado'] <> null){
								$agente[$i]['articulo'] = 33;
								$agente[$i]['id_decreto'] = 4;
								$bandera= true; 
							} else {
							toba::notificacion()->agregar('Por favor adjunte el Certificado correspondiente', "info");
							$bandera = false;   
							}
						} else {
							toba::notificacion()->agregar('Ya hizo uso de esta licencia el presente año', "info");
							$bandera = false;  
						}
					} else {
							toba::notificacion()->agregar('Solamente puede tomar un 1 día', "info");
							$bandera = false;  
					}
					}else if($id_motivo==22){ // Actividades Deportivas
					if ($dias >= 1 and $dias <=15 ) {
						$agente[$i]['articulo'] = null;
					$sql = "SELECT count(*) cant FROM reloj.parte 
								where id_motivo = $id_motivo
								and legajo = $legajo
								and Date_part('year',fecha_inicio_licencia) = $anio";
						$tomo=toba::db('comision')->consultar($sql);
						if ($tomo[0]['cant'] <= 15)	{
						if ($datos['certificado'] <> null){
							$agente[$i]['articulo'] = 38;
							$agente[$i]['id_decreto'] = 4;
							$bandera= true; 
						} else {
							toba::notificacion()->agregar('Por favor adjunte el Certificado correspondiente', "info");
							$bandera = false;   
						}
					} else {
							toba::notificacion()->agregar('Ya hizo uso de todas estas licencia el presente año', "info");
							$bandera = false; 
					}
					} else {
							toba::notificacion()->agregar('Solamente puede tomar hasta 15 días en el año', "info");
							$bandera = false;  
					}

				} else if($id_motivo==49){ // Citación Judicial
					
						$agente[$i]['articulo'] = null;
						if ($datos['certificado'] <> null){
							$agente[$i]['articulo'] = 97;
							$agente[$i]['id_decreto'] = 9;
							$bandera= true; 
						} else {
							toba::notificacion()->agregar('Por favor adjunte el Certificado correspondiente', "info");
							$bandera = false;   
						}
					

				} else if($id_motivo==17){ // Fallecimiento de Conyúge y 1º grado
						
						$agente[$i]['articulo'] = null;
						if ($datos['certificado'] <> null){
							$dias = 10;
							$datos['dias'] = $dias;
							$agente[$i]['articulo'] = 29;
							$agente[$i]['id_decreto'] = 4;
							$bandera= true; 
						} else {
							toba::notificacion()->agregar('Por favor adjunte el Certificado correspondiente', "info");
							$bandera = false;   
						}
					

				} else if($id_motivo==16){ // Fallecimiento de Familiar Cosanguineo 2º
						
						$agente[$i]['articulo'] = null;
						if ($datos['certificado'] <> null){
							$dias = 5;
							$datos['dias'] = $dias;
							$agente[$i]['articulo'] = 31;
							$agente[$i]['id_decreto'] = 4;
							$bandera= true; 
						} else {
							toba::notificacion()->agregar('Por favor adjunte el Certificado correspondiente', "info");
							$bandera = false;   
						}
					

				} else if($id_motivo==18){ // Fallecimiento de pariente politico
						
						$agente[$i]['articulo'] = null;
						if ($datos['certificado'] <> null){
							$dias = 1;
							$datos['dias'] = $dias;
							$agente[$i]['articulo'] = 32;
							$agente[$i]['id_decreto'] = 4;
							$bandera= true; 
						} else {
							toba::notificacion()->agregar('Por favor adjunte el Certificado correspondiente', "info");
							$bandera = false;   
						}
					

				} else if($id_motivo==27){ //Nacimiento (Paternidad)
					
						$agente[$i]['articulo'] = null;
						if ($agente [$i]['tipo_sexo'] == 'M') {
							if ($datos['certificado'] <> null){
								$dias = 15;
								$agente[$i]['articulo'] = 26;
								$agente[$i]['id_decreto'] = 4;
								$bandera= true; 
							} else {
								toba::notificacion()->agregar('Por favor adjunte el Certificado correspondiente', "info");
								$bandera = false;   
							}
						} else {
							toba::notificacion()->agregar('Esta Licencia es por Paternidad', "info");
							$bandera = false;   
						}

				}else if($id_motivo==36){ //Matrimonio
					
						$agente[$i]['articulo'] = null;
							if ($datos['certificado'] <> null){
								$dias = 10;
								$agente[$i]['articulo'] = 28;
								$agente[$i]['id_decreto'] = 4;
								$bandera= true; 
							} else {
								toba::notificacion()->agregar('Por favor adjunte el Certificado correspondiente', "info");
								$bandera = false;   
							}
						

				} else if($id_motivo == 25){ //Matrimonio de un hijo/a
					
						$agente[$i]['articulo'] = null;
							if ($datos['certificado'] <> null){
								$dias = 2;
								$agente[$i]['articulo'] = 27;
								$agente[$i]['id_decreto'] = 4;
								$bandera= true; 
							} else {
								toba::notificacion()->agregar('Por favor adjunte el Certificado correspondiente', "info");
								$bandera = false;   
							}
						

				} else if($id_motivo == 25){ //Adopcion (Maternidad)
					
						$agente[$i]['articulo'] = null;
							if ($datos['certificado'] <> null){
								$dias = 45;
								$agente[$i]['articulo'] = 43;
								$agente[$i]['id_decreto'] = 4;
								$bandera= true; 
							} else {
								toba::notificacion()->agregar('Por favor adjunte el Certificado correspondiente', "info");
								$bandera = false;   
							}
						

				} 	
				 else if($id_motivo == 59){ //Adopcion (Paternidad)
					
						$agente[$i]['articulo'] = null;
							if ($datos['certificado'] <> null){
								$dias = 15;
								$agente[$i]['articulo'] = 25;
								$agente[$i]['id_decreto'] = 4;
								$bandera= true; 
							} else {
								toba::notificacion()->agregar('Por favor adjunte el Certificado correspondiente', "info");
								$bandera = false;   
							}
						

				}else if($id_motivo==14){ // Rendir examen Secundario
					if ($dias >= 1 and $dias <=4 ) {
						$agente[$i]['articulo'] = null;
					$sql = "SELECT count(*) cant FROM reloj.parte 
								where id_motivo = $id_motivo
								and legajo = $legajo
								and Date_part('year',fecha_inicio_licencia) = $anio";
						$tomo=toba::db('comision')->consultar($sql);
						if ($tomo[0]['cant'] <= 20)	{
						if ($datos['certificado'] <> null){
							$agente[$i]['articulo'] = 34;
							$agente[$i]['id_decreto'] = 4;
							$bandera= true; 
						} else {
							toba::notificacion()->agregar('Por favor adjunte el Certificado correspondiente', "info");
							$bandera = false;   
						}
					} else {
							toba::notificacion()->agregar('Ya hizo uso de todas estas licencia el presente año', "info");
							$bandera = false; 
					}
					} else {
							toba::notificacion()->agregar('Solamente puede tomar como máximo 4 días por Certificado', "info");
							$bandera = false;  
					}

				}else if($id_motivo==15){ // Rendir examen Universitario o posgrado
					if ($dias >= 1 and $dias <=5 ) {
						$agente[$i]['articulo'] = null;
					$sql = "SELECT count(*) cant FROM reloj.parte 
								where id_motivo = $id_motivo
								and legajo = $legajo
								and Date_part('year',fecha_inicio_licencia) = $anio";
						$tomo=toba::db('comision')->consultar($sql);
						if ($tomo[0]['cant'] <= 24)	{
						if ($datos['certificado'] <> null){
							$agente[$i]['articulo'] = 35;
							$agente[$i]['id_decreto'] = 4;
							$bandera= true; 
						} else {
							toba::notificacion()->agregar('Por favor adjunte el Certificado correspondiente', "info");
							$bandera = false;   
						}
					} else {
							toba::notificacion()->agregar('Ya hizo uso de todas estas licencia el presente año', "info");
							$bandera = false; 
					}
					} else {
							toba::notificacion()->agregar('Solamente puede tomar como máximo 5 días por Certificado', "info");
							$bandera = false;  
					}

				}


					// Adelanto de Vacaciones
				/* else if ($id_motivo == 55){
					$agente[$i]['id_decreto'] = 4;
					if ($anio != date("Y")) {
						toba::notificacion()->agregar('Para pedir Adelanto de vacaciones recuerde colocar el a&ntildeo en curso', "info");
						$bandera = false;

					} else {
						*$sql = "SELECT dias_adelanto FROM reloj.vacaciones_adelantadas
							where legajo = $legajo and anio = $anio; ";
						$ade = toba::db('comision')->consultar($sql);    
						$adelanto = $ade[0]['dias_adelanto'];
						$bandera = true;

						if ($antiguedad > 20){
							$dias_vacaciones = 40 - $adelanto ;


						} elseif ($antiguedad > 15 && $antiguedad <=20) {
							$dias_vacaciones = 35 - $adelanto;

						} elseif($antiguedad > 10 && $antiguedad <=15) {
							$dias_vacaciones = 30 - $adelanto;

						}elseif ($antiguedad > 5 && $antiguedad <=10) {
							$dias_vacaciones = 25 - $adelanto;

						}elseif ($antiguedad > 0.5 && $antiguedad <=5){
							$dias_vacaciones = 20 - $adelanto;

						} else {
							$bandera = false;
							toba::notificacion()->agregar('Ud. no cuenta con la antig&uuml;edad suficiente para solicitar Adelanto de Vacaciones', "info");

						}
						if ($bandera){
							if ($dias <= $dias_vacaciones){
								$agente[$i]['articulo'] = 55;
								$dias_adelantados = $adelanto + $dias;

							} else {
							toba::notificacion()->agregar('Ud.  cuenta con '. $dias_vacaciones. ' por favor corrija los dias para solicitar Adelanto de Vacaciones', "info");
							$bandera = false;    

							}

						}

							



					}
				}*/



			
				

			} else {
				// Docentes

				if ($id_motivo == 30) { //Razones particulares
						//ei_arbol($id_motivo);
						if (date("Y") == $anio){
							if ($dias <= 2){
							for ($j=0; $j < $cant; $j++){
							$agente [$j]['articulo'] = 0;
							$agente[$j]['id_decreto'] = 8;	
							}
							
							$sql = "SELECT -SUM(dias) +2 dias_restantes 
									FROM reloj.parte
							WHERE legajo = $legajo
							AND id_motivo = 30
							AND  DATE_PART('month', fecha_inicio_licencia) = $m";
							$parte = toba::db('comision')->consultar($sql);
							/*$sql = "SELECT fecha_inicio, fecha_fin*/
							$sql = "SELECT  fecha_fin - fecha_inicio + 1 dias_rp
								FROM reloj.inasistencias
								Where legajo = $legajo AND id_motivo=30 AND extract (month from fecha_inicio)=$m And extract(year from fecha_inicio) = $anio";

							$pendiente = toba::db('comision')->consultar($sql);
								$lim = count($pendiente);
								$dias_tomados = 0;
							//ei_arbol($pendiente);	
								for ($i=0; $i<$lim; $i++){
								$dias_tomados = $dias_tomados + $pendiente[$i]['dias_rp'];
									
								}

							
							$temp[0]['dias_restantes'] = $parte[0]['dias_restantes']+ $dias_tomados + $dias;
							//ei_arbol($temp);
								if(!is_null($temp)&&($temp[0]['dias_restantes'] >= 0 && $temp[0]['dias_restantes']<=2 )){
									$sql="SELECT -SUM(dias) +6 dias_restantes 
									FROM reloj.parte
									WHERE legajo = $legajo
									AND id_motivo = 30
									AND  DATE_PART('year', fecha_inicio_licencia) = $y";
									$temp = toba::db('comision')->consultar($sql);    
									//ei_arbol($temp);
										if (is_null($temp[0]['dias_restantes'])|| ($temp[0]['dias_restantes'] >= 0 && $temp[0]['dias_restantes']<=6 )){
										$lim = count($agente);
										for ($i = 0; $i<$lim; $i++){	
										$agente [$i]['articulo'] = 57;
										}
									$bandera= true;
									//ei_arbol($agente);

									} else {
									toba::notificacion()->agregar('Ud ha excedido la cantidad anual de razones particulares este a&ntilde;o cuenta con '.$temp[0]['dias_restantes'] .' d&iacute;as', "info");
									} 
								} else 
								{
								
								//ei_arbol($agente);    
									$temp[0]['dias_restantes'] = $parte[0]['dias_restantes'] + $dias_tomados -2;
									if ($temp[0]['dias_restantes'] >= 0 ) {
										$temp[0]['dias_restantes'] = 0;
									} else {
										$temp[0]['dias_restantes'] =abs($temp[0]['dias_restantes']);
									}
									
								toba::notificacion()->agregar('Ud ha excedido la cantidad mensual de razones particulares este mes cuenta con '.$temp[0]['dias_restantes'] .' días', "info");
								}
								
								
							} 
							/*else {
								toba::notificacion()->agregar('Ud ha excedido la cantidad de d&iacute;as recuerde que las razones particulares son entre 1 y 2 d&iacute;as' , "info");                                
							}*/
						} else {
						toba::notificacion()->agregar('Introduzca el corriente a&ntildeo. Gracias ', "info");

									$bandera_nodo = false;

						}        

				} elseif ($id_motivo == 35) { // Vacaciones
							
							$agente[$i]['articulo'] = 56;
							$agente[$i]['id_decreto'] = 2;
							$bandera= true;
							$dias_restantes = 0;
							

							$sql = "SELECT dias FROM reloj.vacaciones_restantes
									WHERE legajo = $legajo";
							$dias_vp = toba::db('comision')->consultar($sql);
							
							if(count($dias_vp)>0){
								$dias_restantes=$dias_vp[0]['dias'];
							}

							/*if ($antiguedad > 15){
								
								$dias_totales = 45 + $dias_restantes;
								if ($dias >$dias_totales ) {
									toba::notificacion()->agregar('Los días de vacaciones tienen que ser menores o iguales a '.$dias_totales .' d&iacute;as', "info");
									break;
								}

								if ($dias < 30) { 
									toba::notificacion()->agregar('Los días de vacaciones no pueden ser menores de 30 d&iacute;as', "info");
									break;
									}else {
									
									if ($agrego == 0 and $insertadas > 0 and $hay_cargadas == 0){
									$dias_restantes = $dias_totales - $dias;
										if ($dias_restantes <> 0){
										$sql ="INSERT INTO reloj.vacaciones_restantes(
										legajo, cod_depcia, agrupamiento, anio, dias)
										VALUES ($legajo,4, '$agrupamiento', $anio, $dias_restantes);
										DELETE FROM reloj.vacaciones_restantes
										where legajo=$legajo
										AND anio < $anio;";
										toba::db('comision')->ejecutar($sql);
										$agrego=1;
										}
									}
								}    
							}else {
								$dias_totales = 30 +$dia_restantes;
								if ($dias >$dias_totales ) {
									toba::notificacion()->agregar('Los días de vacaciones tienen que ser menores o iguales a '.$dias_totales .' d&iacute;as', "info");
									break;
								}
								if ($dias < 30) { 
									toba::notificacion()->agregar('Los días de vacaciones no pueden ser menores de 30 d&iacute;as', "info");
									break;
								}else {
									if ($agrego == 0 and $insertadas > 0 and $hay_cargadas == 0){
									$agrego=1;
											}
									$dias_restantes = $dias_totales - $dias;
									if ($dias_restantes <> 0){
									$sql ="INSERT INTO reloj.vacaciones_restantes( legajo, cod_depcia, agrupamiento, anio, dias)
										VALUES ($legajo,4, '$agrupamiento', $anio, $dias_restantes);
										DELETE FROM reloj.vacaciones_restantes
										where legajo=$legajo
										AND anio < $anio;";
									toba::db('comision')->ejecutar($sql);
									}
								}    

							//ei_arbol($dias_totales);}*/

							/*if (date("Y-m-d") >= '2023-12-08'){ //Quitar comentario para limite de dias
							toba::notificacion()->agregar('Se ha pasado la fecha limite de carga de Licencia Anual', "info");

									$bandera_nodo=false;
						} else {	*/
							$bandera= true;
							// }	//Quitar comentario para limite de dias
								
							
				// VAcaciones pendientes docentes            
				}else if ($id_motivo == 57){
					$agente[$i]['articulo'] = null;
					$agente[$i]['id_decreto'] = 8;
					$datos['anio'] = $anio -1;
							
							//ei_arbol ($agente);
							$sql= "SELECT sum(dias)  dias_rest from reloj.vacaciones_restantes
									WHERE legajo = $legajo AND anio <= $anio;";
									

							$resto = toba::db('comision')->consultar($sql);
							$dias_pendientes = $resto[0]['dias_rest'];
							//ei_arbol($resto);
								if ($dias <= $dias_pendientes){
									
							$agente[$i]['articulo'] = 104;
							$bandera= true;    
							$datos ['dias_restantes'] = $dias_pendientes;
							} else {
								toba::notificacion()->agregar('Usted cuenta con  '.$dias_pendientes .' d&iacute;as, coloque una cantidad de d&iacute;as validos.', "info");
							$bandera = false;    
							}
				
				}else if($id_motivo==12){ // Donación de Sangre
					if ($dias==1) {
						$agente[$i]['articulo'] = null;
					$sql = "SELECT count(*) cant FROM reloj.parte 
								where id_motivo = $id_motivo
								and legajo = $legajo
								and anio = $anio";
						$tomo=toba::db('comision')->consultar($sql);
						if ($tomo[0]['cant'] <= 12)	{
						if ($datos['certificado'] <> null){
							$agente[$i]['articulo'] = 89;
							$agente[$i]['id_decreto'] = 8;
							$bandera= true; 
						} else {
							toba::notificacion()->agregar('Por favor adjunte el Certificado correspondiente', "info");
							$bandera = false;   
						}
					} else {
							toba::notificacion()->agregar('Ya hizo uso de todas estas licencia el presente año', "info");
							$bandera = false; 
					}
					} else {
							toba::notificacion()->agregar('Solamente puede tomar un 1 día', "info");
							$bandera = false;  
					}

				}else if($id_motivo==22){ // Actividades Deportivas
					if ($dias >= 1 and $dias <=5 ) {
						$agente[$i]['articulo'] = null;
					$sql = "SELECT count(*) cant FROM reloj.parte 
								where id_motivo = $id_motivo
								and legajo = $legajo
								and anio = $anio";
						$tomo=toba::db('comision')->consultar($sql);
						if ($tomo[0]['cant'] <= 5)	{
						if ($datos['certificado'] <> null){
							$agente[$i]['articulo'] = 83;
							$agente[$i]['id_decreto'] = 8;
							$bandera= true; 
						} else {
							toba::notificacion()->agregar('Por favor adjunte el Certificado correspondiente', "info");
							$bandera = false;   
						}
					} else {
							toba::notificacion()->agregar('Ya hizo uso de todas estas licencia el presente año', "info");
							$bandera = false; 
					}
					} else {
							toba::notificacion()->agregar('Solamente puede tomar hasta 5 días en el año', "info");
							$bandera = false;  
					}

				}else if($id_motivo==49){ // Citación Judicial
					
						$agente[$i]['articulo'] = null;
						if ($datos['certificado'] <> null){
							$agente[$i]['articulo'] = 97;
							$agente[$i]['id_decreto'] = 9;
							$bandera= true; 
						} else {
							toba::notificacion()->agregar('Por favor adjunte el Certificado correspondiente', "info");
							$bandera = false;   
						}
					

				}else if($id_motivo==17){ // Fallecimiento de Conyúge y 1º grado
						
						$agente[$i]['articulo'] = null;
						if ($datos['certificado'] <> null){
							$dias = 7;
							$datos['dias'] = $dias;
							$agente[$i]['articulo'] = 86;
							$agente[$i]['id_decreto'] = 8;
							$bandera= true; 
						} else {
							toba::notificacion()->agregar('Por favor adjunte el Certificado correspondiente', "info");
							$bandera = false;   
						}
					

				} else if($id_motivo==16){ // Fallecimiento de Familiar Cosanguineo 2º
						
						$agente[$i]['articulo'] = null;
						if ($datos['certificado'] <> null){
							$dias = 5;
							$datos['dias'] = $dias;
							$agente[$i]['articulo'] = 87;
							$agente[$i]['id_decreto'] = 8;
							$bandera= true; 
						} else {
							toba::notificacion()->agregar('Por favor adjunte el Certificado correspondiente', "info");
							$bandera = false;   
						}
					

				} else if($id_motivo==27){ //Nacimiento (Paternidad)
					
						$agente[$i]['articulo'] = null;
						if ($agente [$i]['tipo_sexo'] == 'M') {
							if ($datos['certificado'] <> null){
								$dias = 15;
								$agente[$i]['articulo'] = 85;
								$agente[$i]['id_decreto'] = 8;
								$bandera= true; 
							} else {
								toba::notificacion()->agregar('Por favor adjunte el Certificado correspondiente', "info");
								$bandera = false;   
							}
						} else {
							toba::notificacion()->agregar('Esta Licencia es por Paternidad', "info");
							$bandera = false;   
						}

				} else if($id_motivo==36){ //Matrimonio
					
						$agente[$i]['articulo'] = null;
							if ($datos['certificado'] <> null){
								$dias = 10;
								$agente[$i]['articulo'] = 81;
								$agente[$i]['id_decreto'] = 8;
								$bandera= true; 
							} else {
								toba::notificacion()->agregar('Por favor adjunte el Certificado correspondiente', "info");
								$bandera = false;   
							}
						

				} else if($id_motivo==25){ //Matrimonio de un hijo/a
					
						$agente[$i]['articulo'] = null;
							if ($datos['certificado'] <> null){
								$dias = 2;
								$agente[$i]['articulo'] = 82;
								$agente[$i]['id_decreto'] = 8;
								$bandera= true; 
							} else {
								toba::notificacion()->agregar('Por favor adjunte el Certificado correspondiente', "info");
								$bandera = false;   
							}
						

				} else if($id_motivo == 25){ //Adopcion (Maternidad)
					
						$agente[$i]['articulo'] = null;
							if ($datos['certificado'] <> null){
								$dias = 60;
								$agente[$i]['articulo'] = 79;
								$agente[$i]['id_decreto'] = 8;
								$bandera= true; 
							} else {
								toba::notificacion()->agregar('Por favor adjunte el Certificado correspondiente', "info");
								$bandera = false;   
							}
						

				} 	
				 else if($id_motivo == 59){ //Adopcion (Paternidad)
					
						$agente[$i]['articulo'] = null;
							if ($datos['certificado'] <> null){
								$dias = 15;
								$agente[$i]['articulo'] = 44;
								$agente[$i]['id_decreto'] = 2;
								$bandera= true; 
							} else {
								toba::notificacion()->agregar('Por favor adjunte el Certificado correspondiente', "info");
								$bandera = false;   
							}
						

				} else if($id_motivo==15){ // Rendir examen Universitario o posgrado
					if ($dias >= 1 and $dias <=5 ) {
						$agente[$i]['articulo'] = null;
					$sql = "SELECT count(*) cant FROM reloj.parte 
								where id_motivo = $id_motivo
								and legajo = $legajo
								and anio = $anio";
						$tomo=toba::db('comision')->consultar($sql);
						if ($tomo[0]['cant'] <= 28)	{
						if ($datos['certificado'] <> null){
							$agente[$i]['articulo'] = 94;
							$agente[$i]['id_decreto'] = 8;
							$bandera= true; 
						} else {
							toba::notificacion()->agregar('Por favor adjunte el Certificado correspondiente', "info");
							$bandera = false;   
						}
					} else {
							toba::notificacion()->agregar('Ya hizo uso de todas estas licencia el presente año', "info");
							$bandera = false; 
					}
					} else {
							toba::notificacion()->agregar('Solamente puede tomar como máximo 5 días por Certificado', "info");
							$bandera = false;  
					}
				}else if ($id_motivo == 47){ //Examen para rendir concurso
					if ($dias >= 1 and $dias <=3 ) {
						$agente[$i]['articulo'] = null;
					$sql = "SELECT count(*) cant FROM reloj.parte 
								where id_motivo = $id_motivo
								and legajo = $legajo
								and anio = $anio";
						$tomo=toba::db('comision')->consultar($sql);
						if ($tomo[0]['cant'] <= 3)	{
						if ($datos['certificado'] <> null){
							$agente[$i]['articulo'] = 95;
							$agente[$i]['id_decreto'] = 8;
							$bandera= true; 
						} else {
							toba::notificacion()->agregar('Por favor adjunte el Certificado correspondiente', "info");
							$bandera = false;   
						}
					} else {
							toba::notificacion()->agregar('Ya hizo uso de todas estas licencia el presente año', "info");
							$bandera = false; 
					}
					} else {
							toba::notificacion()->agregar('Solamente puede tomar como máximo 3 días por Certificado', "info");
							$bandera = false;  
					}
					

				} else if ($id_motivo == 61 ) {
					if (date("Y") == $anio){
							if ($dias <= 2){
							for ($j=0; $j < $cant; $j++){
							$agente [$j]['articulo'] = 0;
							$agente[$j]['id_decreto'] = 8;	
							}
							
							$sql = "SELECT -SUM(dias) +2 dias_restantes 
									FROM reloj.parte
							WHERE legajo = $legajo
							AND id_motivo = 61
							AND  DATE_PART('month', fecha_inicio_licencia) = $m";
							$parte = toba::db('comision')->consultar($sql);
							/*$sql = "SELECT fecha_inicio, fecha_fin*/
							$sql = "SELECT  fecha_fin - fecha_inicio + 1 dias_rp
								FROM reloj.inasistencias
								Where legajo = $legajo AND id_motivo=61 AND extract (month from fecha_inicio)=$m And extract(year from fecha_inicio) = $anio";

							$pendiente = toba::db('comision')->consultar($sql);
								$lim = count($pendiente);
								$dias_tomados = 0;
							//ei_arbol($pendiente);	
								for ($i=0; $i<$lim; $i++){
								$dias_tomados = $dias_tomados + $pendiente[$i]['dias_rp'];
									
								}

							
							$temp[0]['dias_restantes'] = $parte[0]['dias_restantes']+ $dias_tomados + $dias;
							//ei_arbol($temp);
								if(!is_null($temp)&&($temp[0]['dias_restantes'] >= 0 && $temp[0]['dias_restantes']<=2 )){
									$sql="SELECT -SUM(dias) +6 dias_restantes 
									FROM reloj.parte
									WHERE legajo = $legajo
									AND id_motivo = 61
									AND  DATE_PART('year', fecha_inicio_licencia) = $y";
									$temp = toba::db('comision')->consultar($sql);    
									//ei_arbol($temp);
										if (is_null($temp[0]['dias_restantes'])|| ($temp[0]['dias_restantes'] >= 0 && $temp[0]['dias_restantes']<=6 )){
										$lim = count($agente);
										for ($i = 0; $i<$lim; $i++){	
										$agente [$i]['articulo'] = 109;
										}
									$bandera= true;
									//ei_arbol($agente);

									} else {
									toba::notificacion()->agregar('Ud ha excedido la cantidad anual de razones particulares este a&ntilde;o cuenta con '.$temp[0]['dias_restantes'] .' d&iacute;as', "info");
									} 
								} else 
								{
								
								//ei_arbol($agente);    
									$temp[0]['dias_restantes'] = $parte[0]['dias_restantes'] + $dias_tomados -2;
									if ($temp[0]['dias_restantes'] >= 0 ) {
										$temp[0]['dias_restantes'] = 0;
									} else {
										$temp[0]['dias_restantes'] =abs($temp[0]['dias_restantes']);
									}
									
								toba::notificacion()->agregar('Ud ha excedido la cantidad mensual de razones particulares este mes cuenta con '.$temp[0]['dias_restantes'] .' días', "info");
								}
								
								
							} 
							/*else {
								toba::notificacion()->agregar('Ud ha excedido la cantidad de d&iacute;as recuerde que las razones particulares son entre 1 y 2 d&iacute;as' , "info");                                
							}*/
						} else {
						toba::notificacion()->agregar('Introduzca el corriente a&ntildeo. Gracias ', "info");

									$bandera_nodo = false;

						}      

				} 	

				 /*else if ($id_motivo == 55){ //adelanto de Vacaciones
					$agente[$i]['id_decreto'] = 2;
					if ($anio != date("Y")) {
						toba::notificacion()->agregar('Para pedir Adelanto de vacaciones recuerde colocar el a&ntildeo en curso', "info");
						$bandera = false;

					} else {
						$sql = "SELECT dias_adelanto FROM reloj.vacaciones_adelantadas
							where legajo = $legajo and anio = $anio; ";
						$ade = toba::db('comision')->consultar($sql);    
						$adelanto = $ade[0]['dias_adelanto'];
						$bandera = true;

						if ($antiguedad > 20){
							$dias_vacaciones = 40 - $adelanto ;


						} elseif ($antiguedad > 15 && $antiguedad <=20) {
							$dias_vacaciones = 35 - $adelanto;

						} elseif($antiguedad > 10 && $antiguedad <=15) {
							$dias_vacaciones = 30 - $adelanto;

						}elseif ($antiguedad > 5 && $antiguedad <=10) {
							$dias_vacaciones = 25 - $adelanto;

						}elseif ($antiguedad > 0.5 && $antiguedad <=5){
							$dias_vacaciones = 20 - $adelanto;

						} else {
							$bandera = false;
							toba::notificacion()->agregar('Ud. no cuenta con la antig&uuml;edad suficiente para solicitar Adelanto de Vacaciones', "info");

						}
						if ($bandera){
							if ($dias <= $dias_vacaciones){
								$agente[$i]['articulo'] = 56;
								$dias_adelantados = $adelanto + $dias;

							} else {
							toba::notificacion()->agregar('Ud.  cuenta con '. $dias_vacaciones. ' por favor corrija los d&iacute;as para solicitar Adelanto de Vacaciones', "info");
							$bandera = false;    

							}

						}

					}



			}*/
		}

		$edad = $this->dep('mapuche')->get_edad($legajo, null);
			$datos['dias_restantes'] = $dias_restantes;
		//ei_arbol($agente);    
		if($bandera) {
			$fecha_inicio_licencia = $datos['fecha_inicio_licencia'];
				$fechaentera1 =strtotime($fecha_inicio_licencia);
			$fecha = date_create(date("Y-m-d",$fechaentera1)); 
		//$fecha=date('d/m/Y',strtotime($datos['fecha_inicio_licencia'] ) );
		//ei_arbol($agente);
			$fecha_inicio =$fecha ->format("Y-m-d");
			$dias = $dias - 1;
			$dias_to= $dias. ' days';
			$hasta = date_add($fecha , date_interval_create_from_date_string($dias_to));
			$hasta =$hasta ->format("Y-m-d"); 

			for ($i=0; $i < $cant; $i++){
				$fecha_alta =date("Y-m-d H:i:s");
				$datos ['fecha_alta']=$fecha_alta;
				$usuario_alta = $datos['legajo'];
				$estado = 'A';
				$datos['fecha_inicio'] = $fecha_inicio;
				$datos['hasta']=$hasta;
				$dias= $datos['dias'];
				$cod_depcia = '04';
				$observaciones = $datos['observaciones'];
				$id_motivo = $datos['id_motivo'] ;
				if (isset($agente[$i]['id_decreto'])){
					$id_decreto = $agente[$i]['id_decreto'];
				}
				$datos['id_decreto'] = $id_decreto;
				if (isset($agente[$i]['articulo'])){
					$articulo=$agente[$i]['articulo'];
					$datos['articulo'] = $articulo;
				}
				$catedra=$datos['catedra'];
				$anio=$datos['anio'];
				$superior= $datos['superior'];
				$autoridad=$datos['autoridad'];
				
			}


			
			//ei_arbol($datos);
			if($datos['fecha_inicio_licencia']< '2022-12-26'){
				toba::notificacion()->agregar('Ingrese una fecha mayor o igual al 26/12/2022', "info");
			}else
			{
			//	ei_arbol($datos);
				if ($ya_tomo == 0){
						if($bandera_nodo ){
						//	ei_arbol($id_motivo.' motivo', $id_decreto. ' decreto', $articulo.' articulo');
							if ($id_motivo == 30){
								$sql= "INSERT INTO reloj.inasistencias(	legajo, id_catedra, fecha_inicio, fecha_fin, anio, observaciones, leg_sup, auto_sup, leg_aut, auto_aut, fecha_alta, usuario_alta, estado, id_motivo, id_decreto,id_articulo) VALUES ($usuario_alta, $catedra, '$fecha_inicio', '$hasta',$anio, '$observaciones', $superior, true, $autoridad, true, '$fecha_alta',$usuario_alta ,'A', $id_motivo, $id_decreto,$articulo);";
							} else if ($id_motivo == 57){		
							$sql= "INSERT INTO reloj.inasistencias(	legajo, id_catedra, fecha_inicio, fecha_fin, anio, observaciones, leg_sup, auto_sup, leg_aut, auto_aut, fecha_alta, usuario_alta, estado, id_motivo, id_decreto,id_articulo) VALUES ($usuario_alta, $catedra, '$fecha_inicio', '$hasta',$anio, '$observaciones', $superior, true, $autoridad, true, '$fecha_alta',$usuario_alta ,'A', $id_motivo, $id_decreto,$articulo);";
							} else 
							{
								$sql= "INSERT INTO reloj.inasistencias( legajo, id_catedra, fecha_inicio, fecha_fin, anio, observaciones, leg_sup, auto_sup, leg_aut, auto_aut, fecha_alta, usuario_alta, estado, id_motivo, id_decreto, id_articulo)    VALUES ( $usuario_alta, $catedra, '$fecha_inicio', '$hasta',$anio, '$observaciones', $superior, true, $autoridad, true, '$fecha_alta',$usuario_alta ,'A', $id_motivo, $id_decreto, $articulo);";

							}


/*
		
		$sql = "INSERT INTO reloj.parte(
		legajo, edad, fecha_alta, usuario_alta, estado, fecha_inicio_licencia, dias, cod_depcia, domicilio, localidad, agrupamiento, fecha_nacimiento,
		apellido, nombre, estado_civil, observaciones, id_decreto, id_motivo, id_articulo, tipo_sexo)
	VALUES ($legajo, $edad, '$fecha_alta', $usuario_alta, '$estado', '$fecha_inicio', $dias, '$cod_depcia', '$domicilio', '$localidad', '$agrupamiento', '$fecha_nacimiento', 
		'$apellido', '$nombre',    '$estado_civil', '$observaciones', $id_decreto, $id_motivo,$articulo,'$tipo_sexo');";*/
	

					toba::db('comision')->ejecutar($sql);

					if ($id_motivo <> 30){
					  if ($id_motivo <> 57){
					  	if ($id_motivo <> 61 ){	
					   		if($id_motivo <> 35 ) {

							$sql = "SELECT id_inasistencia FROM reloj.inasistencias
								WHERE legajo = $usuario_alta
								AND fecha_inicio = '$fecha_inicio'
								AND id_motivo = $id_motivo ;";
							$ina = toba::db('comision')->consultar($sql);
							$id_inasistencia = $ina [0]['id_inasistencia'];
							$ruta='C:/Toba/proyectos/ctrl_asis/www/certificados/';
							$ar_nombre_completo = explode('.', $datos['certificado']['name']);
							$archivo_nombre = $ruta.$id_inasistencia.$fecha_inicio.'.pdf' ;
							$datos['archivo'] = $archivo_nombre;
							$datos = $this->procesar_archivo($datos);
							}	
					 	}		
					 }
					}
	
		/////
		//actualizacion o borrado de vacaciones restantes
		//////
					if ($id_motivo == 57){

			toba::notificacion()->agregar('Parte de inasistencia agregado correctamente.', 'info');            
			if ($dias == $dias_pendientes){
				$sql1 = "DELETE FROM reloj.vacaciones_restantes
				where legajo = $legajo
				and anio =$anio ";    
			} else {
				$dias_pendientes = $dias_pendientes - $dias;
				if ($dias_pendientes > 0){
					$datos ['dias_restantes'] = $dias_pendientes;
					$sql1 = "UPDATE reloj.vacaciones_restantes
					set dias = $dias_pendientes
					where legajo = $legajo
					AND anio=$anio ";    
				}
			//toba::notificacion()->agregar('Parte de inasistencia agregado correctamente.', 'info');        
				
			}
			toba::db('comision')->ejecutar($sql1);

		//    toba::notificacion()->agregar('Ud. ya completo el fomulario para '. $depto[0]['nombre_catedra'] , "info");

					} else if ($id_motivo == 55) { 
					/// actualizacion e Insersion de adelantos de vacaciones
			if(isset($adelanto) ){
				$sql1= "UPDATE reloj.vacaciones_adelantadas
				SET dias_adelanto = $dias_adelantados
				WHERE legajo =$legajo and anio = $anio;";
			} else {
				$sql1 = "INSERT INTO reloj.vacaciones_adelantadas (legajo,anio,dias_adelanto)
				VALUES ($legajo, $anio,$dias_adelantados);";
			}
			toba::db('comision')->ejecutar($sql1);    
			toba::notificacion()->agregar('Parte de inasistencia agregado correctamente.', 'info');    
					}


		
		//$this->dep('datos')->tabla('parte')->set($datos);
	
					if (isset ($catedra) and $catedra <> 0 ){

						$sql = "SELECT nombre_catedra from reloj.catedras
						WHERE id_catedra = $catedra;";
						$cat= toba::db('comision')->consultar($sql);
						$datos['catedra'] = $cat[0]['nombre_catedra'];

					}        
		
					if (isset($legajo)){
					//$correo_agente = $this->dep('mapuche')->get_legajos_email($datos['legajo']);
					$correo_agente=$this->dep('datos')->tabla('agentes_mail')->get_correo($datos['legajo']);
					$datos['agente_ayn']=$correo_agente[0]['descripcion'];
		
					}

					if(isset($datos['superior'])and $datos['superior']<>0) {
					//$correo_sup = $this->dep('mapuche')->get_legajos_email($datos['superior']);
					$correo_sup=$this->dep('datos')->tabla('agentes_mail')->get_correo($datos['superior']);	
					
					$datos['superior_ayn']=$correo_sup[0]['descripcion'];
					}

					if(isset($datos['autoridad'])) {
				//	$correo_aut = $this->dep('mapuche')->get_legajos_email($datos['autoridad']);
					$correo_aut=$this->dep('datos')->tabla('agentes_mail')->get_correo($datos['autoridad']);
					$datos['autoridad_ayn']=$correo_aut[0]['descripcion'];
		
					}
					$agente= $this -> dep('mapuche')->get_legajo_todos($legajo); 
					$datos['descripcion']= $agente[0]['descripcion'];
					
					$this->s__datos = $datos;
	
					if (isset($legajo)){
					$sql= "SELECT email from reloj.agentes_mail
					where legajo=$legajo";
					$correo = toba::db('comision')->consultar($sql);
					$this->enviar_correos($correo[0]['email']);
					}

					if(isset($datos['superior'])and $datos['superior']<>0) {
					$superior =$datos['superior'];

					$sql= "SELECT email from reloj.agentes_mail
					where legajo=$superior";
					$correo = toba::db('comision')->consultar($sql);
					$this->enviar_correos_sup($correo[0]['email'],$datos['superior_ayn']);


					}

		/*if(isset($datos['autoridad'])) {
		$superior= $datos['autoridad'];
		//ei_arbol ($superior);

		$sql= "SELECT email from reloj.agentes_mail
		where legajo=$superior";
		$correo = toba::db('comision')->consultar($sql);
		$this->enviar_correos_sup($correo[0]['email'],$datos['autoridad_ayn']);

		}*/
				}
		} else {
			toba::notificacion()->agregar('Ud. ya completo el fomulario para '. $depto[0]['nombre_catedra'] , "info");

		}
		}

		}    
	} 
	}else {
		toba::notificacion()->agregar("Coloque un d&iacute;a mayor que 0","info");

	}        
	}    

	
	function evt__guardar()
	{

		

			//verificamos que no exista otro parte abiertos(estado) con el mismo legajo, motivo y dependencia.
			$datos = $this->dep('datos')->tabla('parte')->get();
			//ei_arbol ($datos);    
			/*$filtro['legajo']     = $datos['legajo'];
			$filtro['id_motivo']  = $datos['id_motivo'];
			$filtro['cod_depcia'] = 04;*/
			
			$this->dep('datos')->sincronizar();
			toba::notificacion()->agregar('Parte de inasistencia agregado correctamente.', 'info');
		
	
	}
function enviar_correos($correo)
	{
		require_once('3ros/phpmailer/class.phpmailer.php');


				$datos =$this->s__datos;  
//ei_arbol($datos);
if ($datos['id_motivo'] == 30) {
	$datos['dias']=$datos['dias']-1;

} 
	$fecha=date('d/m/Y',strtotime($datos['fecha_inicio_licencia'] ) );

	$hasta=date('d/m/Y',strtotime($datos['hasta']) );
	

if ($datos['dias_restantes'] <= 0){
	$datos['dias_restantes'] = 0;
}
	

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
$mail->Password   = "gvcghltncpblkjbl";
//Definimos el remitente (dirección y, opcionalmente, nombre)
$mail->SetFrom('formularios_asistencia@fca.uncu.edu.ar', 'Formulario Personal');
//Esta línea es por si queréis enviar copia a alguien (dirección y, opcionalmente, nombre)

//$mail->AddReplyTo('caifca@fca.uncu.edu.ar','El de la réplica');
//Y, ahora sí, definimos el destinatario (dirección y, opcionalmente, nombre)
//$mail -> AddAddress('ebermejillo@fca.uncu.edu.ar', 'Tester');
$mail->AddAddress($correo, 'El Destinatario'); //Descomentar linea cuando pase a produccion
//Definimos el tema del email


//Para enviar un correo formateado en HTML lo cargamos con la siguiente función. Si no, puedes meterle directamente una cadena de texto.
//$mail->MsgHTML(file_get_contents('correomaquetado.html'), dirname(ruta_al_archivo));
//Y por si nos bloquean el contenido HTML (algunos correos lo hacen por seguridad) una versión alternativa en texto plano (también será válida para lectores de pantalla)
$mail->IsHTML(true); //el mail contiene html


	if ($datos['id_motivo'] == 30) {
		//$motivo = 'Razones Particulares con gose de haberes';
		$mail->Subject = 'Formulario de Solicitud Razones Particulares';
		$body = '<table>
						El/la agente  <b>'.$datos['descripcion'].'</b> perteneciente a la catedra/oficina/ direcci&oacute;n <b>'.$datos['catedra'].'</b>.<br/>
						Solicita Justificaci&oacute;n de Inasistencia por Razones Particulares a partir del d&iacute;a '.$fecha.' hasta '.$hasta. '.
							Teniendo en cuenta las siguientes Observaciones: ' .$datos['observaciones']. '
											
			</table>';

	} else if ($datos['id_motivo'] == 35)
	{
			$mail->Subject = 'Formulario de Licencia Anual por Vacaciones';
		//$motivo = 'Vacaciones'.$datos['anio'];
		$body = '<table>
						El/la agente  <b>'.$datos['descripcion'].'</b> perteneciente a  <b>'.$datos['catedra'].'</b>.<br/>
						Solicita laLicencia Anual por Vacaciones correspondiente al año '.$datos['anio'].' a partir del d&iacute;a '.$fecha.' hasta '.$hasta. '. <br/>
						
											
			</table>'; 
	/*} else if ($datos['id_motivo'] == 55)
	{
		$mail->Subject = 'Formulario de Adelanto de Licencia Anual';
		$body = '<table>

				El/la agente <b>'.$datos['agente_ayn'].'</b> perteneciente a <b>'.$datos['catedra'].'</b> <br/>
				Solicita adelanto de licencia anual correspondiente al' .$datos['anio']. ' a partir del d&iacute;a'.$fecha. ' hasta '.$hasta. '<br/>
				Teniendo en cuenta las siguientes Observaciones: ' .$datos['observaciones']. 'Estos d&iacute;as de adelanto que ud ha solicitado,
				serán restados del total de vacaciones correspondientes al año en curso
			<table/>';*/


	} else if ($datos ['id_motivo'] == 57)
	{
		$mail->Subject = 'Formulario de D&iacute&as Pendientes de la Licencia Anual';
		$body = '<table>

				El/la agente <b>'.$datos['descripcion'].'</b> perteneciente a <b>'.$datos['catedra'].'</b> <br/>
				Solicita los d&iacute;as pendientes de la licencia anual correspondiente al ' .$datos['anio']. ' a partir del d&iacute;a '.$fecha. ' hasta '.$hasta. '<br/>
				Teniendo en cuenta las siguientes Observaciones: ' .$datos['observaciones'].  '<br/>
				Ud. cuenta con '.$datos['dias_restantes'].' d&iacute;as de vacaciones pendientes.
			<table/>';


	} else if ($$datos ['id_motivo'] == 61)
	 	{
			$mail->Subject = 'Formulario de Justificacion de Inasistencia por Excesos de Inasistencia (SIN GOCE)';			
			$body = '<table>

				El/la agente <b>'.$datos['descripcion'].'</b> perteneciente a <b>'.$datos['catedra'].'</b> <br/>
				Solicita solicita Razones Particulares a partir SIN GOCE  a partir del d&iacute;a '.$fecha. ' hasta '.$hasta. '<br/>
				Teniendo en cuenta las siguientes Observaciones: ' .$datos['observaciones'].  '<br/>
			<table/>';	

		} else {
		$body = '<table>

				El/la agente <b>'.$datos['descripcion'].'</b> perteneciente a <b>'.$datos['catedra'].'</b> <br/>
				Justific&oacute;  la inasistencia  desde '.$fecha.' hasta '.$hasta.' presentando el certificado correspondiente a dicha acci&oacute;n.
			<table/>';
		
		switch ($datos ['id_motivo'] ){
			case 12:
				$mail->Subject = 'Formulario de Justificacion de Inasistencia por Donaci&oacute;n de Sangre';
				break;
			case 22:
				$mail->Subject = 'Formulario de Justificacion de Inasistencia por Realización de Actividad Deportiva o Art&iacute;stica';
				break;
			case 49:
				$mail->Subject = 'Formulario de Justificacion de Inasistencia por Citaci&oacute;n Judicial';
				break;
			case 17:
				$mail->Subject = 'Formulario de Justificacion de Inasistencia por Fallecimiento de Cony&uacute;ge o Pariente de Primer Grado';
				break;
			case 16:
				$mail->Subject = 'Formulario de Justificacion de Inasistencia por Fallecimiento de Pariente de Segundo Grado';
				break;
			case 18: 
				$mail->Subject = 'Formulario de Justificacion de Inasistencia por Fallecimiento de Pariente Pol&iacute;tico';
				break;
			case 27: 
				$mail->Subject = 'Formulario de Justificacion de Inasistencia por Nacimiento (Paternidad)';
				break;	
			case 36: 
				$mail->Subject = 'Formulario de Justificacion de Inasistencia por Matrimonio';
				break;
			case 25: 
				$mail->Subject = 'Formulario de Justificacion de Inasistencia por Matrimonio de hijo/a';
				break;				
			case 7: 
				$mail->Subject = 'Formulario de Justificacion de Inasistencia por Adopci&oacute;n (Maternidad)';
				break;
			case 59: 
				$mail->Subject = 'Formulario de Justificacion de Inasistencia por Adopci&oacute;n (Paternidad)';
				break;	
			case 14: 
				$mail->Subject = 'Formulario de Justificacion de Inasistencia por Exam&eacute;n de Nivel Medio';
				break;		
			case 15: 
				$mail->Subject = 'Formulario de Justificacion de Inasistencia por Exam&eacute;n de Nivel Superior';
				break;
			case 47:
				$mail->Subject = 'Formulario de Justificacion de Inasistencia por Exam&eacute;n para Concurso';
				break;
			case 61: 
				$mail->Subject = 'Formulario de Justificacion de Inasistencia por Excesos de Inasistencia (SIN GOCE)';			
				break;	
			}
	
	}


	

	; //date("d/m/y",$fecha)
//ei_arbol ($body);
$mail->Body = $body;
//Enviamos el correo
if(!$mail->Send()) {
	echo "Error: " . $mail->ErrorInfo;
} else {
	toba::notificacion()->agregar('Su formulario ha sido completado y enviado a su correo. Ya puede cerrar la ventana.' , "info");
	echo "Enviado!";
	
}
	

		
	}
function enviar_correos_sup($correo,$destino)
	{
		require_once('3ros/phpmailer/class.phpmailer.php');

				$datos =$this-> s__datos;   
				$fecha=date('d/m/Y',strtotime($datos['fecha_inicio_licencia'] ) );

	$hasta=date('d/m/Y',strtotime($datos['hasta']) );
	
           

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
$mail->Password   = "gvcghltncpblkjbl";
//Definimos el remitente (dirección y, opcionalmente, nombre)
$mail->SetFrom('formularios_asistencia@fca.uncu.edu.ar', 'Formulario Personal');
//Esta línea es por si queréis enviar copia a alguien (dirección y, opcionalmente, nombre)
//$mail->AddReplyTo('caifca@fca.uncu.edu.ar','El de la réplica');
//Y, ahora sí, definimos el destinatario (dirección y, opcionalmente, nombre)
$mail->AddAddress($correo, $destino);
//Definimos el tema del email
//$mail->Subject = 'Formulario de Vacaciones del Agente ' .$datos['agente_ayn'];
//Para enviar un correo formateado en HTML lo cargamos con la siguiente función. Si no, puedes meterle directamente una cadena de texto.
//$mail->MsgHTML(file_get_contents('correomaquetado.html'), dirname(ruta_al_archivo));
//Y por si nos bloquean el contenido HTML (algunos correos lo hacen por seguridad) una versión alternativa en texto plano (también será válida para lectores de pantalla)
$mail->IsHTML(true); //el mail contiene html
	
	

	if ($datos['id_motivo'] == 30) {
		//$motivo = 'Razones Particulares con gose de haberes';
		$mail->Subject = 'Formulario de Solicitud Razones Particulares del Agente ' .$datos['agente_ayn'];
		$body = '<table>
						El/la agente  <b>'.$datos['descripcion'].'</b> perteneciente a la <b>'.$datos['catedra'].'</b> solicita <b> Razones Particulares </b> a partir del d&iacute;a '.$fecha.' hasta '.$hasta. '.<br/>
							Observaciones: ' .$datos['observaciones']. ' - <br/>
							En caso de no estar de acuerdo con la autorizacion enviar un correo a asistencia@fca.uncu.edu.ar .

											
			</table>';

	} else if ($datos['id_motivo'] == 35)
	{
			
		//$motivo = 'Vacaciones'.$datos['anio'];
		$mail->Subject = 'Formulario de Solicitud Licencia Anual del Agente ' .$datos['agente_ayn'];
		$body = '<table>

						El/la agente  <b>'.$datos['descripcion'].'</b> perteneciente a  la <b>'.$datos['catedra'].'</b>.<br/>
						Solicita <b> Licencia Anual por Vacaciones </b> correspondiente al  '.$datos['anio'].' a partir del d&iacute;a '.$fecha.' hasta '.$hasta. '.<br/>
						En caso de no estar de acuerdo con la autorizacion enviar un correo a asistencia@fca.uncu.edu.ar .

											
			</table>';
	} else if ($datos ['id_motivo'] == 57)
	{
		$mail->Subject = 'Formulario de D&iacute&as Pendientes de la Licencia Anual del Agente' .$datos['agente_ayn'];
		$body = '<table>

				El/la agente <b>'.$datos['descripcion'].'</b> perteneciente a <b>'.$datos['catedra'].'</b> <br/>
				Solicita <b>los d&iacute;as pendientes de la licencia anual</b> correspondiente al ' .$datos['anio']. ' a partir del d&iacute;a '.$fecha. ' hasta '.$hasta. '<br/>
				Teniendo en cuenta las siguientes Observaciones: ' .$datos['observaciones'].  '<br/>
				En caso de no estar de acuerdo con la autorizacion enviar un correo a asistencia@fca.uncu.edu.ar .
			<table/>';


	}else if ($$datos ['id_motivo'] == 61)
	 	{
			$mail->Subject = 'Formulario de Justificacion de Inasistencia por Excesos de Inasistencia (SIN GOCE)';			
			$body = '<table>

				El/la agente <b>'.$datos['descripcion'].'</b> perteneciente a <b>'.$datos['catedra'].'</b> <br/>
				Solicita solicita Razones Particulares a partir SIN GOCE  a partir del d&iacute;a '.$fecha. ' hasta '.$hasta. '<br/>
				Teniendo en cuenta las siguientes Observaciones: ' .$datos['observaciones'].  '<br/>
				En caso de no estar de acuerdo con la autorizacion enviar un correo a asistencia@fca.uncu.edu.ar .
			<table/>';	

		}

	else {
		$mail->Subject = 'Formulario de Justificaci&oacute;n de Inasistencia por Donacion de Sangre';
		$body = '<table>

				El/la agente <b>'.$datos['descripcion'].'</b> perteneciente a <b>'.$datos['catedra'].'</b> <br/>
				Justific&oacute;  la inasistencia el dia '.$fecha.' presentando el certificado correspondiente a dicha acci&oacute;n.
			<table/>';
		switch ($datos ['id_motivo'] ){
			case 12:
				$mail->Subject = 'Formulario de Justificacion de Inasistencia por Donaci&oacute;n de Sangre';
				break;
			case 22:
				$mail->Subject = 'Formulario de Justificacion de Inasistencia por Realización de Actividad Deportiva o Art&iacute;stica';
				break;
			case 49:
				$mail->Subject = 'Formulario de Justificacion de Inasistencia por Citaci&oacute;n de Sangre';
				break;
			case 17:
				$mail->Subject = 'Formulario de Justificacion de Inasistencia por Fallecimiento de Cony&uacute;ge o Pariente de Primer Grado';
				break;
			case 16:
				$mail->Subject = 'Formulario de Justificacion de Inasistencia por Fallecimiento de Pariente de Segundo Grado';
				break;
			case 18: 
				$mail->Subject = 'Formulario de Justificacion de Inasistencia por Fallecimiento de Pariente Pol&iacute;tico';
				break;
			case 27: 
				$mail->Subject = 'Formulario de Justificacion de Inasistencia por Nacimiento (Paternidad)';
				break;	
			case 36: 
				$mail->Subject = 'Formulario de Justificacion de Inasistencia por Matrimonio';
				break;
			case 25: 
				$mail->Subject = 'Formulario de Justificacion de Inasistencia por Matrimonio de hijo/a';
				break;				
			case 7: 
				$mail->Subject = 'Formulario de Justificacion de Inasistencia por Adopci&oacute;n (Maternidad)';
				break;
			case 59: 
				$mail->Subject = 'Formulario de Justificacion de Inasistencia por Adopci&oacute;n (Paternidad)';
				break;	
			case 14: 
				$mail->Subject = 'Formulario de Justificacion de Inasistencia por Exam&eacute;n de Nivel Medio';
				break;		
			case 15: 
				$mail->Subject = 'Formulario de Justificacion de Inasistencia por Exam&eacute;n de Nivel Superior';
				break;			
			case 61: 
				$mail->Subject = 'Formulario de Justificacion de Inasistencia por Excesos de Inasistencia (SIN GOCE)';			
				break;	
			case 47:
				$mail->Subject = 'Formulario de Justificacion de Inasistencia por Exam&eacute;n para Concurso';
				break;	
		}

	}

	
	
//ei_arbol($body);
$mail->Body = $body;
//Enviamos el correo
if(!$mail->Send()) {
	echo "Error: " . $mail->ErrorInfo;
} else {
	echo "Enviado!";
}
}
	function procesar_archivo($datos)
      {
			//ei_arbol ($datos);        
          // guardo la dirección y nombre del archivo temporal donde se cargó
          // la imagen.
          $archivo = $datos['certificado']['tmp_name'];
         
          // formateo correctamente el campo archivo que se guarda en la base de
          // datos
        //  $datos['archivo'] = $archivo_nombre;
         
          // determino el directorio donde dejar el archivo definitivo
                  
          $dir_archivo = $ruta.$datos['archivo'];
         
          // copio el archivo temporal al directorio definitivo
          move_uploaded_file($archivo, $dir_archivo);
         
          //$datos = $this->limpiar_datos($datos, 'archivo');
         
         return $datos;
      }
   function extender_objeto_js(){
   	parent::extender_objeto_js();
   	$id_formulario = $this->dep('formulario')->get_id_objeto_js();
	echo "
		  $id_formulario.evt__id_motivo__procesar = function (es_inicial)
		  {
		  	if (this.ef('id_motivo').get_estado() == '35'){
		  		var ano = new Date().getFullYear();
		  		const inicio = new Date(ano, 11, 26);
		  		this.ef('fecha_inicio_licencia').set_fecha(inicio);
		  		this.ef('observaciones').set_estado('');
		  		this.ef('observaciones').desactivar();
		  		this.ef('fecha_inicio_licencia').desactivar();
		  	} else {
		  		this.ef('observaciones').activar();
		  		this.ef('fecha_inicio_licencia').activar();
		  	}
		  } 
		  ";   
   }

	//-----------------------------------------------------------------------------------
	//---- formulario -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__formulario(comision_ei_formulario $form)
	{
		include ("usuario_logueado.php");
		$legajo = usuario_logueado::get_legajo(toba::usuario()-> get_id());
		/*$sql = "SELECT apellido, nombre FROM reloj.agentes
				WHERE legajo = $legajo ;";
		$agente = toba::db('comision')->consultar($sql);*/
	
		$this->$s__agentes = $legajo;
		$datos['legajo'] = $legajo[0]['legajo'];
		$datos['apellido']=$legajo[0]['apellido'];
		$datos['nombre'] = $legajo [0]['nombre'];
		$form ->set_datos($datos);

	}

}
?>