<?php
class ci_control_asistencia_detalle extends ctrl_asis_ci
{
	protected $s__marcas;
	protected $s__agente;
	protected $s__fecha_desde;
	protected $s__fecha_hasta;

	//-----------------------------------------------------------------------------------
	//---- Configuraciones --------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf()
	{

		
		//Primero cargo los parametros recibidos
		$parametros = toba::memoria()->get_parametros();
		$clave_get = toba::memoria()->get_parametro('fila_safe');
		$claves_originales = toba_ei_cuadro::recuperar_clave_fila('396000030', $clave_get); //Control de asistencia - cuadro_resumen

		if(isset($claves_originales['legajo'] )) {
		
			$fecha_desde = $_SESSION['fecha_desde'];
			$fecha_hasta = $_SESSION['fecha_hasta'];

			if (isset($_SESSION['basedatos'])) {
				$filtro_marca['basedatos'] = $_SESSION['basedatos'];
				}

			$filtro['legajo']         = $claves_originales['legajo']; 

			//$agente = toba::tabla('agentes')->get_agente($claves_originales['legajo']);
			
			$sql = "SELECT legajo, apellido, nombre, case 
									  when sum(cant_horas)/5 = 15  then '11:36'
									  when sum(cant_horas)/5 = 11  then '08:48'	
									  when sum(cant_horas)/5 = 10  then '07:00'	
									  when sum(cant_horas)/5 = 9  then '07:24'
									  when sum(cant_horas)/5 = 8  then '05:36'
									  when sum(cant_horas)/5 = 7  then '06:00'
									  when sum(cant_horas)/5 = 6  then '04:12'
									  when sum(cant_horas)/5 = 4  then '02:48'
									  when sum(cant_horas)/5 = 2  then '01:24'
									  end
									  horas_diarias,fec_nacim, dni, fecha_ingreso, estado_civil 

							--caracter,	categoria, agrupamiento, escalafon, cod_depcia,cuil, 

				FROM reloj.agentes WHERE legajo = ".$claves_originales['legajo']."
						
						group by legajo, apellido, nombre, fec_nacim, dni, fecha_ingreso, estado_civil 
							 --categoria, agrupamiento, escalafon, cod_depcia, cuil
							 --, cant_horas";
						
			/*$sql = "SELECT legajo, apellido, nombre, fec_nacim, dni, fec_ingreso, estado_civil, 
							caracter, categoria, agrupamiento, escalafon, cod_depcia, cuil, 
							case 
							when codc_dedic = 'SIMP' then cant_horas / 5
							when codc_dedic = 'SEMI' then cant_horas / 5
							when codc_dedic = 'EXCL' then cant_horas / 5
							end horas_diarias


						FROM uncu.legajo WHERE legajo = '".$claves_originales['legajo']."
						'";*/
			//$agente =  toba::db('mapuche')->consultar_fila($sql); 
			$agente =  toba::db('ctrl_asis')->consultar_fila($sql); 			
			//ei_arbol($sql);
			$horas_diarias= $agente['horas_diarias'];
			$horas_esp = $this->dep('datos')->tabla('conf_jornada')->get_horas_diarias($claves_originales['legajo']);
			
				if(isset($horas_esp[0]['horas'])){
			//ei_arbol($horas_esp);		
					$horas_diarias = '0'.$horas_esp[0]['horas'].':00'; ///Horas especiales jornadas
				}
			$sql = "SELECT escalafon  from reloj.agentes
			--from legajo
					Where legajo =".$claves_originales['legajo'].
					
					";";
		
			//$age = toba::db('mapuche')->consultar_fila($sql); 
			$age = toba::db('ctrl_asis')->consultar_fila($sql); 		
		/*	$cant_cargos = count($age); 
			$bandera = 1;
			for ($i=0;$i<$cant_cargos;$i++){
				if ($bandera==1){
				if ($age[$i]['escalafon'] == 'DOCE' ){
					$escalafon = 'DOCE';
					$bandera = 0;
				} else{
					$escalafon='NODO';
				}

				  
			}*/
			$escalafon = $age['escalafon'];
			//ei_arbol ($escalafon);
			//$agru=$agente['agrupamiento'];
			//$horas_diarias= $agente['horas_diarias'];
			/*$hora_diaria = explode(".", $agente['horas_diarias'] );

			$hora_diaria[1] = floatval('0.'.$hora_diaria[1]); 
			$horas_diarias = $hora_diaria[0] . ':'. $hora_diaria[1] ;
			ei_arbol($hora_diaria);
			$horas_diarias = date('h:i',strtotime($horas_diarias));*/
			//date('h:i A', strtotime($time))

			//substr_replace( '930', ':', -2, 0 )
		//	$hora_diaria = date_create($horas_diarias);
			//ei_arbol($hora_diaria);
			//$hora_diaria =date_format($hora_diaria,"H:i");

			//foto
			if(file_exists('fotos/'.$agente['dni'].'.jpg')){
				$agente['foto']      = '<img style="width: 100px; height: 100px; border-radius: 100px; -moz-border-radius: 100px; -webkit-border-radius: 100px; -khtml-border-radius: 100px;" src="fotos/'.$agente['dni'].'.jpg">';
			}elseif(file_exists('fotos/'.$agente['legajo'].'.jpg')){
				$agente['foto']      = '<img style="width: 100px; height: 100px; border-radius: 100px; -moz-border-radius: 100px; -webkit-border-radius: 100px; -khtml-border-radius: 100px;" src="fotos/'.$agente['legajo'].'.jpg">';
			}else{
				$agente['foto']      = '<img style="width: 100px; height: 100px; border-radius: 100px; -moz-border-radius: 100px; -webkit-border-radius: 100px; -khtml-border-radius: 100px;" src="fotos/unnamed.png">';   
			}

			$agente['nombre_completo'] = $agente['apellido'].', '.$agente['nombre'];
			
			$legajo=$claves_originales['legajo'];
			//setemos adscripciones
			$sql = "SELECT legajo, cod_depcia_origen, fecha_inicio, cod_depcia_destino 
					FROM reloj.adscripcion 
					WHERE legajo = $legajo
					AND fecha_inicio <= '".date("Y-m-d")."' 
					AND fecha_fin is null";
			$adscripciones =  toba::db('ctrl_asis')->consultar($sql); 
			if(count($adscripciones)>0){
				$agente['cod_depcia_destino'] = $adscripciones[0]['cod_depcia_destino'];
			}

			//seteamos fecha ingreso de tabla antiguedad --------------------------------
		//			$dato_antiguedad = toba::tabla('antiguedad')->get_antiguedad($agente['legajo']);
		//	$dato_antiguedad = $this->dep('datos')->tabla('antiguedad')->get_antiguedad($agente['legajo']);
			
	
			/*$agente = toba::db('mapuche')->consultar($sql);          
		$cant = count($agente);*/
		//ei_arbol($agente);
		$legajo=$agente['legajo'];
		$sql = "SELECT MIN(fecha_ingreso) fecha from reloj.agentes
		where legajo = $legajo";
		$fec_ingreso = toba::db('ctrl_asis')->consultar($sql);
		$res = 	$fec_ingreso[0]['fecha'];
		//ei_arbol($res);
		list($y,$m,$d)=explode("-",$res); //2011-03-31
                $fecha = $d."-".$m."-".$y;
                $dias = explode('-', $fecha, 3);
                $dias = mktime(0,0,0,$dias[1],$dias[0],$dias[2]);
                $antiguedad = (int)((time()-$dias)/31556926 );
                
               // ei_arbol($antiguedad);
                if ($antiguedad == 0){
                	$prop_vaca= (int)((time()-$dias)/1729147);
                }
        $sql ="SELECT fecha_ingreso from reloj.antiguedad
        WHERE legajo = $legajo";
        $fec_ingreso = toba::db('ctrl_asis')->consultar($sql);
        if (isset($fec_ingreso[0]['fecha_ingreso'])) {
        	$fec=$fec_ingreso[0]['fecha_ingreso'];
        	list($y,$m,$d)=explode("-",$fec); //2011-03-31
                $fecha = $d."-".$m."-".$y;
                $dias = explode('-', $fecha, 3);
                $dias = mktime(0,0,0,$dias[1],$dias[0],$dias[2]);
                //$antiguedad = ((time()-$dias)/31556926 );
              $antiguedad= (int)((time()-$dias)/31556926 );
            }
          // $agente['ant'] = $antiguedad; 
       
 //ei_arbol($antiguedad);
        //$dias= $datos['dias'];
       // $anio= $datos['anio'];*/
			



			if(!empty($dato_antiguedad['fecha_ingreso'])){
				$agente['fecha_ingreso'] = $dato_antiguedad['fecha_ingreso'];
			}

			//seteamos datos de vacaciones -----------------------------------------------
			if(!empty($agente['fecha_ingreso'])){
				if ($escalafon == 'NODO') {
					if ($antiguedad > 20){
						$dias_totales = 40;
					} elseif ($antiguedad > 15 && $antiguedad <=20)
							{
							$dias_totales = 35;
							} elseif ($antiguedad > 10 && $antiguedad <=15) {
								$dias_totales = 30;

							} elseif ($antiguedad > 5 && $antiguedad <=10)
								{
									$dias_totales = 25;
								} elseif ($antiguedad > 0.5 && $antiguedad <=5)
								{
									$dias_totales = 20;
								} else {
									$dias_totales= (int)((time()-$dias)/1729147); //Proporcional de Vacaciones
								}
				} else {
					if ($antiguedad > 15){
								$dias_totales = 45 ;
					
					} else {
								$dias_totales = 30;			
				
					}
				}





			//	$antiguedadv = toba::tabla('vacaciones_antiguedad')->get_array_antiguedad($agente['fec_ingreso'],$escalafon);
				$agente['dias_vac_antiguedad']   = utf8_decode($dias_totales.' dÃ­as');
				$agente['antiguedad']            = utf8_decode(intval($antiguedad).' aÃ±os');
				//$antiguedad['antiguedad']).' aÃ±os');

				//obtenemos dias tomados---------------------------------------
				/*$dias_tomados = 0;
				list($y,$m,$d) = explode('-',$fecha_desde);
				$filtro_parte['legajo']      = $agente['legajo']; 
				$filtro_parte['id_motivo']   = '35'; //Vacaciones 
				$filtro_parte['anio']        = $y; 
				for ($i=0; $i < 12; $i++) {  // bucle en todos los meses hasta el ultimo mes del aÃ±o
					$mes = $i+1;
					if($mes<10){ $filtro_parte['mes']  = "0".$mes;  }else{  $filtro_parte['mes']  = $mes; }
					
					$partes = toba::tabla('parte')->get_listado($filtro_parte);
					if(count($partes)>0){
						foreach ($partes as $parte) {
							$dias_tomados = $dias_tomados + $parte['dias_mes'];
						}
					}
				}*/

				//$agente['dias_vac_tomadas']     = utf8_decode($dias_tomados.' dÃ­as');
				//$dias_disponibles = toba::tabla('vacaciones_restantes')->get_vac_rest($legajo);
				$sql="SELECT dias FROM reloj.vacaciones_restantes
				where legajo = $legajo ;";
				
				$dias_disponibles= toba::db('ctrl_asis')->consultar($sql);	
				
				if(isset($dias_disponibles)){
					$dias_vac_disponibles= $dias_disponibles[0]['dias'];
				}else {
					
					$dias_vac_disponibles= 0;
				}
				
				//$dias_vac_disponibles = $antiguedad['dias'] - $dias_tomados;
				$agente['dias_vac_tomadas'] = utf8_decode($dias_vac_disponibles.' dÃ­as');

			}             
			//-----------------------------------------------------------------------------------------------------------------

			//seteamos valores en cero
			$agente['fecha_ini']         = null;
			$agente['fecha_desde']         = null;

			$agente['laborables']         = 0;
			$agente['feriados']         = 0;
			$agente['presentes']         = 0;
			$agente['ausentes']         = 0;
			$agente['justificados']     = 0;
			$agente['injustificados']     = 0;
			$agente['partes']             = 0;
			$agente['vacaciones']         = 0;

			$agente['horas_totales']     = 0;
			$agente['horas_promedio']     = 0;

			$horas_totales = 0;
			$contador_marcas = 0;

			$jornada = toba::tabla('conf_jornada')->get_jornada_agente($agente['legajo']);

			$filtro_marca['calcular_horas']     = true;

			if (empty($jornada['fecha_ini'])) { //si no tiene jornada, asignamos jornada predetermianda
				$jornada['fecha_ini']  = $fecha_desde;
				$jornada['normal']     = 1;
				$jornada['h1']         = "08:00:00";
				$jornada['h2']         = "14:00:00";
			}

			//--------------------------------------------------------------------------------------------
			//--------------------------------------------------------------------------------------------
			//reviso fecha desde 
			if($fecha_desde < $jornada['fecha_ini']){
				$fecha_desde = $jornada['fecha_ini'];
			}
			//reviso fecha hasta 
			if(!empty($jornada['fecha_fin']) and $fecha_hasta > $jornada['fecha_fin']){
				$fecha_hasta = $jornada['fecha_fin'];
			}elseif($fecha_hasta > date("Y-m-d")){ 
					$fecha_hasta = date("Y-m-d");
			}                  

			//recorremos todos los dias entre fecha_desde y fecha_hasta
			$fechaInicio = strtotime($fecha_desde);
			$fechaFin    = strtotime($fecha_hasta);
			$j=0;
			
			for($i=$fechaInicio; $i<=$fechaFin; $i+=86400){
				//ei_arbol($dia);
				$dia = date("Y-m-d", $i);
				//$fecha_fin_feriado = $array_marcas[$j]['fecha'];
				
			//	if ((toba::tabla('conf_feriados')->hay_feriado($dia)){// or ( $fecha_fin_feriado == $dia)){
					//$cantidad_feriado = toba::tabla('conf_feriados')->hay_feriado($dia);//revisamos el dÃ­a solo si no es feriado
			
					//$cantidad_feriado = $cantidad_feriado +1;
					//ei_arbol($cantidad_feriado); 
					/*if($cantidad_feriado > 0){ */

					//ei_arbol((toba::tabla('conf_feriados')->hay_feriado($dia,$escalafon)) );
					if ((toba::tabla('conf_feriados')->hay_feriado($dia,$escalafon)) <> 0 ){	//and ($bandera)
					$feriado = toba::tabla('conf_feriados')->get_feriado($dia,$escalafon); 

						//for ( $j=1;$j <= $cantidad_feriado; $j++){
							$agente['feriados']++;
							$array_marcas[] = array(
							'legajo'    => $agente['legajo'],
							'fecha'        => date('Y-m-d',strtotime('+'.$j.' day', strtotime($dia))),
							'descripcion'  => 'Feriado: '.$feriado['descripcion'],
								);

							
						//}
					//	$bandera = false;
								
				/*}elseif ($cantidad_feriado <>0) {
					$agente['feriados']++;

					$feriado = toba::tabla('conf_feriados')->get_feriado($dia);

					$array_marcas[] = array(
							'legajo'    => $agente['legajo'],
							'fecha'        => $dia,
							'descripcion'  => 'Feriado: '.$feriado['descripcion'],
								);*/
								

				}else{
				//	$bandera =true;
					$datos_dia = getdate($i);
					

					switch ($datos_dia['wday']) { //0 (para Domingo) hasta 6 (para SÃ¡bado)
						
						case 1: //lunes

							if($jornada['normal']==1 or $jornada['lunes']==1 ) {
								$this->calculo_dia ('lunes', 'Lunes', $agente, $array_marcas, $contador_marcas, $dia, $filtro_marca);
							}
							break;

						case 2: //martes

							if($jornada['normal']==1 or $jornada['martes']==1 ) {
								$this->calculo_dia ('martes', 'Martes', $agente, $array_marcas, $contador_marcas, $dia, $filtro_marca);
							}
							break;

						case 3: //miercoles

							if($jornada['normal']==1 or $jornada['miercoles']==1 ) {
								$this->calculo_dia ('miercoles', 'Miércoles', $agente, $array_marcas, $contador_marcas, $dia, $filtro_marca);
							}
							break;

						case 4: //jueves

							if($jornada['normal']==1 or $jornada['jueves']==1 ) {
								$this->calculo_dia ('jueves', 'Jueves', $agente, $array_marcas, $contador_marcas, $dia, $filtro_marca);
							}
							break;

						case 5: //viernes

							if($jornada['normal']==1 or $jornada['viernes']==1 ) {
								$this->calculo_dia ('viernes', 'Viernes', $agente, $array_marcas, $contador_marcas, $dia, $filtro_marca);
							}
							//-------------------------------------------------------------------------
							break;

						case 6: //sabado

							if($jornada['sabado']==1) {
								$this->calculo_dia ('sabado', 'Sábado', $agente, $array_marcas, $contador_marcas, $dia, $filtro_marca);
							}
							break;

						case 0: //domingo

							if($jornada['domingo']==1) {
								$this->calculo_dia ('domingo', 'Domingo', $agente, $array_marcas, $contador_marcas, $dia, $filtro_marca);    
							}
							break;
					}//fin switch

				}//fin no es feriado

			}
			//ei_arbol($feriado);
			//fin recorremos todos los dias entre fecha_desde y fecha_hasta


			//Recorremos array de marcas para agregar casos especiales --------------------------------
			if(count($array_marcas)>0){

				$horas_totales = 0;

				foreach ($array_marcas as $key => $marca) {
					
					if($marca['descripcion'] == 'Presente' or strrpos($marca['descripcion'], '#28' ) or !empty($marca['id_info_complementaria']) ){

						//agregarmos horarios vista
						$array_marcas[$key]['entrada_ver']  = $marca['entrada'];
						$array_marcas[$key]['salida_ver']   = $marca['salida'];


						//agregamos horarios que falten, con vista en rojo
						if(!empty($marca['entrada']) and empty($marca['salida']) ) { //tiene solo la entrada

							//si la marca siguiente solo tiene salida, esta bien; sino ponemos la entrada salida con el mismo horario de la entrada
							$key_siguiente = $key+1;
							if(!empty($array_marcas[$key_siguiente]['entrada']) or ( empty($array_marcas[$key_siguiente]['entrada']) and empty($array_marcas[$key_siguiente]['salida']) ) ) { 
								$array_marcas[$key]['salida']      = $marca['entrada'];
								$array_marcas[$key]['salida_ver'] = '<span style="color:#FF0000">'.$marca['entrada'].'</span>';
							}

						}elseif(empty($marca['entrada']) and !empty($marca['salida']) ) { //tiene solo la salida

							//si la marca anterior solo tiene entrada, esta bien, sino ponemos la entrada actual con el mismo horario de la salida
							$key_anterior = $key-1;
							if(!empty($array_marcas[$key_anterior]['salida']) or ( empty($array_marcas[$key_anterior]['entrada']) and empty($array_marcas[$key_anterior]['salida']) ) ) { 
								$array_marcas[$key]['entrada']     = $marca['salida'];
								$array_marcas[$key]['entrada_ver'] = '<span style="color:#FF0000">'.$marca['salida'].'</span>';
							}

						}

						//calculamos horas 
						$horas            = $this->dep('access')->restar_horas($array_marcas[$key]['entrada'],$array_marcas[$key]['salida']);
						$horas_totales = $this->dep('access')->sumar_horas($horas,$horas_totales);

						

						$array_marcas[$key]['horas']         = $horas;
						//$horas_diarias = $horas_diarias. ':00'; 
						//ei_arbol($horas,$horas_diarias);
						if ($horas < $horas_diarias) {
							$array_marcas[$key]['horas_diarias'] = '<b><span style="color:#FF0000">'.$horas_diarias.'</b></span>';
						} else {
							$array_marcas[$key]['horas_diarias'] = $horas_diarias;
						}
						
						$array_marcas[$key]['suma_acum']     = $horas_totales;
						$array_marcas[$key]['prom_acum']     = $this->dep('access')->dividir_horas($horas_totales,$marca['contador_marcas']);//dividendo,divisor    

					}elseif($marca['descripcion'] == 'Ausente'){

						$array_marcas[$key]['prom_acum']     = $this->dep('access')->dividir_horas($horas_totales,$marca['contador_marcas']);

					}
				}
			}
			//-----------------------------------------------------------------------------------------------

			//ei_arbol($array_marcas);
			$agente['fecha_desde']         = $fecha_desde;
			$agente['fecha_hasta']         = $fecha_hasta;

			if($agente['laborables'] > 0){
				$agente['horas_totales']       = $horas_totales;
				#$agente['horas_promedio']       = round( ( $agente['horas_totales'] / $agente['laborables'] ) , 2);
				$agente['horas_promedio']       = $this->dep('access')->dividir_horas($agente['horas_totales'],$agente['laborables']);
			}else{
				$agente['horas_promedio']       = '0:00:00';
			}

			#$agente['presentes']         = 0;
			#$agente['ausentes']         = 0;
			#$agente['justificados']     = 0;
			#$agente['injustificados']     = 0;
			#$agente['partes']             = 0;
			#$agente['vacaciones']         = 0;
			//$agente['horas_promedio']    = $this->mostrar_minutos($agente['horas_promedio']);
			//$agente['horas_totales']     = $this->mostrar_minutos($agente['horas_totales']);

			
			//--------------------------------------------------------------------------------------------
			//--------------------------------------------------------------------------------------------

			#return $agentes;
			//-----------------------------------------------------------------------------------------------------------------


			$this->s__agente = $agente;
			$this->s__fecha_desde = $fecha_desde;
			$this->s__fecha_hasta = $fecha_hasta;
			
			$this->s__marcas = $array_marcas;



			#$this->pantalla()->set_titulo($agente['legajo'].". ".$agente['apellido'].",".$agente['nombre'].". Desde el ".$fecha_desde.", hasta el ".$fecha_hasta);
			#$this->pantalla()->set_descripcion("<br><p>descripcion pantalla</p>");
		}

	}

