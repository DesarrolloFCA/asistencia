<?php
class ci_control extends ctrl_asis_ci
{
    protected $s__filtro;
    protected $s__seleccion;
    //-----------------------------------------------------------------------------------
	//---- cuadro_agentes ---------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro_agentes(ctrl_asis_ei_cuadro $cuadro)
	{
            return $this->cn()->get_agentes($this->s__filtro);
	}

	function evt__cuadro_agentes__seleccion($seleccion)
	{
            $this->s__seleccion = $seleccion;
            $this->set_pantalla('pant_detalle');
	}

	function conf_evt__cuadro_agentes__seleccion(toba_evento_usuario $evento, $fila)
	{
	}

	//-----------------------------------------------------------------------------------
	//---- filtro_agente ----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__filtro_agente(ctrl_asis_ei_formulario $form)
	{
            if(isset($this->s__filtro)) return $this->s__filtro;
	}

	function evt__filtro_agente__filtrar($datos)
	{
            $this->s__filtro = $datos;
	}

	function evt__filtro_agente__cancelar()
	{
            unset($this->s__filtro);
	}

	//-----------------------------------------------------------------------------------
	//---- form_agente ------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_agente(ctrl_asis_ei_formulario $form)
	{
            $salida = $this->cn()->get_detalle_agente($this->s__seleccion);
            toba::memoria()->set_dato('agente', $salida);
            return $salida;
	}

	//-----------------------------------------------------------------------------------
	//---- cuadro_asistencia ------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro_asistencia(ctrl_asis_ei_cuadro $cuadro)
	{
            return $this->cn()->get_marcas_agente();
	}

	//-----------------------------------------------------------------------------------
	//---- form_opciones ----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__form_opciones__vacaciones($datos)
	{
            $this->set_pantalla('pant_vacaciones');
	}

	function evt__form_opciones__justificar_inasistencia($datos)
	{
            $this->set_pantalla('pant_asistencia');
	}

}
?>