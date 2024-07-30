<?php
//ini_set('memory_limit','4096M');
class vistas_access extends toba_datos_relacion
{

	/*
	En SQL server, las tablas comienzan con dbo. 

	CHECKINOUT.USERID   =>  USERINFO.USERID 
							USERINFO.Badgenumb => Numero con el que se identifican: legajo,dni,etc.
							USERINFO.name      => 
							USERINFO.privilege => 3 admin, 0 noraml
	CHECKINOUT.CHECKTIME => marca format dd/mm/aaaa hh:mm:ss a.m.
	CHECKINOUT.CHECKTYPE => I O #Error
	CHECKINOUT.VERIFYCODE => 1,3,15????
	CHECKINOUT.SENSORID  => NO es Sensor (Dispositivo > Dispositivo)
	CHECKINOUT.LOGID     => acc_monitor_log.ID
							acc_monitor_log.device_id => Machines.ID => id del despositivo reloj
															Machines.MachineAlias => Nombre descriptivo
															Machines.area_id=> personnel_area.id
																			personnel_area.areaid (35)
																			personnel_area.areaname (C.I.C.U.N.C)
	*/

	static function get_vista_access($filtro=array())
	{
		$where = array();

		if (isset($filtro['fecha'])) {
			$where[] = " CONVERT(varchar(10), C.CHECKTIME, 120) = '".$filtro['fecha']."'";
		}

		if (isset($filtro['sin_errores'])) {
			$where[] = "C.CHECKTYPE <> '#Error'";
		}
		if (isset($filtro['fecha_desde'])) {
				list($y,$m,$d)=explode("-",$filtro['fecha_desde']); //2011-03-31
				$fecha_desde = $y."-".$m."-".$d;
				$where[] = "CONVERT(varchar(10), C.CHECKTIME, 120) >= ".quote($fecha_desde);
		}
		if (isset($filtro['fecha_hasta'])) {
				list($y,$m,$d)=explode("-",$filtro['fecha_hasta']); //2011-03-31
				$fecha_hasta = $y."-".$m."-".$d; //." 23:59:59";
				$where[] = "CONVERT(varchar(10), C.CHECKTIME, 120) <= ".quote($fecha_hasta);
		}

		if (isset($filtro['badgenumber'])) {
			//$where[] = "U.Badgenumber = ".quote($filtro['badgenumber']);
			$where[] = "U.Badgenumber = ".quote($filtro['badgenumber']);
		}        

		$sql = "SELECT 
		C.CHECKTYPE, 
		U.Badgenumber,
		U.NAME,
		CONVERT(varchar(10), C.CHECKTIME, 120) AS fecha, 
		CONVERT(varchar(19), C.CHECKTIME, 120) AS CHECKTIME,
		C.SENSORID as device_id,
		'access' as basedatos,
		C.USERID 
		FROM CHECKINOUT as C 
			LEFT OUTER JOIN
			USERINFO as U
		ON U.USERID = C.USERID 
		ORDER BY C.USERID,  C.CHECKTIME ASC";

		if (count($where)>0) {
			$sql = sql_concatenar_where($sql, $where);
		} 

		//-------------------------------------------
		$conf_access = file_get_contents('../php/datos/conf_access.txt');
		list($UID,$PWD,$DB,$HOST)=explode(',',$conf_access); //UID:sa,PWD:CitReloj2015,DB:access,HOST:CIT-RELOJ\ASISTENCIA
		$connectionInfo = array( "UID"=>$UID, "PWD"=>$PWD, "Database"=>$DB);
		$conn = sqlsrv_connect($HOST, $connectionInfo);

		if( $conn === false ){
		echo "78: No es posible conectarse al servidor.</br>";
		die( print_r( sqlsrv_errors(), true));
		}

		$result = sqlsrv_query($conn,$sql);
		
		if( $result === false ){
		echo "Error al ejecutar consulta.</br>";
		die( print_r( sqlsrv_errors(), true));
		}

		while ($row = sqlsrv_fetch_array($result)) {
			$array[] = $row;
		}

		sqlsrv_free_stmt($result);
		sqlsrv_close($conn);
		//ei_arbol($result);
		return $array;

	}