	function calculo_dia ($dia_ref, $dia_leyenda, &$agente, &$array_marcas, &$contador_marcas, $dia, $filtro_marca)
	{
		
			$agente['laborables']++;
							
			$id_parte = toba::tabla('parte')->tiene_parte($agente['legajo'], $dia);  

			$id_parte_sanidad = toba::tabla('parte')->tiene_parte_sanidad($agente['legajo'], $dia);
			$info_complementaria = toba::tabla('info_complementaria')->tiene_info_complementaria($agente['legajo'], $dia);                    
			//ei_arbol($id_parte);
			if($id_parte_sanidad > 0){  
				
				$agente['partes_sanidad']++; 

				$agente['ausentes']++; 
				$agente['justificados']++;

				$parte = toba::tabla('parte')->get_parte_sanidad($id_parte_sanidad);
				$array_marcas[] = array(
					'legajo'    => $agente['legajo'],
					'fecha'        => $dia,
					'dia'       => $dia_leyenda,
					'descripcion'  => 'Parte sanidad '.$parte['id_parte'].': '.$parte['motivo']
						);


			}elseif($id_parte > 0){ 
				$agente['partes']++; 
				
				$parte = toba::tabla('parte')->get_parte($id_parte);
				//ei_arbol($parte );
				if($parte['id_motivo'] == 28){ // Permisos excepcionales, muestra las marcas pero no las cuenta

					//---------------------------------------------------------------
					//---------------------------------------------------------------

					$filtro_marca['badgenumber'] = $agente['legajo']; 
					$filtro_marca['fecha']       = $dia;                                    

					$marcas = $this->dep('access')->get_marcas($filtro_marca);
					if(count($marcas)>0){
						
						#$contador_marcas++;

						foreach($marcas as $marca){
							$marca['contador_marcas'] = $contador_marcas;
							$marca['dia'] = $dia_leyenda;
							$marca['descripcion'] = 'Parte '.$parte['id_parte'].': '.$parte['motivo'].' (#'.$parte['id_motivo'].')';

							$array_marcas[] = $marca;
						}

						$agente['ausentes']++; //$agente['presentes']++;
						$agente['justificados']++;

					}else{
						$agente['ausentes']++;
						$agente['injustificados']++;

						$contador_marcas++;
						$array_marcas[] = array(
							'legajo'    => $agente['legajo'],
							'fecha'        => $dia,
							'dia'       => $dia_leyenda,
							'descripcion'  =>'Ausente',
							'contador_marcas' =>$contador_marcas,
							'prom_acum' => $this->dep('access')->dividir_horas($horas_totales,$contador_marcas)
								);    

					}

					//---------------------------------------------------------------
					//---------------------------------------------------------------

				}else{
					
					$agente['ausentes']++; //$agente['presentes']++;
					$agente['justificados']++;

					$array_marcas[] = array(
						'legajo'    => $agente['legajo'],
						'fecha'        => $dia,
						'dia'       => $dia_leyenda,
						'descripcion'  => 'Parte '.$parte['id_parte'].': '.$parte['motivo'].'(#'.$parte['id_motivo'].')'
						);    

				}


			}elseif(!empty($info_complementaria['id_info_complementaria'])){  

				/*if($agente['legajo'] == '28983'){
				ei_arbol($info_complementaria);    
				}*/

				//seteamos marca complementaria
				$marcas[0] = array(
					'marca_id'            => 'IC'.$info_complementaria['id_info_complementaria'],
					'badgenumber'        => $info_complementaria['legajo'],
					'fecha'                => $dia,
					'entrada'             =>  substr($info_complementaria['entrada'], -8,8),
					'basedatos_i'          => '-', 
					'reloj_i'            => null,
					'basedatos_o'          => '-',
					'reloj_o'            => null,
					'salida'             =>  substr($info_complementaria['salida'], -8,8),
					'horas'                =>  $info_complementaria['horas'],
					'id_info_complementaria' => $info_complementaria['id_info_complementaria']
					);    


				//ahora es igual que las marcas normales
		
				$contador_marcas++;

				foreach($marcas as $marca){
					$marca['contador_marcas'] = $contador_marcas;
					$marca['dia'] = $dia_leyenda;
					$marca['descripcion'] = 'IC '.$info_complementaria['id_info_complementaria'].': '.$info_complementaria['observaciones'];

					$array_marcas[] = $marca;
				}

				$agente['presentes']++;


			}else{ //buscamos marcas

				$filtro_marca['badgenumber'] = $agente['legajo']; 
				$filtro_marca['fecha']       = $dia;                                    
				
				$marcas = $this->dep('access')->get_marcas($filtro_marca);
				
				if(count($marcas)>0){
					
					$contador_marcas++;

					foreach($marcas as $marca){
						$marca['contador_marcas'] = $contador_marcas;
						$marca['dia'] = $dia_leyenda;
						$marca['descripcion'] = 'Presente';

						$array_marcas[] = $marca;
					}

					$agente['presentes']++;


				}else{
					$agente['ausentes']++;
					$agente['injustificados']++;

					$contador_marcas++;
					$array_marcas[] = array(
						'legajo'    => $agente['legajo'],
						'fecha'        => $dia,
						'dia'       => $dia_leyenda,
						'descripcion'  =>'Ausente',
						'contador_marcas' =>$contador_marcas,
						'prom_acum' => $this->dep('access')->dividir_horas($horas_totales,$contador_marcas)
							);    
			
				}
			}

	}
														
