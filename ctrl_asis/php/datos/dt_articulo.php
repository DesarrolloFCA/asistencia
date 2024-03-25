<?php
class dt_articulo extends toba_datos_tabla
{
	function get_listado($filtro=array())
	{
		$where = array();
		if (isset($filtro['descripcion'])) {
			$where[] = "t_a.descripcion ILIKE ".quote("%{$filtro['descripcion']}%");
		}
		if (isset($filtro['id_decreto'])) {
			$where[] = "t_a.id_decreto = ".quote($filtro['id_decreto']);
		}
		if (isset($filtro['id_motivo'])) {
			$where[] = "t_a.id_motivo = ".quote($filtro['id_motivo']);
		}        
		$sql = "SELECT
			t_a.id_articulo,
			t_a.descripcion,
			t_a.id_decreto,
			t_d.descripcion as decreto,
			t_a.id_motivo,
			t_m.descripcion as motivo,
			t_a.dias_disponibles, t_a.limite_mensual, 
			t_a.observaciones 
		FROM
			articulo as t_a 
			LEFT OUTER JOIN motivo as t_m ON (t_m.id_motivo = t_a.id_motivo),
			decreto as t_d
		WHERE
				t_a.id_decreto = t_d.id_decreto
		ORDER BY t_d.descripcion ASC, t_a.descripcion ASC";
		if (count($where)>0) {
			$sql = sql_concatenar_where($sql, $where);
		}
		return toba::db('ctrl_asis')->consultar($sql);
	}

	function get_descripciones()
	{
		$sql = "SELECT id_articulo, descripcion FROM articulo ORDER BY descripcion";
		return toba::db('ctrl_asis')->consultar($sql);
	}





	function get_articulo($id_articulo)
	{
		$sql = "SELECT id_articulo, id_decreto, descripcion, dias_disponibles FROM articulo WHERE id_articulo = '$id_articulo' ";
		return toba::db('ctrl_asis')->consultar($sql);
	}

	function get_descripciones_decreto($id_decreto)
	{
		$sql = "SELECT id_articulo, descripcion, dias_disponibles FROM articulo WHERE id_decreto = '$id_decreto' ORDER BY descripcion";
		return toba::db('ctrl_asis')->consultar($sql);
	}

	function get_descripciones_motivo_decreto($id_motivo, $id_decreto)
	{
		$sql = "SELECT id_articulo, descripcion, dias_disponibles FROM articulo 
		WHERE id_decreto = '$id_decreto' AND id_motivo = '$id_motivo' 
		ORDER BY descripcion";
		return toba::db('ctrl_asis')->consultar($sql);
	}

	function get_descripcion($id_articulo)
	{
		$sql = "SELECT id_articulo, descripcion, dias_disponibles FROM articulo WHERE id_articulo = '$id_articulo'";
		$dato = toba::db('ctrl_asis')->consultar_fila($sql);
		return $dato['ctrl_asis'];
	}

	function get_observaciones_articulo($id_articulo)
	{
		$sql = "SELECT id_articulo, observaciones FROM articulo WHERE id_articulo = '$id_articulo'";
		$dato = toba::db('ctrl_asis')->consultar_fila($sql);
		return $dato['observaciones'];
	}

