<?php
class ci_informacion_complementaria extends toba_ci
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
			$cuadro->set_datos($this->dep('datos')->tabla('info_complementaria')->get_listado($this->s__datos_filtro));
		} else {
			$cuadro->set_datos($this->dep('datos')->tabla('info_complementaria')->get_listado());
		}
	}

	function evt__cuadro__eliminar($datos)
	{
		$this->dep('datos')->resetear();
		$this->dep('datos')->cargar($datos);
		$this->dep('datos')->eliminar_todo();
		$this->dep('datos')->resetear();
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

			$datos = $this->dep('datos')->tabla('info_complementaria')->get();

			list($f, $h) = explode(' ',$datos['entrada']);
			$datos['entrada_alta'][0] = $f;
			$datos['entrada_alta'][1] = substr($h, 0, 5);

			
			list($f, $h) = explode(' ',$datos['salida']);
			$datos['salida_alta'][0] = $f;
			$datos['salida_alta'][1] = substr($h, 0, 5);



		} else {

			$datos['entrada_alta'][0]= date("Y-m-d");
			$datos['entrada_alta'][1]= "08:00";
			$datos['salida_alta'][0] = date("Y-m-d");
			$datos['salida_alta'][1] = "16:00";

			$this->pantalla()->eliminar_evento('eliminar');
		}

		$form->set_datos($datos);
	}

	function evt__formulario__modificacion($datos)
	{
		if (!$this->dep('datos')->esta_cargada()) { //agrega
			$datos['fecha_alta']    = date("Y-m-d H:i:s");
			$datos['usuario_alta']  = toba::usuario()->get_id();
		}

		$datos['entrada'] = $datos['entrada_alta'][0].' '.$datos['entrada_alta'][1];
		$datos['salida']  = $datos['salida_alta'][0].' '.$datos['salida_alta'][1];

		//ei_arbol($datos);

		$this->dep('datos')->tabla('info_complementaria')->set($datos);
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

		toba::notificacion()->agregar('Info. Complementaria agregada correctamente.', 'info');    
			#}else{
		
	}

}

?>