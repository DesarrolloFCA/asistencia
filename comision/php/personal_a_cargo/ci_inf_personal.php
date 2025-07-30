<?php
class inf_personal extends comision_ci
{
	protected $s__datos_filtro;
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
		if (usuario_logueado::get_jefe($legajo)) {
			if(isset($this->s__datos_filtro)) {
				$datos = $this->dep('datos')->tabla('inasistencia')->get_inasistencia_sub($legajo_cat,$legajo_dep);
				
			}else {
				$cuadro->set_datos($this->dep('datos')->tabla('inasistencia')->get_inasistencia_sub($legajo_cat,$legajo_dep));

			}	
		
		
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
		if(isset($datos['id_catedra'])) {
			$datos['id_catedra']['condicion'] = '=';
		}
		if (isset($datos['fecha_desde'])) {
			switch ($datos['fecha_desde']['condicion']){
				case "es_igual_a" : $datos['fecha_desde']['condicion'] = '=';
				break;
				case "entre": $datos['fecha_desde']['condicion'] = 'BETWEEN';
				break;
				case "desde": $datos['fecha_desde']['condicion'] = '>=';
				break;
				case "hasta": $datos['fecha_desde']['condicion'] = '<=';
				break;
				default: $datos['fecha_desde']['condicion'] = '=';
			} 
		}
		if (isset($datos['legajo'])) {
			if (count($datos['legajo']['valor']) == 1){
				$datos['legajo']['condicion'] = '=';

			}else {
				$datos['legajo']['condicion'] = 'in (';
			}
		}
		$this->s__datos_filtro = $datos;
		
	}

	function evt__filtro__cancelar()
	{
		unset($this->s__datos_filtro);
	}    

}
?>