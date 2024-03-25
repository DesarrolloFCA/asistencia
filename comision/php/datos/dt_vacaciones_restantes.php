<?php
class dt_vacaciones_restantes extends comision_datos_tabla
{
	function get_listado($filtro=array())
	{
		$where = array();
		if (isset($filtro['legajo'])) {
			$where[] = "legajo = ".quote($filtro['legajo']);
		}
		$sql = "SELECT
			t_vr.legajo,
			t_vr.cod_depcia,
			t_vr.agrupamiento,
			t_vr.anio,
			t_vr.dias
		FROM
			vacaciones_restantes as t_vr";
		if (count($where)>0) {
			$sql = sql_concatenar_where($sql, $where);
		}
		return toba::db('comision')->consultar($sql);
	}

}

?>