<?php
class vistas_sanidad extends toba_datos_relacion
{
	
	//PARTE  -------------------------------------------
	
	function get_listado($filtro=array(), $limit = '', $offset = '')
	{
		$limit= 'Limit 100';

		$where = array();
		$where[] = "t_p.cod_depcia = 04";
		if (isset($filtro['id_parte'])) {
			$where[] = "t_p.id_parte = ".quote($filtro['id_parte']);
		}
		if (isset($filtro['legajo'])) {
			$where[] = "t_p.legajo = ".quote($filtro['legajo']);
		}
		if (isset($filtro['fecha_alta'])) {
			$where[] = "t_p.fecha_alta = ".quote($filtro['fecha_alta']);
		}
		if (isset($filtro['usuario_alta'])) {
			$where[] = "t_p.usuario_alta ILIKE ".quote("%{$filtro['usuario_alta']}%");
		}
		if (isset($filtro['usuario_cierre'])) {
			$where[] = "t_p.usuario_cierre ILIKE ".quote("%{$filtro['usuario_cierre']}%");
		}
		if (isset($filtro['usuario_medico'])) {
			$where[] = "t_p.usuario_medico = ".quote($filtro['usuario_medico']);
		}
		if (isset($filtro['matricula'])) {
			$where[] = "t_p.matricula = ".quote($filtro['matricula']);
		}
		if (isset($filtro['id_decreto'])) {
			$where[] = "t_p.id_decreto = ".quote($filtro['id_decreto']);
		}
		if (isset($filtro['id_articulo'])) {
			$where[] = "t_p.id_articulo = ".quote($filtro['id_articulo']);
		}
		if (isset($filtro['id_motivo'])) {
			$where[] = "t_p.id_motivo = ".quote($filtro['id_motivo']);
		}    
		/*if (isset($filtro['cod_depcia'])) {
			$where[] = "t_p.cod_depcia = ".quote($filtro['cod_depcia']);
		}*/
		if (isset($filtro['agrupamiento'])) {
			$where[] = "t_p.agrupamiento = ".quote($filtro['agrupamiento']);
		}    
		if (isset($filtro['estado'])) {
			$where[] = "t_p.estado = ".quote($filtro['estado']);
		}    
		if (isset($filtro['id_diagnostico'])) {
			$where[] = "t_p.id_diagnostico = ".quote($filtro['id_diagnostico']);
		}    

		if (isset($filtro['eliminados']) and $filtro['eliminados'] == 1) {
			$where[] = "t_p.estado = 'E'";
		}else{
			$where[] = "t_p.estado <> 'E'";
		}

		if (isset($filtro['cierres_de_oficio']) and $filtro['cierres_de_oficio'] == 1) {
			$where[] = "t_p.observaciones_cierre = 'Cierre de oficio'";
		}

		if (isset($filtro['omite_cierre_automatico'])) {
			$where[] = "t_p.omite_cierre_automatico = ".quote($filtro['omite_cierre_automatico']);
		}    

		if (isset($filtro['fecha_desde'])) {
				list($y,$m,$d)=explode("-",$filtro['fecha_desde']); //2011-03-31
				$fecha_desde = $y."-".$m."-".$d;
				$where[] = "t_p.fecha_alta >= ".quote($fecha_desde);
		}
		if (isset($filtro['fecha_hasta'])) {
				list($y,$m,$d)=explode("-",$filtro['fecha_hasta']); //2011-03-31
				
				$fecha_hasta = $y."-".$m."-".$d." 23:59:59";
				$where[] = "t_p.fecha_alta <= ".quote($fecha_hasta);
		}


		if (isset($filtro['anio'])) {
			
			$y = $filtro['anio'];
			$m = $filtro['mes'];
			$d = date("d",(mktime(0,0,0,$m+1,1,$y)-1));

			$fecha_desde = $y."-".$m."-01";
			$fecha_hasta = $y."-".$m."-".$d." 23:59:59";
			$fecha_hasta_simple = $y."-".$m."-".$d;

			#$where[] = "t_p.fecha_inicio_licencia >= ".quote($fecha_desde);          
			$where[] = "t_p.fecha_inicio_licencia <= ".quote($fecha_hasta);

		}

		if (isset($filtro['tipo_sexo'])) {
			$where[] = "t_p.tipo_sexo = ".quote($filtro['tipo_sexo']);
		}

		// Sincronizacion sanidad

		if (isset($filtro['motivos_sincronizacion']) and $filtro['motivos_sincronizacion'] == 1) {

			$where[] = "t_p.id_motivo IN (1,2,3,4,5,6,11)";
		}
		

		//---- orden -----------
		if (isset($filtro['ordenar_por'])) {
			$ordenar_por = "ORDER BY ".$filtro['ordenar_por'];
		}else{
			$ordenar_por = "ORDER BY t_p.id_parte";
		}
		if (isset($filtro['asc_desc'])) {
			$asc_desc = $filtro['asc_desc'];
		}else{
			$asc_desc = "DESC";
		}

		$sql1 = "SELECT 
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
			t_p.t_manana, t_p.t_tarde, t_p.t_noche,    
			t_p.fecha_nacimiento, t_p.apellido, t_p.nombre, t_p.estado_civil,
			t_p.apellido||', '||t_p.nombre as nombre_completo,
			t_p.observaciones,            
			t_p.id_decreto,            
			t_d.descripcion as decreto,
			t_p.id_motivo,
			t_m.descripcion as motivo,
			t_p.id_articulo,
			t_a.descripcion as articulo,
			t_p.usuario_medico,
			t_p.observaciones_cierre, t_p.fecha_cierre, t_p.usuario_cierre, 
				t_p.fecha_baja, t_p.usuario_baja,
				t_e.fecha_entrega_orden, t_e.fecha_examen, t_e.fecha_vencimiento_provisorio, t_e.fecha_alta_definitivo,
				t_p.id_diagnostico, t_di.descripcion as diagnostico,
				t_p.matricula, t_p.certificado, t_p.omite_cierre_automatico, t_p.tipo_sexo, 
				t_dep.desc_dependencia 
		FROM
			parte as t_p    
			LEFT OUTER JOIN decreto as t_d ON (t_p.id_decreto = t_d.id_decreto)
			LEFT OUTER JOIN articulo as t_a ON (t_p.id_articulo = t_a.id_articulo)
			LEFT OUTER JOIN motivo as t_m ON (t_p.id_motivo = t_m.id_motivo)
			LEFT OUTER JOIN examen as t_e ON (t_p.legajo = t_e.legajo)
			LEFT OUTER JOIN diagnostico as t_di ON (t_p.id_diagnostico = t_di.id_diagnostico)
			LEFT OUTER JOIN dependencia as t_dep ON (t_p.cod_depcia = CAST(t_dep.cod_dependencia AS INT))
		$ordenar_por $asc_desc 
		$limit $offset";
		if (count($where)>0) {
			$sql1 = sql_concatenar_where($sql1, $where);
		}
		
		$datos = toba::db('sanidad')->consultar($sql1);
		
		if (count($datos) > 0) {

			foreach($datos as $key=>$dato){
				if( !empty($dato['fecha_inicio_licencia'])){

					$dias_calculo = $dato['dias'] - 1;
					$dias = '+'.$dias_calculo.' day';
					$datos[$key]['fecha_fin_licencia']    = date ( 'Y-m-d' , strtotime ( $dias , strtotime ( $dato['fecha_inicio_licencia'] ) )  ); //sumamos N dias a la fecha de inicio licencia
				}
			}

			if (isset($filtro['anio'])) {

				$datos_nuevos = array();
				
				foreach($datos as $key=>$dato){
					
					if( ($dato['fecha_fin_licencia'] >= $fecha_desde ) and  ($dato['fecha_inicio_licencia'] <= $fecha_hasta ) ){  

	
						$desde  = str_replace('-', '', $fecha_desde); // strtotime($fecha_desde); 
						$hasta  = str_replace('-', '', $fecha_hasta_simple); // strtotime($fecha_hasta_simple); 
						$inicio = str_replace('-', '', $dato['fecha_inicio_licencia']); // strtotime($fecha_desde); 
						$fin    = str_replace('-', '', $dato['fecha_fin_licencia']); // strtotime($dato['fecha_fin_licencia']); 


						if($desde >= $inicio){ //si la fecha desde es mayor al inicio de la liciencia, usamos esa, sino la otra
							$inicio_calculo   = $desde; 
						}else{
							$inicio_calculo   = $inicio; 
						}

						if($hasta <= $fin ){ //si la fecha hasta es es menor al fin de la liciencia, usamos esa, sino la otra
							$fin_calculo   = $hasta; 
						}else{
							$fin_calculo   = $fin; 
						}
						//echo " fin_calculo $fin_calculo - inicio_calculo $inicio_calculo";
						$dato['dias_mes'] = $fin_calculo - $inicio_calculo +1; 

						$datos_nuevos[] =  $dato;
					}

				}    
				

				return $datos_nuevos;
			}

		}

		return $datos;
	}

