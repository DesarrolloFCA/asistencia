<?php
class dt_conf_estados_marcas extends toba_datos_tabla
{
	function get_descripciones()
	{
		$sql = "SELECT estado_marca_id, estado_marca FROM conf_estados_marcas ORDER BY estado_marca";
		return toba::db('ctrl_asis')->consultar($sql);
	}

	function get_listado()
	{
		$sql = "SELECT
			t_cem.estado_marca_id,
			t_cem.estado_marca
		FROM
			conf_estados_marcas as t_cem
		ORDER BY estado_marca";
		return toba::db('ctrl_asis')->consultar($sql);
	}

}
?>