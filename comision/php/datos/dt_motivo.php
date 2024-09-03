<?php
class dt_motivo extends comision_datos_tabla
{
	function get_descripciones()
	{
		$sql = "SELECT id_motivo, substr(descripcion,1,28) descripcion FROM motivo ORDER BY descripcion";
		return toba::db('comision')->consultar($sql);
	}


	function get_descripciones_sistema()
	{
		$sql = "SELECT id_motivo, descripcion FROM motivo where sanidad = 3 ORDER BY descripcion";
		return toba::db('comision')->consultar($sql);
	}
	
}
?>