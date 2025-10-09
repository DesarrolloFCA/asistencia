<?php
class inf_personal extends comision_ci
{
	protected $s__datos_filtro;
	protected $s__datos;
	//-----------------------------------------------------------------------------------
	//---- cuadro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro(comision_ei_cuadro $cuadro)
	{
		include("usuario_logueado.php");
		$legajo_1 = usuario_logueado::get_legajo(toba::usuario()->get_id());
		$legajo = $legajo_1[0]['legajo'];
		$legajo_cat = usuario_logueado::get_legajo_jefe($legajo);
		$legajo_dep = usuario_logueado::get_legajo_dir($legajo);
		$filtro = $this->s__datos_filtro;
		if (usuario_logueado::get_jefe($legajo)) {
			
				$cuadro->set_datos($this->dep('datos')->tabla('inasistencia')->get_inasistencia_sub($legajo_cat,$legajo_dep,$filtro));
		
		}
		
		
	}
 
	//-----------------------------------------------------------------------------------
	//---- filtro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__filtro(comision_ei_filtro $filtro)
	{
		if (isset($this->s__datos_filtro)) {
			
			$filtro->set_datos($this->s__datos_filtro);
		}

	}
	function evt__filtro__filtrar($datos)
	{
		$this->s__datos = $datos;
		
		 $where = $this->dep('filtro')->get_sql_where();
		
		$this->s__datos_filtro = $where;
		
	}

	function evt__filtro__cancelar()
	{
		unset($this->s__datos_filtro);
	}    

}
?>