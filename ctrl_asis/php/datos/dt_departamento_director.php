<?php
class dt_departamento_director extends ctrl_asis_datos_tabla
{
	function get_listado($filtro=array())
	{
		$where = array();
		if (isset($filtro['legajo_dir'])) {
			$where[] = "legajo_dir = ".quote($filtro['legajo_dir']);
		}
		$sql = "SELECT
			t_dd.id_departamento,
			t_dd.legajo_dir
		FROM
			departamento_director as t_dd";
		if (count($where)>0) {
			$sql = sql_concatenar_where($sql, $where);
		}
		return toba::db('ctrl_asis')->consultar($sql);
	}



}
?>