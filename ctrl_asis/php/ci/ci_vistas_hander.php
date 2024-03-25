<?php
class ci_vistas_hander extends toba_ci
{

	protected $s__datos_filtro;
	
	//-----------------------------------------------------------------------------------
	//---- Filtro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

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


	//-----------------------------------------------------------------------------------
	//---- cuadro legajo ----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro_fichada(toba_ei_cuadro $cuadro)
	{
        if (isset($this->s__datos_filtro)) {
            $datos = $this->dep('hander')->get_fichada($this->s__datos_filtro);
        	$cuadro->set_datos($datos);
        }  
	}

	//-----------------------------------------------------------------------------------
	//---- cuadro marcas ----------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	/*
	function conf__cuadro_marcas(toba_ei_cuadro $cuadro)
	{
        if (isset($this->s__datos_filtro)) {
            $datos = $this->dep('access')->get_marcas($this->s__datos_filtro);
        }else{
            $datos = $this->dep('access')->get_marcas();
        }
        $cuadro->set_datos($datos);
	}
	*/
}

?>