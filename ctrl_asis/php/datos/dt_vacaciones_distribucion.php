<?php
class dt_vacaciones_distribucion extends toba_datos_tabla
{
	function get_listado($filtro=array())
	{
		$where = array();
		if (isset($filtro['agente_id'])) {
			$where[] = "agente_id = ".quote($filtro['agente_id']);
		}
		if (isset($filtro['periodo'])) {
			$where[] = "periodo ILIKE ".quote("%{$filtro['periodo']}%");
		}
		if (isset($filtro['fecha_desde'])) {
			$where[] = "fecha_desde = ".quote($filtro['fecha_desde']);
		}
		if (isset($filtro['fecha_hasta'])) {
			$where[] = "fecha_hasta = ".quote($filtro['fecha_hasta']);
		}
		$sql = "SELECT
			t_vd.vaciones_distribucion_id,
			t_vd.agente_id,
			t_vd.periodo,
			t_vd.fecha_desde,
			t_vd.fecha_hasta,
			t_vd.dias
		FROM
			vacaciones_distribucion as t_vd
		ORDER BY periodo";
		if (count($where)>0) {
			$sql = sql_concatenar_where($sql, $where);
		}
		return toba::db('ctrl_asis')->consultar($sql);
	}

}

?>