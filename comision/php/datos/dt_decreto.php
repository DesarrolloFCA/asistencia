<?php
class dt_decreto extends comision_datos_tabla
{
	function get_descripciones()
	{
		$sql = "SELECT id_decreto, descripcion FROM decreto ORDER BY descripcion";
		return toba::db('comision')->consultar($sql);
	}

}

?>