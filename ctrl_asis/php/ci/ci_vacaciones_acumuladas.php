<?php
class ci_vacaciones_acumuladas extends toba_ci
{
	protected $s__datos_filtro;

	protected $s__datos_filtro2;

    protected $s__datos_cuadro;
    protected $s__seleccion;

	//---- Filtro -----------------------------------------------------------------------

	function conf__filtro(toba_ei_formulario $filtro)
	{
		if (isset($this->s__datos_filtro)) {
			$filtro->set_datos($this->s__datos_filtro);
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

	//---- Cuadro -----------------------------------------------------------------------

	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		if (isset($this->s__datos_filtro)) {
			$cuadro->set_datos($this->dep('datos')->tabla('vacaciones_acumuladas')->get_listado($this->s__datos_filtro));
		} else {
			$cuadro->set_datos($this->dep('datos')->tabla('vacaciones_acumuladas')->get_listado());
		}
	}

	function evt__cuadro__eliminar($datos)
	{
		$this->dep('datos')->resetear();
		$this->dep('datos')->cargar($datos);
		$this->dep('datos')->eliminar_todo();
		$this->dep('datos')->resetear();
	}

	function evt__cuadro__seleccion($datos)
	{
		$this->dep('datos')->cargar($datos);
		$this->set_pantalla('pant_edicion');
	}

	//---- Formulario -------------------------------------------------------------------

	function conf__formulario(toba_ei_formulario $form)
	{
		if ($this->dep('datos')->esta_cargada()) {
			$form->set_datos($this->dep('datos')->tabla('vacaciones_acumuladas')->get());
		} else {
			$this->pantalla()->eliminar_evento('eliminar');
		}
	}

	function evt__formulario__modificacion($datos)
	{
		$this->dep('datos')->tabla('vacaciones_acumuladas')->set($datos);
	}



	//---- Formulario -------------------------------------------------------------------

	function conf__form_cierre(toba_ei_formulario $form)
	{
		if (!empty($this->s__datos_filtro2)) {
			$datos = $this->s__datos_filtro2;	
		} else {
			$datos['periodo'] = date("Y");
		}
		$form->set_datos($datos);
	}

	function evt__form_cierre__listar($datos)
	{
		
		$this->s__datos_filtro2 = $datos;	



		//$this->dep('datos')->tabla('vacaciones_acumuladas')->set($datos);
	}



	//---- Cuadro cierre ----------------------------------------------------------------

	function conf__cuadro_cierre(toba_ei_cuadro $cuadro)
	{
		if (isset($this->s__datos_filtro2)) {

			//ei_arbol($this->s__datos_filtro2);


            $agentes =  $this->dep('mapuche')->get_agentes_control_asistencia($this->s__datos_filtro2);

			//ei_arbol($agentes);

			if(count($agentes)>0){

				foreach ($agentes as $key => $dato) {
					
		            //sisamos fecha ingreso de tabla antiguedad si corresponde 
		            $dato_antiguedad = toba::tabla('antiguedad')->get_antiguedad($dato['legajo']);
		            if(!empty($dato_antiguedad['fecha_ingreso'])){
		            	$agentes[$key]['fec_ingreso'] = $dato_antiguedad['fecha_ingreso'];
		            	$fec_ingreso = $dato_antiguedad['fecha_ingreso'];
		            }else{
		            	$fec_ingreso = $dato['fec_ingreso'];
		            }

		            $agrupamiento = $dato['agrupamiento'];

		            $dias_disponibles = 0;
		            $dias_tomados     = 0;
		            $dias_va     	  = 0;


	                if(!empty($fec_ingreso)){
	                	//$dias_disponibles = 10;

	                    //obtenemos dias por antiguedad ------------------------------
	                    $antiguedad = toba::tabla('vacaciones_antiguedad')->get_array_antiguedad($fec_ingreso,$agrupamiento);
	                    

	                    //obtenemos dias tomados---------------------------------------
	                    $dias_tomados = 0;
	                    list($anio_lic,$mes_lic,$dia_lic) = explode('-', $this->s__datos_filtro2['periodo']);
	                    $filtro['legajo']      = $dato['legajo'];
	                    $filtro['id_motivo']   = 35; //vacaciones
	                    $filtro['anio']        = $anio_lic; //date("Y"); //ano actual
	                    for ($i=0; $i < date("m") ; $i++) {  // bucle en todos los meses hasta el mes actual
	                    	$mes = $i+1;
	                    	if($mes<10){
	                    		$filtro['mes']  = "0".$mes;
	                    	}else{
	                    		$filtro['mes']  = $mes;
	                    	}
							
							$partes = toba::tabla('parte')->get_listado($filtro);
							if(count($partes)>0){
								foreach ($partes as $parte) {
									$dias_tomados = $dias_tomados + $parte['dias_mes'];
								}
							}
	                    }


	                    //seteamos dias vac. acum. que tenga de este periodo ya ingresadas, etivando la doble generacion
	                    $dias_va =  toba::tabla('vacaciones_acumuladas')->get_dias($dato['legajo'], $anio_lic);

	                    //seteamos dias sisponibles ------------------------------------
	                    $dias_disponibles = $antiguedad['dias'] - $dias_tomados - $dias_va;


	                }

	                $agentes[$key]['dias_antiguedad'] =  $antiguedad['dias'];

	                $agentes[$key]['dias']          = $dias_disponibles;
	                $agentes[$key]['dias_tomados']  = $dias_tomados;
	                $agentes[$key]['dias_va'] 		= $dias_va;

				}

				$this->s__datos_cuadro = $agentes;
				$cuadro->set_datos($agentes);
			}


   			

		}
	}

        function conf_evt__cuadro_cierre__multiple($evento, $fila)
        {
            if ( $this->s__datos_cuadro[$fila]['dias'] <=  0 ) {
                $evento->anular();
            }
        }

	function evt__cuadro_cierre__multiple($seleccion)
	{
		$this->s__seleccion = $seleccion;
	}

	function evt__cuadro_cierre__generar($datos)
	{
	
		
		//$this->dep('datos')->resetear();

/*

Seleccion
0	 [1]
legajo	32457
1	 [1]
legajo	32458
2	 [1]
legajo	32489


*/
		if(count($this->s__seleccion)>0){
			
			//ei_arbol($this->s__seleccion,'Seleccion');
			
			foreach($this->s__seleccion as $sel){
				
				if($this->dep('datos')->tabla('vacaciones_acumuladas')->insertar_cierre($this->s__datos_filtro2['periodo'],$sel)){
					toba::notificacion()->agregar("Cierre ".$this->s__datos_filtro2['periodo']." generado para el Legajo ".$sel['legajo']." Agrup. ".$sel['agrupamiento'].".", "info");
				}else{
					toba::notificacion()->agregar("Error al generar cierre ".$this->s__datos_filtro2['periodo']." para el Legajo ".$sel['legajo']." Agrup. ".$sel['agrupamiento'].".", "error");
					
				}		
			}

		}else{
			toba::notificacion()->agregar("No hay cierres seleccionados.", "warning");	
		}

	}



	//---- FUNCIONES -------------------------------------------------------------------

	function resetear()
	{
		$this->dep('datos')->resetear();
		$this->set_pantalla('pant_seleccion');
	}


	//---- EVENTOS CI -------------------------------------------------------------------

	function evt__agregar()
	{
		$this->set_pantalla('pant_edicion');
	}

	function evt__volver()
	{
		$this->resetear();
	}

	function evt__eliminar()
	{
		$this->dep('datos')->eliminar_todo();
		$this->resetear();
	}

	function evt__guardar()
	{
		$this->dep('datos')->sincronizar();
		$this->resetear();
	}

	function evt__cierre()
	{
		$this->set_pantalla('pant_cierre');
	}


}

?>