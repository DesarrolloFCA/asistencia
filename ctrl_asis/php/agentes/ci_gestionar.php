<?php
class ci_gestionar extends ctrl_asis_ci
{
    protected $s__agente;
    protected $s__periodo;
	function ini()
	{
            $this->s__agente = toba::memoria()->get_dato('agente');
            $this->s__agente['agente_id'] = consultas_agentes::recuperar_id_x_legajo($this->s__agente['legajo']);
            
	}

	//-----------------------------------------------------------------------------------
	//---- Pantalla resumen ------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_agente(ctrl_asis_ei_formulario $form)
	{
            return $this->cn()->get_agentes($this->s__agente);
	}

	//-----------------------------------------------------------------------------------
	//---- pantalla asistencia ---------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro_resumen(ctrl_asis_ei_cuadro $cuadro)
	{
            if(isset($this->s__periodo))
            {
               return $this->cn()->get_marcas($this->s__agente, $this->s__periodo);
            }
	}
	function conf__filtro_periodo(ctrl_asis_ei_formulario $form)
	{
           if(isset($this->s__periodo)) return $this->s__periodo;
           else return array('periodo'=>date('m/Y'), 'descripcion' => date('m/Y'));
	}

	function evt__filtro_periodo__filtrar($datos)
	{
            $this->s__periodo = $datos;
	}

	function evt__filtro_periodo__cancelar()
	{
            unset($this->s__periodo);
	}
	//-----------------------------------------------------------------------------------
	//---- form_estadistica -------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_estadistica(ctrl_asis_ei_cuadro $cuadro)
	{
           return $this->cn()->estadisticas($this->s__agente);
	}
        

}
?>