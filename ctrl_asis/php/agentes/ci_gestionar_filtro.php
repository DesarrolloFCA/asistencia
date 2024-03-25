<?php
include_once 'datos/consultas_agentes.php';
include_once 'libs/datos_fijos.php';
class ci_gestionar_filtro extends ctrl_asis_ci
{
        protected $s__filtro;
        protected $s__where;
	//-----------------------------------------------------------------------------------
	//---- cuadro_agentes ---------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro_agentes(ctrl_asis_ei_cuadro $cuadro)
	{
            //return $this->cn()->get_agentes($this->s__filtro);
            return consultas_agentes::get_agentes_where($this->s__where);
	}

	function evt__cuadro_agentes__seleccion($seleccion)
	{
            toba::memoria()->set_dato('agente', $seleccion);
            $this->set_pantalla('pant_gestion');
	}

	//-----------------------------------------------------------------------------------
	//---- form_filtro ------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_filtro(ctrl_asis_ei_filtro $filtro)
	{
            if(isset($this->s__filtro)) return $this->s__filtro;
	}

	function evt__form_filtro__filtrar($datos)
	{
            $this->s__filtro = $datos;
            $this->s__datos = $datos;
            $this->s__where = $this->dep('form_filtro')->get_sql_where();
          
             
	}

	function evt__form_filtro__cancelar()
	{
            unset($this->s__filtro);
	}

}

?>