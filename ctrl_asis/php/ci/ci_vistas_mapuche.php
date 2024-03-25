<?php
class ci_vistas_mapuche extends toba_ci
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

	function conf__cuadro_legajo(toba_ei_cuadro $cuadro)
	{
        if (isset($this->s__datos_filtro)) {
            $datos = $this->dep('datos')->get_vista_legajo($this->s__datos_filtro);
        }else{
            $datos = $this->dep('datos')->get_vista_legajo();
        }
        $cuadro->set_datos($datos);
	}

	//-----------------------------------------------------------------------------------
	//---- cuadro familiares ------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
        if (isset($this->s__datos_filtro)) {
            $datos = $this->dep('datos')->get_vista_familiares($this->s__datos_filtro);
        }else{
            $datos = $this->dep('datos')->get_vista_familiares();
        }
        $cuadro->set_datos($datos);
	}

	//-----------------------------------------------------------------------------------
	//---- cuadro domicilio -------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro_domicilio(toba_ei_cuadro $cuadro)
	{
        if (isset($this->s__datos_filtro)) {
            $datos = $this->dep('datos')->get_vista_domicilio($this->s__datos_filtro);
        }else{
            $datos = $this->dep('datos')->get_vista_domicilio();
        }
        $cuadro->set_datos($datos);
	}

}

?>