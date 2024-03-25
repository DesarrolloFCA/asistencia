<?php
include_once 'datos/consultas_agentes.php';
class ci_licencia_anual extends toba_ci
{
	protected $s__datos_filtro;
        protected $s__agente;

	function ini()
        {
            $this->s__agente = toba::memoria()->get_dato('agente');
            $this->s__agente['agente_id'] = consultas_agentes::recuperar_id_x_legajo($this->s__agente['legajo']);
        }

	function conf__filtro(toba_ei_formulario $filtro)
	{
            if(isset($this->s__agente))
            {
                $this->s__datos_filtro['agente_id'] = $this->s__agente['agente_id'];
            }
            
            $filtro->set_datos($this->s__datos_filtro);
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
            ei_arbol($this->s__datos_filtro);
		if (isset($this->s__datos_filtro)) {
			$cuadro->set_datos($this->dep('datos')->tabla('vacaciones_agentes')->get_listado($this->s__datos_filtro));
		} else {
			$cuadro->set_datos($this->dep('datos')->tabla('vacaciones_agentes')->get_listado());
		}
	}

	function evt__cuadro__seleccion($datos)
	{
		$this->dep('datos')->cargar($datos);
	}

	//---- Formulario -------------------------------------------------------------------

	function conf__formulario(toba_ei_formulario $form)
	{
		if ($this->dep('datos')->esta_cargada()) {
			$form->set_datos($this->dep('datos')->tabla('vacaciones_agentes')->get());
		}
	}

	function evt__formulario__alta($datos)
	{
            $datos['agente_id'] =  $this->s__agente['agente_id'];
		$this->dep('datos')->tabla('vacaciones_agentes')->set($datos);
		$this->dep('datos')->sincronizar();
		$this->resetear();
	}

	function evt__formulario__modificacion($datos)
	{
            $datos['agente_id'] =  $this->s__agente['agente_id'];
		$this->dep('datos')->tabla('vacaciones_agentes')->set($datos);
		$this->dep('datos')->sincronizar();
		$this->resetear();
	}

	function evt__formulario__baja()
	{
		$this->dep('datos')->eliminar_todo();
		$this->resetear();
	}

	function evt__formulario__cancelar()
	{
		$this->resetear();
	}

	function resetear()
	{
		$this->dep('datos')->resetear();
	}

	//-----------------------------------------------------------------------------------
	//---- filtro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__filtro__distribuir($datos)
	{
            $this->set_pantalla('pant_distribucion');
	}

	//-----------------------------------------------------------------------------------
	//---- fa ---------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__fa(ctrl_asis_ei_formulario $form)
	{
            return $this->s__agente;
	}

	//-----------------------------------------------------------------------------------
	//---- ml_d -------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__ml_d(ctrl_asis_ei_formulario_ml $form_ml)
	{
	}

	function evt__ml_d__procesar_ml($datos)
	{
            $this->cn()->procesar_licencia_anual($this->s__agente,$datos); 
	}

}
?>