<?php
class dt_motivo extends toba_datos_tabla
{
	function get_listado($filtro=array())
	{
		$where = array();
		if (isset($filtro['descripcion'])) {
			$where[] = "descripcion ILIKE ".quote("%{$filtro['descripcion']}%");
		}
		$sql = "SELECT
			t_m.id_motivo,
			t_m.descripcion,
			t_m.sanidad, 
			t_m.observaciones
		FROM
			motivo as t_m
		ORDER BY t_m.descripcion";
		if (count($where)>0) {
			$sql = sql_concatenar_where($sql, $where);
		}
		return toba::db('ctrl_asis')->consultar($sql);
	}

	function get_descripciones()
	{
		$sql = "SELECT id_motivo, descripcion FROM motivo ORDER BY descripcion";
		return toba::db('ctrl_asis')->consultar($sql);
	}









	function get_descripciones_sanidad()
	{
		$sql = "SELECT id_motivo, descripcion FROM motivo WHERE sanidad = 1 ORDER BY descripcion";
		return toba::db('ctrl_asis')->consultar($sql);
	}


	function get_descripcion($id_motivo)
	{
		$sql = "SELECT id_motivo, descripcion FROM motivo WHERE id_motivo = '$id_motivo'";
		$dato = toba::db('ctrl_asis')->consultar_fila($sql);
		return $dato['descripcion'];
	}

}
?>