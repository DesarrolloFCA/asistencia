<?php
class dt_parte extends toba_datos_tabla
{

	
	function get_listado($filtro=array())
	{
		$legajo = $filtro['legajo'];
		$anio = (string)$filtro['anio'];
		$anio_anterior = (string)$filtro['anio'] -1 ;
		$motivo = $filtro['id_motivo'];
		$where = array();

		if (isset($filtro['estado'])) {
			$where[] = "estado ILIKE ".quote("%{$filtro['estado']}%");
		}
		if (isset($filtro['fecha_inicio_licencia'])) {
			$where[] = "fecha_inicio_licencia = ".quote($filtro['fecha_inicio_licencia']);
		}
		if (isset($filtro['apellido'])) {
			$where[] = "apellido ILIKE ".quote("%{$filtro['apellido']}%");
		}
		if (isset($filtro['nombre'])) {
			$where[] = "nombre ILIKE ".quote("%{$filtro['nombre']}%");
		}
		if (isset($filtro['legajo'])) {
			$where[] = "legajo = $legajo ";
		}
		if (isset($filtro['id_motivo'])){

			$where[]= "t_p.id_motivo = $motivo ";
			if ($filtro['id_motivo']== 35){
				$anio = (string) date('Y');
				$anio_anterior = (string) date('Y')-1;
				if (isset($filtro['anio'])) {
				$where[] = "fecha_inicio_licencia between '$anio_anterior-12-01' AND '$anio-11-30' ";
				}	

			}
		}
		

		/*if(isset($filtro['legajo'])){
			$legajo =$filtro['legajo'];
			$where[] = "legajo = $legajo";
		}*/
		if(isset($filtro['id_parte'])){
			$id_parte =$filtro['id_parte'];
			$where[] = "id_parte = $id_parte";
		}
		//ei_arbol($filtro);

		$sql = "SELECT
			t_p.id_parte,
			t_p.legajo,
			t_p.edad,
			t_p.fecha_alta,
			t_p.usuario_alta,
			t_p.estado,
			t_p.fecha_inicio_licencia,
			t_p.dias,
			t_p.cod_depcia,
			t_p.domicilio,
			t_p.localidad,
			t_p.agrupamiento,
			t_p.fecha_nacimiento,
			t_p.apellido,
			t_p.nombre,
			t_p.estado_civil,
			t_p.observaciones,
			t_d.descripcion as id_decreto_nombre,
			t_m.descripcion as id_motivo_nombre,
			t_p.id_articulo,
			t_p.observaciones_cierre,
			t_p.fecha_baja,
			t_p.usuario_baja,
			t_p.tipo_sexo,
			t_p.usuario_cierre,
			t_p.fecha_cierre,
			t_p.dias2,
			t_p.dias3,
			t_p.dias4,
			t_p.dias5,
			t_p.dias6,
			t_p.dias7,
			t_p.dias8,
			t_p.dias9,
			t_p.id_parte_sanidad, t_m.descripcion motivo,
			t_p.id_motivo
		FROM
			parte as t_p	LEFT OUTER JOIN decreto as t_d ON (t_p.id_decreto = t_d.id_decreto)
			LEFT OUTER JOIN motivo as t_m ON (t_p.id_motivo = t_m.id_motivo)
		ORDER BY nombre";
		if (count($where)>0) {
			$sql = sql_concatenar_where($sql, $where);
		}
		//ei_arbol ($sql);
		return toba::db('comision')->consultar($sql);
	}


	

}
?>