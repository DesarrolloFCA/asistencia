<?php
class dt_parte_anio extends ctrl_asis_datos_tabla
{
	function get_descripciones()
	{
		$sql = "SELECT parte_anio_id FROM parte_anio";
		return toba::db('ctrl_asis')->consultar($sql);
	}

	function get_vacaciones_disponibles_por_año($filtro){
		$sql = "";
		if (isset($filtro['legajo']) && isset($filtro['agrupamiento'])) {
			$sql = "SELECT legajo, anio, sum(dias) as total_dias
  			FROM reloj.parte INNER JOIN reloj.parte_anio ON reloj.parte.id_parte = reloj.parte_anio.id_parte WHERE id_motivo = 35 and legajo = ". $filtro['legajo'] ." GROUP BY legajo, anio ORDER BY anio asc";
		}
		return toba::db('ctrl_asis')->consultar($sql);	
	}

	function generar_parte_anio($id_parte, $anio){
		$sql = "INSERT INTO parte_anio (id_parte_anio, id_parte, anio) VALUES (DEFAULT, '$id_parte', '$anio')";
		return toba::db('ctrl_asis')->consultar($sql);
	}

}
?>