	static function get_CHECKINOUT($filtro=array())
	{
/*if($filtro['fecha'] == '2015-09-01' and ($filtro['badgenumber'] == '8049' or $filtro['badgenumber'] == '28813' ) ){
$inicio = microtime(true);   
}*/

		// access --------------------------------------------------
		if (!isset($filtro['basedatos']) or $filtro['basedatos']=='access') { 

		$where = array();

		if (isset($filtro['fecha'])) {
			$where[] = " CONVERT(varchar(10), C.CHECKTIME, 120) = '".$filtro['fecha']."'";
		}

		if (isset($filtro['sin_errores'])) {
			$where[] = "C.CHECKTYPE <> '#Error'";
		}
		if (isset($filtro['fecha_desde'])) {
				list($y,$m,$d)=explode("-",$filtro['fecha_desde']); //2011-03-31
				$fecha_desde = $y."-".$m."-".$d;
				$where[] = "CONVERT(varchar(10), C.CHECKTIME, 120) >= ".quote($fecha_desde);
		}
		if (isset($filtro['fecha_hasta'])) {
				list($y,$m,$d)=explode("-",$filtro['fecha_hasta']); //2011-03-31
				$fecha_hasta = $y."-".$m."-".$d; //." 23:59:59";
				$where[] = "CONVERT(varchar(10), C.CHECKTIME, 120) <= ".quote($fecha_hasta);
		}

		if (isset($filtro['badgenumber'])) {
			//$where[] = "U.Badgenumber = ".quote($filtro['badgenumber']);
			$where[] = "U.Badgenumber = ".quote($filtro['badgenumber']);
		}        

		#if (isset($filtro['id_dispositivo'])) {
		#    $where[] = "M.ID = ".quote($filtro['id_dispositivo']);
		#}
/*
		$sql = "SELECT C.USERID, 
		CONVERT(varchar(19), C.CHECKTIME, 120) AS CHECKTIME, 
		CONVERT(varchar(10), C.CHECKTIME, 120) AS fecha, 
		C.CHECKTYPE, C.VERIFYCODE, C.SENSORID, C.LOGID, C.MachineId, C.UserExtFmt, C.WorkCode, C.Memoinfo, C.sn,
		U.Badgenumber,U.name,U.privilege,L.device_id,
		M.ID as id_dispositivo,M.MachineAlias,M.area_id,A.areaid,A.areaname 
		FROM CHECKINOUT as C 
			LEFT OUTER JOIN acc_monitor_log as L ON (L.ID = C.LOGID) 
			LEFT OUTER JOIN Machines as M ON (M.ID = L.device_id )
			LEFT OUTER JOIN personnel_area as A ON (A.id = M.area_id ), 
			USERINFO as U
		WHERE U.USERID = C.USERID 
		ORDER BY C.USERID,  C.CHECKTIME ASC";
*/
		$sql = "SELECT 
		C.CHECKTYPE, 
		U.Badgenumber,
		U.NAME,
		CONVERT(varchar(10), C.CHECKTIME, 120) AS fecha, 
		CONVERT(varchar(19), C.CHECKTIME, 120) AS CHECKTIME, 
		C.SENSORID as device_id,
		'access' as basedatos,
		C.USERID 
		FROM CHECKINOUT as C 
			LEFT OUTER JOIN
			USERINFO as U
		ON U.USERID = C.USERID 
		ORDER BY U.Badgenumber, C.CHECKTIME ASC";

		if (count($where)>0) {
			$sql = sql_concatenar_where($sql, $where);
		} 
/*if($filtro['badgenumber'] == '32277' and $filtro['fecha'] == '2017-03-06'){

	file_put_contents('c:/temp/sqlaccess.txt', $sql);
}
*/


		//-------------------------------------------
		$conf_access = file_get_contents('../php/datos/conf_access.txt');
		list($UID,$PWD,$DB,$HOST)=explode(',',$conf_access); //UID:sa,PWD:CitReloj2015,DB:access,HOST:CIT-RELOJ\ASISTENCIA
		$connectionInfo = array( "UID"=>$UID, "PWD"=>$PWD, "Database"=>$DB);
		$conn = sqlsrv_connect($HOST, $connectionInfo);

		if( $conn === false ){
		//echo "No es posible conectarse al servidor.</br>";
		echo "179: No es posible conectarse al servidor.</br>";
		die( print_r( sqlsrv_errors(), true));
		}

		$result = sqlsrv_query($conn,$sql);
		
		if( $result === false ){
		echo "Error al ejecutar consulta.</br>";
		die( print_r( sqlsrv_errors(), true));
		}

		while ($row = sqlsrv_fetch_array($result)) {
			$array[] = $row;
		}

		sqlsrv_free_stmt($result);
		sqlsrv_close($conn);
		
		}

		//------------SQL HANDER--------------

		if (!isset($filtro['basedatos']) or $filtro['basedatos']=='hander') { 
		$where2 = array();

		if (isset($filtro['fecha'])) {
			$where2[] = " CONVERT(varchar(10), FechaHora, 120) = '".$filtro['fecha']."'";
		}

		if (isset($filtro['fecha_desde'])) {
				list($y,$m,$d)=explode("-",$filtro['fecha_desde']); //2011-03-31
				$fecha_desde = $y."-".$m."-".$d;
				$where2[] = "CONVERT(varchar(10), FechaHora, 120) >= ".quote($fecha_desde);
		}
		if (isset($filtro['fecha_hasta'])) {
				list($y,$m,$d)=explode("-",$filtro['fecha_hasta']); //2011-03-31
				$fecha_hasta = $y."-".$m."-".$d; //." 23:59:59";
				$where2[] = "CONVERT(varchar(10), FechaHora, 120) <= ".quote($fecha_hasta);
		}

		if (isset($filtro['badgenumber'])) {
			$where2[] = "Convert(Int, Tarjeta) = ".quote($filtro['badgenumber']);
		}        

		#if (isset($filtro['id_dispositivo'])) {
		#    $where2[] = "M.ID = ".quote($filtro['id_dispositivo']);
		#}

		/*$sql = "SELECT 
				Evento as CHECKTYPE,
				id as LOGID,
				CONVERT(Int, Tarjeta) as  Badgenumber,
				CONVERT(varchar(10), FechaHora, 120) as fecha,
				CONVERT(varchar(19), FechaHora, 120) as CHECKTIME,
				'hander' as  basedatos,
				Reloj as device_id 
				FROM Fichada as F 
				ORDER BY Tarjeta,  FechaHora ASC"; */
	
		$sql = "SELECT Evento as CHECKTYPE, id as LOGID,
				CONVERT(Int, Tarjeta) as  Badgenumber,
				CONVERT(varchar(10), FechaHora, 120) as fecha,
				CONVERT(varchar(19), FechaHora, 120) as CHECKTIME,
				'hander' as  basedatos,
				Reloj as device_id 
				FROM Fichada as F "; 


		if (count($where2)>0) {
			$sql = sql_concatenar_where($sql, $where2);
		}
/*if($filtro['fecha'] == '2015-09-01' and ($filtro['badgenumber'] == '8049' or $filtro['badgenumber'] == '28813' ) ){
echo $sql;  
} */
		//-------------------------------------------
		$conf_hander = file_get_contents('../php/datos/conf_hander.txt');
		list($UID,$PWD,$DB,$HOST)=explode(';',$conf_hander); //UID:sa,PWD:CitReloj2015,DB:access,HOST:CIT-RELOJ\ASISTENCIA
		$connectionInfo = array( "UID"=>$UID, "PWD"=>$PWD, "Database"=>$DB); //$connectionInfo = array( "UID"=>"citreloj", "PWD"=>"reloj2015", "Database"=>"Hander");
		$conn = sqlsrv_connect($HOST, $connectionInfo); //$conn = sqlsrv_connect( '172.22.32.27\SQLESPRESS,2523', $connectionInfo);

		if( $conn === false ){
		echo "260: No es posible conectarse al servidor.</br>";
		die( print_r( sqlsrv_errors(), true));
		}

		$result = sqlsrv_query($conn,$sql);
		
		if( $result === false ){
		echo "Error al ejecutar consulta.</br>";
		die( print_r( sqlsrv_errors(), true));
		}else{

			while ($row = sqlsrv_fetch_array($result)) {
				$array[] = $row;
			}

			sqlsrv_free_stmt($result);
			sqlsrv_close($conn); 
						
		}
		//-------------------------------------------
		}
/*
if($filtro['fecha'] == '2015-09-01' and ($filtro['badgenumber'] == '8049' or $filtro['badgenumber'] == '28813' ) ){
$final = microtime(true);
//echo "Final: ".memory_get_usage()." bytes <br>";
//echo "Peak: ".memory_get_peak_usage()." bytes <br>";
	$total = $final - $inicio;
echo 'Tiempo en ejecutar '.$agente['legajo'].' el script: '.$total.' segundos<br>';
}
*/

		return $array;

	}

