<?php
class dt_conf_dependencia extends toba_datos_tabla
{
	function get_listado($filtro=array())
	{
		$where = array();
		if (isset($filtro['dependencia'])) {
			$where[] = "dependencia ILIKE ".quote("%{$filtro['dependencia']}%");
		}
		$sql = "SELECT
			t_cd.dependencia_id,
			t_cd.dependencia
		FROM
			conf_dependencia as t_cd
		ORDER BY dependencia";
		if (count($where)>0) {
			$sql = sql_concatenar_where($sql, $where);
		}
		return toba::db('ctrl_asis')->consultar($sql);
	}
	function get_descripciones()
	{
		$sql = "SELECT dependencia_id, dependencia FROM conf_dependencia ORDER BY dependencia";
		return toba::db('ctrl_asis')->consultar($sql);
	}

}
?>