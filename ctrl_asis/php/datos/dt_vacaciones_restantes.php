<?php
class dt_vacaciones_restantes extends ctrl_asis_datos_tabla
{
	function get_listado($filtro=array())
	{
		$where = array();
		if (isset($filtro['legajo'])) {
			$where[] = "t_vt.legajo = ".quote($filtro['legajo']);
		}
		if (isset($filtro['agrupamiento'])) {
			$where[] = "t_vt.agrupamiento = ".quote($filtro['agrupamiento']);
		}
		$sql = "SELECT
			t_vt.legajo,
			t_vt.cod_depcia,
			t_vt.agrupamiento,
			t_vt.anio,
			t_vt.dias
		FROM
			vacaciones_restantes as t_vt";

		if (count($where)>0) {
			$sql = sql_concatenar_where($sql, $where);
		}
		return toba::db('ctrl_asis')->consultar($sql);
	}

	function get_dias($legajo, $anio ) //, $agrupamiento)
	{

		//ei_arbol($legajo,$anio);
		$sql = "SELECT
			sum(t_vt.dias) as dias_restantes																																																																						
		FROM
			vacaciones_restantes as t_vt where t_vt.legajo = '$legajo' and t_vt.anio = '$anio' --and t_vt.agrupamiento = '$agrupamiento'";

		$datos = toba::db('ctrl_asis')->consultar_fila($sql);
		if(is_numeric($datos['dias_restantes'])){
			return $datos['dias_restantes'];
		}else{
			return NULL;
		}
	}

	function generar_vacaciones_restantes($legajo, $cod_depcia, $agrupamiento, $anio, $dias){
		$sql = "INSERT INTO vacaciones_restantes (legajo,cod_depcia, agrupamiento, anio, dias) VALUES ('$legajo', '$cod_depcia', '$agrupamiento', '$anio', '$dias')";
		return toba::db('ctrl_asis')->consultar($sql);
	}
	function get_vac_rest($legajo)
	{
		//ei_arbol($legajo);
		$slq="SELECT dias FROM reloj.vacaciones_restantes
		where legajo =". $legajo.";";
		//ei_arbol($sql);
		return toba::db('ctrl_asis')->consultar($sql);	
	}


}
?>