	function get_marcas($filtro=array()){
		
		$filtro['sin_errores'] = true;
		$array = $this->get_CHECKINOUT($filtro);
		$datos = array();
		

/*if($filtro['badgenumber']=='17995'){

	foreach($array as $row){
		if($row['fecha'] == '2017-04-04'){
			ei_arbol($row);
			//break;
		}
	}
}*/


		if(count($array)>0){ 
			
			$cont = 0;
			
			
			//if($filtro['badgenumber']=='28983'){

				//logica sin entrada y salida ------------------------------------------------------
				$resto_entrada = 0; //0 impar, 1 par  
				foreach($array as $key=>$row){

					if ($key%2==$resto_entrada){ //Entrada

							//set entrada
							$datos[$cont]= array(
								'marca_id'    => $row['LOGID'],
									#'legajo'    => $row['Badgenumber'],
								'badgenumber' => $row['Badgenumber'],
								'fecha'       => $row['fecha'],
								'entrada'     => substr($row['CHECKTIME'],11,8),
									#'salida'    =>,
								'basedatos_i' => $row['basedatos'],
								'reloj_i'     => $row['device_id']
								);

					}else{ //Salida

						$cont_anterior = $cont - 1;
						$hora_anterior = str_replace(':', '', $datos[$cont]['entrada']);
						$hora_actual   = str_replace(':', '', substr($row['CHECKTIME'],11,8)  );
						$diferencia    = $hora_actual - $hora_anterior;

						/*if($row['LOGID'] == '2823890' or $row['LOGID'] == '2823891'){
							$mostrar = "hora_anterior: $hora_anterior, hora_actual: $hora_actual, diferencia: $diferencia";
							ei_arbol($mostrar,"calculo diferencia");
						}*/

						
						if($diferencia > 200){  //08:04:51 - 08:04:50   es menor a 120 segundos, se omite 

							$datos[$cont]['basedatos_o'] = $row['basedatos'];
							$datos[$cont]['reloj_o'] = $row['device_id'];
							$datos[$cont]['fecha']   = $row['fecha'];
							$datos[$cont]['salida']  = substr($row['CHECKTIME'],11,8);
							$cont++;
						}else{

							//cambia resto entrada, para que tome la que sigue como salida
							if($resto_entrada == 0){

								$resto_entrada = 1; //0 impar, 1 par 

							}else{
								$resto_entrada = 0; //0 impar, 1 par 
							}
						}    
					}  


				}
				//---------------------------------------------------------------------------------
			
			/*}else{

				//logica de entrada y salida ------------------------------------------------------
				foreach($array as $key=>$row){

					switch ($row['CHECKTYPE']) {
						case 'E': //Entrada hander
						case 'I': //Entrada access
						case '1': //Entrada interna access


							//si la marca anterior es entrada, la ponemos como salida
							#if($row['basedatos'] == 'hander'){
								$key_anterior = $key -1;
								if($array[$key_anterior]['USERID'] == $row['USERID'] and 
									($array[$key_anterior]['CHECKTYPE'] == 'E' 
									or $array[$key_anterior]['CHECKTYPE'] == 'I' 
									or $array[$key_anterior]['CHECKTYPE'] == '1' ) ){ 
									$datos[$cont]['basedatos_o'] = $row['basedatos'];
									$datos[$cont]['reloj_o'] = $row['device_id'];
									$datos[$cont]['fecha']   = $row['fecha'];
									$datos[$cont]['salida']  = substr($row['CHECKTIME'],11,8);                               
									$cont++;

									break;
								}                            
							#}

							//set entrada
							$datos[$cont]= array(
								'marca_id'    => $row['LOGID'],
									#'legajo'    => $row['Badgenumber'],
								'badgenumber' => $row['Badgenumber'],
								'fecha'       => $row['fecha'],
								'entrada'     => substr($row['CHECKTIME'],11,8),
									#'salida'    =>,
								'basedatos_i' => $row['basedatos'],
								'reloj_i'     => $row['device_id']
								);

							break;
						
						case 'S': //Salida hander
						case 'O': //Salida access
						case '0': //Salida interna access
							$datos[$cont]['basedatos_o'] = $row['basedatos'];
							$datos[$cont]['reloj_o'] = $row['device_id'];
							$datos[$cont]['fecha']   = $row['fecha'];
							$datos[$cont]['salida']  = substr($row['CHECKTIME'],11,8);
							$cont++;

							break;

						case '#Error':
						default:

							break;           
					}

				}
				//---------------------------------------------------------------------------------

			}*/




		}
		//-----------------------
		if(isset($filtro['calcular_horas']) and count($datos) > 0){
			foreach($datos as $key=>$dato){
				$datos[$key]['horas']     = $this->restar_horas($dato['entrada'],$dato['salida']);
			}
		}
		return $datos;
	}


