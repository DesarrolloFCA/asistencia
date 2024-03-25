<?php
class ci_antiguedades extends toba_ci
{
	protected $s__datos_filtro;


	//---- Filtro -----------------------------------------------------------------------

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

	//---- Cuadro -----------------------------------------------------------------------

	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		if (isset($this->s__datos_filtro)) {
			$listado=$this->dep('datos')->tabla('antiguedad')->get_listado($this->s__datos_filtro);
		} else {
			$listado=$this->dep('datos')->tabla('antiguedad')->get_listado();
		}
		$cantidad = count($listado);
		for ($i=0;$i< $cantidad; $i++){
			$legajo=$listado[$i]['legajo'];
			//ei_arbol($legajo);
			$agente = $this->dep('datos2')->get_legajos_autoridad($legajo);
			if(isset($agente)){
			$listado[$i]['ayp']=$agente[0]['apellido'].', '.$agente[0]['nombre'];
			}
		}
		$cuadro->set_datos($listado);

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
			$form->set_datos($this->dep('datos')->tabla('antiguedad')->get());
		} else {
			$this->pantalla()->eliminar_evento('eliminar');
		}
	}

	function evt__formulario__modificacion($datos)
	{
		//$datos['tm'] = date('d-m-Y H:i:s');
		//ei_arbol($datos);
		$this->dep('datos')->tabla('antiguedad')->set($datos);
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

}

?>