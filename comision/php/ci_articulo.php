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
		$datos['anio'] = date('Y');

		$bandera_nodo = true;
		//$temp = $this->$s__agentes;
		//$datos['legajo']=$temp[0]['legajo'];
		//ei_arbol($datos);
		$legajo = $datos['legajo'];
		$id_catedra = $datos['catedra'];
		$anio = $datos['anio'];
		$id_motivo = $datos['id_motivo'];
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




				$sql = "SELECT t_l.legajo, t_l.apellido, t_l.nombre, t_l.fec_nacim, t_l.dni, t_l.fecha_ingreso, t_l.estado_civil, 
						t_l.caracter, t_l.categoria, t_l.agrupamiento, t_l.escalafon, 
						t_l.fec_nacim as fecha_nacimiento, t_l.cuil,
						t_d.pais, t_d.provincia, t_d.codigo_postal, t_d.localidad, t_d.manzana, 
						t_d.zona_paraje_barrio, t_d.calle, t_d.numero, t_d.piso, t_d.dpto_oficina, t_d.telefono, t_l.tipo_sexo,
						t_d.telefono_celular
						FROM reloj.agentes  as t_l LEFT JOIN reloj.domicilio as t_d
						ON t_l.legajo = t_d.legajo
						WHERE t_l.legajo = $legajo";
			} else {

				$ya_tomo = 0;
				$sql = "SELECT nombre_catedra, id_departamento FROM reloj.catedras
				WHERE id_catedra = $id_catedra";
				$depto = toba::db('comision')->consultar($sql);


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

			$dias = $datos['dias'];
			$anio = $datos['anio'];
			if ($id_motivo == 57) {
				$sql = "SELECT sum(dias) dias_restantes FROM reloj.vacaciones_restantes
				WHERE legajo = $legajo
				AND anio <= $anio";
			} else if ($id_motivo == 35) {
				$sql = "SELECT sum(dias) dias_restantes FROM reloj.vacaciones_restantes
			WHERE legajo = $legajo
			AND anio < $anio";
			}
			$temp = toba::db('comision')->consultar($sql);

			if (isset($temp)) {
				$dias_restantes = $temp['dias_restantes'];
			} else {
				$dias_restantes = 0;
			}
			$fecha_inicio_licencia = $datos['fecha_inicio_licencia'];
			$fechaentera1 = strtotime($fecha_inicio_licencia);
			$fecha_inicio = date_create(date("Y-m-d", $fechaentera1));
			$hoy = date_create(date("Y-m-d"));
			$diferencia = date_diff($fecha_inicio, $hoy);
			$y = date("Y", $fechaentera1);
			$m = date("m", $fechaentera1);

			$agrupamiento = $agente[0]['escalafon'];
			$agrego = 0;
			$sql = "SELECT sum(dias) dias_restantes FROM reloj.vacaciones_restantes
		WHERE legajo = $legajo
		AND anio = $anio";
			$vac_pen = toba::db('comision')->consultar($sql);
			$insertadas = count($vac_pen);
			//  ei_arbol($insertadas);

			for ($i = 0; $i < $cant; $i++) {

				if ($agente[$i]['escalafon'] == 'NODO') { //No docente

					if ($id_motivo == 30) { //Razones Particulares

						if (date("Y") == $anio) {
							if ($dias <= 2) {
								$agente[$i]['articulo'] = null;
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
								for ($i = 0; $i < $lim; $i++) {
									$dias_tomados = $pendiente[$i]['dias_no_pasados'] + $dias_tomados;
								}

								if ($parte[0]['dias_restantes'] == null) {
									$parte[0]['dias_restantes'] = 2;
								}

								$temp[0]['dias_restantes'] = $parte[0]['dias_restantes'] - $dias_tomados - $dias;
								//ei_arbol($temp[0]['dias_restantes'] < 0);                        
								if (!is_null($temp) && ($temp[0]['dias_restantes'] >= 0 && $temp[0]['dias_restantes'] <= 2)) {
									$sql = "SELECT -SUM(dias) +6 dias_restantes 
									FROM reloj.parte
									WHERE legajo = $legajo
									AND id_motivo = 30
									AND extract (month from fecha_inicio_licencia)=$m
									AND  DATE_PART('year', fecha_inicio_licencia) = $y";
									$temp = toba::db('comision')->consultar($sql);
									$bandera = false;

									//ei_arbol($temp);
									if (is_null($temp[0]['dias_restantes']) || ($temp[0]['dias_restantes'] >= 0  && $temp[0]['dias_restantes'] <= 6)) {
										$lim = count($agente);
										for ($i = 0; $i < $lim; $i++) {
											$agente[$i]['articulo'] = 40;
										}
										$bandera = true;
										//ei_arbol($agente);

									} else {
										//if(!is_null($temp[0]['dias_restantes'])&&!($temp[0]['dias_restantes']< 0 &&$temp[0]['dias_restantes'] > 6) ){
										toba::notificacion()->agregar('Ud ha excedido la cantidad anual de razones particulares este a&ntilde;o cuenta con ' . $temp[0]['dias_restantes'] . ' d&iacute;as', "info");
										$bandera = false;
									}
								} else {

									//ei_arbol($agente);    
									$temp[0]['dias_restantes'] = $parte[0]['dias_restantes'] + $dias_tomados - 2;
									if ($temp[0]['dias_restantes'] >= 0) {
										$temp[0]['dias_restantes'] = 0;
									} else {
										$temp[0]['dias_restantes'] = abs($temp[0]['dias_restantes']);
									}

									toba::notificacion()->agregar('Ud ha excedido la cantidad mensual de razones particulares este mes cuenta con ' . $temp[0]['dias_restantes'] . ' d&iacute;as', "info");

									$bandera_nodo = false;
								}
							}
						
						} else {
							toba::notificacion()->agregar('Introduzca el corriente a&ntilde;o. Gracias ', "info");

							$bandera_nodo = false;
						}
					} elseif ($id_motivo == 35) { //Vacaciones


						$agente[$i]['articulo'] = 55;
						$agente[$i]['id_decreto'] = 4;
						$dias_restantes = 0;

						$sql = "SELECT dias FROM reloj.vacaciones_restantes
									WHERE legajo = $legajo";
						$dias_vp = toba::db('comision')->consultar($sql);

						if (count($dias_vp) > 0) {
							$dias_restantes = $dias_vp[0]['dias'];
						}
						if ($resto[0]['tiene'] > 0) {
							$hay_cargadas = 1;
						} else {
							$hay_cargadas = 0;
						}

						$bandera = true;


						/// Vacaciones Pendientes no docente

					} else if ($id_motivo == 57) {
						$agente[$i]['articulo'] = null;
						$agente[$i]['id_decreto'] = 4;
						$datos['anio'] = $anio - 1;
						//ei_arbol ($agente);
						$sql = "SELECT sum(dias)  dias_rest from reloj.vacaciones_restantes
									WHERE legajo = $legajo AND anio <= $anio;";


						$resto = toba::db('comision')->consultar($sql);
						$dias_pendientes = $resto[0]['dias_rest'];
						//ei_arbol($resto);
						if ($dias <= $dias_pendientes) {
							/*toba::notificacion()->agregar('Usted cuenta con  '.$dias_pendientes .' días pendientes del año'.$anio. ' coloque una cantidad de dias validos.', "info");*/
							$agente[$i]['articulo'] = 106;
							$bandera = true;
						} else {
							toba::notificacion()->agregar('Usted cuenta con  ' . $dias_pendientes . ' d&iacute;as, coloque una cantidad de d&iacute;as validos.', "info");
							$bandera = false;
						}
					} else if ($id_motivo == 12) { // Donación de Sangre
						if ($dias == 1) {
							$agente[$i]['articulo'] = null;
							$sql = "SELECT count(*) cant FROM reloj.parte 
								where id_motivo = $id_motivo
								and legajo = $legajo
								and Date_part('year',fecha_inicio_licencia) = $anio";
							$tomo = toba::db('comision')->consultar($sql);
							if ($tomo[0]['cant'] <= 0) {

								if ($datos['certificado'] <> null) {
									$agente[$i]['articulo'] = 33;
									$agente[$i]['id_decreto'] = 4;
									$bandera = true;
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
					} else if ($id_motivo == 22) { // Actividades Deportivas
						if ($dias >= 1 and $dias <= 15) {
							$agente[$i]['articulo'] = null;
							$sql = "SELECT count(*) cant FROM reloj.parte 
								where id_motivo = $id_motivo
								and legajo = $legajo
								and Date_part('year',fecha_inicio_licencia) = $anio";
							$tomo = toba::db('comision')->consultar($sql);
							if ($tomo[0]['cant'] <= 15) {
								if ($datos['certificado'] <> null) {
									$agente[$i]['articulo'] = 38;
									$agente[$i]['id_decreto'] = 4;
									$bandera = true;
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
					} else if ($id_motivo == 49) { // Citación Judicial

						$agente[$i]['articulo'] = null;
						if ($datos['certificado'] <> null) {
							$agente[$i]['articulo'] = 97;
							$agente[$i]['id_decreto'] = 9;
							$bandera = true;
						} else {
							toba::notificacion()->agregar('Por favor adjunte el Certificado correspondiente', "info");
							$bandera = false;
						}
					} else if ($id_motivo == 17) { // Fallecimiento de Conyúge y 1º grado

						$agente[$i]['articulo'] = null;
						if ($datos['certificado'] <> null) {
							$dias = 10;
							$datos['dias'] = $dias;
							$agente[$i]['articulo'] = 29;
							$agente[$i]['id_decreto'] = 4;
							$bandera = true;
						} else {
							toba::notificacion()->agregar('Por favor adjunte el Certificado correspondiente', "info");
							$bandera = false;
						}
					} else if ($id_motivo == 16) { // Fallecimiento de Familiar Cosanguineo 2º

						$agente[$i]['articulo'] = null;
						if ($datos['certificado'] <> null) {
							$dias = 5;
							$datos['dias'] = $dias;
							$agente[$i]['articulo'] = 31;
							$agente[$i]['id_decreto'] = 4;
							$bandera = true;
						} else {
							toba::notificacion()->agregar('Por favor adjunte el Certificado correspondiente', "info");
							$bandera = false;
						}
					} else if ($id_motivo == 18) { // Fallecimiento de pariente politico

						$agente[$i]['articulo'] = null;
						if ($datos['certificado'] <> null) {
							$dias = 1;
							$datos['dias'] = $dias;
							$agente[$i]['articulo'] = 32;
							$agente[$i]['id_decreto'] = 4;
							$bandera = true;
						} else {
							toba::notificacion()->agregar('Por favor adjunte el Certificado correspondiente', "info");
							$bandera = false;
						}
					} else if ($id_motivo == 27) { //Nacimiento (Paternidad)

						$agente[$i]['articulo'] = null;
						if ($agente[$i]['tipo_sexo'] == 'M') {
							if ($datos['certificado'] <> null) {
								$dias = 15;
								$agente[$i]['articulo'] = 26;
								$agente[$i]['id_decreto'] = 4;
								$bandera = true;
							} else {
								toba::notificacion()->agregar('Por favor adjunte el Certificado correspondiente', "info");
								$bandera = false;
							}
						} else {
							toba::notificacion()->agregar('Esta Licencia es por Paternidad', "info");
							$bandera = false;
						}
					} else if ($id_motivo == 36) { //Matrimonio

						$agente[$i]['articulo'] = null;
						if ($datos['certificado'] <> null) {
							$dias = $this->contarDiasCorridosParaHabiles($datos['fecha_inicio_licencia'],10);
							
							//$dias = 10;
							$agente[$i]['articulo'] = 28;
							$agente[$i]['id_decreto'] = 4;
							$bandera = true;
						} else {
							toba::notificacion()->agregar('Por favor adjunte el Certificado correspondiente', "info");
							$bandera = false;
						}
					} else if ($id_motivo == 25) { //Matrimonio de un hijo/a

						$agente[$i]['articulo'] = null;
						if ($datos['certificado'] <> null) {
							$dias = 2;
							$agente[$i]['articulo'] = 27;
							$agente[$i]['id_decreto'] = 4;
							$bandera = true;
						} else {
							toba::notificacion()->agregar('Por favor adjunte el Certificado correspondiente', "info");
							$bandera = false;
						}
					} else if ($id_motivo == 25) { //Adopcion (Maternidad)

						$agente[$i]['articulo'] = null;
						if ($datos['certificado'] <> null) {
							$dias = 45;
							$agente[$i]['articulo'] = 43;
							$agente[$i]['id_decreto'] = 4;
							$bandera = true;
						} else {
							toba::notificacion()->agregar('Por favor adjunte el Certificado correspondiente', "info");
							$bandera = false;
						}
					} else if ($id_motivo == 59) { //Adopcion (Paternidad)

						$agente[$i]['articulo'] = null;
						if ($datos['certificado'] <> null) {
							$dias = 15;
							$agente[$i]['articulo'] = 25;
							$agente[$i]['id_decreto'] = 4;
							$bandera = true;
						} else {
							toba::notificacion()->agregar('Por favor adjunte el Certificado correspondiente', "info");
							$bandera = false;
						}
					} else if ($id_motivo == 14) { // Rendir examen Secundario
						if ($dias >= 1 and $dias <= 4) {
							$agente[$i]['articulo'] = null;
							$sql = "SELECT count(*) cant FROM reloj.parte 
								where id_motivo = $id_motivo
								and legajo = $legajo
								and Date_part('year',fecha_inicio_licencia) = $anio";
							$tomo = toba::db('comision')->consultar($sql);
							if ($tomo[0]['cant'] <= 20) {
								if ($datos['certificado'] <> null) {
									$agente[$i]['articulo'] = 34;
									$agente[$i]['id_decreto'] = 4;
									$bandera = true;
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
					} else if ($id_motivo == 15) { // Rendir examen Universitario o posgrado
						if ($dias >= 1 and $dias <= 5) {
							$agente[$i]['articulo'] = null;
							$sql = "SELECT count(*) cant FROM reloj.parte 
								where id_motivo = $id_motivo
								and legajo = $legajo
								and Date_part('year',fecha_inicio_licencia) = $anio";
							$tomo = toba::db('comision')->consultar($sql);
							if ($tomo[0]['cant'] <= 24) {
								if ($datos['certificado'] <> null) {
									$agente[$i]['articulo'] = 35;
									$agente[$i]['id_decreto'] = 4;
									$bandera = true;
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
						
						if (date("Y") == $anio) {
							if ($dias <= 2) {
								for ($j = 0; $j < $cant; $j++) {
									$agente[$j]['articulo'] = 0;
									$agente[$j]['id_decreto'] = 8;
								}

								$sql = "SELECT SUM(dias) dias_restantes 
										FROM reloj.parte
										WHERE legajo = $legajo    
										AND id_motivo = 30    
										AND  DATE_PART('month', fecha_inicio_licencia) = $m
										and DATE_PART('year', fecha_inicio_licencia) = $anio";
								$parte = toba::db('comision')->consultar($sql);
								/*$sql = "SELECT fecha_inicio, fecha_fin*/
								$sql = "SELECT  fecha_fin - fecha_inicio + 1 dias_no_pasados
								FROM reloj.inasistencias
								Where legajo = $legajo AND id_motivo=30 AND extract (month from fecha_inicio)=$m And extract(year from fecha_inicio) = $anio";
								
								$pendiente = toba::db('comision')->consultar($sql);
								$lim = count($pendiente);
								$dias_tomados = 0;
								
								for ($i = 0; $i < $lim; $i++) {
									$dias_tomados = $dias_tomados + $pendiente[$i]['dias_no_pasados'];
								}
								if ($parte[0]['dias_restantes'] == null) {
									$parte[0]['dias_restantes'] = 2;
								}
							//	ei_arbol($parte);
								$temp[0]['dias_restantes'] = $parte[0]['dias_restantes'] - $dias_tomados - $dias;
							//	ei_arbol( $parte[0]['dias_restantes'].' en parte' , $dias_tomados .'dias tomados' , $dias.' dias a tomar');
								if (!is_null($temp) && ($temp[0]['dias_restantes'] >= 0 && $temp[0]['dias_restantes'] <= 2)) {
									$sql = "SELECT -SUM(dias) +6 dias_restantes 
									FROM reloj.parte
									WHERE legajo = $legajo
									AND id_motivo = 30
									AND  DATE_PART('year', fecha_inicio_licencia) = $y";
									$temp = toba::db('comision')->consultar($sql);
									$bandera = false;
									//ei_arbol($temp);
									if (is_null($temp[0]['dias_restantes']) || ($temp[0]['dias_restantes'] >= 0 && $temp[0]['dias_restantes'] <= 6)) {
										$lim = count($agente);
										for ($i = 0; $i < $lim; $i++) {
											$agente[$i]['articulo'] = 57;
										}
										$bandera = true;
										//ei_arbol($agente);

									} else {
										$bandera = false;
										toba::notificacion()->agregar('Ud ha excedido la cantidad anual de razones particulares este a&ntilde;o cuenta con ' . $temp[0]['dias_restantes'] . ' d&iacute;as', "info");
									}
								} else {

									//ei_arbol($agente);    
									$temp[0]['dias_restantes'] = $parte[0]['dias_restantes'] + $dias_tomados - 2;
									if ($temp[0]['dias_restantes'] >= 0) {
										$temp[0]['dias_restantes'] = 0;
									} else {
										$temp[0]['dias_restantes'] = abs($temp[0]['dias_restantes']);
									}

									toba::notificacion()->agregar('Ud ha excedido la cantidad mensual de razones particulares este mes cuenta con ' . $temp[0]['dias_restantes'] . ' días', "info");
									$bandera = false;
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
						$bandera = true;
						$dias_restantes = 0;


						$sql = "SELECT dias FROM reloj.vacaciones_restantes
									WHERE legajo = $legajo";
						$dias_vp = toba::db('comision')->consultar($sql);

						if (count($dias_vp) > 0) {
							$dias_restantes = $dias_vp[0]['dias'];
						}


						$bandera = true;



						// VAcaciones pendientes docentes            
					} else if ($id_motivo == 57) {
						$agente[$i]['articulo'] = null;
						$agente[$i]['id_decreto'] = 8;
						$datos['anio'] = $anio - 1;

						//ei_arbol ($agente);
						$sql = "SELECT sum(dias)  dias_rest from reloj.vacaciones_restantes
									WHERE legajo = $legajo AND anio <= $anio;";


						$resto = toba::db('comision')->consultar($sql);
						$dias_pendientes = $resto[0]['dias_rest'];
						//ei_arbol($resto);
						if ($dias <= $dias_pendientes) {

							$agente[$i]['articulo'] = 104;
							$bandera = true;
							$datos['dias_restantes'] = $dias_pendientes;
						} else {
							toba::notificacion()->agregar('Usted cuenta con  ' . $dias_pendientes . ' d&iacute;as, coloque una cantidad de d&iacute;as validos.', "info");
							$bandera = false;
						}
					} else if ($id_motivo == 12) { // Donación de Sangre
						if ($dias == 1) {
							$agente[$i]['articulo'] = null;
							$sql = "SELECT count(*) cant FROM reloj.parte 
								where id_motivo = $id_motivo
								and legajo = $legajo
								and DATE_PART('year', fecha_inicio_licencia) = $anio";
							$tomo = toba::db('comision')->consultar($sql);
							if ($tomo[0]['cant'] <= 12) {
								if ($datos['certificado'] <> null) {
									$agente[$i]['articulo'] = 89;
									$agente[$i]['id_decreto'] = 8;
									$bandera = true;
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
					} else if ($id_motivo == 22) { // Actividades Deportivas
						if ($dias >= 1 and $dias <= 5) {
							$agente[$i]['articulo'] = null;
							$sql = "SELECT count(*) cant FROM reloj.parte 
								where id_motivo = $id_motivo
								and legajo = $legajo
								and DATE_PART('year', fecha_inicio_licencia)= $anio";
							$tomo = toba::db('comision')->consultar($sql);
							if ($tomo[0]['cant'] <= 5) {
								if ($datos['certificado'] <> null) {
									$agente[$i]['articulo'] = 83;
									$agente[$i]['id_decreto'] = 8;
									$bandera = true;
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
					} else if ($id_motivo == 49) { // Citación Judicial

						$agente[$i]['articulo'] = null;
						if ($datos['certificado'] <> null) {
							$agente[$i]['articulo'] = 97;
							$agente[$i]['id_decreto'] = 9;
							$bandera = true;
						} else {
							toba::notificacion()->agregar('Por favor adjunte el Certificado correspondiente', "info");
							$bandera = false;
						}
					} else if ($id_motivo == 17) { // Fallecimiento de Conyúge y 1º grado

						$agente[$i]['articulo'] = null;
						if ($datos['certificado'] <> null) {
							$dias = 7;
							$datos['dias'] = $dias;
							$agente[$i]['articulo'] = 86;
							$agente[$i]['id_decreto'] = 8;
							$bandera = true;
						} else {
							toba::notificacion()->agregar('Por favor adjunte el Certificado correspondiente', "info");
							$bandera = false;
						}
					} else if ($id_motivo == 16) { // Fallecimiento de Familiar Cosanguineo 2º

						$agente[$i]['articulo'] = null;
						if ($datos['certificado'] <> null) {
							$dias = 5;
							$datos['dias'] = $dias;
							$agente[$i]['articulo'] = 87;
							$agente[$i]['id_decreto'] = 8;
							$bandera = true;
						} else {
							toba::notificacion()->agregar('Por favor adjunte el Certificado correspondiente', "info");
							$bandera = false;
						}
					} else if ($id_motivo == 27) { //Nacimiento (Paternidad)

						$agente[$i]['articulo'] = null;
						if ($agente[$i]['tipo_sexo'] == 'M') {
							if ($datos['certificado'] <> null) {
								$dias = 15;
								$agente[$i]['articulo'] = 85;
								$agente[$i]['id_decreto'] = 8;
								$bandera = true;
							} else {
								toba::notificacion()->agregar('Por favor adjunte el Certificado correspondiente', "info");
								$bandera = false;
							}
						} else {
							toba::notificacion()->agregar('Esta Licencia es por Paternidad', "info");
							$bandera = false;
						}
					} else if ($id_motivo == 36) { //Matrimonio

						$agente[$i]['articulo'] = null;
						if ($datos['certificado'] <> null) {
							$dias = $this->contarDiasCorridosParaHabiles($datos['fecha_inicio_licencia'],10);

							$agente[$i]['articulo'] = 81;
							$agente[$i]['id_decreto'] = 8;
							$bandera = true;
						} else {
							toba::notificacion()->agregar('Por favor adjunte el Certificado correspondiente', "info");
							$bandera = false;
						}
					} else if ($id_motivo == 25) { //Matrimonio de un hijo/a

						$agente[$i]['articulo'] = null;
						if ($datos['certificado'] <> null) {
							$dias = 2;
							$agente[$i]['articulo'] = 82;
							$agente[$i]['id_decreto'] = 8;
							$bandera = true;
						} else {
							toba::notificacion()->agregar('Por favor adjunte el Certificado correspondiente', "info");
							$bandera = false;
						}
					} else if ($id_motivo == 25) { //Adopcion (Maternidad)

						$agente[$i]['articulo'] = null;
						if ($datos['certificado'] <> null) {
							$dias = 60;
							$agente[$i]['articulo'] = 79;
							$agente[$i]['id_decreto'] = 8;
							$bandera = true;
						} else {
							toba::notificacion()->agregar('Por favor adjunte el Certificado correspondiente', "info");
							$bandera = false;
						}
					} else if ($id_motivo == 59) { //Adopcion (Paternidad)

						$agente[$i]['articulo'] = null;
						if ($datos['certificado'] <> null) {
							$dias = 15;
							$agente[$i]['articulo'] = 44;
							$agente[$i]['id_decreto'] = 2;
							$bandera = true;
						} else {
							toba::notificacion()->agregar('Por favor adjunte el Certificado correspondiente', "info");
							$bandera = false;
						}
					} else if ($id_motivo == 15) { // Rendir examen Universitario o posgrado
						if ($dias >= 1 and $dias <= 5) {
							$agente[$i]['articulo'] = null;
							$sql = "SELECT count(*) cant FROM reloj.parte 
								where id_motivo = $id_motivo
								and legajo = $legajo
								and DATE_PART('year', fecha_inicio_licencia) = $anio";
							$tomo = toba::db('comision')->consultar($sql);
							if ($tomo[0]['cant'] <= 28) {
								if ($datos['certificado'] <> null) {
									$agente[$i]['articulo'] = 94;
									$agente[$i]['id_decreto'] = 8;
									$bandera = true;
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
					} else if ($id_motivo == 47) { //Examen para rendir concurso
						if ($dias >= 1 and $dias <= 3) {
							$agente[$i]['articulo'] = null;
							$sql = "SELECT count(*) cant FROM reloj.parte 
								where id_motivo = $id_motivo
								and legajo = $legajo
								and DATE_PART('year', fecha_inicio_licencia) = $anio";
							$tomo = toba::db('comision')->consultar($sql);
							if ($tomo[0]['cant'] <= 3) {
								if ($datos['certificado'] <> null) {
									$agente[$i]['articulo'] = 95;
									$agente[$i]['id_decreto'] = 8;
									$bandera = true;
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
					} else if ($id_motivo == 61) {
						if (date("Y") == $anio) {
							if ($dias <= 2) {
								for ($j = 0; $j < $cant; $j++) {
									$agente[$j]['articulo'] = 0;
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
								for ($i = 0; $i < $lim; $i++) {
									$dias_tomados = $dias_tomados + $pendiente[$i]['dias_rp'];
								}


								$temp[0]['dias_restantes'] = $parte[0]['dias_restantes'] + $dias_tomados + $dias;
								//ei_arbol($temp);
								if (!is_null($temp) && ($temp[0]['dias_restantes'] >= 0 && $temp[0]['dias_restantes'] <= 2)) {
									$sql = "SELECT -SUM(dias) +6 dias_restantes 
									FROM reloj.parte
									WHERE legajo = $legajo
									AND id_motivo = 61
									AND  DATE_PART('year', fecha_inicio_licencia) = $y";
									$temp = toba::db('comision')->consultar($sql);
									//ei_arbol($temp);
									if (is_null($temp[0]['dias_restantes']) || ($temp[0]['dias_restantes'] >= 0 && $temp[0]['dias_restantes'] <= 6)) {
										$lim = count($agente);
										for ($i = 0; $i < $lim; $i++) {
											$agente[$i]['articulo'] = 109;
										}
										$bandera = true;
										//ei_arbol($agente);

									} else {
										toba::notificacion()->agregar('Ud ha excedido la cantidad anual de razones particulares este a&ntilde;o cuenta con ' . $temp[0]['dias_restantes'] . ' d&iacute;as', "info");
									}
								} else {

									//ei_arbol($agente);    
									$temp[0]['dias_restantes'] = $parte[0]['dias_restantes'] + $dias_tomados - 2;
									if ($temp[0]['dias_restantes'] >= 0) {
										$temp[0]['dias_restantes'] = 0;
									} else {
										$temp[0]['dias_restantes'] = abs($temp[0]['dias_restantes']);
									}

									toba::notificacion()->agregar('Ud ha excedido la cantidad mensual de razones particulares este mes cuenta con ' . $temp[0]['dias_restantes'] . ' días', "info");
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
				}

				$edad = $this->dep('mapuche')->get_edad($legajo, null);
				$datos['dias_restantes'] = $dias_restantes;
				//ei_arbol($agente);    
				if ($bandera) {
					$fecha_inicio_licencia = $datos['fecha_inicio_licencia'];
					$fechaentera1 = strtotime($fecha_inicio_licencia);
					$fecha = date_create(date("Y-m-d", $fechaentera1));
					//$fecha=date('d/m/Y',strtotime($datos['fecha_inicio_licencia'] ) );
					//ei_arbol($agente);
					$fecha_inicio = $fecha->format("Y-m-d");
					$dias = $dias - 1;
					$dias_to = $dias . ' days';
					$hasta = date_add($fecha, date_interval_create_from_date_string($dias_to));
					$hasta = $hasta->format("Y-m-d");

					for ($i = 0; $i < $cant; $i++) {
						$fecha_alta = date("Y-m-d H:i:s");
						$datos['fecha_alta'] = $fecha_alta;
						$usuario_alta = $datos['legajo'];
						$estado = 'A';
						$datos['fecha_inicio'] = $fecha_inicio;
						$datos['hasta'] = $hasta;
						$dias = $datos['dias'];
						$cod_depcia = '04';
						$observaciones = $datos['observaciones'];
						$id_motivo = $datos['id_motivo'];
						if (isset($agente[$i]['id_decreto'])) {
							$id_decreto = $agente[$i]['id_decreto'];
						}
						$datos['id_decreto'] = $id_decreto;
						if (isset($agente[$i]['articulo'])) {
							$articulo = $agente[$i]['articulo'];
							$datos['articulo'] = $articulo;
						}
						$catedra = $datos['catedra'];
						$anio = $datos['anio'];
						$superior = $datos['superior'];
						$autoridad = $datos['autoridad'];
					}



					//ei_arbol($datos);
					if ($datos['fecha_inicio_licencia'] < '2022-12-26') {
						toba::notificacion()->agregar('Ingrese una fecha mayor o igual al 26/12/2026', "info");
					} else {
						//	ei_arbol($datos);
						if ($ya_tomo == 0) {
							if ($bandera_nodo) {

								$existe = $this->dep('datos')->tabla('parte')->get_duplicado_inasistencia($legajo, $fecha_inicio, $id_motivo);
								if ($existe != 0) {
									toba::notificacion()->agregar('Este pedido fue anteriormente ingresado', "info");
									break;
								} else {
									//	ei_arbol($id_motivo.' motivo', $id_decreto. ' decreto', $articulo.' articulo');
									if ($id_motivo == 30) {
										$sql = "INSERT INTO reloj.inasistencias(	legajo, id_catedra, fecha_inicio, fecha_fin, anio, observaciones, leg_sup, auto_sup, leg_aut, auto_aut, fecha_alta, usuario_alta, estado, id_motivo, id_decreto,id_articulo) VALUES ($usuario_alta, $catedra, '$fecha_inicio', '$hasta',$anio, '$observaciones', $superior, true, $autoridad, true, '$fecha_alta',$usuario_alta ,'A', $id_motivo, $id_decreto,$articulo);";
									} else if ($id_motivo == 57) {
										$sql = "INSERT INTO reloj.inasistencias(	legajo, id_catedra, fecha_inicio, fecha_fin, anio, observaciones, leg_sup, auto_sup, leg_aut, auto_aut, fecha_alta, usuario_alta, estado, id_motivo, id_decreto,id_articulo) VALUES ($usuario_alta, $catedra, '$fecha_inicio', '$hasta',$anio, '$observaciones', $superior, true, $autoridad, true, '$fecha_alta',$usuario_alta ,'A', $id_motivo, $id_decreto,$articulo);";
									} else {
										$sql = "INSERT INTO reloj.inasistencias( legajo, id_catedra, fecha_inicio, fecha_fin, anio, observaciones, leg_sup, auto_sup, leg_aut, auto_aut, fecha_alta, usuario_alta, estado, id_motivo, id_decreto, id_articulo)    VALUES ( $usuario_alta, $catedra, '$fecha_inicio', '$hasta',$anio, '$observaciones', $superior, true, $autoridad, true, '$fecha_alta',$usuario_alta ,'A', $id_motivo, $id_decreto, $articulo);";
									}


									/*
		
		$sql = "INSERT INTO reloj.parte(
		legajo, edad, fecha_alta, usuario_alta, estado, fecha_inicio_licencia, dias, cod_depcia, domicilio, localidad, agrupamiento, fecha_nacimiento,
		apellido, nombre, estado_civil, observaciones, id_decreto, id_motivo, id_articulo, tipo_sexo)
	VALUES ($legajo, $edad, '$fecha_alta', $usuario_alta, '$estado', '$fecha_inicio', $dias, '$cod_depcia', '$domicilio', '$localidad', '$agrupamiento', '$fecha_nacimiento', 
		'$apellido', '$nombre',    '$estado_civil', '$observaciones', $id_decreto, $id_motivo,$articulo,'$tipo_sexo');";*/


									toba::db('comision')->ejecutar($sql);

									if ($id_motivo <> 30) {
										if ($id_motivo <> 57) {
											if ($id_motivo <> 61) {
												if ($id_motivo <> 35) {

													$sql = "SELECT id_inasistencia FROM reloj.inasistencias
													WHERE legajo = $usuario_alta
													AND fecha_inicio = '$fecha_inicio'
													AND id_motivo = $id_motivo ;";
													$ina = toba::db('comision')->consultar($sql);
													$id_inasistencia = $ina[0]['id_inasistencia'];
													$ruta = 'C:/Toba/proyectos/ctrl_asis/www/certificados/';
													$ar_nombre_completo = explode('.', $datos['certificado']['name']);
													$archivo_nombre = $ruta . $id_inasistencia . $fecha_inicio . '.pdf';
													$datos['archivo'] = $archivo_nombre;
													$datos = $this->procesar_archivo($datos);
												}
											}
										}
									}

									/////
									//actualizacion o borrado de vacaciones restantes
									//////
									if ($id_motivo == 57) {

										toba::notificacion()->agregar('Parte de inasistencia agregado correctamente.', 'info');
										if ($dias == $dias_pendientes) {
											$sql1 = "DELETE FROM reloj.vacaciones_restantes
				where legajo = $legajo
				and anio =$anio ";
										} else {
											$dias_pendientes = $dias_pendientes - $dias;
											if ($dias_pendientes > 0) {
												$datos['dias_restantes'] = $dias_pendientes;
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
										if (isset($adelanto)) {
											$sql1 = "UPDATE reloj.vacaciones_adelantadas
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

									if (isset($catedra) and $catedra <> 0) {

										$sql = "SELECT nombre_catedra from reloj.catedras
						WHERE id_catedra = $catedra;";
										$cat = toba::db('comision')->consultar($sql);
										$datos['catedra'] = $cat[0]['nombre_catedra'];
									}

									if (isset($legajo)) {
										//$correo_agente = $this->dep('mapuche')->get_legajos_email($datos['legajo']);
										$correo_agente = $this->dep('datos')->tabla('agentes_mail')->get_correo($datos['legajo']);
										$datos['agente_ayn'] = $correo_agente[0]['descripcion'];
									}

									if (isset($datos['superior']) and $datos['superior'] <> 0) {
										//$correo_sup = $this->dep('mapuche')->get_legajos_email($datos['superior']);
										$correo_sup = $this->dep('datos')->tabla('agentes_mail')->get_correo($datos['superior']);

										$datos['superior_ayn'] = $correo_sup[0]['descripcion'];
									}

									if (isset($datos['autoridad'])) {
										//	$correo_aut = $this->dep('mapuche')->get_legajos_email($datos['autoridad']);
										$correo_aut = $this->dep('datos')->tabla('agentes_mail')->get_correo($datos['autoridad']);
										$datos['autoridad_ayn'] = $correo_aut[0]['descripcion'];
									}
									$agente = $this->dep('mapuche')->get_legajo_todos($legajo);
									$datos['descripcion'] = $agente[0]['descripcion'];

									$this->s__datos = $datos;

									if (isset($legajo)) {
										$sql = "SELECT email from reloj.agentes_mail
											where legajo=$legajo";
										$correo = toba::db('comision')->consultar($sql);
										$this->enviar_correos($correo[0]['email']);
									}

									if (isset($datos['superior']) and $datos['superior'] <> 0) {
										$superior = $datos['superior'];

										$sql = "SELECT email from reloj.agentes_mail
											where legajo=$superior";
										$correo = toba::db('comision')->consultar($sql);
										$this->enviar_correos_sup($correo[0]['email'], $datos['superior_ayn']);
									}
								}
							}
						} else {
							toba::notificacion()->agregar('Ud. ya completo el fomulario para ' . $depto[0]['nombre_catedra'], "info");
						}
					}
				}
			}
		} else {
			toba::notificacion()->agregar("Coloque un d&iacute;a mayor que 0", "info");
		}
	}


	function evt__guardar()
	{
		//verificamos que no exista otro parte abiertos(estado) con el mismo legajo, motivo y dependencia.
		$datos = $this->dep('datos')->tabla('parte')->get();


		$this->dep('datos')->sincronizar();
		toba::notificacion()->agregar('Parte de inasistencia agregado correctamente.', 'info');
	}
	function enviar_correos($correo)
	{
		require_once('mail/tobamail.php');
		$datos = $this->s__datos;
		$hacia = $correo;

		if ($datos['id_motivo'] == 30) {
			$datos['dias'] = $datos['dias'] - 1;
		}
		$fecha = date('d/m/Y', strtotime($datos['fecha_inicio_licencia']));
		$hasta = date('d/m/Y', strtotime($datos['hasta']));

		if ($datos['dias_restantes'] <= 0) {
			$datos['dias_restantes'] = 0;
		}


		if ($datos['id_motivo'] == 30) {
			//$motivo = 'Razones Particulares con gose de haberes';
			$asunto = 'Formulario de Solicitud Razones Particulares';
			$cuerpo = '<table>
						El/la agente  <b>' . $datos['descripcion'] . '</b> perteneciente a la catedra/oficina/ direcci&oacute;n <b>' . $datos['catedra'] . '</b>.<br/>
						Solicita Justificaci&oacute;n de Inasistencia por Razones Particulares a partir del d&iacute;a ' . $fecha . ' hasta ' . $hasta . '.
							Teniendo en cuenta las siguientes Observaciones: ' . $datos['observaciones'] . '
											
			</table>';
		} else if ($datos['id_motivo'] == 35) {
			$asunto = 'Formulario de Licencia Anual por Vacaciones';
			//$motivo = 'Vacaciones'.$datos['anio'];
			$cuerpo = '<table>
						El/la agente  <b>' . $datos['descripcion'] . '</b> perteneciente a  <b>' . $datos['catedra'] . '</b>.<br/>
						Solicita laLicencia Anual por Vacaciones correspondiente al año ' . $datos['anio'] . ' a partir del d&iacute;a ' . $fecha . ' hasta ' . $hasta . '. <br/>
						
											
			</table>';
		} else if ($datos['id_motivo'] == 57) {
			$asunto = 'Formulario de D&iacute&as Pendientes de la Licencia Anual';
			$cuerpo = '<table>

				El/la agente <b>' . $datos['descripcion'] . '</b> perteneciente a <b>' . $datos['catedra'] . '</b> <br/>
				Solicita los d&iacute;as pendientes de la licencia anual correspondiente al ' . $datos['anio'] . ' a partir del d&iacute;a ' . $fecha . ' hasta ' . $hasta . '<br/>
				Teniendo en cuenta las siguientes Observaciones: ' . $datos['observaciones'] .  '<br/>
				Ud. cuenta con ' . $datos['dias_restantes'] . ' d&iacute;as de vacaciones pendientes.
			<table/>';
		} else if ($$datos['id_motivo'] == 61) {
			$asunto = 'Formulario de Justificacion de Inasistencia por Excesos de Inasistencia (SIN GOCE)';
			$cuerpo = '<table>

				El/la agente <b>' . $datos['descripcion'] . '</b> perteneciente a <b>' . $datos['catedra'] . '</b> <br/>
				Solicita solicita Razones Particulares a partir SIN GOCE  a partir del d&iacute;a ' . $fecha . ' hasta ' . $hasta . '<br/>
				Teniendo en cuenta las siguientes Observaciones: ' . $datos['observaciones'] .  '<br/>
			<table/>';
		} else {
			$cuerpo = '<table>

				El/la agente <b>' . $datos['descripcion'] . '</b> perteneciente a <b>' . $datos['catedra'] . '</b> <br/>
				Justific&oacute;  la inasistencia  desde ' . $fecha . ' hasta ' . $hasta . ' presentando el certificado correspondiente a dicha acci&oacute;n.
			<table/>';

			switch ($datos['id_motivo']) {
				case 12:
					$asunto = 'Formulario de Justificacion de Inasistencia por Donaci&oacute;n de Sangre';
					break;
				case 22:
					$asunto = 'Formulario de Justificacion de Inasistencia por Realización de Actividad Deportiva o Art&iacute;stica';
					break;
				case 49:
					$asunto = 'Formulario de Justificacion de Inasistencia por Citaci&oacute;n Judicial';
					break;
				case 17:
					$asunto = 'Formulario de Justificacion de Inasistencia por Fallecimiento de Cony&uacute;ge o Pariente de Primer Grado';
					break;
				case 16:
					$asunto = 'Formulario de Justificacion de Inasistencia por Fallecimiento de Pariente de Segundo Grado';
					break;
				case 18:
					$asunto = 'Formulario de Justificacion de Inasistencia por Fallecimiento de Pariente Pol&iacute;tico';
					break;
				case 27:
					$asunto = 'Formulario de Justificacion de Inasistencia por Nacimiento (Paternidad)';
					break;
				case 36:
					$asunto = 'Formulario de Justificacion de Inasistencia por Matrimonio';
					break;
				case 25:
					$asunto = 'Formulario de Justificacion de Inasistencia por Matrimonio de hijo/a';
					break;
				case 7:
					$asunto = 'Formulario de Justificacion de Inasistencia por Adopci&oacute;n (Maternidad)';
					break;
				case 59:
					$asunto = 'Formulario de Justificacion de Inasistencia por Adopci&oacute;n (Paternidad)';
					break;
				case 14:
					$asunto = 'Formulario de Justificacion de Inasistencia por Exam&eacute;n de Nivel Medio';
					break;
				case 15:
					$asunto = 'Formulario de Justificacion de Inasistencia por Exam&eacute;n de Nivel Superior';
					break;
				case 47:
					$asunto = 'Formulario de Justificacion de Inasistencia por Exam&eacute;n para Concurso';
					break;
				case 61:
					$asunto = 'Formulario de Justificacion de Inasistencia por Excesos de Inasistencia (SIN GOCE)';
					break;
			}
		}; //date("d/m/y",$fecha)

		//Enviamos el correo
		$mail = new TobaMail($correo, $asunto, $cuerpo, $desde, '');

		// Agregar un archivo adjunto
		//$mail->agregarAdjunto('nombre_archivo.pdf', '/ruta/al/archivo/nombre_archivo.pdf');

		try {
			$mail->ejecutar();
			echo "Correo enviado exitosamente.<br>";
		} catch (Exception $e) {
			echo "Error al enviar el correo: " . $e->getMessage();
		}
	}
	function enviar_correos_sup($correo, $destino)
	{
		require_once('mail/tobamail.php');
		$datos = $this->s__datos;
		$hacia = $correo;

		$fecha = date('d/m/Y', strtotime($datos['fecha_inicio_licencia']));

		$hasta = date('d/m/Y', strtotime($datos['hasta']));

		if ($datos['id_motivo'] == 30) {
			//$motivo = 'Razones Particulares con gose de haberes';
			$asunto = 'Formulario de Solicitud Razones Particulares del Agente ' . $datos['agente_ayn'];
			$cuerpo = '<table>
						El/la agente  <b>' . $datos['descripcion'] . '</b> perteneciente a la <b>' . $datos['catedra'] . '</b> solicita <b> Razones Particulares </b> a partir del d&iacute;a ' . $fecha . ' hasta ' . $hasta . '.<br/>
							Observaciones: ' . $datos['observaciones'] . ' - <br/>
							Usted deber&aacute; ingersar a <a href="https://sistemas.fca.uncu.edu.ar/solicitudes" target="_blank">https://sistemas.fca.uncu.edu.ar/solicitudes</a>, para aprobar o rechazar la solicitud .

											
			</table>';
		} else if ($datos['id_motivo'] == 35) {

			//$motivo = 'Vacaciones'.$datos['anio'];
			$asunto = 'Formulario de Solicitud Licencia Anual del Agente ' . $datos['agente_ayn'];
			$cuerpo = '<table>

						El/la agente  <b>' . $datos['descripcion'] . '</b> perteneciente a  la <b>' . $datos['catedra'] . '</b>.<br/>
						Solicita <b> Licencia Anual por Vacaciones </b> correspondiente al  ' . $datos['anio'] . ' a partir del d&iacute;a ' . $fecha . ' hasta ' . $hasta . '.<br/>
						Usted deber&aacute; ingersar a <a href="https://sistemas.fca.uncu.edu.ar/solicitudes" target="_blank">https://sistemas.fca.uncu.edu.ar/solicitudes</a>, para aprobar o rechazar la solicitud .

											
			</table>';
		} else if ($datos['id_motivo'] == 57) {
			$asunto = 'Formulario de D&iacute&as Pendientes de la Licencia Anual del Agente' . $datos['agente_ayn'];
			$cuerpo = '<table>

				El/la agente <b>' . $datos['descripcion'] . '</b> perteneciente a <b>' . $datos['catedra'] . '</b> <br/>
				Solicita <b>los d&iacute;as pendientes de la licencia anual</b> correspondiente al ' . $datos['anio'] . ' a partir del d&iacute;a ' . $fecha . ' hasta ' . $hasta . '<br/>
				Teniendo en cuenta las siguientes Observaciones: ' . $datos['observaciones'] .  '<br/>
				Usted deber&aacute; ingersar a <a href="https://sistemas.fca.uncu.edu.ar/solicitudes" target="_blank">https://sistemas.fca.uncu.edu.ar/solicitudes</a>, para aprobar o rechazar la solicitud .
			<table/>';
		} else if ($$datos['id_motivo'] == 61) {
			$asunto = 'Formulario de Justificacion de Inasistencia por Excesos de Inasistencia (SIN GOCE)';
			$cuerpo = '<table>

				El/la agente <b>' . $datos['descripcion'] . '</b> perteneciente a <b>' . $datos['catedra'] . '</b> <br/>
				Solicita solicita Razones Particulares a partir SIN GOCE  a partir del d&iacute;a ' . $fecha . ' hasta ' . $hasta . '<br/>
				Teniendo en cuenta las siguientes Observaciones: ' . $datos['observaciones'] .  '<br/>
				Usted deber&aacute; ingersar a <a href="https://sistemas.fca.uncu.edu.ar/solicitudes" target="_blank">https://sistemas.fca.uncu.edu.ar/solicitudes</a>, para aprobar o rechazar la solicitud .
			<table/>';
		} else {
			$asunto = 'Formulario de Justificaci&oacute;n de Inasistencia por Donacion de Sangre';
			$cuerpo = '<table>

				El/la agente <b>' . $datos['descripcion'] . '</b> perteneciente a <b>' . $datos['catedra'] . '</b> <br/>
				Justific&oacute;  la inasistencia el dia ' . $fecha . ' presentando el certificado correspondiente a dicha acci&oacute;n.
			<table/>';
			switch ($datos['id_motivo']) {
				case 12:
					$asunto = 'Formulario de Justificacion de Inasistencia por Donaci&oacute;n de Sangre';
					break;
				case 22:
					$asunto = 'Formulario de Justificacion de Inasistencia por Realización de Actividad Deportiva o Art&iacute;stica';
					break;
				case 49:
					$asunto = 'Formulario de Justificacion de Inasistencia por Citaci&oacute;n de Sangre';
					break;
				case 17:
					$asunto = 'Formulario de Justificacion de Inasistencia por Fallecimiento de Cony&uacute;ge o Pariente de Primer Grado';
					break;
				case 16:
					$asunto = 'Formulario de Justificacion de Inasistencia por Fallecimiento de Pariente de Segundo Grado';
					break;
				case 18:
					$asunto = 'Formulario de Justificacion de Inasistencia por Fallecimiento de Pariente Pol&iacute;tico';
					break;
				case 27:
					$asunto = 'Formulario de Justificacion de Inasistencia por Nacimiento (Paternidad)';
					break;
				case 36:
					$asunto = 'Formulario de Justificacion de Inasistencia por Matrimonio';
					break;
				case 25:
					$asunto = 'Formulario de Justificacion de Inasistencia por Matrimonio de hijo/a';
					break;
				case 7:
					$asunto = 'Formulario de Justificacion de Inasistencia por Adopci&oacute;n (Maternidad)';
					break;
				case 59:
					$asunto = 'Formulario de Justificacion de Inasistencia por Adopci&oacute;n (Paternidad)';
					break;
				case 14:
					$asunto = 'Formulario de Justificacion de Inasistencia por Exam&eacute;n de Nivel Medio';
					break;
				case 15:
					$asunto = 'Formulario de Justificacion de Inasistencia por Exam&eacute;n de Nivel Superior';
					break;
				case 61:
					$asunto = 'Formulario de Justificacion de Inasistencia por Excesos de Inasistencia (SIN GOCE)';
					break;
				case 47:
					$asunto = 'Formulario de Justificacion de Inasistencia por Exam&eacute;n para Concurso';
					break;
			}
		}
		//Enviamos el correo
		$mail = new TobaMail($correo, $asunto, $cuerpo, $desde, ['asistencia@fca.uncu.edu.ar']);

		// Agregar un archivo adjunto
		//$mail->agregarAdjunto('nombre_archivo.pdf', '/ruta/al/archivo/nombre_archivo.pdf');

		try {
			$mail->ejecutar();
			echo "Correo enviado exitosamente.<br>";
		} catch (Exception $e) {
			echo "Error al enviar el correo: " . $e->getMessage();
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

		$dir_archivo = $ruta . $datos['archivo'];

		// copio el archivo temporal al directorio definitivo
		move_uploaded_file($archivo, $dir_archivo);

		//$datos = $this->limpiar_datos($datos, 'archivo');

		return $datos;
	}
	function extender_objeto_js()
	{
		parent::extender_objeto_js();
		$id_formulario = $this->dep('formulario')->get_id_objeto_js();
		echo "
		  $id_formulario.evt__id_motivo__procesar = function (es_inicial)
		  {
		  	if (this.ef('id_motivo').get_estado() == '35'){
		  		var ano = new Date().getFullYear();
		  		const inicio = new Date(ano, 11, 23);
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
		include("usuario_logueado.php");
		$legajo = usuario_logueado::get_legajo(toba::usuario()->get_id());
		/*$sql = "SELECT apellido, nombre FROM reloj.agentes
				WHERE legajo = $legajo ;";
		$agente = toba::db('comision')->consultar($sql);*/

		$this->s__agentes = $legajo;
		$datos['legajo'] = $legajo[0]['legajo'];
		$datos['apellido'] = $legajo[0]['apellido'];
		$datos['nombre'] = $legajo[0]['nombre'];
		$form->set_datos($datos);
	}
	function contarDiasCorridosParaHabiles($fechaInicio,$diasHabilesNecesarios) {
		$fecha = $fechaInicio ? new DateTime($fechaInicio) : new DateTime();
		$contadorHabiles = 0;
		$diasTotales = 0;
		
		$sql = "SELECT generate_series FROM reloj.vw_feriados";
		$tf= toba::db('comision')->consultar($sql);
		$feriados=[];
		if(!empty($tf)){
		for ($i=0;$i<count($tf);$i++){
			$feriados = $tf[$i]['generate_series'];
		}
		}	
		while ($contadorHabiles < $diasHabilesNecesarios) {
			$diaSemana = $fecha->format('N'); // 6 = sábado, 7 = domingo
       		$fechaActual = $fecha->format('Y-m-d');
			
			if ($diaSemana < 6 && !in_array($fechaActual, $feriados)) {
				$contadorHabiles++;
			}
	
			$diasTotales++;
			if ($contadorHabiles < $diasHabilesNecesarios) {
				$fecha->modify('+1 day');
			}
		}
	
		return $diasTotales;
	}
}
