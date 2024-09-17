<?php
class informes_inasistencias extends ctrl_asis_ci
{
	protected $s__datos_filtro;
	//-----------------------------------------------------------------------------------
	//---- cuadro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro(ctrl_asis_ei_cuadro $cuadro)
	{
		$hoy = new DateTime();
		$filtro = $this->s__datos_filtro;
		$where=array();
	// Clonar el objeto para calcular 30 días atrás
		$fechaAtras = clone $hoy;
		$fechaAtras->modify('-30 days');
		$fecha_ini = $fechaAtras->format('Y-m-d'); 

// Clonar el objeto para calcular 15 días hacia adelante
		$fechaAdelante = clone $hoy;
		$fechaAdelante->modify('+15 days');
		$fecha_final= $fechaAdelante->format('Y-m-d');
		if (isset($filtro['fecha_ini']['valor']) or isset($filtro['fecha_final']['valor']) ) {
			if(isset($filtro['fecha_ini']['valor'])){
				$fecha_ini = $filtro['fecha_ini']['valor'];
			}
			if(isset($filtro['fecha_final']['valor'])){
				$fecha_final = $filtro['fecha_final']['valor'];
			}
		}
		$where[]="fecha between '$fecha_ini' AND '$fecha_final'";
		if(isset($filtro['legajo']['valor'])){
			$legajo =$filtro['legajo']['valor'];
			$where [] = "legajo = $legajo";
		}
		
		$sql= "SELECT * from reloj.vw_inasistencia_informe";
		$sql = sql_concatenar_where($sql, $where);
		$datos = toba::db('ctrl_asis')->consultar($sql);
		$cuadro->set_datos($datos);
	}

	//-----------------------------------------------------------------------------------
	//---- filtro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__filtro(ctrl_asis_ei_filtro $filtro)
	{
		if (isset($this->s__datos_filtro)){
			$filtro->set_datos($this->s__datos_filtro);
		}
	}

	function evt__filtro__filtrar($datos)
	{
		$this->s__datos_filtro =$datos;
	}

	function evt__filtro__cancelar()
	{
		unset($this->s__datos_filtro);
	}

}
?>