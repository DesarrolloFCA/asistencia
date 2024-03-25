<?php

class consultas_areas {
    function get_listado($filtro=array())
	{
		$where = array();
		if (isset($filtro['dependencia_id'])) {
			$where[] = "dependencia_id = ".quote($filtro['dependencia_id']);
		}
		if (isset($filtro['area_id'])) {
			$where[] = "area_id = ".quote($filtro['area_id']);
		}
		if (isset($filtro['area'])) {
			$where[] = "area ILIKE ".quote("%{$filtro['area']}%");
		}
		if (isset($filtro['agente_id'])) {
			$where[] = "agente_id = ".quote($filtro['agente_id']);
		}
		$sql = "SELECT
			t_ca.area_id,
			t_ca.agente_id,
			t_ca.nombres_responsable,
			t_cd.dependencia as dependencia_id_nombre,
			t_ca.area
		FROM
			conf_areas as t_ca,
			conf_dependencia as t_cd
		WHERE
				t_ca.dependencia_id = t_cd.dependencia_id
		ORDER BY area";
		if (count($where)>0) {
			$sql = sql_concatenar_where($sql, $where);
		}
		return toba::db('ctrl_asis')->consultar($sql);
	}
        
        function get_nombre($filtro=array())
	{
		$where = array();
		if (isset($filtro['area_id'])) {
			$where[] = "area_id = ".quote($filtro['area_id']);
		}
		$sql = "SELECT
			t_ca.area_id,
			t_ca.agente_id,
			t_ca.nombres_responsable,
			t_cd.dependencia as dependencia_id_nombre,
			t_ca.area
		FROM
			conf_areas as t_ca,
			conf_dependencia as t_cd
		WHERE
				t_ca.dependencia_id = t_cd.dependencia_id
		ORDER BY area";
		if (count($where)>0) {
			$sql = sql_concatenar_where($sql, $where);
		}
		$resp =  toba::db('ctrl_asis')->consultar($sql);
                return $resp[0]['area'];
	}
}
