<?php
class dt_tipos_feriados extends ctrl_asis_datos_tabla
{
	function get_descripciones()
	{
		$sql = "SELECT id_feriado_tipo, descripcion FROM tipos_feriados ORDER BY descripcion";
		return toba::db('ctrl_asis')->consultar($sql);
	}

}

?>