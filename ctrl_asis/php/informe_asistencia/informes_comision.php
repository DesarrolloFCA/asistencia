<?php
class informes_comision extends ctrl_asis_ci
{
	//-----------------------------------------------------------------------------------
	//---- cuadro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro(ctrl_asis_ei_cuadro $cuadro)
	{
		$hoy = new DateTime();

// Clonar el objeto para calcular 30 días atrás
$fechaAtras = clone $hoy;
$fechaAtras->modify('-30 days');
$fecha_ini = $fechaAtras->format('Y-m-d'); 

// Clonar el objeto para calcular 15 días hacia adelante
$fechaAdelante = clone $hoy;
$fechaAdelante->modify('+15 days');
$fecha_final= $fechaAdelante->format('Y-m-d');
		//$cuadro->set_datos($this->dep('datos')->tabla('comision')->get_listado($this->s__datos_filtro));
		$sql= "SELECT * from reloj.vw_comision_informe
		where fecha between '$fecha_ini' AND '$fecha_final' ";
		//$datos =$this->dep('datos')->tabla('comision')->get_listado($this->s__datos_filtro);
		$datos = toba::db('ctrl_asis')->consultar($sql);
		$cuadro->set_datos($datos);
	}

	//-----------------------------------------------------------------------------------
	//---- cuadro_1 ---------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro_1(ctrl_asis_ei_cuadro $cuadro)
	{
	}

	//-----------------------------------------------------------------------------------
	//---- filtro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__filtro(ctrl_asis_ei_filtro $filtro)
	{
	}

	function evt__filtro__filtrar($datos)
	{
	}

	function evt__filtro__cancelar()
	{
	}

}
?>