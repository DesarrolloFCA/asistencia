<?php
class dt_parte extends toba_datos_tabla
{

	function get_listado($filtro=array())
	{
		$where = array();
		if (isset($filtro['id_parte'])) {
			$where[] = "t_p.id_parte = ".quote($filtro['id_parte']);
		}
		if (isset($filtro['legajo'])) {
			$where[] = "t_p.legajo = ".quote($filtro['legajo']);
		}
		if (isset($filtro['fecha_alta'])) {
			$where[] = "t_p.fecha_alta = ".quote($filtro['fecha_alta']);
		}
		if (isset($filtro['fecha_inicio_licencia_desde'])) {
			$where[] = "t_p.fecha_inicio_licencia >= ".quote($filtro['fecha_inicio_licencia_desde']);
		}
		if (isset($filtro['fecha_inicio_licencia_hasta'])) {
			$where[] = "t_p.fecha_inicio_licencia <= ".quote($filtro['fecha_inicio_licencia_hasta']);
		}
		if (isset($filtro['usuario_alta'])) {
			$where[] = "t_p.usuario_alta ILIKE ".quote("%{$filtro['usuario_alta']}%");
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

		// Integracion con sanidad
		if (isset($filtro['id_parte_sanidad'])) {
			$where[] = "t_p.id_parte_sanidad = ".quote($filtro['id_parte_sanidad']);
		}
		// Integracion con sanidad
		if (!($filtro['con_sanidad'] == 1)) {
			$where[] = "t_p.id_parte_sanidad IS NULL";
		}    
		
		if (isset($filtro['cod_depcia'])) {
			#$where[] = "t_p.cod_depcia = ".quote($filtro['cod_depcia']);
				
			if (isset($filtro['adscripcion']) and $filtro['adscripcion'] == 1) {

				$tiene_adscripcion = false;
				//---incluir adscripciones a la dependencia
				$sql = "SELECT legajo, cod_depcia_origen, fecha_inicio, cod_depcia_destino 
						FROM reloj.adscripcion 
						WHERE cod_depcia_destino = '".$filtro['cod_depcia']."' 
						AND fecha_inicio <= '".date("Y-m-d")."' 
						AND fecha_fin is null";
				$legajos1 =  toba::db('ctrl_asis')->consultar($sql); 
				if (count($legajos1)>0) {
					foreach($legajos1 as $k=> $legajo){
						if($k==0){
							$legajos_incluir = $legajo['legajo'];
						}else{
							$legajos_incluir.= ",".$legajo['legajo'];
						}        
					}
					$where[] = "(t_p.cod_depcia = '".$filtro['cod_depcia']."' OR t_p.legajo IN ($legajos_incluir) )";
					$tiene_adscripcion = true;
				}

				//---excluir adscripciones a otras dependencias
				$sql = "SELECT legajo, cod_depcia_origen, fecha_inicio, cod_depcia_destino 
						FROM reloj.adscripcion 
						WHERE cod_depcia_origen = '".$filtro['cod_depcia']."' 
						AND fecha_inicio <= '".date("Y-m-d")."' 
						AND fecha_fin is null";
				$legajos2 =  toba::db('ctrl_asis')->consultar($sql); 
				if (count($legajos2)>0) {
					foreach($legajos2 as $k=> $legajo){
						if($k==0){
							$legajos_omitir = $legajo['legajo'];
						}else{
							$legajos_omitir.= ",".$legajo['legajo'];
						}        
					}
					$where[] = "(t_p.cod_depcia = '".$filtro['cod_depcia']."' OR t_p.legajo NOT IN ($legajos_omitir) )";
					$tiene_adscripcion = true;
				}

				if ($tiene_adscripcion == false) {
					$where[] = "t_p.cod_depcia = '".$filtro['cod_depcia']."'";
				}

			}else{

				$where[] = "t_p.cod_depcia = '".$filtro['cod_depcia']."'";
			}

		}elseif(!empty($_SESSION['dependencia'])){

			if($_SESSION['dependencia'] == 53){ //53 Secretaria de gestión económica
				
				/* 
				01 Rectorado
				15 Organismos artisiticos
				21 Direccion de mantenimiento
				22 Dirección General económico financiero
				24 Deportes
				25 Direccion gral de medicina laboral
				27 ECT
				28 Bibliotecta
				29 Direccion de obras
				31 Jardines maternales
				32 Secretaria extensión
				33 Secretaria de bienestar
				34 jardín maternal semillita
				35 CICUNC
				37 orientación vocacional
				42 educación a distancia
				43 Coordinación económica financiera
				44 Secretaria Academica
				46 representación buenos aires
				50 Dirección general administrativa
				53 Secretaria de gestión económica
				57 Sec. relaciones institucionales
				60 Secretaria de Relaciones Internacionales
				68 Secretaria de Desarrollo Institucional
				92 Sec. de Ciencia Técnica y posgrado
				94 Fundar
				*/
				$where[] = "t_p.cod_depcia IN ('01','15','21','22','24','25','27','28','29','31','32','33','34','35','37','42','43','44','46','50','53','57','60','68','92','94')";

			}elseif($_SESSION['dependencia'] == 32){ 
				$where[] = "t_p.cod_depcia IN ('15','32')"; 

			}elseif($_SESSION['dependencia'] == 33){ 
				$where[] = "t_p.cod_depcia IN ('31','33','34')"; 

			}else{
				$where[] = "t_p.cod_depcia = '".$_SESSION['dependencia']."'";
			}
		}

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
		}elseif(isset($filtro['eliminados']) and $filtro['eliminados'] == 2){
			//Trea los partes con cualquier estado
		}
		else{
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
			if (isset($filtro['parte_anio'])) {
				$where[] = "t_p_a.anio = ".quote($filtro['parte_anio']);
			}else{
				$where[] = "t_p.fecha_inicio_licencia <= ".quote($fecha_hasta);
			}
		}

		if (isset($filtro['tipo_sexo'])) {
			$where[] = "t_p.tipo_sexo = ".quote($filtro['tipo_sexo']);
		}
		
		$sql = "SELECT 
			t_p.id_parte,
			t_p.legajo,
			t_p.edad,
			t_p.fecha_alta,
			t_p.usuario_alta,
			t_p.estado,
			t_p.fecha_inicio_licencia,
			t_p.dias,t_p.dias2,t_p.dias3,t_p.dias4,t_p.dias5,t_p.dias6,t_p.dias7,t_p.dias8,t_p.dias9,
			t_p.cod_depcia,    
			t_p.domicilio,
			t_p.localidad,
			t_p.agrupamiento,     
			t_p.fecha_nacimiento, t_p.apellido, t_p.nombre, t_p.estado_civil,
			t_p.apellido||', '||t_p.nombre as nombre_completo,
			t_p.observaciones,            
			t_p.id_decreto,            
			t_d.descripcion as decreto,
			t_p.id_motivo,
			t_m.descripcion as motivo,
			t_p.id_articulo,
			t_a.descripcion as articulo,
			t_p_a.anio as anio_parte,
			
			t_p.observaciones_cierre, t_p.fecha_cierre, t_p.usuario_cierre, 
				t_p.fecha_baja, t_p.usuario_baja,
				t_p.tipo_sexo 
			
		FROM
			parte as t_p    
			LEFT OUTER JOIN decreto as t_d ON (t_p.id_decreto = t_d.id_decreto)
			LEFT OUTER JOIN articulo as t_a ON (t_p.id_articulo = t_a.id_articulo)
			LEFT OUTER JOIN motivo as t_m ON (t_p.id_motivo = t_m.id_motivo)
			LEFT OUTER JOIN parte_anio t_p_a ON (t_p.id_parte = t_p_a.id_parte)
		ORDER BY t_p.id_parte DESC"; 
		//t_p.t_manana, t_p.t_tarde, t_p.t_noche, t_p.usuario_medico,
		if (count($where)>0) {
			$sql = sql_concatenar_where($sql, $where);
		}
		$datos = toba::db('ctrl_asis')->consultar($sql);
		if (count($datos) > 0) {

			foreach($datos as $key=>$dato){
				if( !empty($dato['fecha_inicio_licencia'])){

					$dias_calculo = $dato['dias'] - 1;
					$dias = '+'.$dias_calculo.' day';
					$datos[$key]['fecha_fin_licencia']    = date ( 'Y-m-d' , strtotime ( $dias , strtotime ( $dato['fecha_inicio_licencia'] ) )  ); //sumamos N dias a la fecha de inicio licencia
					$datos[$key]['descripcion_dias'] = "d�a/s desde el";
				}

				//Se agrega anio del parte en cuestion en caso de tenerlo
				if(!empty($dato['anio_parte'])){
					$datos[$key]['motivo'] = $dato['motivo'] . " (" . $dato['anio_parte'] . ")";
				}
			}

			if (isset($filtro['anio']) && !isset($filtro['no_anio_fechas'])) {

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

	function get_parte($id_parte){
		$filtro['id_parte'] = $id_parte;
		$datos = $this->get_listado($filtro);
		return $datos[0];
	}


	function set_estado($id_parte, $estado){
		
		if($estado == 'E'){  //es eliminacion
			$fecha_baja   = date("Y-m-d H:i:s");
			$usuario_baja = toba::usuario()->get_id();
			$sql = "UPDATE parte SET estado = '$estado', usuario_baja = '$usuario_baja', fecha_baja = '$fecha_baja' WHERE id_parte = '$id_parte' ";
		}else{
			$sql = "UPDATE parte SET estado = '$estado' WHERE id_parte = '$id_parte' ";
		}

		return toba::db('ctrl_asis')->ejecutar($sql);

	}

	function cerrar_partes_vencidos($dias = '-7 day'){

		$filtro['estado']         = 'A';
		$filtro['fecha_hasta']     = date ( 'Y-m-d' , strtotime ( $dias , strtotime ( date('Y-m-d') ) )  ); //restamos N dias a la fecha de hoy
		$filtro['omite_cierre_automatico'] = 0;

		$partes = $this->get_listado($filtro);
		
		if(count($partes)>0){

			$estado                 = 'C';                      //Cerrado
			$dias                     = 1;                        //Predeterminado
			$usuario_medico          = 'dr_dipasquale';            //Dipascuale
			$usuario_cierre         = @toba::usuario()->get_id();    
			if(empty($usuario_cierre)){ $usuario_cierre = 'toba'; }                
			$fecha_cierre           = date("Y-m-d H:i:s"); 
			$observaciones_cierre     = 'Cierre de oficio';  
			$id_decreto                = 3;                         //Ordenanza 60/84 C.S.

			foreach($partes as $parte){

				$id_motivo                = $parte['id_motivo'];    //viene del alta
				$articulo                 = toba::tabla('articulo')->get_descripciones_motivo_decreto($id_motivo,$id_decreto);
				
				if(count($articulo)>0){ 
					$id_articulo = $articulo[0]['id_articulo']; 
					
					$sql = "UPDATE parte SET 
							estado                     = '$estado', 
							dias                     = '$dias',
							id_motivo                 = '$id_motivo',
							id_decreto                 = '$id_decreto',
							id_articulo             = '$id_articulo', 
							usuario_medico             = '$usuario_medico',
							usuario_cierre           = '$usuario_cierre', 
							fecha_cierre             = '$fecha_cierre',
							observaciones_cierre     = '$observaciones_cierre'
							WHERE id_parte = '".$parte['id_parte']."'";


				}else{

					$sql = "UPDATE parte SET 
							estado                     = '$estado', 
							dias                     = '$dias',
							id_motivo                 = '$id_motivo',
							id_decreto                 = '$id_decreto',
							usuario_medico             = '$usuario_medico',
							usuario_cierre           = '$usuario_cierre', 
							fecha_cierre             = '$fecha_cierre',
							observaciones_cierre     = '$observaciones_cierre'
							WHERE id_parte = '".$parte['id_parte']."'";

				}   

				if(toba::db('ctrl_asis')->ejecutar($sql)){
					toba::notificacion()->agregar('Parte de inasistencia '.$parte['id_parte'].' cerrado/levantado correctamente.', 'info');
				}else{
					toba::notificacion()->agregar('Error al intentar cerrar parte '.$parte['id_parte'].'. Contacte al administrador.', 'error');
				}                

			}

			
		}else{

			list($y,$m,$d)=explode("-",$filtro['fecha_hasta']); //2011-03-31
			$fecha_hasta = $d."/".$m."/".$y;

			toba::notificacion()->agregar("No hay partes de inasistencia Abiertos y anteriores al $fecha_hasta", 'info');
		}

	}

	function get_certificado($id_parte)
	{
		$sql = "SELECT certificado FROM parte WHERE id_parte = '$id_parte'";
		return toba::db('ctrl_asis')->consultar_fila($sql);
	}

	function get_parte_anio($id_parte){
		$sql = "SELECT anio FROM parte_anio WHERE id_parte = '$id_parte'";
		return toba::db('ctrl_asis')->consultar_fila($sql);
	}    


	function tiene_parte($legajo, $dia){

		$where = array();

		$where[] = "t_p.estado = 'C'";
		$where[] = "t_p.legajo = ".quote($legajo);

		#$where[] = "t_p.fecha_alta >= ".quote($dia);
			
		$fecha_desde = $filtro['fecha_licencia'];
		$fecha_hasta = $dia." 23:59:59";
		$where[] = "t_p.fecha_inicio_licencia <= ".quote($fecha_hasta);

		$sql = "SELECT t_p.id_parte, t_p.legajo, t_p.fecha_inicio_licencia, t_p.dias
				FROM parte as t_p    
			LEFT OUTER JOIN decreto as t_d ON (t_p.id_decreto = t_d.id_decreto)
			LEFT OUTER JOIN articulo as t_a ON (t_p.id_articulo = t_a.id_articulo)
			LEFT OUTER JOIN motivo as t_m ON (t_p.id_motivo = t_m.id_motivo)
		ORDER BY t_p.id_parte DESC";
		if (count($where)>0) {
			$sql = sql_concatenar_where($sql, $where);
		}

		$datos = toba::db('ctrl_asis')->consultar($sql);
		
		
		/*if($legajo == '32009' and $dia == '2015-09-21'){
		echo $sql;    
		}*/


		if(count($datos)>0){

			foreach($datos as $dato) {

				$dias_calculo = $dato['dias'] - 1;
				$dias = '+'.$dias_calculo.' day';
				$fecha_fin_licencia  = date( 'Y-m-d' , strtotime ( $dias , strtotime ( $dato['fecha_inicio_licencia'] ) )  ); //sumamos N dias a la fecha de inicio 

				if($fecha_fin_licencia >= $dia){ //si la fecha de finalizacion de la licencia es = > a la de referencia, es que tiene parte
					
					/*if($legajo == '32009' and $dia == '2015-09-21'){
						ei_arbol($dato,'parte');
					}*/

					return $dato['id_parte'];// true;
				//}else{
				//    return false;
				}    
			}
		}


		return false;

	}


	//=============================================================
	//======================== SANIDAD ============================
	//=============================================================


	function tiene_parte_sanidad($legajo, $dia){

		$where = array();

		$where[] = "t_p.estado = 'C'";
		$where[] = "t_p.legajo = ".quote($legajo);

		#$where[] = "t_p.fecha_alta >= ".quote($dia);
			
		$fecha_desde = $filtro['fecha_licencia'];
		$fecha_hasta = $dia." 23:59:59";
		$where[] = "t_p.fecha_inicio_licencia <= ".quote($fecha_hasta);

		$sql = "SELECT t_p.id_parte, t_p.legajo, t_p.fecha_inicio_licencia,    t_p.dias
				FROM parte as t_p    
			LEFT OUTER JOIN decreto as t_d ON (t_p.id_decreto = t_d.id_decreto)
			LEFT OUTER JOIN articulo as t_a ON (t_p.id_articulo = t_a.id_articulo)
			LEFT OUTER JOIN motivo as t_m ON (t_p.id_motivo = t_m.id_motivo)
		ORDER BY t_p.id_parte DESC";
		if (count($where)>0) {
			$sql = sql_concatenar_where($sql, $where);
		}


		$datos = toba::db('sanidad')->consultar($sql);
		

		if(count($datos)>0){

			foreach($datos as $dato) {

				$dias_calculo = $dato['dias'] - 1;
				$dias = '+'.$dias_calculo.' day';
				$fecha_fin_licencia  = date( 'Y-m-d' , strtotime ( $dias , strtotime ( $dato['fecha_inicio_licencia'] ) )  ); //sumamos N dias a la fecha de inicio 

				if($fecha_fin_licencia >= $dia){ //si la fecha de finalizacion de la licencia es = > a la de referencia, es que tiene parte
					
					/*if($legajo == '32009' and $dia == '2015-09-21'){
						ei_arbol($dato,'parte');
					}*/

					return $dato['id_parte'];// true;
				//}else{
				//    return false;
				}    
			}
		}    

		return false;    

	}
	
	function get_parte_sanidad($id_parte){


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
			t_p.fecha_baja, t_p.usuario_baja,
			t_p.tipo_sexo,
			t_p.observaciones_cierre, t_p.fecha_cierre, t_p.usuario_cierre
			
		FROM
			parte as t_p    
			LEFT OUTER JOIN decreto as t_d ON (t_p.id_decreto = t_d.id_decreto)
			LEFT OUTER JOIN articulo as t_a ON (t_p.id_articulo = t_a.id_articulo)
			LEFT OUTER JOIN motivo as t_m ON (t_p.id_motivo = t_m.id_motivo)
		WHERE t_p.id_parte = '$id_parte'";

		return  toba::db('sanidad')->consultar_fila($sql);
	}

	function get_descripciones()
	{
		$sql = "SELECT id_parte, nombre FROM parte ORDER BY nombre";
		return toba::db('ctrl_asis')->consultar($sql);
	}

	function get_parte_por_id_parte_sanidad($id_parte_sanidad){
		$filtro['id_parte_sanidad'] = $id_parte_sanidad;
		$filtro['con_sanidad'] = 1;
		$filtro["eliminados"] = 2; // filtra partes con cualquier estado actuals
		$datos = $this->get_listado($filtro);
		return $datos[0];
	}

	// Guarda partes de sanidad de acuerdo a la estructura interna de los partes de asistencia
	function guardar_parte_desde_sanidad($parte_sanidad_datos){
		$id_parte_sanidad = $parte_sanidad_datos['id_parte'];
		$legajo = $parte_sanidad_datos['legajo'];
		$edad = $parte_sanidad_datos['edad'];
		$fecha_alta = $parte_sanidad_datos['fecha_alta'];
		$usuario_alta = $parte_sanidad_datos['usuario_alta'];
		$estado = $parte_sanidad_datos['estado'];
		$fecha_inicio_licencia = $parte_sanidad_datos['fecha_inicio_licencia'];
		$dias = $parte_sanidad_datos['dias'];
		$cod_depcia = $parte_sanidad_datos['cod_depcia'];
		$domicilio =str_replace("'","`",  addslashes(iconv('ASCII', 'UTF-8//IGNORE', $parte_sanidad_datos['domicilio'])));
		$localidad =str_replace("'","`",  addslashes(iconv('ASCII', 'UTF-8//IGNORE', $parte_sanidad_datos['localidad'])));
		$agrupamiento = $parte_sanidad_datos['agrupamiento'];
		$fecha_nacimiento = $parte_sanidad_datos['fecha_nacimiento'];
		$apellido = str_replace("'","`",  addslashes(iconv('ASCII', 'UTF-8//IGNORE', $parte_sanidad_datos['apellido'])));
		$nombre =str_replace("'","`",  addslashes(iconv('ASCII', 'UTF-8//IGNORE', $parte_sanidad_datos['nombre'])));
		$estado_civil = $parte_sanidad_datos['estado_civil'];
		$observaciones =str_replace("'","`",  addslashes(iconv('ASCII', 'UTF-8//IGNORE', $parte_sanidad_datos['observaciones'])));
		$id_motivo = $this->get_id_motivo_desde_sanidad($parte_sanidad_datos['id_motivo']);
		$id_decreto = $this->get_id_decreto_desde_sanidad($parte_sanidad_datos['id_decreto']);
		$id_articulo = $this->get_id_articulo_por_motivo_y_decreto($id_motivo, $id_decreto);
		$fecha_baja = $parte_sanidad_datos['fecha_baja'];
		$usuario_baja = $parte_sanidad_datos['usuario_baja'];
		$tipo_sexo = $parte_sanidad_datos['tipo_sexo'];
		$observaciones_cierre =str_replace("'","`",  addslashes(iconv('ASCII', 'UTF-8//IGNORE', $parte_sanidad_datos['observaciones_cierre'])));
		$fecha_cierre = isset($parte_sanidad_datos['fecha_cierre']) ? $parte_sanidad_datos['fecha_cierre'] : $fecha_alta;
		$usuario_cierre = $parte_sanidad_datos['usuario_cierre'];
		$parte = $this->get_parte_por_id_parte_sanidad($id_parte_sanidad);
		if (isset($parte) and $parte['id_parte'] != null){
			//actualizar parte
			$sql = "UPDATE parte
			SET edad = '$edad', legajo = '$legajo',estado = '$estado', fecha_inicio_licencia = '$fecha_inicio_licencia', 
			dias = '$dias', cod_depcia = '$cod_depcia', domicilio = '$domicilio', localidad = '$localidad',
			agrupamiento = '$agrupamiento',
			fecha_nacimiento = '$fecha_nacimiento', apellido = '$apellido', nombre = '$nombre', estado_civil = '$estado_civil', observaciones = '$observaciones',
			id_motivo = '$id_motivo', id_decreto = '$id_decreto', id_articulo = '$id_articulo', 
			tipo_sexo = '$tipo_sexo', observaciones_cierre = '$observaciones_cierre', fecha_cierre = '$fecha_cierre', usuario_cierre = '$usuario_cierre'
			WHERE id_parte_sanidad = '$id_parte_sanidad'";
		}
		else{
			//crear parte
			$sql = "INSERT INTO parte (id_parte, id_parte_sanidad, legajo, edad, fecha_alta, 
			usuario_alta, estado, fecha_inicio_licencia, dias,cod_depcia, domicilio, localidad, agrupamiento,
			fecha_nacimiento, apellido, nombre, estado_civil,
			observaciones, id_decreto, id_motivo, id_articulo, tipo_sexo, 
			observaciones_cierre, fecha_cierre, usuario_cierre) 
			VALUES (DEFAULT, '$id_parte_sanidad', '$legajo', '$edad', '$fecha_alta', '$usuario_alta', '$estado',
				'$fecha_inicio_licencia', '$dias', '$cod_depcia', '$domicilio', '$localidad', '$agrupamiento',
				'$fecha_nacimiento', '$apellido', '$nombre', '$estado_civil', '$observaciones', 
				'$id_decreto', '$id_motivo', '$id_articulo', '$tipo_sexo', '$observaciones_cierre', '$fecha_cierre', '$usuario_cierre')";
			}
		return toba::db('ctrl_asis')->consultar($sql);
	}

	private function get_id_motivo_desde_sanidad($id_motivo_sanidad){
		$id_motivo = null;
		switch ($id_motivo_sanidad) {
			case 11:
				$id_motivo = 45;
				break;			
			default:
				//1,2,3,4,5,6
				$id_motivo = $id_motivo_sanidad;
				break;
		}
		return $id_motivo;
	}

	private function get_id_decreto_desde_sanidad($id_decreto_sanidad){
		$id_decreto = null;
		switch ($id_decreto_sanidad) {
			case 7:
				$id_decreto = 8;
				break;			
			default:
				//2,3,4,5
				$id_decreto = $id_decreto_sanidad;
				break;
		}
		return $id_decreto;
	}

	private function get_id_articulo_por_motivo_y_decreto($id_motivo, $id_decreto){
		$filtro['id_motivo'] = $id_motivo;
		$filtro['id_decreto'] = $id_decreto;
		$articulo = toba::componente('dt_articulo')->get_listado($filtro)[0];
		return $articulo['id_articulo'];
	}

}
?>