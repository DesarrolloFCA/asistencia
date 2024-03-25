<?php
class dt_antiguedad extends toba_datos_tabla
{
	function get_listado($filtro=array())
	{
		$where = array();
		if (isset($filtro['legajo'])) {
			$where[] = "legajo = ".quote($filtro['legajo']);
		}
		$sql = "SELECT
			*
		FROM
			antiguedad as t_a";
		if (count($where)>0) {
			$sql = sql_concatenar_where($sql, $where);
		}
		return toba::db('ctrl_asis')->consultar($sql);
	}

	function get_antiguedad($legajo)
	{
		$sql = "SELECT legajo, fecha_ingreso,dias FROM antiguedad WHERE legajo = '$legajo'";

		return toba::db('ctrl_asis')->consultar_fila($sql);
	}


}

?>