	//-----------------------------------------------------------------------------------
	//---- cuadro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro(ctrl_asis_ei_cuadro $cuadro)
	{

		if(count($this->s__marcas)>0){
			$cuadro->set_datos($this->s__marcas);
			list($y,$m,$d) = explode('-', $this->s__fecha_desde);
			$fecha_desde = "$d/$m/$y";
			list($y,$m,$d) = explode('-', $this->s__fecha_hasta);
			$fecha_hasta = "$d/$m/$y";
			$cuadro->set_titulo("Asistencia desde el ".$fecha_desde.", hasta el ".$fecha_hasta);
		}
		
		#$this->pantalla->set_titulo = 'Detalle asistencia';
	}

														
	//-----------------------------------------------------------------------------------
	//---- form agente ------------------------------------------------------------------
	//-----------------------------------------------------------------------------------


	function conf__agente(toba_ei_formulario $form)
	{ 

		if (isset($this->s__agente)) { 

			$form->set_datos($this->s__agente);

			$form->set_titulo("Datos agente ".$this->s__agente['legajo'].".");

		}
	}

	//-----------------------------------------------------------------------------------
	//---- funciones ------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function vista_impresion( toba_impresion $salida )
	{
		$salida->titulo("Detalle control asistencia");
		$this->dependencia('agente')->vista_impresion($salida);
		$this->dependencia('cuadro')->vista_impresion($salida);
	}
	
