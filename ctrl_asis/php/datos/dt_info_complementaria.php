<?php
class dt_info_complementaria extends toba_datos_tabla
{
	function get_listado($filtro=array())
	{
		$where = array();
		if (isset($filtro['id_info_complementaria'])) {
			$where[] = "t_ic.id_info_complementaria = ".quote($filtro['id_info_complementaria']);
		}
		if (isset($filtro['legajo'])) {
			$where[] = "t_ic.legajo = ".quote($filtro['legajo']);
		}
		if (isset($filtro['fecha_alta'])) {
			$where[] = "t_ic.fecha_alta = ".quote($filtro['fecha_alta']);
		}
		if (isset($filtro['usuario_alta'])) {
			$where[] = "t_ic.usuario_alta ILIKE ".quote("%{$filtro['usuario_alta']}%");
		}
		if (isset($filtro['fecha_desde'])) {
				list($y,$m,$d)=explode("-",$filtro['fecha_desde']); //2011-03-31
				$fecha_desde = $y."-".$m."-".$d;
				$where[] = "t_ic.fecha_alta >= ".quote($fecha_desde);
		}
		if (isset($filtro['fecha_hasta'])) {
				list($y,$m,$d)=explode("-",$filtro['fecha_hasta']); //2011-03-31
				
				$fecha_hasta = $y."-".$m."-".$d." 23:59:59";
				$where[] = "t_ic.fecha_alta <= ".quote($fecha_hasta);
		}

		$sql = "SELECT
			t_ic.id_info_complementaria,
			t_ic.fecha_alta,
			t_ic.usuario_alta,
			t_ic.legajo,
			t_ic.entrada,
			t_ic.salida,
			t_ic.observaciones,
			t_ic.cod_depcia,
			t_ic.agrupamiento,
			t_ic.apellido||', '||t_ic.nombre as nombre_completo,
			t_ic.apellido, 
			t_ic.nombre
		FROM
			info_complementaria as t_ic
		ORDER BY usuario_alta";
		if (count($where)>0) {
			$sql = sql_concatenar_where($sql, $where);
		}
		

		$datos = toba::db('ctrl_asis')->consultar($sql);

		if(count($datos)>0){

			foreach ($datos as $key => $dato) {

				$datos[$key]['horas']  = $this->restar_horas(substr($dato['entrada'], -8,8),substr($dato['salida'], -8,8));
			}
		}

		return $datos;
	}


	function tiene_info_complementaria($legajo, $dia){

		$where = array();

		//$where[] = "t_p.estado = 'C'";
		$where[] = "t_ic.legajo = ".quote($legajo);

		
		$fecha_desde = $dia;
		$fecha_hasta = $dia." 23:59:59";

		$where[] = "t_ic.entrada >= ".quote($fecha_desde);
		$where[] = "t_ic.salida <= ".quote($fecha_hasta);

		$sql = "SELECT
			t_ic.id_info_complementaria,
			t_ic.fecha_alta,
			t_ic.usuario_alta,
			t_ic.legajo,
			t_ic.entrada,
			t_ic.salida,
			t_ic.observaciones,
			t_ic.cod_depcia,
			t_ic.agrupamiento,
			t_ic.apellido||', '||t_ic.nombre as nombre_completo,
			t_ic.apellido, 
			t_ic.nombre
		FROM
			info_complementaria as t_ic";

		if (count($where)>0) {
			$sql = sql_concatenar_where($sql, $where);
		}

		$datos = toba::db('ctrl_asis')->consultar_fila($sql);

		if(!empty($datos['id_info_complementaria'])){
			//$datos['horas']     = $this->restar_horas($datos['entrada'],$datos['salida']);
			$datos['horas']     = $this->restar_horas( substr($datos['entrada'], -8,8),substr($datos['salida'], -8,8) );
		}
		

		return $datos;

	}

    function restar_horas($horaini,$horafin)
    {
        $horai=substr($horaini,0,2);
        $mini=substr($horaini,3,2);
        $segi=substr($horaini,6,2);
     
        $horaf=substr($horafin,0,2);
        $minf=substr($horafin,3,2);
        $segf=substr($horafin,6,2);
     
        $ini=((($horai*60)*60)+($mini*60)+$segi);
        $fin=((($horaf*60)*60)+($minf*60)+$segf);
     
        $dif=$fin-$ini;
     
        $difh=floor($dif/3600);
        $difm=floor(($dif-($difh*3600))/60);
        $difs=$dif-($difm*60)-($difh*3600);
        #return date("H:i:s",mktime($difh,$difm,$difs));
        return date("H:i",mktime($difh,$difm,$difs));
    }



}

?>