	function get_dias($legajo, $agrupamiento, $fecha_inicio_licencia, $id_articulo, $anio)
	{
		
		if ($id_articulo == 55 or $id_articulo == 56){
			$sql = "SELECT  dias dias_disponibles
					FROM reloj.antiguedad
					WHERE legajo = $legajo;";
			$dato = toba::db('ctrl_asis')->consultar_fila($sql);
		
		} else{


		$sql = "SELECT id_articulo, id_motivo, descripcion, dias_disponibles, limite_mensual FROM reloj.articulo WHERE id_articulo = '$id_articulo'";
		$dato = toba::db('ctrl_asis')->consultar_fila($sql);
		}
		$dias_articulo  = $dato['dias_disponibles'];
		$limite_mensual = $dato['limite_mensual'];
		list($anio_lic,$mes_lic,$dia_lic) = explode('-', $fecha_inicio_licencia);
		
		if($dias_articulo > 0){

			//obtenemos dias tomados en lo que va del año---------------------------------------
			$dias_tomados = 0;
			$filtro['legajo']      = $legajo;
			$filtro['id_motivo']   = $dato['id_motivo'];
			$filtro['agrupamiento']= $agrupamiento;
			$filtro['anio']        = $anio_lic; //date("Y"); //ano actual
			for ($i=0; $i < date("m") ; $i++) {  // bucle en todos los meses hasta el mes actual
				$mes = $i+1;
				if($mes<10){
					$filtro['mes']  = "0".$mes;
				}else{
					$filtro['mes']  = $mes;
				}
				
				$partes = toba::tabla('parte')->get_listado($filtro);
				if(count($partes)>0){
					foreach ($partes as $parte) {
						$dias_tomados = $dias_tomados + $parte['dias_mes'];
					}
				}
			}

			$dias_disponibles = $dias_articulo - $dias_tomados;

			//obtenemos dias tomados en el mes ------------------------------------------------
			if($limite_mensual > 0){
				
				$dias_tomados_mes = 0;
				$filtro['mes']  = $mes_lic; //date("m");
				$partes = toba::tabla('parte')->get_listado($filtro);
				if(count($partes)>0){
					foreach ($partes as $parte) {
						$dias_tomados_mes = $dias_tomados_mes + $parte['dias_mes'];
					}
				}

				if($dias_disponibles >= $limite_mensual){
					$dias_disponibles = $limite_mensual - $dias_tomados_mes;
				}
			}

			//seteamos dias sisponibles ------------------------------------
			#$dias_disponibles = $dias_articulo - $dias_tomados;
			for ($i=0; $i < $dias_disponibles; $i++) { 
				$dia = $i + 1;
				$array[$i]['dias'] = $dia;
			}

			return $array;
		
		}else{ //revisar tratamiento interno

			switch ($dato['id_motivo']) {

				case '35': //Vacaciones - seteamos dias de vacaciones por antiguedad

					
					//seteamos fecha ingreso de tabla antiguedad --------------------------------
					$dato_antiguedad = toba::tabla('antiguedad')->get_antiguedad($legajo);
					
					if(!empty($dato_antiguedad['fecha_ingreso'])){
						$agente['fec_ingreso'] = $dato_antiguedad['fecha_ingreso'];
					}else{
						
						$sql = "SELECT fec_ingreso FROM uncu.legajo_cargos WHERE legajo = '$legajo' and agrupamiento = '$agrupamiento' ";
						$agente =  toba::db('mapuche')->consultar_fila($sql); 
					}    
					if(!empty($agente['fec_ingreso'])){

						//obtenemos dias por antiguedad ------------------------------
						$antiguedad = toba::tabla('vacaciones_antiguedad')->get_array_antiguedad($agente['fec_ingreso'],$agrupamiento, $anio);
						

						//obtenemos dias tomados---------------------------------------
						$dias_tomados = 0;
						list($anio_lic,$mes_lic,$dia_lic) = explode('-', $fecha_inicio_licencia);
						$filtro['legajo']      = $legajo;
						$filtro['id_motivo']   = $dato['id_motivo'];
						$filtro['agrupamiento']= $agrupamiento;
						$filtro['parte_anio']  = $anio; //ano seleccionado por vacaciones
						$filtro['anio']        = $anio_lic; //date("Y"); //ano actual
						/*for ($i=0; $i < date("m") ; $i++) {  // bucle en todos los meses hasta el mes actual
							$mes = $i+1;
							if($mes<10){
								$filtro['mes']  = "0".$mes;
							}else{
								$filtro['mes']  = $mes;
							}*/
							//ei_arbol($filtro);
							$partes = toba::tabla('parte')->get_listado_vaca($filtro);
							$lim=count($partes);
							if($lim>0){

								/*for($i=0;$i<=$lim;$i++){
									$dias_tomados = $dias_tomados + $partes[$i]['dias'];
								}
								/*foreach ($partes as $parte) {
									$dias_tomados = $dias_tomados + $parte['dias'];
								}*/
								//ei_arbol($partes);
								//ei_arbol($dias_tomados);
								$dias_tomados = $partes[0]['sum'];
							//}
						}
											
						$vacaciones_restantes = toba::tabla('vacaciones_restantes')->get_dias($legajo, $anio, $agrupamiento);
					//	ei_arbol($vacaciones_restantes);
						if (is_null($vacaciones_restantes)){
							$dias_disponibles = $antiguedad['dias'] - $dias_tomados;
						}else{
							$dias_disponibles = $vacaciones_restantes - $dias_tomados;
						}
						//seteamos dias sisponibles ------------------------------------

						for ($i=0; $i < $dias_disponibles; $i++) { 
							$dia = $i + 1;
							$array[$i]['dias'] = $dia;
						}

						return $array;

					}

					break;
			}
		}
	}

}
?>