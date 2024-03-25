<?php
class vac_pen_agente extends ctrl_asis_ci
{
	protected $s__datos_filtro;
	protected $s__datos;
	//-----------------------------------------------------------------------------------
	//---- cuadro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro(ctrl_asis_ei_cuadro $cuadro)
	{
		 
		 $anio = 0;
		 if (isset($this->s__datos_filtro)) {
		 	$legajo = $this->s__datos_filtro['legajo']['valor'];
		 	//ei_arbol($this->s__datos_filtro);
		 	$cod_depcia = 04;
		 	if (isset($this->s__datos_filtro['legajo']['anio'])){
		 		$anio=$this->s__datos_filtro['legajo']['anio'];	
		 	}
		 	else{
		 		$anio=date('Y') -1 ;
		 	}
		 	$cargos_todos = true;
		 	$datos_legajo = $this->dep('mapuche')->get_dependencias_legajo_por_estado_cargo($legajo, $cargos_todos, $cod_depcia);
		 	$filtro['legajo'] = $legajo;
		 	$filtro['cargos_todos'] = $cargos_todos;
            $datos_agente = $this->dep('mapuche')->get_datos_agente($filtro);
            $apellido = $datos_agente[0]['apellido'];
            $nombre = $datos_agente[0]['nombre'];
            $vacaciones_restantes = toba::tabla('vacaciones_restantes')->get_dias($legajo, $anio);
           // ei_arbol($apellido,$nombre);
            if($vacaciones_restantes > 0){
            	$respuesta[0]['legajo'] = $legajo;
            	$respuesta[0]['ayn'] = $apellido .', '.$nombre;
            	$respuesta[0]['anio']=$anio;
            	$respuesta[0]['dias'] = $vacaciones_restantes;

            	//array_push($resumen, $respuesta);
            }
            
            $this->s__datos = $respuesta;
        // ei_arbol($resumen);
        
        } else {
        	$cargos_todos = true;
        	$vacaciones_restantes = toba::tabla('vacaciones_restantes')->get_listado();
        	$cant_pendientes = count($vacaciones_restantes);
        	
        	for ($i=0; $i<$cant_pendientes; $i++) {
        		$legajo=$vacaciones_restantes[$i]['legajo'];
        		//ei_arbol($vacaciones_restantes[$i]['legajo']);
        		if($legajo>0){
		 			$filtro['legajo'] = $vacaciones_restantes[$i]['legajo'];
		 			$filtro['cargos_todos'] = $cargos_todos;
            		$datos_agente = $this->dep('mapuche')->get_datos_agente($filtro);
            		$apellido = $datos_agente[0]['apellido'];
           			$nombre = $datos_agente[0]['nombre'];
            		$respuesta[$i]['ayn'] = $apellido .', '.$nombre;	
            		$respuesta[$i]['dias'] = $vacaciones_restantes[$i]['dias'];
            		$respuesta[$i]['legajo'] = $vacaciones_restantes[$i]['legajo'];
            		$respuesta[$i]['anio'] = $vacaciones_restantes[$i]['anio'];


        		}

        	
        		

       		}
        	$this->s__datos = $respuesta;
        //	ei_arbol($this->s__datos);
    	}

         $cuadro->set_datos($this->s__datos);
	}

	//-----------------------------------------------------------------------------------
	//---- filtro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__filtro__filtrar($datos)
    {
        $this->s__datos_filtro = $datos;
    }

    function evt__filtro__cancelar()
    {
        unset($this->s__datos_filtro);
    }

    function conf__filtro(ctrl_asis_ei_filtro $filtro)
    {
        if (isset($this->s__datos_filtro)) {
            $filtro->set_datos($this->s__datos_filtro);
        }

        

    }

}
?>