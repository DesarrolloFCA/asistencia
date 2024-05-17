<?php
class dt_agentes extends comision_datos_tabla
{
    function get_agentes_sub ($legajo) {
		//ei_arbol($legajo);
        $sql = "SELECT apellido||', '|| nombre as descripcion from reloj.agentes
		WHERE legajo = $legajo";
		return toba::db('comision')->consultar($sql);
	}
}

?>