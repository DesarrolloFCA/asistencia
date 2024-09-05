<?php
class ci_vacaciones_restantes_por_anio extends ctrl_asis_ci
{
	protected $s__datos_filtro;
	protected $s__datos;
	//---- Cuadro -----------------------------------------------------------------------

	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		if (isset($this->s__datos_filtro)) {
			$listado = $this->dep('datos')->tabla('vacaciones_restantes')->get_listado($this->s__datos_filtro);
		} else {
			$listado =$this->dep('datos')->tabla('vacaciones_restantes')->get_listado();
		}
		//ei_arbol($listado);
		$cantidad = count($listado);
		for ($i=0;$i< $cantidad; $i++){
			$legajo=$listado[$i]['legajo'];
			//ei_arbol($legajo);
			$agente = $this->dep('datos2')->get_legajos_autoridad($legajo);
			if(isset($agente)){
			$listado[$i]['nombres']=$agente[0]['nombre'];
			$listado[$i]['apellido']=$agente[0]['apellido'];
			}
		}
		$cuadro->set_datos($listado);
		/*if($this->s__datos_filtro){
			$cuadro->set_datos($this->dep('datos')->tabla('vacaciones_restantes')->get_listado($this->s__datos_filtro));
		}*/
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
			$form->set_datos($this->dep('datos')->tabla('vacaciones_restantes')->get());
		} else {
			$this->pantalla()->eliminar_evento('eliminar');
		}
	}

	function evt__formulario__modificacion($datos)
	{
		
		$this->dep('datos')->tabla('vacaciones_restantes')->set($datos);
	}

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

	//-----------------------------------------------------------------------------------
	//---- filtro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__filtro(ctrl_asis_ei_formulario $filtro)
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

}
?>