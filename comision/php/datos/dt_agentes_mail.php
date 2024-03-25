<?php
class dt_agentes_mail extends comision_datos_tabla
{
	function get_correo ($legajo) {
		$sql = "SELECT email as descripcion from reloj.agentes_mail
		WHERE legajo = $legajo";
		return toba::db('comision')->consultar($sql);
	}
}

?>