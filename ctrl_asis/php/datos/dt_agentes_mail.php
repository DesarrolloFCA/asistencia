<?php
class dt_agentes_mail extends ctrl_asis_datos_tabla
{
	function get_legajo_mail($legajo){
		$sql = "SELECT email FROM reloj.agentes_mail
		where legajo = $legajo";
		return toba::db('ctrl_asis')->consultar($sql);
	}
	function get_descripciones()
	{
		$sql = "SELECT legajo, email FROM agentes_mail ORDER BY email";
		return toba::db('ctrl_asis')->consultar($sql);
	}




}
?>