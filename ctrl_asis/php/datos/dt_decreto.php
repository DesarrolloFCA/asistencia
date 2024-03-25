<?php
class dt_decreto extends toba_datos_tabla
{
	function get_listado($filtro=array())
	{
		$where = array();
		if (isset($filtro['descripcion'])) {
			$where[] = "descripcion ILIKE ".quote("%{$filtro['descripcion']}%");
		}
		$sql = "SELECT
			t_d.id_decreto,
			t_d.descripcion 
		FROM
			decreto as t_d
		ORDER BY t_d.descripcion";
		if (count($where)>0) {
			$sql = sql_concatenar_where($sql, $where);
		}
		
		$datos = toba::db('ctrl_asis')->consultar($sql);

		if(count($datos) > 0){
			foreach ($datos as $key => $dato) {
				$sql = "SELECT codagrup FROM  agrupacion_decreto WHERE id_decreto = '".$dato['id_decreto']."'  
						ORDER BY codagrup ASC";
				$agrupamientos = toba::db('ctrl_asis')->consultar($sql);
				if(count($agrupamientos)>0){
					$string = '';
					foreach($agrupamientos as $k=>$agrupamiento){
						if($k > 0){ $string .= ', '; }
						$string .= $agrupamiento['codagrup'];
					}

					$datos[$key]['agrupamientos'] = $string;
				}
			}
		}

		return $datos;
	}


	function agregar_decreto($datos=array())
	{
			$sql="SELECT last_value+1 as id_decreto from decreto_id_decreto_seq;";
			$resultado = toba::db('sanidad')->consultar_fila($sql);
			$id_decreto = $resultado['id_decreto'];

			$sql="INSERT INTO decreto(id_decreto, descripcion)
					VALUES ('".$id_decreto."', '".$datos['descripcion']."');";

			toba::db('ctrl_asis')->ejecutar($sql);

			$sql="ALTER SEQUENCE decreto_id_decreto_seq RESTART WITH ".$id_decreto.";";
			toba::db('ctrl_asis')->ejecutar($sql);

			return $id_decreto;
	}

	function get_descripciones()
	{
		$sql = "SELECT id_decreto, descripcion FROM decreto ORDER BY descripcion";
		return toba::db('ctrl_asis')->consultar($sql);
	}






	function get_descripciones_codagrup($codagrup)
	{
		$sql = "SELECT t_d.id_decreto, t_d.descripcion 
		FROM decreto as t_d, agrupacion_decreto as t_ad
		WHERE t_ad.codagrup = '$codagrup' 
			AND t_ad.id_decreto = t_d.id_decreto
			ORDER BY t_d.descripcion";
		return toba::db('ctrl_asis')->consultar($sql);
	}

	function get_descripciones_codagrup_motivo($codagrup,$id_motivo)
	{        
		$sql = "SELECT t_d.id_decreto, t_d.descripcion 
		FROM decreto as t_d, agrupacion_decreto as t_ad
		WHERE t_ad.codagrup = '$codagrup' 
			AND t_ad.id_decreto = t_d.id_decreto 
			AND t_ad.id_decreto IN (select id_decreto from articulo where id_motivo = '$id_motivo')
			ORDER BY t_d.descripcion";
		return toba::db('ctrl_asis')->consultar($sql);
	}


	function get_descripcion($id_decreto)
	{
		$sql = "SELECT id_decreto, descripcion FROM decreto WHERE id_decreto = '$id_decreto'";
		$dato = toba::db('ctrl_asis')->consultar_fila($sql);
		return $dato['descripcion'];
	}
}
?>