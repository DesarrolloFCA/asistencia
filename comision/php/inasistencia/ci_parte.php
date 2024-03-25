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
			$this->s__datos = $this->dep('datos')->tabla('parte')->get_listado($this->s__datos_filtro);
			$cuadro->set_datos($this->s__datos);
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
		//ei_arbol($datos);
		$this->dep('datos')->tabla('parte')->set($datos);
		//validar que venga un anio para partes de vacaciones
		if(isset($datos['anio']) && $datos['id_motivo'] == '35') {
			$this->dep('datos')->tabla('parte_anio')->set($datos);
		}
		$this->s__accion = 'alta';
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

}
?>