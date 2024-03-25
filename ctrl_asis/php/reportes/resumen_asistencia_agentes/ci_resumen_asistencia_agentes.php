<?php
class ci_resumen_asistencia_agentes extends ctrl_asis_ci
{
    protected $s__filtro;
    protected $s__where;
  
	//-----------------------------------------------------------------------------------
	//---- agentes ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__agentes(ctrl_asis_ei_cuadro $cuadro)
	{
            if(isset($this->s__filtro))
                return $this->cn()->get_agentes($this->s__filtro);
	}

	

	//-----------------------------------------------------------------------------------
	//---- filtro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__filtro(ctrl_asis_ei_formulario $form)
	{
            if(isset($this->s__filtro)) return $this->s__filtro;
	}

	function evt__filtro__filtrar($datos)
	{
            $this->s__filtro = $datos;
	}

	function evt__filtro__cancelar()
	{
            unset($this->s__filtro);
	}

}
?>