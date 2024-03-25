<?php
class dt_agentes extends toba_datos_tabla {

	function get_descripciones()
	{
		$sql = "SELECT agente_id, agente_id FROM agentes ORDER BY agente_id";
		return toba::db('ctrl_asis')->consultar($sql);
	}

	function get_agente($agente_id)
	{
		$sql = "SELECT agente_id, legajo, dependencia_id, area_id, cuil, ut  
		FROM agentes WHERE agente_id = '$agente_id'";
		return toba::db('ctrl_asis')->consultar_fila($sql);
	}

}
?>