	static function get_dispositivos()
	{

		$sql = "SELECT M.ID as id_dispositivo,
		M.MachineAlias,
		M.area_id,
		A.areaid, A.areaid as cod_depcia, 
		A.areaname
		FROM Machines as M, personnel_area as A
		WHERE A.id = M.area_id "; 

		//-------------------------------------------
		$conf_access = file_get_contents('../php/datos/conf_access.txt');
		list($UID,$PWD,$DB,$HOST)=explode(',',$conf_access); //UID:sa,PWD:CitReloj2015,DB:access,HOST:CIT-RELOJ\ASISTENCIA
		$connectionInfo = array( "UID"=>$UID, "PWD"=>$PWD, "Database"=>$DB);
		$conn = sqlsrv_connect($HOST, $connectionInfo);

		if( $conn === false ){
		echo "391: No es posible conectarse al servidor.</br>";
		die( print_r( sqlsrv_errors(), true));
		}

		$result = sqlsrv_query($conn,$sql);
		
		if( $result === false ){
		echo "Error al ejecutar consulta.</br>";
		die( print_r( sqlsrv_errors(), true));
		}


		while ($row = sqlsrv_fetch_array($result)) {
			$array[] = $row;
		}

		sqlsrv_free_stmt($result);
		sqlsrv_close($conn);
		//-------------------------------------------

		return $array;


	}

	/*static function get_dispositivo($id_dispositivo)
	{

		$conn = odbc_connect("access","sa","reloj2015" );

		$sql = "SELECT M.ID as id_dispositivo,
		M.MachineAlias,
		M.area_id,
		A.areaid, A.areaid as cod_depcia, 
		A.areaname
		FROM Machines as M, personnel_area as A
		WHERE A.id = M.area_id 
		AND A.areaid = '$id_dispositivo'"; 

		$result=odbc_exec($conn,$sql);

		while ($row = odbc_fetch_array($result)) {
			$array = $row;
		}
		return $array; 
	}*/

	static function get_dispositivos_dependencia($cod_depcia)
	{
		$sql = "SELECT M.ID as id_dispositivo,
		M.MachineAlias,
		M.area_id,
		A.areaid,
		A.areaname
		FROM Machines as M, personnel_area as A
		WHERE A.id = M.area_id 
			AND A.areaid= '$cod_depcia'"; 

		//-------------------------------------------
		$conf_access = file_get_contents('../php/datos/conf_access.txt');
		list($UID,$PWD,$DB,$HOST)=explode(',',$conf_access); //UID:sa,PWD:CitReloj2015,DB:access,HOST:CIT-RELOJ\ASISTENCIA
		$connectionInfo = array( "UID"=>$UID, "PWD"=>$PWD, "Database"=>$DB);
		$conn = sqlsrv_connect($HOST, $connectionInfo);

		if( $conn === false ){
		echo "456: No es posible conectarse al servidor.</br>";
		die( print_r( sqlsrv_errors(), true));
		}

		$result = sqlsrv_query($conn,$sql);
		
		if( $result === false ){
		echo "Error al ejecutar consulta.</br>";
		die( print_r( sqlsrv_errors(), true));
		}

		while ($row = sqlsrv_fetch_array($result)) {
			$array[] = $row;
		}

		sqlsrv_free_stmt($result);
		sqlsrv_close($conn);
		//-------------------------------------------

		return $array;

	}