	function get_total_registros($filtro=array())
	{
		$where = array();
		$where[] = "t_p.cod_depcia = 04";
		
		if (isset($filtro['id_parte'])) {
			$where[] = "t_p.id_parte = ".quote($filtro['id_parte']);
		}
		if (isset($filtro['legajo'])) {
			$where[] = "t_p.legajo = ".quote($filtro['legajo']);
		}
		if (isset($filtro['fecha_alta'])) {
			$where[] = "t_p.fecha_alta = ".quote($filtro['fecha_alta']);
		}
		if (isset($filtro['usuario_alta'])) {
			$where[] = "t_p.usuario_alta ILIKE ".quote("%{$filtro['usuario_alta']}%");
		}
		if (isset($filtro['usuario_cierre'])) {
			$where[] = "t_p.usuario_cierre ILIKE ".quote("%{$filtro['usuario_cierre']}%");
		}
		if (isset($filtro['usuario_medico'])) {
			$where[] = "t_p.usuario_medico = ".quote($filtro['usuario_medico']);
		}
		if (isset($filtro['matricula'])) {
			$where[] = "t_p.matricula = ".quote($filtro['matricula']);
		}
		if (isset($filtro['id_decreto'])) {
			$where[] = "t_p.id_decreto = ".quote($filtro['id_decreto']);
		}
		if (isset($filtro['id_articulo'])) {
			$where[] = "t_p.id_articulo = ".quote($filtro['id_articulo']);
		}
		if (isset($filtro['id_motivo'])) {
			$where[] = "t_p.id_motivo = ".quote($filtro['id_motivo']);
		}    
		/*if (isset($filtro['cod_depcia'])) {
			$where[] = "t_p.cod_depcia = ".quote($filtro['cod_depcia']);
		}*/
		if (isset($filtro['agrupamiento'])) {
			$where[] = "t_p.agrupamiento = ".quote($filtro['agrupamiento']);
		}    
		if (isset($filtro['estado'])) {
			$where[] = "t_p.estado = ".quote($filtro['estado']);
		}    
		if (isset($filtro['id_diagnostico'])) {
			$where[] = "t_p.id_diagnostico = ".quote($filtro['id_diagnostico']);
		}    

		if (isset($filtro['eliminados']) and $filtro['eliminados'] == 1) {
			$where[] = "t_p.estado = 'E'";
		}else{
			$where[] = "t_p.estado <> 'E'";
		}

		if (isset($filtro['cierres_de_oficio']) and $filtro['cierres_de_oficio'] == 1) {
			$where[] = "t_p.observaciones_cierre = 'Cierre de oficio'";
		}

		if (isset($filtro['omite_cierre_automatico'])) {
			$where[] = "t_p.omite_cierre_automatico = ".quote($filtro['omite_cierre_automatico']);
		}    

		if (isset($filtro['fecha_desde'])) {
				list($y,$m,$d)=explode("-",$filtro['fecha_desde']); //2011-03-31
				$fecha_desde = $y."-".$m."-".$d;
				$where[] = "t_p.fecha_alta >= ".quote($fecha_desde);
		}
		if (isset($filtro['fecha_hasta'])) {
				list($y,$m,$d)=explode("-",$filtro['fecha_hasta']); //2011-03-31
				
				$fecha_hasta = $y."-".$m."-".$d." 23:59:59";
				$where[] = "t_p.fecha_alta <= ".quote($fecha_hasta);
		}

		if (isset($filtro['anio'])) {
			
			$y = $filtro['anio'];
			$m = $filtro['mes'];
			$d = date("d",(mktime(0,0,0,$m+1,1,$y)-1));

			$fecha_desde = $y."-".$m."-01";
			$fecha_hasta = $y."-".$m."-".$d." 23:59:59";
			$fecha_hasta_simple = $y."-".$m."-".$d;
		
			$where[] = "t_p.fecha_inicio_licencia <= ".quote($fecha_hasta);
		}

		if (isset($filtro['tipo_sexo'])) {
			$where[] = "t_p.tipo_sexo = ".quote($filtro['tipo_sexo']);
		}

		$sql = "SELECT count(t_p.id_parte) as cantidad
				FROM parte as t_p";
		if (count($where)>0) {
			$sql = sql_concatenar_where($sql, $where);
		}

		$datos = toba::db('sanidad')->consultar_fila($sql);
		
		return $datos['cantidad'];
	}

	function get_parte($id_parte){
		$filtro['id_parte'] = $id_parte;
		$classname = 'vistas_sanidad';
		$datos = $this->get_listado($filtro);
		return $datos[0];
	}
	
	//APEX_USUARIO -------------------------------------------
	
	function get_nombre_usuario($usuario) {
		$sql = "SELECT usuario, nombre FROM uncu.apex_usuario WHERE usuario = '$usuario'";//desarrollo.apex_usuario
		$dato = toba::db('toba_sanidad')->consultar_fila($sql);
		if(!empty($dato['nombre'])){
			return $dato['nombre'];
		}else{
			return $usuario;
		}      
	}
	
	//DIAGNOSITO  -------------------------------------------
	function get_diagnostico($id_diagnostico)
	{
		$sql = "SELECT id_diagnostico, descripcion, descripcion as diagnostico FROM diagnostico WHERE id_diagnostico = '$id_diagnostico' ORDER BY descripcion";
		return toba::db('sanidad')->consultar_fila($sql);
	}    
}
?>