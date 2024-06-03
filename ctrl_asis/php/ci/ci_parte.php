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
		if ($item->get_id() == '3481') {
			$this->set_pantalla('pant_alta');
		}

		if (isset($_GET['levantar']) and $_GET['levantar'] == 'true') {
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
		} else {
			if (!empty($_SESSION['dependencia'])) {
				$f['cod_depcia']  = $_SESSION['dependencia'];
			}
			if (!empty($_SESSION['agente'])) {
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
		} else {
			if (!empty($_SESSION['dependencia'])) {
				$f['cod_depcia']  = $_SESSION['dependencia'];
			}
			if (!empty($_SESSION['agente'])) {
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
			for ($i = 0; $i < $max; $i++) {
				$dias_calculo = $datos[$i]['dias'] - 1;
				$dias = '+' . $dias_calculo . ' day';
				$datos[$i]['fecha_fin_licencia']     = date('Y-m-d', strtotime($dias, strtotime($datos[$i]['fecha_inicio_licencia']))); //sumamos N dias a la fecha de inicio licencia
				//list($y,$m,$d)=explode("-",$datos['fecha_fin_licencia']); //2011-03-31
				//$datos[$i]['fecha_fin_licencia'] = $d."/".$m."/".$y;
				$datos[$i]['nombre_completo'] = $datos[$i]['apellido'] . ", " . $datos[$i]['nombre'];
			}
			//ei_arbol($this->s__datos_filtro);
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
		$this->dep('datos')->tabla('parte')->set_estado($datos['id_parte'], 'E');

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
			if (!empty($datos['id_motivo']) && $datos['id_motivo'] == 35) {
				$parte_anio = toba::tabla('parte')->get_parte_anio($datos['id_parte']);
				$datos['anio']  = $parte_anio['anio'];
			} else {
				$datos['anio']  = "-";
			}

			$form->set_datos($datos);

			$fecha_inicio_licencia = substr($datos['fecha_inicio_licencia'], 0, 7); //yyyy-mm
			if ($fecha_inicio_licencia <> date("Y-m") and $_SESSION['admin'] <> true) {
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
		$id_motivo = $datos['id_motivos'];
		$agrupamiento = $datos['agrupamiento'];
		$anio = $datos['anio'];
		$dias = $datos['dias'];
		//ei_arbol($datos);
		$this->dep('datos')->tabla('parte')->set($datos);
		//validar que venga un anio para partes de vacaciones
		if (isset($datos['anio']) && $datos['id_motivo'] == '35') {
			$dato_antiguedad = toba::tabla('antiguedad')->get_antiguedad($datos['legajo']);
			//ei_arbol($dato_antiguedad);
			if (!empty($dato_antiguedad['fecha_ingreso'])) {
				$agente['fec_ingreso'] = $dato_antiguedad['fecha_ingreso'];
			} else {

				$sql = "SELECT fec_ingreso FROM reloj.agentes WHERE legajo = '$legajo' and agrupamiento = '$agrupamiento' ";
				$agente =  toba::db('ctrol_asis')->consultar_fila($sql);
			}
			if (!empty($agente['fec_ingreso'])) {

				//obtenemos dias por antiguedad ------------------------------
				$antiguedad = toba::tabla('vacaciones_antiguedad')->get_array_antiguedad($agente['fec_ingreso'], $agrupamiento, $anio);
				$dias_tomados = 0;
				list($anio_lic, $mes_lic, $dia_lic) = explode('-', $fecha_inicio_licencia);
				$filtro['legajo']      = $legajo;
				$filtro['id_motivo']   = $datos['id_motivo'];
				$filtro['agrupamiento'] = $agrupamiento;
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
				$lim = count($partes);

				if ($lim > 0) {

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
				} else {
					$dias_tomados = 0;
				}

				$vacaciones_restantes = toba::tabla('vacaciones_restantes')->get_dias($legajo, $anio, $agrupamiento);


				if (is_null($vacaciones_restantes)) {

					$dias_disponibles = $dato_antiguedad['dias'] - $dias_tomados; //$antiguedad['dias'] - $dias_tomados;

				} else {
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
		if ($datos['id_motivo'] == '55') {

			$anio == date('Y');
			$sql = "INSERT INTO reloj.vacaciones_adelantadas ( legajo, anio, dias_adelanto) 
						values ($legajo, $anio,$dias)";
			toba::db('ctrl_asis')->ejecutar($sql);
		}
		$sql = "SELECT email from reloj.agentes_mail
					where legajo=$legajo";
		$correo = toba::db('ctrl_asis')->consultar($sql);
		$this->s__datos = $datos;
		$this->enviar_correos($correo[0]['email']);
		$this->s__accion = 'alta';
	}


	//---- Formulario levantar -------------------------------------------------------------------

	function conf__form_levantar(toba_ei_formulario $form)
	{
		if ($this->dep('datos')->esta_cargada()) { //modificacion
			$datos = $this->dep('datos')->tabla('parte')->get();

			if ($datos['t_manana'] == 1) {
				$datos['t_manana_vista'] = 'Si';
			} else {
				$datos['t_manana_vista'] = 'No';
			}
			if ($datos['t_tarde']  == 1) {
				$datos['t_tarde_vista']  = 'Si';
			} else {
				$datos['t_tarde_vista']  = 'No';
			}
			if ($datos['t_noche']  == 1) {
				$datos['t_noche_vista']  = 'Si';
			} else {
				$datos['t_noche_vista']  = 'No';
			}

			$dependencia = $this->dep('mapuche')->get_dependencia($datos['cod_depcia']);
			$datos['desc_depcia'] = $dependencia['desc_depcia'];

			list($y, $m, $d) = explode("-", $datos['fecha_nacimiento']); //2011-03-31
			$datos['fecha_nacimiento'] = $d . "/" . $m . "/" . $y;

			list($f, $h) = explode(" ", $datos['fecha_alta']);
			list($y, $m, $d) = explode("-", $f); //2015-04-17 08:49:03
			$datos['fecha_alta'] = $d . "/" . $m . "/" . $y . " " . $h;

			$datos['usuario_alta_nombre']   = $this->dep('datos2')->tabla('apex_usuario')->get_nombre_usuario($datos['usuario_alta']);


			// si es medico, lo pongo predeterminado -------------------------------------------
			$grupos_acceso = toba::instancia()->get_grupos_acceso(toba::usuario()->get_id(), toba::proyecto()->get_id());
			foreach ($grupos_acceso as $grupo) {
				if ($grupo == 'medico') { //es medico
					$datos['usuario_medico'] = toba::usuario()->get_id();
					$form->set_solo_lectura('usuario_medico');
					break;
				}
			}

			// si tiene certificado provisorio, avisamos ---------------------------------------
			$cert = toba::tabla('examen')->get_certificado($datos['legajo']);
			if (substr($cert, 0, 10) == 'PROVISORIO') {
				toba::notificacion()->agregar('Atenci�n: Legajo con certificado ' . $cert, 'info');
			}

			$form->set_datos($datos);
		}
	}

	function evt__form_levantar__modificacion($datos)
	{

		if ($this->dep('datos')->esta_cargada()) { //modificacion

			//certificado ----------------------------------------------------------
			$folder_path = "../www/certificados/";
			if (isset($datos['certificado']) and !empty($datos['certificado']['name'])) {
				$nombre_certificado = $_SESSION['id_parte'] . substr($datos['certificado']['name'], -4, 4);
				$destino            = $folder_path . $nombre_certificado;
				move_uploaded_file($datos['certificado']['tmp_name'], $destino);
				if (is_file($destino)) {
					$datos['certificado'] = $nombre_certificado;
				}
			} elseif (isset($datos['certificado']) and empty($datos['certificado']['name'])) {
				$datos['certificado'] = $datos['certificado']['name'];
			} else {
				//Mantener el valor anterior
				$url = $this->dep('datos')->tabla('parte')->get_certificado($_SESSION['id_parte']);
				if (empty($url['certificado'])) {
					$datos['certificado'] = $datos['certificado']['name'];
				} else {
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

			if ($datos['t_manana'] == 1) {
				$datos['t_manana_vista'] = 'Si';
			} else {
				$datos['t_manana_vista'] = 'No';
			}
			if ($datos['t_tarde']  == 1) {
				$datos['t_tarde_vista']  = 'Si';
			} else {
				$datos['t_tarde_vista']  = 'No';
			}
			if ($datos['t_noche']  == 1) {
				$datos['t_noche_vista']  = 'Si';
			} else {
				$datos['t_noche_vista']  = 'No';
			}

			$dependencia = $this->dep('mapuche')->get_dependencia($datos['cod_depcia']);
			$datos['desc_depcia'] = $dependencia['desc_depcia'];

			list($y, $m, $d) = explode("-", $datos['fecha_nacimiento']); //2011-03-31
			$datos['fecha_nacimiento'] = $d . "/" . $m . "/" . $y;

			$dias_calculo = $datos['dias'] - 1;
			$dias = '+' . $dias_calculo . ' day';
			$datos['fecha_fin_licencia']     = date('Y-m-d', strtotime($dias, strtotime($datos['fecha_inicio_licencia']))); //sumamos N dias a la fecha de inicio licencia
			list($y, $m, $d) = explode("-", $datos['fecha_fin_licencia']); //2011-03-31
			$datos['fecha_fin_licencia'] = $d . "/" . $m . "/" . $y;

			list($y, $m, $d) = explode("-", $datos['fecha_inicio_licencia']); //2011-03-31
			$datos['fecha_inicio_licencia'] = $d . "/" . $m . "/" . $y;

			list($f, $h) = explode(" ", $datos['fecha_alta']);
			list($y, $m, $d) = explode("-", $f); //2015-04-17 08:49:03
			$datos['fecha_alta'] = $d . "/" . $m . "/" . $y . " " . $h;

			list($f, $h) = explode(" ", $datos['fecha_cierre']);
			list($y, $m, $d) = explode("-", $f); //2015-04-17 08:49:03
			$datos['fecha_cierre'] = $d . "/" . $m . "/" . $y . " " . $h;

			$datos['usuario_alta_nombre']   = $this->dep('datos2')->tabla('apex_usuario')->get_nombre_usuario($datos['usuario_alta']);
			$datos['usuario_cierre_nombre'] = $this->dep('datos2')->tabla('apex_usuario')->get_nombre_usuario($datos['usuario_cierre']);
			$datos['usuario_medico_nombre'] = $this->dep('datos2')->tabla('apex_usuario')->get_nombre_usuario($datos['usuario_medico']);

			$datos['id_motivo_vista']       = toba::tabla('motivo')->get_descripcion($datos['id_motivo']);
			$datos['id_decreto_vista']         = toba::tabla('decreto')->get_descripcion($datos['id_decreto']);

			if (!empty($datos['id_articulo'])) {
				$datos['id_articulo_vista']     = toba::tabla('articulo')->get_descripcion($datos['id_articulo']);
			}

			if (!empty($datos['id_diagnostico'])) {
				$diagnostico = toba::tabla('diagnostico')->get_diagnostico($datos['id_diagnostico']);
				$datos['diagnostico']  = $diagnostico['diagnostico'];
			}

			if (!empty($datos['certificado'])) {
				$folder_path = "certificados/";
				$datos['url_certificado']  = '<a href="' . $folder_path . $datos['certificado'] . '" target="_blank">Descargar certificado</a>';
			}

			//En caso de que sea un parte de vacaciones
			if (!empty($datos['id_motivo']) && $datos['id_motivo'] == 35) {
				$parte_anio = toba::tabla('parte')->get_parte_anio($datos['id_parte']);
				$datos['anio']  = $parte_anio['anio'];
			} else {
				$datos['anio']  = "-";
			}

			if ($datos['estado'] == 'C') {
				$datos['estado'] = "Cerrado/Levantado";
			}

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

		$this->dep('datos')->tabla('parte')->set_estado($_SESSION['id_parte'], 'E');

		$this->resetear();
	}

	function evt__guardar()
	{

		//mostramos mensaje segun accion
		if ($this->s__accion == 'alta') {

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
		} elseif ($this->s__accion == 'modificacion') {
			$this->dep('datos')->sincronizar();
			toba::notificacion()->agregar('Parte de inasistencia modificado correctamente.', 'info');
		} elseif ($this->s__accion == 'levantar') {
			$this->dep('datos')->sincronizar();
			toba::notificacion()->agregar('Parte de inasistencia cerrado/levantado correctamente.', 'info');
		} else {
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
		} else {
			if (!empty($_SESSION['dependencia'])) {
				$f['cod_depcia']  = $_SESSION['dependencia'];
			}
			if (!empty($_SESSION['agente'])) {
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
		require_once('mail/tobamail.php');

		$datos = $this->s__datos;

		//ei_arbol($datos);
		if ($datos['id_motivo'] == 30) {
			$datos['dias'] = $datos['dias'] - 1;
		}
		$fecha = date('d/m/Y', strtotime($datos['fecha_inicio_licencia']));

		if ($datos['dias_restantes'] <= 0) {
			$datos['dias_restantes'] = 0;
		}

		$fecha_inicio_licencia = $datos['fecha_inicio_licencia'];
		$fechaentera1 = strtotime($fecha_inicio_licencia);
		$fecha = date_create(date("Y-m-d", $fechaentera1));
		$fecha_inicio = $fecha->format("Y-m-d");

		$dias = $datos['dias'];

		$dias = $datos['dias'] - 1;
		$dias_to = '+ ' . $dias . ' days';

		//$hasta=date('d/m/Y',strtotime($hasta) );
		//$hasta = date ( 'd/m/Y' , strtotime ( $dias , strtotime ( $datos['fecha_inicio_licencia'] ) )  ); //sumamos N dias a la fecha de inicio licencia
		$hasta = date('d/m/Y',  strtotime($datos['fecha_inicio_licencia']  . $dias_to));
		$fecha = date('d/m/Y', strtotime($datos['fecha_inicio_licencia']));

	
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
						El/la agente  <b>' . $datos['apellido'] . ', ' . $datos['nombre'] . '</b>, legajo:  <b>' . $datos['legajo'] . '</b>.<br/>
						Solicita laLicencia Anual por Vacaciones correspondiente al a�o ' . $datos['anio'] . ' a partir del d&iacute;a ' . $fecha . ' hasta ' . $hasta . '. <br/>
						
											
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
					$asunto = 'Formulario de Justificacion de Inasistencia por Realizaci�n de Actividad Deportiva o Art&iacute;stica';
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
		$mail = new TobaMail($correo, $asunto, $cuerpo, $desde,'');

		// Agregar un archivo adjunto
		//$mail->agregarAdjunto('nombre_archivo.pdf', '/ruta/al/archivo/nombre_archivo.pdf');

		try {
			$mail->ejecutar();
			echo "Correo enviado exitosamente.<br>";
		} catch (Exception $e) {
			echo "Error al enviar el correo: " . $e->getMessage();
		}
	}
}
