<?php
class vacaciones_procesadas extends ctrl_asis_ci
{
	protected $s__datos_filtro;
	protected $s__datos;
	//-----------------------------------------------------------------------------------
	//---- cuadro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro(ctrl_asis_ei_cuadro $cuadro)
	{
		if (isset($this->s__datos_filtro)){
		 	if( isset($this->s__datos_filtro['anio']['valor'])){
		 		$anio= $this->s__datos_filtro['anio']['valor'];
		 	} else{
		 	$anio = null;
		 	}
		 $completado = toba::tabla('inasistencias')->get_llenado($anio);
		 
		 $cargos_todos = true;
		 $cant_compl= count ($completado);
		 	for ($i=0; $i<$cant_compl;$i++){
		 		if(isset($completado[$i]['legajo'])){
		 		$filtro['legajo'] = $completado[$i]['legajo'];
		 		$filtro['cargos_todos'] = $cargos_todos;
            	$datos_agente = $this->dep('mapuche')->get_datos_agente($filtro);
            	$completado[$i]['ayn'] = $datos_agente[0]['apellido'].', ' .$datos_agente[0]['nombre'];
            	}
            
          	}  
        $cuadro->set_datos($completado);
    	}
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