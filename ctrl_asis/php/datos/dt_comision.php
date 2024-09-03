<?php
class dt_comision extends ctrl_asis_datos_tabla
{
	function get_listado($filtro=array())
	{
		$where = array();
		//ei_arbol($filtro);
		if (isset($filtro['legajo'])) {
			$where[] = "legajo = ".quote($filtro['legajo']);
		}
		$sql = "SELECT id_comision, legajo, catedra, lugar, 
			motivo, fecha, horario, observaciones, legajo_sup, 
			legajo_aut, autoriza_sup, autoriza_aut, fecha_fin, horario_fin, fuera
	FROM reloj.comision
	WHERE
		(pasada is null or pasada = false)
		ORDER BY catedra";
		if (count($where)>0) {
		
			$sql = sql_concatenar_where($sql, $where);
		}
		
		return toba::db('ctrl_asis')->consultar($sql);
	}
	

}
?>