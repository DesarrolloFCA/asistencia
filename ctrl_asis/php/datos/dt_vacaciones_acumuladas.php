<?php
class dt_vacaciones_acumuladas extends ctrl_asis_datos_tabla
{
	function get_listado($filtro=array())
	{
		$where = array();
		if (isset($filtro['legajo'])) {
			$where[] = "legajo = ".quote($filtro['legajo']);
		}
		if (isset($filtro['anio'])) {
			$where[] = "anio ILIKE ".quote("%{$filtro['anio']}%");
		}
		$sql = "SELECT
			t_va.legajo,
			t_va.anio,
			t_va.dias
		FROM
			vacaciones_acumuladas as t_va";
		if (count($where)>0) {
			$sql = sql_concatenar_where($sql, $where);
		}
		return toba::db('ctrl_asis')->consultar($sql);
	}



	function get_dias($legajo, $anio='')
	{

		if(!empty($anio)){
			$and_anio = "and t_va.anio = '$anio'";
		}

		$sql = "SELECT sum(t_va.dias) as dias_acumulados FROM vacaciones_acumuladas as t_va
				WHERE t_va.legajo = '$legajo' $and_anio"; 

		$datos = toba::db('ctrl_asis')->consultar_fila($sql);

		if(!empty($datos['dias_acumulados'])){
			return $datos['dias_acumulados'];
		}else{
			return 0;
		}
	}

	function insertar_cierre($periodo,$sel)
	{

		if($sel['dias'] > 0){
			$sql = "INSERT INTO reloj.vacaciones_acumuladas(legajo, anio, dias) VALUES ('".$sel['legajo']."', '$periodo','".$sel['dias']."')"; 
			//echo $sql;

			return toba::db('ctrl_asis')->ejecutar($sql);

			//return 1;
		}else{

			return 0;
		}

	}

}
?>