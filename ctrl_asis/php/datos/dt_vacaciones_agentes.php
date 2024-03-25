<?php
class dt_vacaciones_agentes extends toba_datos_tabla
{
	function get_listado($filtro=array())
	{
		$where = array();
		if (isset($filtro['agente_id'])) {
			$where[] = "t_a.agente_id = ".quote($filtro['agente_id']);
		}
		if (isset($filtro['periodo'])) {
			$where[] = "t_va.periodo ILIKE ".quote("%{$filtro['periodo']}%");
		}
		$sql = "SELECT
			t_va.vacaciones_agente_id,
			t_a.agente_id as agente_id_nombre,
			t_va.periodo,
			t_va.dias_totales,
			t_va.dias_disponibles
		FROM
			vacaciones_agentes as t_va,
			agentes as t_a
		WHERE
				t_va.agente_id = t_a.agente_id
		ORDER BY periodo";
		if (count($where)>0) {
			$sql = sql_concatenar_where($sql, $where);
		}
                
		return toba::db('ctrl_asis')->consultar($sql);
	}

}

?>