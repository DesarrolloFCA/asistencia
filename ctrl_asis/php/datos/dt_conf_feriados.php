<?php
class dt_conf_feriados extends ctrl_asis_datos_tabla
{
	function hay_feriado($fecha,$agru)
	{
		
		/*$sql = "Select (feriado_fecha_fin - feriado_fecha  ) resultado From reloj.conf_feriados where feriado_fecha ='$fecha' ";
		$res = toba::db('ctrl_asis')->consultar_fila($sql);
		if ($res['resultado'] > 0 ) {
			
			$sql = "SELECT count(feriado_id) as resultado FROM reloj.conf_feriados where feriado_fecha = '$fecha' ";
			$res = toba::db('ctrl_asis')->consultar_fila($sql);
	//	}*/
		
		if ($agru == 'NODO'){
			$agru='Personal de Apoyo';
		} else if($agru=='DOCE') {
			$agru = 'Docentes';
		}else {
				$agru='Todos';
		}		
		$sql= "Select feriado_id as resultado from reloj.conf_feriados
		 where '$fecha' between feriado_fecha and feriado_fecha_fin
		 and agrupamiento in ('$agru',NULL,'Todos');";

		$res = toba::db('ctrl_asis')->consultar($sql);
		

		if(count($res)> 0){
			//ei_arbol($res);
			return $res[0]['resultado'];

		} else {
			
			$a = 0;
			return $a; 
		}
	
	}
	

	function get_feriado($fecha)
	{
		
		$sql = "SELECT feriado_id, feriado, feriado as descripcion, feriado_fecha FROM reloj.conf_feriados where '$fecha' between feriado_fecha and feriado_fecha_fin";

		return toba::db('ctrl_asis')->consultar_fila($sql);
		
	}

	function get_listado($filtro=array())
	{
			
		
		$where = array();
		if (isset($filtro['feriado'])) {
			$where[] = "t_cf.feriado ILIKE ".quote("%{$filtro['feriado']}%");
		}
		if (isset($filtro['feriado_fecha'])) {
			$where[] = "t_cf.feriado_fecha = ".quote($filtro['feriado_fecha']);
		}
		if (isset($filtro['fecha_desde'])) {
				list($y,$m,$d)=explode("-",$filtro['fecha_desde']); //2011-03-31
				$fecha_desde = $y."-".$m."-".$d;
				$where[] = "t_cf.feriado_fecha >= ".quote($fecha_desde);
		}
		if (isset($filtro['fecha_hasta'])) {
				list($y,$m,$d)=explode("-",$filtro['fecha_hasta']); //2011-03-31
				
				$fecha_hasta = $y."-".$m."-".$d." 23:59:59";
				$where[] = "t_cf.feriado_fecha <= ".quote($fecha_hasta);
		}

		
		$sql = "SELECT
			t_cf.feriado_id,
			t_cf.feriado,
			t_cf.feriado_fecha as fecha,t_cf.feriado_fecha_fin,t_cf.agrupamiento as agrupamiento ,t_cf.tipo_feriado
		FROM
			conf_feriados as t_cf
		ORDER BY t_cf.feriado_fecha DESC";
		if (count($where)>0) {
			$sql = sql_concatenar_where($sql, $where);
		}
		
		return toba::db('ctrl_asis')->consultar($sql);
	}
	function dia_feriado($dia){
		$sql = "SELECT agrupamiento from reloj.conf_feriados
				Where '$dia' between feriado_fecha_fin and feriado_fecha";
		$feriado=toba::db('ctrl_asis')->consultar_fila($sql);
		if(isset($feriado)){
			return $feriado['agrupamiento'];
		}else {
			return false;
		}

				
	}
	
	function suma_feriados($filtro=array())	{
		$where = array();
		if (isset($filtro['feriado'])) {
			$where[] = "t_cf.feriado ILIKE ".quote("%{$filtro['feriado']}%");
		}
		if (isset($filtro['feriado_fecha'])) {
			$where[] = "t_cf.feriado_fecha = ".quote($filtro['feriado_fecha']);
		}
		if (isset($filtro['fecha_desde'])) {
				list($y,$m,$d)=explode("-",$filtro['fecha_desde']); //2011-03-31
				$fecha_desde = $y."-".$m."-".$d;
				$where[] = "t_cf.feriado_fecha >= ".quote($fecha_desde);
		}
		if (isset($filtro['fecha_hasta'])) {
				list($y,$m,$d)=explode("-",$filtro['fecha_hasta']); //2011-03-31
				
				$fecha_hasta = $y."-".$m."-".$d." 23:59:59";
				$where[] = "t_cf.feriado_fecha <= ".quote($fecha_hasta);
		}

		
		$sql = "SELECT
			
			(t_cf.feriado_fecha_fin - t_cf.feriado_fecha) + 1 dias_feriados ,t_cf.agrupamiento as agrupamiento 
		FROM
			conf_feriados as t_cf
		ORDER BY t_cf.feriado_fecha DESC";

		if (count($where)>0) {
			$sql = sql_concatenar_where($sql, $where);
		}
		
		$feriado= toba::db('ctrl_asis')->consultar($sql);
		$lim=count($feriado);
		
		$total_dias= 0;
		for ($i=0;$i<$lim;$i++){
			$total_dias = $total_dias + $feriado[$i]['dias_feriados'];
		}
		return $total_dias;
	}	

}
?>