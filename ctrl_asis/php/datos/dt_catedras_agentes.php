<?php
class dt_catedras_agentes extends ctrl_asis_datos_tabla
{
	function get_listado($filtro=array())
	{
		$where = array();
		if (isset($filtro['id_catedra'])) {
			$where[] = "t_ca.id_catedra = ".quote($filtro['id_catedra']);
		}
		if (isset($filtro['legajo'])) {
			$where[] = "t_ca.legajo = ".quote($filtro['legajo']);
		}
		$sql = "SELECT
			t_ca.id_catedra, nombre_catedra id_catedra_nombre,
			t_ca.legajo,
			t_ca.jefe
		FROM
			catedras_agentes as t_ca 
		INNER JOIN catedras a on t_ca.id_catedra = a.id_catedra";
		if (count($where)>0) {
			$sql = sql_concatenar_where($sql, $where);
		}
		return toba::db('ctrl_asis')->consultar($sql);
	}

}

?>