	function vista_excel(toba_vista_excel $salida)
	{
		
		
		$excel = $salida->get_excel();
		
		$excel->setActiveSheetIndex(0);
		$this->dependencia('cuadro')->vista_excel($salida);
		$excel->getActiveSheet()->setTitle('Detalle Asistencia');
		
	}

	/*function vista_pdf(toba_vista_pdf $salida)
	{

		$pdf = $salida->get_pdf();
		$pdf->ezSetMargins(80, 50, 30, 30);    //top, bottom, left, right
				
		//Pie de pÃ¡gina
		$formato = 'PÃ¡gina {PAGENUM} de {TOTALPAGENUM}';
		$pdf->ezStartPageNumbers(300, 20, 8, 'left', $formato, 1);    //x, y, size, pos, texto, pagina inicio

		//Inserto los componentes usando la API de toba_vista_pdf
		$salida->titulo("Detalle control asistencia");
		$this->dependencia('agente')->vista_pdf($salida);
		$this->dependencia('cuadro')->vista_pdf($salida);    
		
		//Encabezado
		$pdf = $salida->get_pdf();
		foreach ($pdf->ezPages as $pageNum=>$id){
			$pdf->reopenObject($id);
			$imagen = toba::proyecto()->get_path().'/www/img/logo_toba_siu.jpg';
			$pdf->addJpegFromFile($imagen, 50, 780, 141, 45);    //imagen, x, y, ancho, alto
				$pdf->closeObject();        
		}        
		
	}*/

	private static function _checkSheetTitle($pValue)
	{
    // Some of the printable ASCII characters are invalid:  * : / \ ? [ ]
    if (str_replace(self::$_invalidCharacters, '', $pValue) !== $pValue) {
        //throw new Exception('Invalid character found in sheet title');

        //Hack to remove bad characters from sheet name instead of throwing an exception
        return str_replace(self::$_invalidCharacters, '', $pValue);
    }

    // Maximum 31 characters allowed for sheet title
    if (PHPExcel_Shared_String::CountCharacters($pValue) > 31) {
        throw new Exception('Maximum 31 characters allowed in sheet title.');
    }

    return $pValue;
	}


}
?>