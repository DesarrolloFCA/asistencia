<?php
class dt_vacaciones_acumuladas extends comision_datos_tabla
{
	function get_descripciones()
	{
		$sql = "SELECT anio,  FROM vacaciones_acumuladas ORDER BY ";
		return toba::db('comision')->consultar($sql);
	}

}

?>