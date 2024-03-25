<?php
class dt_parte extends toba_datos_tabla
{

	function get_listado_vaca($filtro=array())
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
			
				if (isset($filtro['anio'])) {
				$where[] = "fecha_inicio_licencia between '$anio_anterior-12-01' AND '$anio-11-30' ";
				}	

			}
		}
		
		$sql = "SELECT
			sum(t_p.dias)
		FROM
			parte as t_p";
		if (count($where)>0) {
			$sql = sql_concatenar_where($sql, $where);
		}
		//ei_arbol($sql);
		return toba::db('ctrl_asis')->consultar($sql);
	}	
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
			
				if (isset($filtro['anio'])) {
				$where[] = "fecha_inicio_licencia between '$anio_anterior-12-01' AND '$anio-11-30' ";
				}	

			}
		}
		

		if(isset($filtro['legajo'])){
			$legajo =$filtro['legajo'];
			$where[] = "legajo = $legajo";
		}
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

		return toba::db('ctrl_asis')->consultar($sql);
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
 //ei_arbol ($dia,$legajo);
		$where = array();

		$where[] = "t_p.estado = 'C'";
		$where[] = "t_p.legajo = ".quote($legajo);

		#$where[] = "t_p.fecha_alta >= ".quote($dia);
			
		$fecha_desde = $filtro['fecha_licencia'];
		$fecha_hasta = $dia." 23:59:59";
		$where[] = "t_p.fecha_inicio_licencia <= ".quote($fecha_hasta);

		$sql = "SELECT t_p.id_parte, t_p.legajo, t_p.fecha_inicio_licencia, t_p.dias,t_m.descripcion
			FROM parte as t_p    
			LEFT OUTER JOIN decreto as t_d ON (t_p.id_decreto = t_d.id_decreto)
			LEFT OUTER JOIN articulo as t_a ON (t_p.id_articulo = t_a.id_articulo)
			LEFT OUTER JOIN motivo as t_m ON (t_p.id_motivo = t_m.id_motivo)
		ORDER BY t_p.id_parte DESC";
		if (count($where)>0) {
			$sql = sql_concatenar_where($sql, $where);
		}
		//ei_arbol($sql);
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
// Vacaciones



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

	function get_listado_fca($filtro=array())
	{
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
			t_p.id_parte_sanidad
		FROM
			parte as t_p	LEFT OUTER JOIN decreto as t_d ON (t_p.id_decreto = t_d.id_decreto)
			LEFT OUTER JOIN motivo as t_m ON (t_p.id_motivo = t_m.id_motivo)
			where cast(fecha_inicio_licencia as date) + cast( dias || ' days' as interval) >= cast(Current_timestamp as date)
			And t_p.id_motivo <> 56
			and estado ='A'
			ORDER BY fecha_inicio_licencia";
		if (count($where)>0) {
			$sql = sql_concatenar_where($sql, $where);
		}
		return toba::db('ctrl_asis')->consultar($sql);
	}

}
?>