	function sumar_horas($hora1,$hora2)
	{
		list($h1,$m1,$s1)  = explode(":", $hora1);
		list($h2,$m2,$s2)  = explode(":", $hora2);

		$horas = $h1 + $h2;
		$minutos = $m1 + $m2;

		if($minutos >= 60){ 
			$horas = $horas + 1;
			$minutos = $minutos - 60;
		}

		if($minutos < 10){ 
			$minutos = "0".$minutos;
		}

		return "$horas:$minutos";
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

	function dividir_horas($horas_dividendo,$divisor)
	{
		
		/*$hora=substr($horas_dividendo,0,2);
		$min=substr($horas_dividendo,3,2);
		$seg=substr($horas_dividendo,6,2);*/

		list($hora,$min,$seg)  = explode(":", $horas_dividendo);
		$seg = 0;

		$dividendo=((($hora*60)*60)+($min*60)+$seg); //a segundos

		$resultado = $dividendo / $divisor; //res en segundos

		$resultadoh=floor($resultado/3600);
		$resultadom=floor(($resultado-($resultadoh*3600))/60);
		$resultados=$resultado-($resultadom*60)-($resultadoh*3600);

		return date("H:i",mktime($resultadoh,$resultadom,$resultados));

	}

	//-----------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------

	function get_lista_resumen($personas, $filtro=array())
	{
		$total = 0;
		//ei_arbol(round((memory_get_usage()/(1024*1024)),2));
		$agentes =$personas;
		ei_arbol($filtro);
		ei_arbol($personas);	
		$fecha_desde = $filtro['fecha_desde'];
		$fecha_hasta = $filtro['fecha_hasta'];
		//if ($fecha_desde <> $fecha_hasta){
		//$feriados = toba::tabla('conf_feriados')->get_listado($filtro);
		$feriados = toba::tabla('conf_feriados')->suma_feriados($filtro);
		$cantidad_feriado = 3;
		//$cantidad_feriado = count($feriados);
		//ei_arbol($feriados);
		/*for($i=0;$i<$cantidad_feriado;$i++){
			$fechaferiado = strtotime($feriados[$i]['fecha']);
			$feriados[$i]['fecha']= date("Y-m-d", $fechaferiado);

		}*/
			//ei_arbol($feriados);
		
			if (isset($filtro['basedatos'])) {
				$filtro_marca['basedatos'] = $filtro['basedatos'];
			}

			if(count($agentes)>0){
			/*
			Bucle por agente, para calcular presentismo, y razones de los ausentes
			*/
			//ei_arbol(round((memory_get_usage()/(1024*1024)),2));
				foreach($agentes as $key=>$agente){

				//seteamos valores en cero
					if(file_exists('fotos/'.$agente['dni'].'.jpg')){
						$agentes[$key]['foto']      = '<img style="width: 50px; height: 50px; border-radius: 100px; -moz-border-radius: 100px; -webkit-border-radius: 100px; -khtml-border-radius: 100px;" src="fotos/'.$agente['dni'].'.jpg">';
					}else{
						$agentes[$key]['foto']      = '<img style="width: 50px; height: 50px; border-radius: 100px; -moz-border-radius: 100px; -webkit-border-radius: 100px; -khtml-border-radius: 100px;" src="fotos/unnamed.png">';   
					}

					$agentes[$key]['nombre_completo'] = $agente['apellido'].', '.$agente['nombre'];


				//setemos adscripciones
					$sql = "SELECT legajo, cod_depcia_origen, fecha_inicio, cod_depcia_destino 
						FROM reloj.adscripcion 
						WHERE legajo = '".$agente['legajo']."' 
						AND fecha_inicio <= '".date("Y-m-d")."' 
						AND fecha_fin is null";
					$adscripciones =  toba::db('ctrl_asis')->consultar($sql); 
					if(count($adscripciones)>0){
						$agentes[$key]['cod_depcia_destino'] = $adscripciones[0]['cod_depcia_destino'];
					}

				//------------------------------------------------------------------
					$agentes[$key]['fecha_ini']         = null;
					$agentes[$key]['fecha_desde']       = null;

					$agentes[$key]['laborables']        = 0;
					$agentes[$key]['feriados']          = 0;
					$agentes[$key]['presentes']         = 0;
					$agentes[$key]['ausentes']          = 0;
					$agentes[$key]['justificados']      = 0;
					$agentes[$key]['injustificados']    = 0;
					$agentes[$key]['partes']            = 0;
					$agentes[$key]['partes_sanidad']    = 0;

					$agentes[$key]['horas_totales']     = 0;
					$agentes[$key]['horas_promedio']      = 0;

					unset($array_marcas);
					$array_marcas = array();
					$contador_marcas = 0;

					$jornada = toba::tabla('conf_jornada')->get_jornada_agente($agente['legajo']);

					$filtro_marca['calcular_horas']     = true;

					if (empty($jornada['fecha_ini'])) { //si no tiene jornada, asignamos jornada predetermianda
						$jornada['fecha_ini']  = $fecha_desde;
						$jornada['normal']     = 1;
						$jornada['h1']         = "08:00:00";
						$jornada['h2']         = "14:00:00";
					}

					$fecha_desde_local = $fecha_desde;
					$fecha_hasta_local = $fecha_hasta;

				//--------------------------------------------------------------------------------------------
				//--------------------------------------------------------------------------------------------
				//reviso fecha desde 
					if($fecha_desde < $jornada['fecha_ini']){
						$fecha_desde_local = $jornada['fecha_ini'];
					}
				//reviso fecha hasta 
					if(!empty($jornada['fecha_fin']) and $fecha_hasta > $jornada['fecha_fin']){
						$fecha_hasta_local = $jornada['fecha_fin'];
					}elseif($fecha_hasta > date("Y-m-d")){ 
						$fecha_hasta_local = date("Y-m-d");
					}                   

				//recorremos todos los dias entre fecha_desde y fecha_hasta
					
					$fechaInicio = strtotime($fecha_desde_local);
					$fechaFin    = strtotime($fecha_hasta_local);
					$agrupamiento = $agentes[$key]['escalafon'];
					//ei_arbol(($fechaFin -$fechaInicio)/86400);
					for($i=$fechaInicio; $i<=$fechaFin; $i+=86400){

					$dia = date("Y-m-d", $i);
					//$cantidad_feriado =toba::tabla('conf_feriados')->hay_feriado($dia) ;
					//ei_arbol(toba::tabla('conf_feriados')->hay_feriado($dia));
					//if(toba::tabla('conf_feriados')->hay_feriado($dia)){ //revisamos el día solo si no es feriado
					//ei_arbol(round((memory_get_usage()/(1024*1024)),2));
					$v= toba::tabla('conf_feriados')->hay_feriado($dia,$agrupamiento);
					//ei_arbol($v,$dia,$agrupamiento);
					if ($v > 0) {
						$agentes[$key]['feriados']++;
					
					//ei_arbol($v);
					//ei_arbol($cantidad_feriado);

					
					//if ($cantidad_feriado < 0 ) {	
					//	for ($i=0; $i< $cantidad_feriado; $i++){
							

					///		if ($feriados[$i]['fecha'] == $dia and ($feriados[$i]['agrupamiento'] == $agrupamiento or $feriados[$i]['agrupamiento']=='Todos'  ) ) {	
						
					//			$agentes[$key]['feriados']++;
					//		}
					//	}
						/*if ($feriados['fecha'] == $dia ) {	
						
						$agentes[$key]['feriados']++;*/

						
					}else{

						$datos_dia = getdate($i);

						switch ($datos_dia['wday']) { //0 (para Domingo) hasta 6 (para Sábado)
							
							case 1: //lunes

								if($jornada['normal']==1 or $jornada['lunes']==1 ) {
									$this->calculo_dia ('lunes', 'lunes', $key, $agentes, $array_marcas, $contador_marcas, $dia, $filtro_marca);
								}
								break;

							case 2: //martes

								if($jornada['normal']==1 or $jornada['martes']==1 ) {
									$this->calculo_dia ('martes', 'martes', $key, $agentes, $array_marcas, $contador_marcas, $dia, $filtro_marca);
								}
								break;

							case 3: //miercoles

								if($jornada['normal']==1 or $jornada['miercoles']==1 ) {
									$this->calculo_dia ('miercoles', 'miercoles', $key, $agentes, $array_marcas, $contador_marcas, $dia, $filtro_marca);
								}
								break;

							case 4: //jueves

								if($jornada['normal']==1 or $jornada['jueves']==1 ) {
									$this->calculo_dia ('jueves', 'jueves', $key, $agentes, $array_marcas, $contador_marcas, $dia, $filtro_marca);
								}
								break;

							case 5: //viernes

								if($jornada['normal']==1 or $jornada['viernes']==1 ) {
									$this->calculo_dia ('viernes', 'viernes', $key, $agentes, $array_marcas, $contador_marcas, $dia, $filtro_marca);
								}
								break;

							case 6: //sabado

								if($jornada['sabado']==1) {
									$this->calculo_dia ('sabado', 'sabado', $key, $agentes, $array_marcas, $contador_marcas, $dia, $filtro_marca);
								}
								break;

							case 0: //domingo

								if($jornada['domingo']==1) {
									$this->calculo_dia ('domingo', 'domingo', $key, $agentes, $array_marcas, $contador_marcas, $dia, $filtro_marca);
								}
								break;

						}//fin switch

					}//fin no es feriado
					//$cantidad_feriado = -1;
				}//fin recorremos todos los dias entre fecha_desde y fecha_hasta
			//	ei_arbol($agentes);

				//Recorremos array de marcas para agregar casos especiales -------------------------------- 
				$horas_totales = 0;
				$prom_acum     = 0;                
				if(count($array_marcas)>0){

					foreach ($array_marcas as $m => $marca) {
						
						if($marca['descripcion'] == 'Presente'){

							//agregamos horarios que falten, con vista en rojo
							if(!empty($marca['entrada']) and empty($marca['salida']) ) { //tiene solo la entrada

								//si la marca siguiente solo tiene salida, esta bien; sino ponemos la entrada salida con el mismo horario de la entrada
								$m_siguiente = $m+1;
								if(!empty($array_marcas[$m_siguiente]['entrada']) or ( empty($array_marcas[$m_siguiente]['entrada']) and empty($array_marcas[$m_siguiente]['salida']) ) ) { 
									$array_marcas[$m]['salida']     = $marca['entrada'];
								}


							}elseif(empty($marca['entrada']) and !empty($marca['salida']) ) { //tiene solo la salida

								//si la marca anterior solo tiene entrada, esta bien; sino ponemos la entrada actual con el mismo horario de la salida
								$m_anterior = $m-1;
								if(!empty($array_marcas[$m_anterior]['salida']) or ( empty($array_marcas[$m_anterior]['entrada']) and empty($array_marcas[$m_anterior]['salida']) ) ) { 
									$array_marcas[$m]['entrada']     = $marca['salida'];
								}

							}
							// hora de entrada
							if ($fechaInicio == $fechaFin){
								$hora_entrada = $array_marcas[$m]['entrada'];
								$hora_salida =  $array_marcas[$m]['salida'];
							} else {
							 $hora_entrada = '';
							}
							
							//calculamos horas 
							$horas         = $this->restar_horas($array_marcas[$m]['entrada'],$array_marcas[$m]['salida']);
							$horas_totales = $this->sumar_horas($horas,$horas_totales);
							$prom_acum = $this->dividir_horas($horas_totales,$marca['contador_marcas']);//dividendo,divisor    

						}elseif($marca['descripcion'] == 'Ausente'){
							$hora_entrada = null;
							$hora_salida = null;
							$prom_acum = $this->dividir_horas($horas_totales,$marca['contador_marcas']);//dividendo,divisor    

						}
					}
					
					/*$array_res = array('horas_totales'  => $horas_totales, 'prom_acum' => $prom_acum);
					if($agente['legajo'] == '32011'){
						ei_arbol($array_marcas,"Agente ".$agente['legajo']);     
						ei_arbol($array_res," Res: Agente ".$agente['legajo']);  
					} */                  
				}

				//-----------------------------------------------------------------------------------------------
				$agentes[$key]['fecha_desde']       = $fecha_desde;
				$agentes[$key]['fecha_hasta']       = $fecha_hasta;
				$dias = (strtotime($fecha_desde)-strtotime($fecha_hasta))/86400;
				$dias = abs($dias);
				$dias =floor($dias)+1 - $agentes[$key]['feriados'];
				$agentes[$key]['entrada'] = $hora_entrada;
				$agentes[$key]['salida'] = $hora_salida;
				
			
				if ($agentes[$key]['escalafon']== 'DOCE'){
					switch ($agentes[$key]['horas_requeridas_prom']){
						case 2: 
						$horas_diarias= '01:24';
						break;	
						case 4 : 
						$horas_diarias= '02:48';
						break;	
						case 40:
						$horas_diarias = '05:36';
						break;
					}
				} else if ($agentes[$key]['escalafon']== 'NODO'){
						$horas_diarias = '06:00';

				}
				//ei_arbol($prom_acum);
				$agentes[$key]['horas_requeridas_prom'] = $this->calculo_horas_req($horas_diarias,$dias);
				if($agentes[$key]['laborables'] > 0){

					$agentes[$key]['horas_totales']       = $horas_totales;
					$agentes[$key]['horas_promedio']      = $prom_acum;
				}else{
					$agentes[$key]['horas_promedio']      = '0:00:00';
				}
			
				//if($agentes[$key]['horas_promedio'] < $agentes[$key]['horas_requeridas_prom']){ //dif negativa

					//$desviacion_horario = $this->restar_horas($agentes[$key]['horas_promedio'],$agentes[$key]['horas_requeridas_prom']);

				//	$entero_desviacion  = str_replace(':', '', $desviacion_horario);

					/* if((int)$entero_desviacion > 30){
						$agentes[$key]['desviacion_horario']    = "<p style='background-color: #DD4B39; color:#FFFFFF; padding: 2px;'>$desviacion_horario</p>";
					}else{
						$agentes[$key]['desviacion_horario']    = "<p style='background-color: #f39c12; color:#FFFFFF; padding: 2px;'>$desviacion_horario</p>";
					}*/

				//}else{ //dif positiva
					$desviacion_horario =  $this->restar_horas($agentes[$key]['horas_requeridas_prom'],$agentes[$key]['horas_promedio']);
					$agentes[$key]['desviacion_horario']    = $desviacion_horario;
			//	}
				
				//--------------------------------------------------------------------------------------------
				//--------------------------------------------------------------------------------------------

			}//fin bucle agentes
		//	ei_arbol(round((memory_get_usage()/(1024*1024)),2));
		}

		//} else
		
		/*if (isset($filtro['con_marcas']) and $filtro['con_marcas']==1) {
			$array = array();
			if(count($agentes)>0){
				foreach ($agentes as $key => $value) {
					if($value['horas_totales']>0){
						$array[]=$value;
		
				}
			}
			return $array;
		}*/
				//ei_arbol ($agentes);
				
			//	ei_arbol(round((memory_get_usage()/(1024*1024)),2));
	if ( $filtro['fecha_desde'] ==	$filtro['fecha_hasta']) {
					
					if ($filtro['marcas']== 0) {
				 //ausentes
				//$h = $this->s__datos;
			
					$personas = array_filter( $agentes, function( $agentes ) {
					return ( $agentes['presentes']== 0);
					});
					
					} elseif ($filtro['marcas'] == 1) {
					
					$personas = array_filter( $agentes, function( $agentes ) {
					
					return ( $agentes['presentes']== 1);
					});
				
					} else {
					$personas =$agentes;
					}
				
				}
				else {	
				$personas= $agentes;
				}
			
		//$personas['total']= count($personas);

		return $personas;
		//return $agentes;
	}


	function calculo_dia ($dia_ref, $dia_leyenda, $key, &$agentes, &$array_marcas, &$contador_marcas, $dia, $filtro_marca)
	{
		
		$agente = $agentes[$key];
		//------------------------

		
		$agentes[$key]['laborables']++;
						
		$id_parte = toba::tabla('parte')->tiene_parte($agente['legajo'], $dia);
		$id_parte_sanidad = toba::tabla('parte')->tiene_parte_sanidad($agente['legajo'], $dia);
		$info_complementaria = toba::tabla('info_complementaria')->tiene_info_complementaria($agente['legajo'], $dia);                  
		$hay_feriado= toba::tabla('conf_feriados')->dia_feriado($dia);
		//ei_arbol($hay_feriado);

		if($id_parte_sanidad > 0){  
			
			$agentes[$key]['partes_sanidad']++;
			
			$agentes[$key]['ausentes']++; 
			$agentes[$key]['justificados']++;

			$array_marcas[] = array(
				'legajo'    => $agente['legajo'],
				'fecha'        => $dia,
				'dia'       => $dia_leyenda,
				'descripcion'  => 'Parte' # sanidad '.$parte['id_parte'].': '.$parte['motivo']
					);
			}elseif (isset($hay_feriado)){
					if ($hay_feriado == 'Todos'){
					$agentes[$key]['feriados']++;
					$agentes[$key]['laborables']--;	
					} elseif ($agentes[$key]['escalafon'] == 'NODO'and $hay_feriado == 'Personal de Apoyo'){
						$agentes[$key]['feriados']++;
						$agentes[$key]['laborables']--;	
					} else {
						$agentes[$key]['feriados']++;
						$agentes[$key]['laborables']--;	
					}	
		
		}elseif($id_parte > 0){ 
			$agentes[$key]['partes']++; 

			$parte = toba::tabla('parte')->get_parte($id_parte);

			if($parte['id_motivo'] == 28){ // Permisos excepcionales, muestra las marcas pero no las cuenta

				//---------------------------------------------------------------
				//---------------------------------------------------------------

				$filtro_marca['badgenumber'] = $agente['legajo']; 
				$filtro_marca['fecha']       = $dia;                                    

				$marcas = $this->get_marcas($filtro_marca);
				if(count($marcas)>0){
					
					#$contador_marcas++;

					foreach($marcas as $marca){
						$marca['contador_marcas'] = $contador_marcas;
						$marca['dia'] = $dia_leyenda;
						$marca['descripcion'] = 'Parte '.$parte['id_parte'].': '.$parte['motivo'].' (#'.$parte['id_motivo'].')';

						$array_marcas[] = $marca;
					}

					$agentes[$key]['ausentes']++; //$agente['presentes']++;
					$agentes[$key]['justificados']++;

				
					

				}else{
					$agentes[$key]['ausentes']++;
					$agentes[$key]['injustificados']++;

					$contador_marcas++;
					$array_marcas[] = array(
						'legajo'    => $agente['legajo'],
						'fecha'        => $dia,
						'dia'       => $dia_leyenda,
						'descripcion'  =>'Ausente'
						#,'contador_marcas' =>$contador_marcas
						#,'prom_acum' => $this->dep('access')->dividir_horas($horas_totales,$contador_marcas)
							); 

				}

				//---------------------------------------------------------------
				//---------------------------------------------------------------
			}else{

				$agentes[$key]['ausentes']++; //$agente['presentes']++;
				$agentes[$key]['justificados']++;
			
				$array_marcas[] = array(
					'legajo'    => $agente['legajo'],
					'fecha'        => $dia,
					'dia'       => $dia_leyenda,
					'descripcion'  => 'Parte' # '.$parte['id_parte'].': '.$parte['motivo']
						);

			}

		}elseif(!empty($info_complementaria['id_info_complementaria'])){  

			/*if($agente['legajo'] == '28983'){
			ei_arbol($info_complementaria); 
			}*/

			//seteamos marca complementaria
			$marcas[0] = array(
				'marca_id'          => 'IC'.$info_complementaria['id_info_complementaria'],
				'badgenumber'       => $info_complementaria['legajo'],
				'fecha'             => $dia,
				'entrada'           =>  substr($info_complementaria['entrada'], -8,8),
				'basedatos_i'       => '-', 
				'reloj_i'           => null,
				'basedatos_o'       => '-',
				'reloj_o'           => null,
				'salida'            =>  substr($info_complementaria['salida'], -8,8),
				'horas'             =>  $info_complementaria['horas'],
				'id_info_complementaria' => $info_complementaria['id_info_complementaria']
				);  


			//ahora es igual que las marcas normales

			$contador_marcas++;

			foreach($marcas as $marca){
				$marca['contador_marcas'] = $contador_marcas;
				$marca['dia'] = $dia_leyenda;
				$marca['descripcion'] = 'Presente';

				$array_marcas[] = $marca;
			}

			$agentes[$key]['presentes']++;


		}else{ //buscamos marcas

			$filtro_marca['badgenumber'] = $agente['legajo']; // NOTA: podriamos pasar legajo o dni, segun se use el badgenumber en los relojes
			$filtro_marca['fecha']       = $dia; 

			$marcas = $this->get_marcas($filtro_marca);
			if(count($marcas)>0){
				
				$contador_marcas++;

				foreach($marcas as $marca){
					$marca['contador_marcas'] = $contador_marcas;
					$marca['dia'] = $dia_leyenda;
					$marca['descripcion'] = 'Presente';

					$array_marcas[] = $marca;
				}

				$agentes[$key]['presentes']++;

			}else{
				$agentes[$key]['ausentes']++;
				$agentes[$key]['injustificados']++;

				$contador_marcas++;
				$array_marcas[] = array(
					'legajo'    => $agente['legajo'],
					'fecha'        => $dia,
					'dia'       => $dia_leyenda,
					'descripcion'  =>'Ausente',
					'contador_marcas' =>$contador_marcas
						#,'prom_acum' => $this->dep('access')->dividir_horas($horas_totales,$contador_marcas)
						); 

			}
		}

	}
	function calculo_horas_req ($hora_min,$dias){
		$horas_min = explode(":",$horas_diarias);
						$todo[$i]['h_min'] = $horas_min[0] +($horas_min[1]/60);

						//Horas totales ideales trabajadas
					//	ei_arbol($horas_min);
						$horas= $dias * $horas_min[0];
						
						// Calculos de minutos
						$minutos = $dias * $horas_min[1];

						while ($minutos >= 60){
							$minutos = $minutos - 60;
							$tmp ++;
						}

						$horas = $horas + $tmp;
						
						if($minutos < 10) {
							$minutos = '0'.$minutos;
						} 

						$requerido = $horas .':'.$minutos;
						return $requerido;

	}
}
?>