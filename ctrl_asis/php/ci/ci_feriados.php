<?php
class ci_feriados extends toba_ci
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
			$cuadro->set_datos($this->dep('datos')->tabla('conf_feriados')->get_listado($this->s__datos_filtro));
		} else {
			$cuadro->set_datos($this->dep('datos')->tabla('conf_feriados')->get_listado());
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
			$form->set_datos($this->dep('datos')->tabla('conf_feriados')->get());
		} else {
			$this->pantalla()->eliminar_evento('eliminar');
		}
	}

	function evt__formulario__modificacion($datos)
	{
	 
	 $feriado=$datos['feriado_fecha'];
	 $sql = "Select feriado_fecha from reloj.conf_feriados
		where feriado_fecha ='$feriado';";
	$res = toba::db('ctrl_asis')->consultar_fila($sql);
  	
	if (count($res)<1) {
	 
	// if ($datos['feriado_fecha']<>$datos['feriado_fecha_fin']){
		 $fechaInicio =  strtotime($datos['feriado_fecha']);
		 $fechaFin = strtotime($datos['feriado_fecha_fin']);
	 //$dif = ($fechaFin - $fechaInicio);
	
	 $j=0;
	 for($i=$fechaInicio; $i<=$fechaFin; $i+=86400){
		
		 $dia = date("Y-m-d", $i);
		 $cuadro[$j]['feriado']=$datos['feriado'];
		 $cuadro[$j]['tipo_feriado']= $datos['tipo_feriado'];
		 $cuadro[$j]['feriado_fecha'] = $dia;
		 $cuadro[$j]['feriado_fecha_fin'] =$datos['feriado_fecha_fin'];
		 $cuadro[$j]['agrupamiento']=$datos['agrupamiento'];
		 $j++;
	 }
	 $j = $j-1;
	 $h = 0;
	
	do{
		$feriado = $cuadro[$h]['feriado'];
		$tipo_feriado = $cuadro[$h]['tipo_feriado'];
		$fecha_i = $cuadro[$h]['feriado_fecha'];
		$fecha_f = $cuadro[$h]['feriado_fecha_fin'];
		$agrupamiento = $cuadro[$h]['agrupamiento'];
		$sql = "INSERT INTO reloj.conf_feriados (feriado, tipo_feriado, feriado_fecha,feriado_fecha_fin, agrupamiento)
		VALUES ('$feriado',$tipo_feriado, '$fecha_i', '$fecha_f' ,'$agrupamiento');";
		
		toba::db('ctrl_asis')->ejecutar($sql); 
		$h++;
		//ei_arbol($cuadro[$h]);
		//$this->dep('datos')->tabla('conf_feriados')->set($cuadro[$h]);
	
	} while ($h=$j);
	}else {
	
	$this->dep('datos')->tabla('conf_feriados')->set($datos);
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
		$this->dep('datos')->sincronizar();
		$this->resetear();
	}

}

?>