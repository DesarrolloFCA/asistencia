<?php
class ci_parte extends toba_ci
{
	protected $s__datos_filtro;
	protected $s__datos;
	protected $s__accion;

	protected $s__cuadro_xls = 'cuadro';

	//-----------------------------------------------------------------------------------
	//---- Configuraciones --------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf()
	{
		$info['basica'] = toba::solicitud()->get_datos_item();
		$item = new toba_item_info($info);
		if($item->get_id() == '3481'){
			$this->set_pantalla('pant_alta');
		}

		if(isset($_GET['levantar']) and $_GET['levantar'] == 'true'){
			$this->dep('datos')->tabla('parte')->cerrar_partes_vencidos();
		}

		//olcultamos tabs 
		$this->pantalla()->tab('pant_alta')->ocultar();
		$this->pantalla()->tab('pant_levantar')->ocultar();
		$this->pantalla()->tab('pant_vista')->ocultar();
		$this->pantalla()->tab('pant_edicion')->ocultar();
	}

	//---- Filtro -----------------------------------------------------------------------

	function conf__filtro(toba_ei_formulario $filtro)
	{
		if (isset($this->s__datos_filtro)) {
			$filtro->set_datos($this->s__datos_filtro);
		}else{
			if(!empty($_SESSION['dependencia'])){ 
				$f['cod_depcia']  = $_SESSION['dependencia'];
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


	function conf__filtro2(toba_ei_formulario $filtro)
	{
		if (isset($this->s__datos_filtro)) {
			$filtro->set_datos($this->s__datos_filtro);
		}else{
			if(!empty($_SESSION['dependencia'])){ 
				$f['cod_depcia']  = $_SESSION['dependencia'];
			}
			if(!empty($_SESSION['agente'])){ 
				$f['legajo']  = $_SESSION['agente'];
			}
			$filtro->set_datos($f);
		}

	}

	function evt__filtro2__filtrar($datos)
	{
		$this->s__datos_filtro = $datos;
	}

	function evt__filtro2__cancelar()
	{
		unset($this->s__datos_filtro);
	}


	//---- Cuadro -----------------------------------------------------------------------

	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		$this->s__cuadro_xls = 'cuadro';

		if (isset($this->s__datos_filtro)) {
			$datos = $this->dep('datos')->tabla('parte')->get_listado($this->s__datos_filtro);
			$max = count($datos);
			for($i=0;$i<$max-1;$i++){
			$dias_calculo = $datos[$i]['dias'] - 1;
			$dias = '+'.$dias_calculo.' day';
			$datos[$i]['fecha_fin_licencia']     = date ( 'Y-m-d' , strtotime ( $dias , strtotime ( $datos[$i]['fecha_inicio_licencia'] ) )  ); //sumamos N dias a la fecha de inicio licencia
			//list($y,$m,$d)=explode("-",$datos['fecha_fin_licencia']); //2011-03-31
			//$datos[$i]['fecha_fin_licencia'] = $d."/".$m."/".$y;
			$datos[$i]['nombre_completo'] = $datos[$i]['apellido'].", ".$datos[$i]['nombre'];
			$id_parte = $datos[$i]['id_parte'];
			
			$comision = $this->dep('datos')->tabla('parte')->get_motivo($id_parte);
			$datos [$i]['comision'] = $comision;
			}
			
			$this->s__datos = $datos;
			$cuadro->set_datos($this->s__datos);
			//ei_arbol($this->s__datos);
			
		#} else {
		#    $this->s__datos = $this->dep('datos')->tabla('parte')->get_listado();
		}
		#$cuadro->set_datos($this->s__datos);
	}

		//Si no esta en estado A (abierto), ocultamos el boton para levantar el parte
		function conf_evt__cuadro__levantar($evento, $fila)
		{
				if ($this->s__datos[$fila]['estado'] <> 'A') {
						$evento->anular();
				}
		}

			//Si no esta en estado A (abierto), ocultamos el boton para levantar el parte
		function conf_evt__cuadro__ver($evento, $fila)
		{
				if ($this->s__datos[$fila]['estado'] <> 'C') {
						$evento->anular();
				}
		}       

		function evt__cuadro__eliminar($datos)
		{
			/*$this->dep('datos')->resetear();
			$this->dep('datos')->cargar($datos);
			$this->dep('datos')->eliminar_todo();
			$this->dep('datos')->resetear();*/

			//$this->dep('datos')->eliminar_todo();
			$this->dep('datos')->tabla('parte')->set_estado($datos['id_parte'],'E');

			$this->resetear();
		}

		function evt__cuadro__seleccion($datos)
		{
			$this->dep('datos')->cargar($datos);
			$this->s__id_parte = $datos['id_parte'];
			$_SESSION['id_parte'] = $datos['id_parte'];
			$this->set_pantalla('pant_edicion');
		}
		

		function evt__cuadro__levantar($datos)
		{
			$this->dep('datos')->cargar($datos);
			$this->s__id_parte = $datos['id_parte'];
			$_SESSION['id_parte'] = $datos['id_parte'];
			$this->set_pantalla('pant_levantar');
		}


		function evt__cuadro__ver($datos)
		{
			
			$this->dep('datos')->cargar($datos);
			$this->s__id_parte = $datos['id_parte'];
			$_SESSION['id_parte'] = $datos['id_parte'];            
			$this->set_pantalla('pant_vista');
		}

	//---- Cuadro2 -----------------------------------------------------------------------

	function conf__cuadro2(toba_ei_cuadro $cuadro)
	{
		$this->s__cuadro_xls = 'cuadro2';

		if (isset($this->s__datos_filtro)) {
			$this->s__datos = $this->dep('datos')->tabla('parte')->get_listado($this->s__datos_filtro);
			$cuadro->set_datos($this->s__datos);

		#} else {
		#    $this->s__datos = $this->dep('datos')->tabla('parte')->get_listado();
		}
		#$cuadro->set_datos($this->s__datos);
	}


		function evt__cuadro2__seleccion($datos)
		{
			$this->dep('datos')->cargar($datos);
			$this->s__id_parte = $datos['id_parte'];
			$_SESSION['id_parte'] = $datos['id_parte'];
			$this->set_pantalla('pant_edicion');
		}

		function evt__cuadro2__ver($datos)
		{
			$this->dep('datos')->cargar($datos);
			$this->s__id_parte = $datos['id_parte'];
			$_SESSION['id_parte'] = $datos['id_parte'];            
			$this->set_pantalla('pant_vista');
		}

	//---- Cuadro usado por el evente imprimir_xls  ------------------------------------

	/*function conf__cuadro_print(toba_ei_cuadro $cuadro)
	{
		if (isset($this->s__datos_filtro)) {
			$filtro = $this->s__datos_filtro;
		}

		#$filtro['fecha_desde'] = date("Y-m-d");
		#$filtro['fecha_hasta'] = date("Y-m-d");
		$filtro['estado'] = 'A'; //agregado para que si o si sean solo los abierto
		$cuadro->set_datos($this->dep('datos')->tabla('parte')->get_listado($filtro));
	}*/


	//---- Formulario -------------------------------------------------------------------

	function conf__formulario(toba_ei_formulario $form)
	{ 

		if ($this->dep('datos')->esta_cargada()) { //modificacion

			$datos = $this->dep('datos')->tabla('parte')->get();
			
			//En caso de que sea un parte de vacaciones
			if(!empty($datos['id_motivo']) && $datos['id_motivo'] == 35){
				$parte_anio = toba::tabla('parte')->get_parte_anio($datos['id_parte']);
				$datos['anio']  = $parte_anio['anio'];
			}else{
				$datos['anio']  = "-";
			}
			if (isset($datos['id_comision'])){
				$id_comision=$datos['id_comision'];
				$sql = "SELECT motivo, observaciones FROM reloj.comision
				Where id_comision = $id_comision ";
				$comision = toba::db('ctrl_asis')->consultar_fila($sql);
				$datos['comision'] = $comision['motivo'];
				$datos['observaciones'] = $comision['observaciones'];
			}

			$form->set_datos($datos);

			$fecha_inicio_licencia = substr($datos['fecha_inicio_licencia'], 0,7); //yyyy-mm
			if($fecha_inicio_licencia <> date("Y-m") and $_SESSION['admin'] <> true ){
				$this->pantalla()->eliminar_evento('eliminar');
			}

		}
	}

	function evt__formulario__modificacion($datos)
	{
		/*
		if ($this->dep('datos')->esta_cargada()) {//modificacion

			//certificado ----------------------------------------------------------
			$folder_path = "../www/certificados/";
			if (isset($datos['certificado']) and !empty($datos['certificado']['name'])) {
					$nombre_certificado = $_SESSION['id_parte'].substr($datos['certificado']['name'],-4,4); 
					$destino            = $folder_path.$nombre_certificado;
					move_uploaded_file($datos['certificado']['tmp_name'], $destino ) ;
					if ( is_file( $destino ) ){
							$datos['certificado'] = $nombre_certificado;
					}
			}elseif (isset($datos['certificado']) and empty($datos['certificado']['name'])) {
					$datos['certificado'] = $datos['certificado']['name'];
			}else{
					//Mantener el valor anterior
					$url= $this->dep('datos')->tabla('parte')->get_certificado($_SESSION['id_parte']);
					if(empty($url['certificado'])){
							$datos['certificado'] = $datos['certificado']['name'];
					}else{
							$datos['certificado'] = $url['certificado'];
					}
			}
			

		} else { //alta

			//seteamos datos fijos, de campos ocultos
			$datos['fecha_alta']   = date("Y-m-d H:i:s");
			$datos['usuario_alta'] = toba::usuario()->get_id();
			#$datos['estado']   = 'A';  //A/Abierto - Vigente (el médico lo puede editar),L/Levantado - Cerrado (ya no se puede modificar)
		}
		*/

		$this->dep('datos')->tabla('parte')->set($datos);
		$this->s__accion = 'modificacion';        
	}


	//---- Formulario alta -------------------------------------------------------------------

	function conf__form_alta(toba_ei_formulario $form)
	{
		$datos['fecha_inicio_licencia'] = date("Y-m-d");
		$form->set_datos($datos);
	}

	function evt__form_alta__modificacion($datos)
	{        
		//seteamos datos fijos, de campos ocultos
		$datos['estado']           = 'C';  // A/Abierto - Vigente (el médico lo puede editar),L/Levantado - Cerrado (ya no se puede modificar)
		$datos['fecha_alta']    = date("Y-m-d H:i:s");
		$datos['usuario_alta']  = toba::usuario()->get_id();

		$datos['fecha_cierre']    = date("Y-m-d H:i:s");
		$datos['usuario_cierre']  = toba::usuario()->get_id();
		$legajo = $datos['legajo'];
		$dependencia = $datos['cod_depcia'];
		$id_motivo= $datos['id_motivos'];
		$anio=$datos['anio'];
		$agrupamiento = $datos['agrupamiento'];
		$dias_restantes = 0;
		$dias = $datos['dias'];
		if($datos['id_motivo'] == '36'){
			$datos['dias'] =$this->contarDiasCorridosParaHabiles( $datos['fecha_inicio_licencia'],10);
		   }
		
		$this->dep('datos')->tabla('parte')->set($datos);
		//ei_arbol($datos);
		//validar que venga un anio para partes de vacaciones
		if ($datos['id_motivo']==57){
			$sql = "SELECT max(anio) anio_v, sum(dias) dias_t from reloj.vacaciones_restantes
					WHERE legajo = '$legajo';";
			$agente = toba::db('ctrl_asis')->consultar($sql);
			
			if (isset($agente)){
				$datos [anio] = $agente[0]['anio_v'];
				$anio=$datos['anio'];
				$dias_restantes = $agente [0]['dias_t'] - $dias;
				$bandera= 1;
				
				$datos['dias_restantes'] = $dias_restantes;
				if( $dias_restantes < 0 ){
					toba::notificacion()->agregar('El agente '.$datos['legajo'].' - '.$datos['apellido'].', '.$datos['nombre'] .' tiene' .$agente['dias_t'].' dias pendientes de la licencia anual de a�o '.$datos['anio'] . '.Pro favor elegija una cantidad de dias menor o igual que la especificada', 'error');
				$bandera= 0;
				} else if($dias_restantes == 0) {
					$sql = "DELETE from reloj.vacaciones_restantes
							WHERE legajo = '$legajo' AND  anio = '$anio'";
							toba::db('ctrl_asis')->ejecutar($sql);	

				}else {

				$sql = "UPDATE reloj.vacaciones_restantes
						SET dias = $dias_restantes
						WHERE legajo = '$legajo ' AND  anio ='$anio' ";
						//ei_arbol($dias_restantes);
						toba::db('ctrl_asis')->ejecutar($sql);	
				}		
			} else {
				 toba::notificacion()->agregar('El agente'.$datos['legajo'].' - '.$datos['apellido'].', '.$datos['nombre'] .' no posee dias pendientes de la licencia anual de a�o '.$datos['anio'] . '.', 'error');  
				$bandera= 0;
			}
			
			$this->s__datos =$datos;
		}
		
	
		if(isset($datos['anio']) && $datos['id_motivo'] == '35') {
			$dato_antiguedad = toba::tabla('antiguedad')->get_antiguedad($datos['legajo']);
			$bandera = 1;
			//ei_arbol($dato_antiguedad);
			if(!empty($dato_antiguedad['fecha_ingreso'])){
						$agente['fec_ingreso'] = $dato_antiguedad['fecha_ingreso'];
					}else{
						
						$sql = "SELECT fec_ingreso FROM reloj.agentes WHERE legajo = '$legajo' and agrupamiento = '$agrupamiento' ";
						$agente =  toba::db('ctrl_asis')->consultar_fila($sql); 
					}    
					if(!empty($agente['fec_ingreso'])){

						//obtenemos dias por antiguedad ------------------------------
						$antiguedad = toba::tabla('vacaciones_antiguedad')->get_array_antiguedad($agente['fec_ingreso'],$agrupamiento, $anio);
						$dias_tomados = 0;
						list($anio_lic,$mes_lic,$dia_lic) = explode('-', $fecha_inicio_licencia);
						$filtro['legajo']      = $legajo;
						$filtro['id_motivo']   = $datos['id_motivo'];
						$filtro['agrupamiento']= $agrupamiento;
						$filtro['parte_anio']  = $anio; //ano seleccionado por vacaciones
						$filtro['anio']        = $anio; //date("Y"); //ano actual
						/*for ($i=0; $i < date("m") ; $i++) {  // bucle en todos los meses hasta el mes actual
							$mes = $i+1;
							if($mes<10){
								$filtro['mes']  = "0".$mes;
							}else{
								$filtro['mes']  = $mes;
							}*/
							//ei_arbol($filtro);
							
							$partes = toba::tabla('parte')->get_listado_vaca($filtro);
							$lim=count($partes);
							
							if($lim>0){

								/*for($i=0;$i<=$lim;$i++){
									$dias_tomados = $dias_tomados + $partes[$i]['dias'];
								}
								/*foreach ($partes as $parte) {
									$dias_tomados = $dias_tomados + $parte['dias'];
								}*/
								//ei_arbol($partes);
								//ei_arbol($dias_tomados);
								$dias_tomados = $partes[0]['sum'];
							//}
							} else 
							{ 
								$dias_tomados = 0;
							}
											
						$vacaciones_restantes = toba::tabla('vacaciones_restantes')->get_dias($legajo, $anio, $agrupamiento);
						
						
						if (is_null($vacaciones_restantes)){

							$dias_disponibles = $dato_antiguedad['dias'] - $dias_tomados ; //$antiguedad['dias'] - $dias_tomados;

					
						}else{
							//ei_arbol($vacaciones_restantes);
							$dias_disponibles = $vacaciones_restantes - $dias_tomados;
						}
					}
					
					$dias_restantes = $dias_disponibles - $dias;
					
					$sql = "INSERT INTO reloj.vacaciones_restantes(
						legajo, cod_depcia, agrupamiento, anio, dias)
						VALUES ($legajo, '04','$agrupamiento' , $anio, $dias_restantes);";
						toba::db('ctrl_asis')->ejecutar($sql);	
					



			 $this->dep('datos')->tabla('parte_anio')->set($datos);


		} 
		if ($datos['id_motivo'] == '55' ){
					
					$anio== date('Y');
						$sql = "INSERT INTO reloj.vacaciones_adelantadas ( legajo, anio, dias_adelanto) 
						values ($legajo, $anio,$dias)";
						toba::db('ctrl_asis')->ejecutar($sql);	

					
		}
		
		$sql= "SELECT email from reloj.agentes_mail
					where legajo=$legajo";
					$correo = toba::db('ctrl_asis')->consultar($sql);
		$this->s__datos =$datos;
		if ($bandera <> 0){
		$this->enviar_correos($correo[0]['email']);
		}
		//$this->s__accion = 'alta';
	}
	function contarDiasCorridosParaHabiles($fechaInicio,$diasHabilesNecesarios) {
		$fecha = $fechaInicio ? new DateTime($fechaInicio) : new DateTime();
		$contadorHabiles = 0;
		$diasTotales = 0;
		
		$sql = "SELECT generate_series FROM reloj.vw_feriados";
		$tf= toba::db('ctrl_asis')->consultar($sql);
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


	//---- Formulario levantar -------------------------------------------------------------------

	function conf__form_levantar(toba_ei_formulario $form)
	{
		if ($this->dep('datos')->esta_cargada()) { //modificacion
			$datos = $this->dep('datos')->tabla('parte')->get();

			if($datos['t_manana'] == 1) { $datos['t_manana_vista'] = 'Si'; }else{ $datos['t_manana_vista'] = 'No';  }
			if($datos['t_tarde']  == 1) { $datos['t_tarde_vista']  = 'Si'; }else{ $datos['t_tarde_vista']  = 'No';  }
			if($datos['t_noche']  == 1) { $datos['t_noche_vista']  = 'Si'; }else{ $datos['t_noche_vista']  = 'No';  }

			$dependencia = $this->dep('mapuche')->get_dependencia($datos['cod_depcia']);
			$datos['desc_depcia'] = $dependencia['desc_depcia'];

			list($y,$m,$d)=explode("-",$datos['fecha_nacimiento']); //2011-03-31
			$datos['fecha_nacimiento'] = $d."/".$m."/".$y;

			list($f,$h)=explode(" ",$datos['fecha_alta']);
			list($y,$m,$d)=explode("-",$f); //2015-04-17 08:49:03
			$datos['fecha_alta'] = $d."/".$m."/".$y." ".$h;

			$datos['usuario_alta_nombre']   = $this->dep('datos2')->tabla('apex_usuario')->get_nombre_usuario($datos['usuario_alta']);


			// si es medico, lo pongo predeterminado -------------------------------------------
			$grupos_acceso = toba::instancia()->get_grupos_acceso(toba::usuario()->get_id(), toba::proyecto()->get_id() );
			foreach ($grupos_acceso as $grupo){
				if ($grupo == 'medico') { //es medico
					$datos['usuario_medico'] = toba::usuario()->get_id();
					$form->set_solo_lectura('usuario_medico');           
					break;
				}
			}

			// si tiene certificado provisorio, avisamos ---------------------------------------
			$cert = toba::tabla('examen')->get_certificado($datos['legajo']); 
			if(substr($cert, 0, 10) == 'PROVISORIO'){
				toba::notificacion()->agregar('Atenci�n: Legajo con certificado '.$cert, 'info');
			}

			$form->set_datos($datos);
		}
	}

	function evt__form_levantar__modificacion($datos)
	{
		
		if ($this->dep('datos')->esta_cargada()) {//modificacion
		
			//certificado ----------------------------------------------------------
			$folder_path = "../www/certificados/";
			if (isset($datos['certificado']) and !empty($datos['certificado']['name'])) {
					$nombre_certificado = $_SESSION['id_parte'].substr($datos['certificado']['name'],-4,4); 
					$destino            = $folder_path.$nombre_certificado;
					move_uploaded_file($datos['certificado']['tmp_name'], $destino ) ;
					if ( is_file( $destino ) ){
							$datos['certificado'] = $nombre_certificado;
					}
			}elseif (isset($datos['certificado']) and empty($datos['certificado']['name'])) {
					$datos['certificado'] = $datos['certificado']['name'];
			}else{
					//Mantener el valor anterior
					$url= $this->dep('datos')->tabla('parte')->get_certificado($_SESSION['id_parte']);
					if(empty($url['certificado'])){
							$datos['certificado'] = $datos['certificado']['name'];
					}else{
							$datos['certificado'] = $url['certificado'];
					}
			}

			//seteamos datos fijos, de campos ocultos
			$datos['fecha_cierre']   = date("Y-m-d H:i:s");
			$datos['usuario_cierre'] = toba::usuario()->get_id();
			$datos['estado']        = 'C'; //A/Abierto - Vigente (el médico lo puede editar),C/Levantado - Cerrado (ya no se puede modificar)

			$this->dep('datos')->tabla('parte')->set($datos);
			$this->s__accion == 'levantar';
		}    
	}


	//---- Formulario Vista-------------------------------------------------------------------

	function conf__form_vista(toba_ei_formulario $form)
	{ 
		if ($this->dep('datos')->esta_cargada()) { //modificacion
			$datos = $this->dep('datos')->tabla('parte')->get();
			if (isset($datos['id_comision'])){
				$id_comision=$datos['id_comision'];
				$sql = "SELECT motivo, observaciones FROM reloj.comision
				Where id_comision = $id_comision ";
				$comision = toba::db('ctrl_asis')->consultar_fila($sql);
				$datos['comision'] = $comision['motivo'];
				$datos['observaciones'] = $comision['observaciones'];
			}
			if($datos['t_manana'] == 1) { $datos['t_manana_vista'] = 'Si'; }else{ $datos['t_manana_vista'] = 'No';  }
			if($datos['t_tarde']  == 1) { $datos['t_tarde_vista']  = 'Si'; }else{ $datos['t_tarde_vista']  = 'No';  }
			if($datos['t_noche']  == 1) { $datos['t_noche_vista']  = 'Si'; }else{ $datos['t_noche_vista']  = 'No';  }

			$dependencia = $this->dep('mapuche')->get_dependencia($datos['cod_depcia']);
			$datos['desc_depcia'] = $dependencia['desc_depcia'];

			list($y,$m,$d)=explode("-",$datos['fecha_nacimiento']); //2011-03-31
			$datos['fecha_nacimiento'] = $d."/".$m."/".$y;

			$dias_calculo = $datos['dias'] - 1;
			$dias = '+'.$dias_calculo.' day';
			$datos['fecha_fin_licencia']     = date ( 'Y-m-d' , strtotime ( $dias , strtotime ( $datos['fecha_inicio_licencia'] ) )  ); //sumamos N dias a la fecha de inicio licencia
			list($y,$m,$d)=explode("-",$datos['fecha_fin_licencia']); //2011-03-31
			$datos['fecha_fin_licencia'] = $d."/".$m."/".$y;

			list($y,$m,$d)=explode("-",$datos['fecha_inicio_licencia']); //2011-03-31
			$datos['fecha_inicio_licencia'] = $d."/".$m."/".$y;   

			list($f,$h)=explode(" ",$datos['fecha_alta']);
			list($y,$m,$d)=explode("-",$f); //2015-04-17 08:49:03
			$datos['fecha_alta'] = $d."/".$m."/".$y." ".$h;

			list($f,$h)=explode(" ",$datos['fecha_cierre']);
			list($y,$m,$d)=explode("-",$f); //2015-04-17 08:49:03
			$datos['fecha_cierre'] = $d."/".$m."/".$y." ".$h;

			$datos['usuario_alta_nombre']   = $this->dep('datos2')->tabla('apex_usuario')->get_nombre_usuario($datos['usuario_alta']);
			$datos['usuario_cierre_nombre'] = $this->dep('datos2')->tabla('apex_usuario')->get_nombre_usuario($datos['usuario_cierre']);
			$datos['usuario_medico_nombre'] = $this->dep('datos2')->tabla('apex_usuario')->get_nombre_usuario($datos['usuario_medico']);

			$datos['id_motivo_vista']       = toba::tabla('motivo')->get_descripcion($datos['id_motivo']);
			$datos['id_decreto_vista']         = toba::tabla('decreto')->get_descripcion($datos['id_decreto']);
			
			if(!empty($datos['id_articulo']) ) {
			$datos['id_articulo_vista']     = toba::tabla('articulo')->get_descripcion($datos['id_articulo']);
			}
			
			if(!empty($datos['id_diagnostico']) ) {
				$diagnostico = toba::tabla('diagnostico')->get_diagnostico($datos['id_diagnostico']);
				$datos['diagnostico']  = $diagnostico['diagnostico'];
			}

			if(!empty($datos['certificado']) ) {
				$folder_path = "certificados/";
				$datos['url_certificado']  = '<a href="'.$folder_path.$datos['certificado'].'" target="_blank">Descargar certificado</a>';
			}

			//En caso de que sea un parte de vacaciones
			if(!empty($datos['id_motivo']) && $datos['id_motivo'] == 35){
				$parte_anio = toba::tabla('parte')->get_parte_anio($datos['id_parte']);
				$datos['anio']  = $parte_anio['anio'];
			}else{
				$datos['anio']  = "-";
			}

			if($datos['estado'] == 'C'){ $datos['estado'] = "Cerrado/Levantado"; }
			
			$form->set_datos($datos);
		}
	}

	//---- FUNCIONES -------------------------------------------------------------------

	function resetear()
	{
		$this->dep('datos')->resetear();
		unset($_SESSION['id_parte']);
		$this->set_pantalla('pant_seleccion');
	}

	/*function vista_excel(toba_vista_excel $salida)
	{ PABLO WILSON
		$excel = $salida->get_excel();
		$excel->setActiveSheetIndex(0);
		$excel->getActiveSheet()->setTitle('Partes de Inasistencia');
		$this->dependencia($this->s__cuadro_xls)->vista_excel($salida);
		$salida->crear_hoja("Cuadro por articulo");
		$excel->setActiveSheetIndex(1);
		$this->dependencia('cuadro_articulo')->vista_excel($salida);
		$salida->set_nombre_archivo("ParteInasistencia.xls");
		
		
	}*/
	function vista_excel(toba_vista_excel $salida)
		{
			$excel = $salida->get_excel();
			$excel->setActiveSheetIndex(0);
			$excel->getActiveSheet()->setTitle('Partes de Inasistencia');
			
			$this->dependencia($this->s__cuadro_xls)->vista_excel($salida);
		}

	//---- EVENTOS CI -------------------------------------------------------------------

	function evt__agregar()
	{
		$this->set_pantalla('pant_alta');
	}

	function evt__volver()
	{
		$this->resetear();
	}

	function evt__eliminar()
	{
		//$this->dep('datos')->eliminar_todo();
	
		$this->dep('datos')->tabla('parte')->set_estado($_SESSION['id_parte'],'E');

		$this->resetear();
	}

	function evt__guardar()
	{

		//mostramos mensaje segun accion
		if($this->s__accion == 'alta')    { 

			//verificamos que no exista otro parte abiertos(estado) con el mismo legajo, motivo y dependencia.
			$datos = $this->dep('datos')->tabla('parte')->get();
			//$filtro['estado']     = 'A';
			$filtro['legajo']     = $datos['legajo'];
			$filtro['id_motivo']  = $datos['id_motivo'];
			$filtro['cod_depcia'] = $datos['cod_depcia'];
							
			#$datos_parte = $this->dep('datos')->tabla('parte')->get_listado($filtro);
			
			#if(count($datos_parte) == 0){
			$this->dep('datos')->sincronizar();
			toba::notificacion()->agregar('Parte de inasistencia agregado correctamente.', 'info');
			#}else{
			#    toba::notificacion()->agregar('El Parte '.$datos_parte[0]['id_parte'].' posee el mismo legajo, motivo y dependencia. Por favor verifique si conincide con el que desea agregar.', 'error');    
			#}
		}elseif($this->s__accion == 'modificacion'){ 
			$this->dep('datos')->sincronizar();
			toba::notificacion()->agregar('Parte de inasistencia modificado correctamente.', 'info');  

		}elseif($this->s__accion == 'levantar'){ 
			$this->dep('datos')->sincronizar();
			toba::notificacion()->agregar('Parte de inasistencia cerrado/levantado correctamente.', 'info');  
		}else{
			$this->dep('datos')->sincronizar();
		}

		$this->resetear();
	}

	function evt__cerrar_partes()
	{

		$this->dep('datos')->tabla('parte')->cerrar_partes_vencidos();

	}

	//-----------------------------------------------------------------------------------
	//---- filtro3 ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__filtro3(ctrl_asis_ei_formulario $filtro)
	{
		if (isset($this->s__datos_filtro)) {
			$filtro->set_datos($this->s__datos_filtro);
		}else{
			if(!empty($_SESSION['dependencia'])){
				$f['cod_depcia']  = $_SESSION['dependencia'];
			}
			if(!empty($_SESSION['agente'])){
				$f['legajo']  = $_SESSION['agente'];
			}
			$filtro->set_datos($f);
		}
	}

	function evt__filtro3__filtrar($datos)
	{
		$this->s__datos_filtro = $datos;
	}

	function evt__filtro3__cancelar()
	{
		unset($this->s__datos_filtro);
	}

	//-----------------------------------------------------------------------------------
	//---- cuadro3 ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro3(ctrl_asis_ei_cuadro $cuadro)
	{
		$this->s__cuadro_xls = 'cuadro3';
		if (isset($this->s__datos_filtro)) {
			$this->s__datos = $this->dep('datos')->tabla('parte')->get_listado($this->s__datos_filtro);
			$cuadro->set_datos($this->s__datos);
		} 
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

	

if ($datos['dias_restantes'] <= 0){
	$datos['dias_restantes'] = 0;
}
	

			$fecha_inicio_licencia = $datos['fecha_inicio_licencia'];
			$fechaentera1 =strtotime($fecha_inicio_licencia);
			$fecha = date_create(date("Y-m-d",$fechaentera1)); 
			$fecha_inicio =$fecha ->format("Y-m-d");

			$dias = $datos['dias'];

			//$fecha_inicio_licencia = $datos['fecha_inicio_licencia'];
			//$fechaentera1 =strtotime($fecha_inicio_licencia);
			//$fecha = date_create(date("Y-m-d",$fechaentera1)); 
			//$fecha_inicio =$fecha ->format("Y-m-d");
			$dias = $datos['dias']-1;
			$dias_to='+ '. $dias. ' days';
			//$hasta = date_add($fecha , date_interval_create_from_date_string($dias_to));
			//$hasta =$hasta ->format("Y-m-d"); 

	
		//$hasta=date('d/m/Y',strtotime($hasta) );
		//$hasta = date ( 'd/m/Y' , strtotime ( $dias , strtotime ( $datos['fecha_inicio_licencia'] ) )  ); //sumamos N dias a la fecha de inicio licencia
		$hasta = date ( 'd/m/Y' ,  strtotime ( $datos['fecha_inicio_licencia']  . $dias_to )  );	
		$fecha=date('d/m/Y',strtotime($datos['fecha_inicio_licencia'] ) );

//ei_arbol($hasta);
		
$mail = new phpmailer();
$mail->IsSMTP();

//Esto es para activar el modo depuraci�n. En entorno de pruebas lo mejor es 2, en producci�n siempre 0
// 0 = off (producci�n)
// 1 = client messages
// 2 = client and server messages
$mail->SMTPDebug  = 0;
//Ahora definimos gmail como servidor que aloja nuestro SMTP
$mail->Host       = 'smtp.gmail.com';
//El puerto ser� el 587 ya que usamos encriptaci�n TLS
$mail->Port       = 587;
//Definmos la seguridad como TLS
$mail->SMTPSecure = 'tls';
//Tenemos que usar gmail autenticados, as� que esto a TRUE
$mail->SMTPAuth   = true;
//Definimos la cuenta que vamos a usar. Direcci�n completa de la misma

$mail->Username   = "formularios_asistencia@fca.uncu.edu.ar";
//Introducimos nuestra contrase�a de gmail
$mail->Password   = "#1754OpD_;-?)(Fc4MtSKm*0-*#1=/Fz";
//Definimos el remitente (direcci�n y, opcionalmente, nombre)
$mail->SetFrom('formularios_asistencia@fca.uncu.edu.ar', 'Formulario Personal');
//Esta l�nea es por si quer�is enviar copia a alguien (direcci�n y, opcionalmente, nombre)

//$mail->AddReplyTo('caifca@fca.uncu.edu.ar','El de la r�plica');
//Y, ahora s�, definimos el destinatario (direcci�n y, opcionalmente, nombre)
//$mail -> AddAddress('ebermejillo@fca.uncu.edu.ar', 'Tester');
$mail->AddAddress($correo, 'El Destinatario'); //Descomentar linea cuando pase a produccion
//Definimos el tema del email


//Para enviar un correo formateado en HTML lo cargamos con la siguiente funci�n. Si no, puedes meterle directamente una cadena de texto.
//$mail->MsgHTML(file_get_contents('correomaquetado.html'), dirname(ruta_al_archivo));
//Y por si nos bloquean el contenido HTML (algunos correos lo hacen por seguridad) una versi�n alternativa en texto plano (tambi�n ser� v�lida para lectores de pantalla)
$mail->IsHTML(true); //el mail contiene html


	if ($datos['id_motivo'] == 30) {
		//$motivo = 'Razones Particulares con gose de haberes';
		$mail->Subject = 'Formulario de Solicitud Razones Particulares';
		$body = '<table>
						El/la agente  <b>'.$datos['apellido'].', '. $datos['nombre'].'</b> perteneciente a la catedra/oficina/ direcci&oacute;n <b>'.$datos['catedra'].'</b>.<br/>
						Solicita Justificaci&oacute;n de Inasistencia por Razones Particulares a partir del d&iacute;a '.$fecha.' hasta '.$hasta. '.
							Teniendo en cuenta las siguientes Observaciones: ' .$datos['observaciones']. '
											
			</table>';

	} else if ($datos['id_motivo'] == 35)
	{
		$mail->Subject = 'Formulario de Licencia Anual por Vacaciones';
		//$motivo = 'Vacaciones'.$datos['anio'];
		$body = '<table>
						El/la agente  <b>'.$datos['apellido'].', '. $datos['nombre'].'</b>, legajo:  <b>'.$datos['legajo'].'</b>.<br/>
						Solicita laLicencia Anual por Vacaciones correspondiente al a�o '.$datos['anio'].' a partir del d&iacute;a '.$fecha.' hasta '.$hasta. '. <br/>
						
											
			</table>'; 
	/*} else if ($datos['id_motivo'] == 55)
	{
		$mail->Subject = 'Formulario de Adelanto de Licencia Anual';
		$body = '<table>

				El/la agente <b>'.$datos['agente_ayn'].'</b> perteneciente a <b>'.$datos['catedra'].'</b> <br/>
				Solicita adelanto de licencia anual correspondiente al' .$datos['anio']. ' a partir del d&iacute;a'.$fecha. ' hasta '.$hasta. '<br/>
				Teniendo en cuenta las siguientes Observaciones: ' .$datos['observaciones']. 'Estos d&iacute;as de adelanto que ud ha solicitado,
				ser�n restados del total de vacaciones correspondientes al a�o en curso
			<table/>';*/


	} else if ($datos ['id_motivo'] == 57)
	{
		$mail->Subject = 'Formulario de D&iacute&as Pendientes de la Licencia Anual';
		$body = '<table>

				El/la agente <b>'.$datos['apellido'].', '. $datos['nombre'].'</b> <br/>
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
				$mail->Subject = 'Formulario de Justificacion de Inasistencia por Realizaci�n de Actividad Deportiva o Art&iacute;stica';
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
			case 62 :
				$mail->Subject = 'Formulario de Justificacion de Inasistencia por Home Office por Resolucion';			
				break;	
			case 63 :
				$mail->Subject = 'Formulario de Justificacion de Inasistencia por Vacaciones Extraordinarias';			
				break;	
			case 64 :
				$mail->Subject = 'Formulario de Justificacion de Inasistencia por A&ncute;o Sabatico';			
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

}
?>