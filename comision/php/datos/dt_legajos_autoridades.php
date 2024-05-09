<?php
class dt_legajos_autoridades extends comision_datos_tabla
{
	function get_descripciones()
	{
		$sql = "SELECT legajo, autoridad FROM legajos_autoridad ORDER BY autoridad";
		return toba::db('comision')->consultar($sql);
	}



}
?>