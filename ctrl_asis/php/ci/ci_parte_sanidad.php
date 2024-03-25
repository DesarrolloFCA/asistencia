<?php
class ci_parte_sanidad extends toba_ci
{
	protected $s__datos_filtro;
	protected $s__datos;
	protected $s__accion;

	//-----------------------------------------------------------------------------------
	//---- Configuraciones --------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	//---- Filtro -----------------------------------------------------------------------

	function conf__filtro(toba_ei_formulario $filtro)
	{
		if (isset($this->s__datos_filtro)) {
			$filtro->set_datos($this->s__datos_filtro);
		}else{
			$filtro->colapsar();
		}

	}

	function evt__filtro__filtrar($datos)
	{
		$datos['cod_depcia']=04;
		ei_arbol($datos);
		$this->s__datos_filtro = $datos;
	}

	function evt__filtro__cancelar()
	{
		unset($this->s__datos_filtro);
	}

	//---- Cuadro -----------------------------------------------------------------------

	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		//Paginado a cargo del ci --------------------------------------------
		$tamanio_pagina = $cuadro->get_tamanio_pagina();
		$limit  = "LIMIT $tamanio_pagina";
		$offset = "OFFSET ". ($cuadro->get_pagina_actual() - 1) * $tamanio_pagina;

		if (isset($this->s__datos_filtro)) {
			$total_registros =  $this->dep('sanidad')->get_total_registros($this->s__datos_filtro);

			$this->s__datos = $this->dep('sanidad')->get_listado($this->s__datos_filtro, $limit, $offset);
		} else {
			$total_registros =  $this->dep('sanidad')->get_total_registros();
			$this->s__datos = $this->dep('sanidad')->get_listado(array(),$limit, $offset);
		}

		$cuadro->set_total_registros($total_registros); 
		//ei_arbol($this->s__datos);   
		$cuadro->set_datos($this->s__datos);
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


		function evt__cuadro__ver($datos)
		{
			# bandera 
			// $this->dep('datos')->cargar($datos);
			$this->s__id_parte = $datos['id_parte'];
			$_SESSION['id_parte'] = $datos['id_parte'];            
			$this->set_pantalla('pant_vista');
		}


	
	//---- Formulario Vista-------------------------------------------------------------------

	function conf__form_vista(toba_ei_formulario $form)
	{ 
		if (!empty($this->s__id_parte)) { //modificacion
			
			$datos = $this->dep('sanidad')->get_parte($this->s__id_parte);
			
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

			//$datos['usuario_alta_nombre']   = $this->dep('sanidad')->get_nombre_usuario($datos['usuario_alta']);
			//$datos['usuario_cierre_nombre'] = $this->dep('sanidad')->get_nombre_usuario($datos['usuario_cierre']);
			//$datos['usuario_medico_nombre'] = $this->dep('sanidad')->get_nombre_usuario($datos['usuario_medico']);

			$datos['id_motivo_vista']       = toba::tabla('motivo')->get_descripcion($datos['id_motivo']);
			$datos['id_decreto_vista']      = toba::tabla('decreto')->get_descripcion($datos['id_decreto']);
			
			if(!empty($datos['id_articulo']) ) {
			$datos['id_articulo_vista']     = toba::tabla('articulo')->get_descripcion($datos['id_articulo']);
			}
			
			if(!empty($datos['id_diagnostico']) ) {
				$diagnostico =  $this->dep('sanidad')->get_diagnostico($datos['id_diagnostico']);
				$datos['diagnostico']  = $diagnostico['diagnostico'];
			}

			if(!empty($datos['certificado']) ) {
				$folder_path = "certificados/";
				$datos['url_certificado']  = '<a href="'.$folder_path.$datos['certificado'].'" target="_blank">Descargar certificado</a>';
			}

			if($datos['estado'] == 'C'){ $datos['estado'] = "Cerrado/Levantado"; }

			$form->set_datos($datos);
		}
	}

	//---- FUNCIONES -------------------------------------------------------------------

	function resetear()
	{
		unset($_SESSION['id_parte']);
		$this->set_pantalla('pant_seleccion');
	}

	function vista_excel(toba_vista_excel $salida)
	{
		$excel = $salida->get_excel();
		$excel->setActiveSheetIndex(0);
		$excel->getActiveSheet()->setTitle('Partes de Inasistencia');
		$this->dependencia('cuadro_print')->vista_excel($salida);
	}

	//---- EVENTOS CI -------------------------------------------------------------------


	function evt__volver()
	{
		$this->resetear();
	}

}
?>