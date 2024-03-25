<?php
class dt_catedras extends ctrl_asis_datos_tabla
{
	function get_descripciones()
	{
		$sql = "SELECT id_catedra, nombre_catedra FROM catedras ORDER BY nombre_catedra";
		return toba::db('ctrl_asis')->consultar($sql);
	}




	function get_catedra ($catedra)
	{
		$sql = "SELECT  nombre_catedra,id_departamento FROM catedras where id_catedra = $catedra";
		return toba::db('ctrl_asis')->consultar($sql);
	}

	function get_listado($filtro=array())
	{
		$where = array();
		if (isset($filtro['nombre_catedra'])) {
			$where[] = "nombre_catedra ILIKE ".quote("%{$filtro['nombre_catedra']}%");
		}
		if (isset($filtro['id_departamento'])) {
			$where[] = "t_d.id_departamento = ".quote($filtro['id_departamento']);
		}
		$sql = "SELECT
			t_c.id_catedra,
			t_c.nombre_catedra,
			t_d.departamento as id_departamento_nombre
		FROM
			catedras as t_c	LEFT OUTER JOIN departamentos as t_d ON (t_c.id_departamento = t_d.id_departamento)
		ORDER BY nombre_catedra";
		if (count($where)>0) {
			$sql = sql_concatenar_where($sql, $where);
		}
		return toba::db('ctrl_asis')->consultar($sql);
	}



}
?>