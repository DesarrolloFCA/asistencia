<?php
class dt_adscripcion extends toba_datos_tabla
{
	function get_listado($filtro=array())
	{
		$where = array();
		if (isset($filtro['legajo'])) {
			$where[] = "legajo = ".quote($filtro['legajo']);
		}
		if (isset($filtro['cod_depcia_origen'])) {
			$where[] = "cod_depcia_origen ILIKE ".quote("%{$filtro['cod_depcia_origen']}%");
		}
		if (isset($filtro['cod_depcia_destino'])) {
			$where[] = "cod_depcia_destino ILIKE ".quote("%{$filtro['cod_depcia_destino']}%");
		}
		$sql = "SELECT
			t_a.legajo,
			t_a.cod_depcia_origen,
			t_a.fecha_inicio,
			t_a.cod_depcia_destino,
			t_a.fecha_fin
		FROM
			adscripcion as t_a";
		if (count($where)>0) {
			$sql = sql_concatenar_where($sql, $where);
		}
		
		$datos = toba::db('ctrl_asis')->consultar($sql);

		//---------------------------------------------------------

		if(count($datos)>0){


			foreach ($datos as $key => $dato) {
		        $sql = "SELECT apellido||', '||nombre as nombre_completo FROM uncu.legajo WHERE legajo = '".$dato['legajo']."'";
		        $dato_legajo = toba::db('mapuche')->consultar_fila($sql); 
		        $datos[$key]['nombre_completo']  = $dato_legajo['nombre_completo'];
   
		        $sql = "SELECT cod_depcia, desc_depcia, desc_depcia as descripcion FROM uncu.dependencia WHERE cod_depcia='".$dato['cod_depcia_origen']."'";
		        $dato_dep = toba::db('mapuche')->consultar_fila($sql); 
		        $datos[$key]['desc_depcia_origen']  = $dato_dep['descripcion'];
						        
		        $sql = "SELECT cod_depcia, desc_depcia, desc_depcia as descripcion FROM uncu.dependencia WHERE cod_depcia='".$dato['cod_depcia_origen']."'";
		        $dato_dep = toba::db('mapuche')->consultar_fila($sql); 
		        $datos[$key]['desc_depcia_origen']  = $dato_dep['descripcion'];
				
				$sql = "SELECT cod_depcia, desc_depcia, desc_depcia as descripcion FROM uncu.dependencia WHERE cod_depcia='".$dato['cod_depcia_destino']."'";
		        $dato_dep = toba::db('mapuche')->consultar_fila($sql); 
		        $datos[$key]['desc_depcia_destino'] = $dato_dep['descripcion'];

			}
		}

		//---------------------------------------------------------

		return $datos;
	}

}

?>