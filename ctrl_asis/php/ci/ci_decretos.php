<?php
class ci_decretos extends toba_ci
{
	protected $s__datos_filtro;
	protected $s__id_decreto;

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
			$cuadro->set_datos($this->dep('datos')->tabla('decreto')->get_listado($this->s__datos_filtro));
		} else {
			$cuadro->set_datos($this->dep('datos')->tabla('decreto')->get_listado());
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
		$this->s__id_decreto = $datos['id_decreto'];

		$this->dep('datos')->cargar($datos);
		$this->set_pantalla('pant_edicion');
	}

	//---- Formulario -------------------------------------------------------------------

	function conf__formulario(toba_ei_formulario $form)
	{
		if ($this->dep('datos')->esta_cargada()) {

			$datos = $this->dep('datos')->tabla('decreto')->get();
			
			//---------------------------------------------------
			//agrupamientos
			$agrupamientos = $this->dep('datos')->tabla('agrupacion_decreto')->get_filas(null, true);

			$array_seleccionados = array();
			if(count($agrupamientos)>0){
					foreach ($agrupamientos as $a){
							$array_seleccionados[] = $a['codagrup'];
					}
			}
			$datos['agrupamientos'] = $array_seleccionados;
			//----------------------------------------------------

		} else {

			$this->pantalla()->eliminar_evento('eliminar');
		}

		$form->set_datos($datos);
	}

	function evt__formulario__modificacion($datos)
	{

		$this->dep('datos')->tabla('decreto')->set($datos);

		if ($this->dep('datos')->esta_cargada()) { //modificar

				$this->dep('datos')->tabla('agrupacion_decreto')->eliminar_fila_decreto($this->s__id_decreto);
				$this->dep('datos')->tabla('agrupacion_decreto')->insertar_fila_decreto($this->s__id_decreto,$datos['agrupamientos']);

				$this->dep('datos')->sincronizar();

		}else{ //agregar

				$this->s__id_decreto =  $this->dep('datos')->tabla('decreto')->agregar_decreto($datos);

				$this->dep('datos')->tabla('agrupacion_decreto')->eliminar_fila_decreto($this->s__id_decreto);
				$this->dep('datos')->tabla('agrupacion_decreto')->insertar_fila_decreto($this->s__id_decreto,$datos['agrupamientos']);

		}

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
		#$this->dep('datos')->sincronizar();
		$this->resetear();
	}

}

?>