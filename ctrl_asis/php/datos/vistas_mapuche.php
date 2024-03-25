<?php
class vistas_mapuche extends toba_datos_relacion
{
 
    static function get_total_agentes_control_asistencia($filtro, $limit='', $offset='')
    {
      
        $where = array();

		//$filtro['cod_depcia'] = 4;
        /*if (isset($filtro['desde'])) {
            $where[] = "RowNum >= '".$filtro['desde']."'";
            $where[] = "RowNum < '".$filtro['hasta']."'";
        } */ 


        if (isset($filtro['legajo'])) {
                $where[] = "legajo = '".$filtro['legajo']."'";
        }     
        if (isset($filtro['dni'])) {
                $where[] = "dni = '".$filtro['dni']."'";
        }  
        if (isset($filtro['desc_nombre'])) {
                $where[] = "desc_nombre ILIKE ".quote("%{$filtro['desc_nombre']}%");
        }
        if (isset($filtro['cod_depcia'])) {
                
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
                    $where[] = "(cod_depcia = '".$filtro['cod_depcia']."' OR legajo IN ($legajos_incluir) )";
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
                    $where[] = "(cod_depcia = '".$filtro['cod_depcia']."' OR legajo NOT IN ($legajos_omitir) )";
                    $tiene_adscripcion = true;
                }

                if ($tiene_adscripcion == false) {
                    $where[] = "cod_depcia = '".$filtro['cod_depcia']."'";
                }

            }else{

                $where[] = "cod_depcia = '".$filtro['cod_depcia']."'";
            }
        }
       // ei_arbol($sql);

        if (isset($filtro['con_marcas']) and $filtro['con_marcas']==1) { 

            $primero = true;     
      
            if (!isset($filtro['basedatos']) or $filtro['basedatos']=='access') { 
                //--- access ----------------------------------------------------------------
                $sql = "SELECT DISTINCT U.Badgenumber as legajo
                FROM dbo.CHECKINOUT as C, dbo.USERINFO as U
                WHERE U.USERID = C.USERID 
                 AND CONVERT(varchar(10), C.CHECKTIME, 120) >= '".$filtro['fecha_desde']."'
                 AND CONVERT(varchar(10), C.CHECKTIME, 120) <= '".$filtro['fecha_hasta']."'
                  ";

                //-------------------------------------------
                $conf_access = file_get_contents('../php/datos/conf_access.txt');
                list($UID,$PWD,$DB,$HOST)=explode(',',$conf_access); //UID:sa,PWD:CitReloj2015,DB:access,HOST:CIT-RELOJ\ASISTENCIA
                $connectionInfo = array( "UID"=>$UID, "PWD"=>$PWD, "Database"=>$DB);
                $conn = sqlsrv_connect($HOST, $connectionInfo);

                if( $conn === false ){
                echo "No es posible conectarse al servidor.</br>";
                die( print_r( sqlsrv_errors(), true));
                }

                $result = sqlsrv_query($conn,$sql);
                
                if( $result === false ){
                echo "Error al ejecutar consulta.</br>";
                die( print_r( sqlsrv_errors(), true));
                }

                while ($row = sqlsrv_fetch_array($result)) {
                    if($primero){
                        $primero = false; 
                        $string_legajo = "'".$row['legajo']."'"; 
                    }else{
                        $string_legajo .= ",'".$row['legajo']."'"; 
                    }
                }

                sqlsrv_free_stmt($result);
                sqlsrv_close($conn);
            }

            if (!isset($filtro['basedatos']) or $filtro['basedatos']=='hander') { 
                //--- hander ----------------------------------------------------------------
                $sql = "SELECT DISTINCT Convert(Int, Tarjeta) as legajo
                FROM Fichada
                WHERE CONVERT(varchar(10), FechaHora, 120) >= '".$filtro['fecha_desde']."'
                 AND CONVERT(varchar(10), FechaHora, 120) <= '".$filtro['fecha_hasta']."'
                  ";

                //-------------------------------------------
                //$connectionInfo = array( "UID"=>"citreloj", "PWD"=>"reloj2015", "Database"=>"Hander");
                //$conn = sqlsrv_connect( '172.22.32.27\SQLESPRESS,2523', $connectionInfo);
                $conf_hander = file_get_contents('../php/datos/conf_hander.txt');
                list($UID,$PWD,$DB,$HOST)=explode(';',$conf_hander);
                $connectionInfo = array( "UID"=>$UID, "PWD"=>$PWD, "Database"=>$DB);
                $conn = sqlsrv_connect($HOST, $connectionInfo);

                if( $conn === false ){
                echo "No es posible conectarse al servidor.</br>";
                die( print_r( sqlsrv_errors(), true));
                }

                $result = sqlsrv_query($conn,$sql);
                
                if( $result === false ){
                echo "Error al ejecutar consulta.</br>";
                die( print_r( sqlsrv_errors(), true));
                }

                while ($row = sqlsrv_fetch_array($result)) {
                    if($primero){
                        $primero = false; 
                        $string_legajo = "'".$row['legajo']."'"; 
                    }else{
                        $string_legajo .= ",'".$row['legajo']."'"; 
                    }
                }

                sqlsrv_free_stmt($result);
                sqlsrv_close($conn);
            }

            //-------------------------------------------
            if(!empty($string_legajo)){
                $where[] = "legajo IN ($string_legajo)";
            }         
        }


        if (isset($filtro['agrupamiento'])) {
            $where[] = "agrupamiento = '".$filtro['agrupamiento']."'";
        }  
       
        $sql = "SELECT DISTINCT t_l.legajo, t_l.apellido, t_l.nombre, t_l.fec_nacim, dni, t_l.fec_ingreso, t_l.estado_civil, 
               t_l.caracter, t_l.categoria, t_l.agrupamiento, t_l.escalafon, t_l.cod_depcia, t_l.cuil, t_l.email, 
               t_h.cant_horas, (t_h.cant_horas / 5) as horas_requeridas_prom 
              FROM uncu.legajo as t_l, uncu.cant_hora as t_h 
              WHERE t_h.categoria = t_l.categoria 
              ORDER BY t_l.legajo, t_l.apellido, t_l.nombre, t_l.fec_nacim, t_l.dni, t_l.fec_ingreso, t_l.estado_civil, 
                   t_l.caracter, t_l.categoria, t_l.agrupamiento, t_l.escalafon, t_l.cod_depcia, t_l.cuil, t_l.email 
               $limit $offset";

        if (count($where)>0) {
            $sql = sql_concatenar_where($sql, $where);
        } 
		
        return  toba::db('mapuche')->consultar($sql); 

    }



    static function get_agentes_control_asistencia($filtro, $limit='', $offset='')
    {
        $where = array();
		$mensaje ="";
		$filtro['cod_depcia'] = '04'; // Seteo dependencia
		/*if ($filtro['tipo_busqueda']== 1 and !isset($filtro['agente'])) {
			$mensaje = toba::mensajes()->get('apellido');
		}
		if ($filtro['tipo_busqueda']== 2 and !isset($filtro['legajo'])) {
			$mensaje = toba::mensajes()->get('legajo');
		}		
		if ($filtro['tipo_busqueda']== 3 and !isset($filtro['dni'])) {
			$mensaje = toba::mensajes()->get('dni');
		}
        /*if (isset($filtro['desde'])) {
            $where[] = "RowNum >= '".$filtro['desde']."'";
            $where[] = "RowNum < '".$filtro['hasta']."'";
        } */ 
		
	   /* switch ($filtro['tipo_busqueda']){
			case 2: // legajo
				if (isset($filtro['legajo'])) {
                $partir = explode(",",$filtro['legajo']);
				//$anadir = "","";
				$elementos = count($partir);
				$lista ="";
				for($i=0;$i<$elementos;$i++){
					if ($i == 0){
						$lista = "legajo in ('".$partir[$i]."'";
					}else{	
					$lista =$lista . ",'".$partir[$i]."'"; 
					}
				}	
				
				$where[] = $lista . ")"; //"legajo in ('".$lista."')" $filtro['legajo']
				} 
		break;
			case 3: //DNI 
			if (isset($filtro['dni'])) {
                $where[] = "dni = '".$filtro['dni']."'";
			}
			break;
			case 1: // Apellido y nombre*/
			//if (isset($filtro['agente'])) {
			//$where [] = "legajo = '".$filtro['agente']."'";
            //ei_arbol($filtro);
            
			if (isset($filtro['catedra'])) {
				$id_catedra = $filtro['catedra'];
            
				$sql = "SELECT legajo from catedras_agentes
						where id_catedra = '$id_catedra';";
				$legajos = toba::db('ctrl_asis')->consultar($sql);
                			
            $cant = count($legajos);
				
                for ($i=0; $i < $cant ; $i++){
			
					$filtro['agente'][$i] = $legajos[$i]['legajo'];
				}
			}	
			
				if (isset($filtro['agente']) and ((($filtro['agente'][0])<>'nopar')or ($filter <> null))) {
                //$partir = explode(",",$filtro['agente']);
				
				//$anadir = "","";
				$elementos = count($filtro['agente']);
				$lista ="";
				for($i=0;$i<$elementos;$i++){
					if ($i == 0){
						$lista = "legajo in ('".$filtro['agente'][$i]."'";
					}else{	
					$lista =$lista . ",'".$filtro['agente'][$i]."'"; 
					}
				}	
			
				$where[] = $lista . ")"; //"legajo in ('".$lista."')" $filtro['legajo']*/
				
			}	
			//break;
		//}	
		
		if ($where[0] == ')') {
			$where = '';
		}	
		
        if (isset($filtro['desc_nombre'])) {
                $where[] = "desc_nombre ILIKE ".quote("%{$filtro['desc_nombre']}%");
        }
        if (isset($filtro['cod_depcia'])) {
                
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
                    $where[] = "(cod_depcia = '".$filtro['cod_depcia']."' OR legajo IN ($legajos_incluir) )";
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
                    $where[] = "(cod_depcia = '".$filtro['cod_depcia']."' OR legajo NOT IN ($legajos_omitir) )";
                    $tiene_adscripcion = true;
                }

                if ($tiene_adscripcion == false) {
                    $where[] = "cod_depcia = '".$filtro['cod_depcia']."'";
                }

            }else{

                $where[] = "cod_depcia = '".$filtro['cod_depcia']."'";
            }
        }
       

        if (isset($filtro['con_marcas']) and $filtro['con_marcas']==1) {
		

            $primero = true;     
      
            if (!isset($filtro['basedatos']) or $filtro['basedatos']=='access') { 
                //--- access ----------------------------------------------------------------
                $sql = "SELECT DISTINCT U.Badgenumber as legajo
                FROM dbo.CHECKINOUT as C, dbo.USERINFO as U
                WHERE U.USERID = C.USERID 
                 AND CONVERT(varchar(10), C.CHECKTIME, 120) >= '".$filtro['fecha_desde']."'
                 AND CONVERT(varchar(10), C.CHECKTIME, 120) <= '".$filtro['fecha_hasta']."'
                  ";

                //-------------------------------------------
                $conf_access = file_get_contents('../php/datos/conf_access.txt');
                list($UID,$PWD,$DB,$HOST)=explode(',',$conf_access); //UID:sa,PWD:CitReloj2015,DB:access,HOST:CIT-RELOJ\ASISTENCIA
                $connectionInfo = array( "UID"=>$UID, "PWD"=>$PWD, "Database"=>$DB);
                $conn = sqlsrv_connect($HOST, $connectionInfo);

                if( $conn === false ){
                echo "No es posible conectarse al servidor.</br>";
                die( print_r( sqlsrv_errors(), true));
                }

                $result = sqlsrv_query($conn,$sql);
                
                if( $result === false ){
                echo "Error al ejecutar consulta.</br>";
                die( print_r( sqlsrv_errors(), true));
                }

                while ($row = sqlsrv_fetch_array($result)) {
                    if($primero){
                        $primero = false; 
                        $string_legajo = "'".$row['legajo']."'"; 
                    }else{
                        $string_legajo .= ",'".$row['legajo']."'"; 
                    }
                }

                sqlsrv_free_stmt($result);
                sqlsrv_close($conn);
            }

            if (!isset($filtro['basedatos']) or $filtro['basedatos']=='hander') { 
                //--- hander ----------------------------------------------------------------
                $sql = "SELECT DISTINCT Convert(Int, Tarjeta) as legajo
                FROM Fichada
                WHERE CONVERT(varchar(10), FechaHora, 120) >= '".$filtro['fecha_desde']."'
                 AND CONVERT(varchar(10), FechaHora, 120) <= '".$filtro['fecha_hasta']."'
                  ";

                //-------------------------------------------
                //$connectionInfo = array( "UID"=>"citreloj", "PWD"=>"reloj2015", "Database"=>"Hander");
                //$conn = sqlsrv_connect( '172.22.32.27\SQLESPRESS,2523', $connectionInfo);
                $conf_hander = file_get_contents('../php/datos/conf_hander.txt');
                list($UID,$PWD,$DB,$HOST)=explode(';',$conf_hander);
                $connectionInfo = array( "UID"=>$UID, "PWD"=>$PWD, "Database"=>$DB);
                $conn = sqlsrv_connect($HOST, $connectionInfo);

                if( $conn === false ){
                echo "No es posible conectarse al servidor.</br>";
                die( print_r( sqlsrv_errors(), true));
                }

                $result = sqlsrv_query($conn,$sql);
                
                if( $result === false ){
                echo "Error al ejecutar consulta.</br>";
                die( print_r( sqlsrv_errors(), true));
                }

                while ($row = sqlsrv_fetch_array($result)) {
                    if($primero){
                        $primero = false; 
                        $string_legajo = "'".$row['legajo']."'"; 
                    }else{
                        $string_legajo .= ",'".$row['legajo']."'"; 
                    }
                }

                sqlsrv_free_stmt($result);
                sqlsrv_close($conn);
            }

            //-------------------------------------------
            if(!empty($string_legajo)){
				
                $where[] = "legajo IN ($string_legajo)";
				
            }         
        }


        if (isset($filtro['agrupamiento'])) {
            
            if ($filtro ['agrupamiento'] == 'doc') {

                //$where[] = "agrupamiento = '".$filtro['agrupamiento']."'";
                $where[] = "agrupamiento <> 'NODO'";
            } else {

           $where[] = "agrupamiento = 'NODO'";
           }

        }

        $nombre_tabla = self::get_nombre_tabla_legajos_por_estado_cargo($filtro['cargos_todos']);
       
        $sql = "SELECT  t_l.legajo, t_l.apellido, t_l.nombre, 
		t_l.fec_nacim, dni, t_l.fec_ingreso, t_l.estado_civil, t_l.caracter, t_l.categoria,
		t_l.agrupamiento, t_l.escalafon ,-- t_l.cod_depcia, 
		t_l.cuil, t_l.email, 
               t_h.cant_horas, (t_h.cant_horas / 5) as horas_requeridas_prom 
              FROM $nombre_tabla as t_l, uncu.cant_hora as t_h 
              WHERE t_h.categoria = t_l.categoria 
              ORDER BY t_l.legajo
			  --, t_l.apellido, t_l.nombre, t_l.fec_nacim, t_l.dni, t_l.fec_ingreso, t_l.estado_civil, t_l.caracter, t_l.categoria, t_l.agrupamiento, t_l.escalafon, t_l.cod_depcia, t_l.cuil, t_l.email 
            --  $limit $offset
			   ";

        if (count($where)>0) {
            $sql = sql_concatenar_where($sql, $where);
        } 
		//$temp = toba::db('mapuche')->consultar($sql); 
	  
	
       return  toba::db('mapuche')->consultar($sql); 
		
    }


    //Las descripción del estado del EF se cargan a partir del resultado de este método. El formato de retorno es una cadena simple. 
    static function get_legajo_todos($legajo=null)
    {
        if (!empty($legajo)) {
          $where = "WHERE legajo = '$legajo' ";
        }
        
        $sql = "SELECT legajo, apellido||', '||nombre||' ('||legajo||')' as descripcion, cuil
                FROM reloj.agentes $where
                ORDER BY legajo, apellido, nombre";

        return toba::db('ctrl_asis')->consultar($sql); 

    }

    //Las descripción del estado del EF se cargan a partir del resultado de este método. El formato de retorno es una cadena simple. 
    static function get_legajo_busca($legajo=null)
    {
        if (!isset($legajo)) {
            return array();
        }
        #$legajo = quote($legajo);

        $nombre_tabla = "reloj.agentes";

        $and = '';
        if(!empty($_SESSION['dependencia'])){

            $nombre_tabla = "reloj.agentes";

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
                $and = "AND cod_depcia IN ('01','15','21','22','24','25','27','28','29','31','32','33','34','35','37','42','43','44','46','50','53','57','60','68','92','94')";

            }elseif($_SESSION['dependencia'] == 32){
                $and = "AND cod_depcia IN ('15','32')";


            }elseif($_SESSION['dependencia'] == 33){
                $and = "AND cod_depcia IN ('31','33','34','24')";

            }elseif($_SESSION['dependencia'] == 21){
                $and = "AND cod_depcia IN ('21','29')";

            }
            else{
                $and = "AND cod_depcia = '".$_SESSION['dependencia']."'";
            }

        }
        //se usa vista legajo_todos para obtener todos el personal activo/inactivo. Analizar optimizacion
        $sql = "SELECT legajo, apellido||', '||nombre||' ('||legajo||')' as descripcion
                FROM $nombre_tabla WHERE legajo = '$legajo' $and 
                ORDER BY apellido, nombre";

        $result = toba::db('ctrl_asis')->consultar($sql); 
        if (! empty($result)) {
            return $result[0]['descripcion'];
        }
    }

    //Buscar legajos por filtro con posibilidad de traer con cargos inactivos
    function get_legajos_busca_por_estado_cargo($filtro=null, $cargos_todos=null){
        return self::get_legajos_busca($filtro, null, null, $cargos_todos);
    }

    //Las opciones se cargan a partir del resultado del método. 
    //El formato de retorno generalmente es una matriz fila por columnas, salvo los editables y el popup que requieren el retorno de un único valor. 
    static function get_legajos_busca($filtro=null, $locale=null, $cod_depcia=null, $cargos_todos=null)
    {
        if (! isset($filtro) || ($filtro == null) || trim($filtro) == '') {
            return array();
        }
        //Si necesita buscar legajos por cargos inactivos (1)
        $nombre_tabla = $cargos_todos == "1" ? "uncu.legajo_todos" : "uncu.legajo";
        $and = '';
        if(!empty($_SESSION['dependencia'])){

            $nombre_tabla = "reloj.agentes";
           
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
                $and = "AND cod_depcia IN ('01','15','21','22','24','25','27','28','29','31','32','33','34','35','37','42','43','44','46','50','53','57','60','68','92','94')";

            }elseif($_SESSION['dependencia'] == 32){
                $and = "AND cod_depcia IN ('15','32')";

            }elseif($_SESSION['dependencia'] == 33){
                $and = "AND cod_depcia IN ('31','33','34','24')";

            }
            elseif($_SESSION['dependencia'] == 21){
                $and = "AND cod_depcia IN ('21','29')";

            }else{
                $and = "AND cod_depcia = '".$_SESSION['dependencia']."'";
            }
        }

        if(is_numeric($filtro)){ //es numero  
            $filtro = quote("{$filtro}%");
            $sql = "SELECT DISTINCT legajo, legajo||' - '||apellido||', '||nombre as descripcion
                FROM $nombre_tabla WHERE CAST(legajo AS TEXT) ILIKE $filtro $and
                ORDER BY legajo 
                LIMIT 20 ";            
        }else{//es string 
            $filtro = strtoupper($filtro);
            $filtro = quote("{$filtro}%");
            $sql = "SELECT DISTINCT legajo, apellido||', '||nombre||' ('||legajo||')' as descripcion
                FROM $nombre_tabla
                WHERE apellido ILIKE $filtro $and
                LIMIT 20 ";           
        }

        return toba::db('ctrl_asis')->consultar($sql);    
    }




    //Las opciones se cargan a partir del resultado del método. 
    //El formato de retorno generalmente es una matriz fila por columnas, salvo los editables y el popup que requieren el retorno de un único valor. 
    static function get_legajos_busca_todos($filtro=null, $locale=null)
    {
        if (! isset($filtro) || ($filtro == null) || trim($filtro) == '') {
            return array();
        }

        if(is_numeric($filtro)){ //es numero  
            $filtro = quote("{$filtro}%");
            $sql = "SELECT DISTINCT legajo, legajo||' - '||apellido||', '||nombre as descripcion
                FROM reloj.agentes WHERE CAST(legajo AS TEXT) ILIKE $filtro $and
                ORDER BY legajo 
                LIMIT 20 ";            
        }else{//es string 
            $filtro = strtoupper($filtro);
            $filtro = quote("{$filtro}%");
            $sql = "SELECT DISTINCT legajo, apellido||', '||nombre||' ('||legajo||')' as descripcion
                FROM reloj.agentes
                WHERE apellido ILIKE $filtro 
                LIMIT 20 ";           
        }
       
        return toba::db('ctrl_asis')->consultar($sql);    
    }

    function get_vista_legajo($filtro=array())
    {
        $where = array();
		//$filtro['cod_depcia']=04;
        if (isset($filtro['legajo'])) {
                $where[] = "t_l.legajo = '".$filtro['legajo']."'";
        }        
        if (isset($filtro['dni'])) {
                $where[] = "t_l.dni = '".$filtro['dni']."'";
        } 
        if (isset($filtro['desc_nombre'])) {
                $where[] = "t_l.desc_nombre ILIKE ".quote("%{$filtro['desc_nombre']}%");
        }

        if (isset($filtro['cod_depcia'])) {
                $where[] = "t_l.cod_depcia = '".$filtro['cod_depcia']."'";
        }
        if (isset($filtro['agrupamiento'])) {
                $where[] = "t_l.agrupamiento = ".quote("%{$filtro['agrupamiento']}%");
        }
        /*$orderby = '';
        if (isset($filtro['con_marcas']) and $filtro['con_marcas'] == 1) {
            
            //BANDERA
            $where[] = "t_l.legajo IN(3, 51, 8049, 10919, 11537, 12385, 12519, 12520, 14021, 14171, 14237, 14315, 14556, 15756, 15763, 15766, 15772, 15777, 15804, 16155, 16687, 16694, 16719, 16724, 16933, 17022, 17068, 17247, 17248, 17400, 17500, 17706, 17854, 17868, 18084, 18101, 18126, 18500, 18515, 18558, 18618, 18807, 18862, 18977, 19025, 19046, 19166, 19176, 19177, 19185, 19205, 19391, 19461, 19509, 19510, 19610, 19801, 19968, 20022, 20032, 20036, 20063, 20088, 20142, 20177, 20178, 20179, 20187, 20192, 20205, 20206, 20207, 20283, 20333, 20344, 20477, 20479, 20566, 20596, 20603, 20699, 20708, 20738, 20761, 20776, 20832, 20834, 20918, 20919, 21096, 21176, 21308, 21525, 21613, 21627, 21741, 22048, 22115, 22189, 22239, 22552, 22625, 22729, 22854, 23165, 23177, 23189, 23205, 23293, 23399, 23558, 23638, 23640, 23695, 23714, 23820, 23889, 23917, 23920, 23930, 24091, 24256, 24482, 24498, 24512, 24537, 24538, 24540, 24647, 24659, 24661, 24705, 24823, 24899, 24929, 24930, 25038, 25385, 25408, 25565, 25588, 25599, 25741, 25813, 25864, 26725, 26846, 26859, 26996, 26997, 26999, 27000, 27003, 27008, 27026, 27129, 27223, 27265, 27266, 27277, 27279, 27469, 27502, 27584, 27782, 27803, 27809, 27821, 27823, 27845, 27918, 27961, 27962, 28073, 28106, 28117, 28267, 28274, 28295, 28296, 28337, 28338, 28341, 28428, 28431, 28471, 28541, 28575, 28580, 28582, 28654, 28833, 28834, 28835, 28836, 28914, 28915, 28916, 28917, 28918, 28922, 28924, 28925, 28979, 28983, 28984, 28992, 28993, 28995, 28997, 28999, 29001, 29003, 29005, 29006, 29014, 29018, 29019, 29033, 29035, 29038, 29049, 29053, 29089, 29094, 29145, 29170, 29211, 29242, 29244, 29283, 29310, 29370, 29431, 29763, 29877, 29893, 29894, 30006, 30151, 30298, 30299, 30300, 30341, 30399, 30451, 30492, 30815, 30816, 30929, 30931, 30966, 30967, 30968, 30969, 30974, 30976, 30980, 30985, 30986, 30987, 30988, 30990, 30995, 30996, 30997, 30998, 31001, 31008, 31018, 31019, 31034, 31037, 31038, 31040, 31046, 31047, 31048, 31049, 31050, 31051, 31052, 31054, 31055, 31060, 31061, 31069, 31070, 31072, 31074, 31075, 31076, 31077, 31078, 31079, 31080, 31082, 31083, 31084, 31085, 31092, 31093, 31094, 31096, 31097, 31102, 31108, 31115, 31135, 31160, 31169, 31243, 31269, 31295, 31311, 31334, 31352, 31354, 31365, 31425, 31430, 31433, 31435, 31441, 31442, 31443, 31446, 31447, 31542, 31546, 31549, 31610, 31708, 31711, 31785, 31786, 31791, 31800, 31801, 31802, 31803, 31804, 31810, 31816, 31821, 31827, 31835, 31836, 31837, 31841, 31842, 31843, 31850, 31855, 31856, 31857, 31860, 31861, 31862, 31865, 31866, 31906, 31907, 31908, 31909, 31911, 31913, 31916, 31962, 32003, 32004, 32009, 32010, 32011, 32013, 32014, 32016, 32031, 32039, 32040, 32041, 32047, 32048, 32127, 32128, 32133, 32134, 32136, 32137, 32166, 32239, 32251, 32260, 32440, 32461, 50000, 
28580, 28813, 28813, 28983, 29094, 31115, 32009, 32010, 32011, 32249, 32249, 32257, 32273, 32274, 32275, 32276, 32277, 32278, 32279, 32280)";

            $sql = "SELECT DISTINCT t_l.legajo, t_l.apellido, t_l.nombre, t_l.fec_nacim, t_l.dni, t_l.fec_ingreso, t_l.estado_civil, 
                               t_l.caracter, t_l.categoria, t_l.agrupamiento, t_l.escalafon, t_l.cod_depcia, t_l.cuil, 
                               t_l.mayor_dedicacion, t_l.funcion_critica, t_l.tipo_sexo, t_l.email,
                               t_d.desc_depcia, t_d.cod_depcia||'. '||t_d.desc_depcia as descripcion_dependencia
                          FROM uncu.legajo as t_l, uncu.dependencia as t_d 
                          WHERE t_d.cod_depcia = t_l.cod_depcia 
                          ORDER BY t_l.cod_depcia ASC, t_l.apellido ASC, t_l.nombre, t_l.legajo, t_l.fec_nacim, t_l.dni, t_l.fec_ingreso, t_l.estado_civil, 
                               t_l.caracter, t_l.categoria, t_l.agrupamiento, t_l.escalafon,t_l.cuil, 
                               t_l.mayor_dedicacion, t_l.funcion_critica, t_l.tipo_sexo, t_l.email,
                               t_d.desc_depcia ASC";
        }else{*/

            $sql = "SELECT t_l.legajo, t_l.apellido, t_l.nombre, t_l.fec_nacim, t_l.dni, t_l.fec_ingreso, t_l.estado_civil, 
                               t_l.caracter, t_l.categoria, t_l.agrupamiento, t_l.escalafon, t_l.cod_depcia, t_l.cuil, 
                               t_l.mayor_dedicacion, t_l.funcion_critica, t_l.tipo_sexo, t_l.email,
                               t_d.desc_depcia, t_d.cod_depcia||'. '||t_d.desc_depcia as descripcion_dependencia
                          FROM uncu.legajo as t_l, uncu.dependencia as t_d 
                          WHERE t_d.cod_depcia = t_l.cod_depcia
                          ORDER BY t_d.cod_depcia ASC, t_l.legajo ASC";
        //}  


        if (count($where)>0) {
                $sql = sql_concatenar_where($sql, $where);
        }

        return toba::db('mapuche')->consultar($sql); 
    }
        //static function get_legajos_depencencia($cod_depcia)
        static function get_legajos_depencencia()
		{
           /* $sql= "SELECT legajo from reloj.adscripcion
                    where cod_depcia_destino = '04'";
            $legajos = toba::db('datos')->consultar;
            $reg = count($legajos);
            if($reg<>0){
            for ($i=0 ; $i<$reg;$i++){
                if ($i<>0){
                    $legajos_incluir = ',';

                } else {
                    $legajos_incluir = 'in (';
                }

                $legajos_incluir= $legajos[$i]['legajo'];
             }
             $legajos_incluir = ')';
            }

            if (isset($legajo_incluir))*/
            $sql = "SELECT legajo, legajo||' - '||apellido||', '||nombre as descripcion 
                    FROM reloj.agentes
                    --WHERE legajo in ((Select legajo from reloj.agente where cod_depcia = '04'))
                   -- or legajo in (20738,30560)
                    ORDER BY apellido, legajo ASC";

            return toba::db('ctrl_asis')->consultar($sql); 

        }

        static function get_legajos()
        {
            $sql = "SELECT legajo, legajo||' - '||apellido||', '||nombre as descripcion 
                    FROM reloj.agentes ORDER BY legajo";

            return toba::db('ctrl_asis')->consultar($sql); 
        }

       static function get_apellido_fca($legajo)
        {
            
            $sql = "SELECT legajo, apellido FROM reloj.agentes WHERE legajo = '$legajo'";
            $res = toba::db('ctrl_asis')->consultar_fila($sql); 
            if(!empty($res['apellido'])){
                return $res['apellido'];
            }
        }
	   static function get_apellido($legajo, $cargos_todos=null)
        {
            //$nombre_tabla = self::get_nombre_tabla_legajos_por_estado_cargo($cargos_todos);

            $sql = "SELECT legajo, apellido FROM reloj.agentes WHERE legajo = '$legajo'";
            $res = toba::db('ctrl_asis')->consultar_fila($sql); 
            if(!empty($res['apellido'])){
                return $res['apellido'];
            }
        }
        static function get_nombre_legajo($legajo, $cargos_todos=null)
        {
            //$nombre_tabla = self::get_nombre_tabla_legajos_por_estado_cargo($cargos_todos);
            $sql = "SELECT legajo, nombre FROM reloj.agentes WHERE legajo = '$legajo'";
            $res = toba::db('ctrl_asis')->consultar_fila($sql); 
            if(!empty($res['nombre'])){
                return $res['nombre'];
            }
        }

        static function get_fecha_nacimiento($legajo, $cargos_todos=null)
        {
            //$nombre_tabla = self::get_nombre_tabla_legajos_por_estado_cargo($cargos_todos);
            $sql = "SELECT legajo, fec_nacim as fecha_nacimiento
                    FROM reloj.agentes WHERE legajo = '$legajo'";
            $res = toba::db('ctrl_asis')->consultar_fila($sql); 
            if(!empty($res['fecha_nacimiento'])){
                list($y,$m,$d)=explode("-",$res['fecha_nacimiento']); //2011-03-31
                $fecha = $d."/".$m."/".$y;
                return $fecha;
            }
        }

        static function get_edad($legajo, $cargos_todos=null)
        {
            //$nombre_tabla = self::get_nombre_tabla_legajos_por_estado_cargo($cargos_todos);
            $sql = "SELECT legajo, fec_nacim as fecha_nacimiento
                    FROM reloj.agentes WHERE legajo = '$legajo'";
            $res = toba::db('ctrl_asis')->consultar_fila($sql); 
            if(!empty($res['fecha_nacimiento'])){
                list($y,$m,$d)=explode("-",$res['fecha_nacimiento']); //2011-03-31
                $fecha = $d."-".$m."-".$y;
                $dias = explode('-', $fecha, 3);
                $dias = mktime(0,0,0,$dias[1],$dias[0],$dias[2]);
                $edad = (int)((time()-$dias)/31556926 );
                return $edad;
            }
        }

        static function get_estado_civil($legajo, $cargos_todos=null)
        {
           // $nombre_tabla = self::get_nombre_tabla_legajos_por_estado_cargo($cargos_todos);
            $sql = "SELECT legajo, estado_civil, estado_civil as desc_estado_civil
                    FROM reloj.agentes WHERE legajo = '$legajo'";

            $res = toba::db('ctrl_asis')->consultar($sql); 
            return $res;
            /*if(!empty($res['estado_civil'])){
                return $res['estado_civil'];
            } */
        }

        static function get_legajo($legajo)
        {
            $sql = "SELECT legajo, apellido, nombre, fec_nacim, dni, fecha_ingreso, estado_civil, caracter, categoria, agrupamiento, escalafon, 
                           fec_nacim as fecha_nacimiento, cuil
                    FROM reloj.agentes WHERE legajo = '$legajo'";

            return toba::db('mapuche')->consultar_fila($sql); 
        } 

        static function get_cuil($legajo)
        {
            $sql = "SELECT legajo, cuil FROM uncu.legajo WHERE legajo = '$legajo'";

            $res = toba::db('mapuche')->consultar_fila($sql); 
            if(!empty($res['cuil'])){
                return $res['cuil'];
            }
        }
        
        static function get_tipo_sexo($legajo, $cargos_todos=null)
        {
            //$nombre_tabla = self::get_nombre_tabla_legajos_por_estado_cargo($cargos_todos);
            $sql = "SELECT tipo_sexo FROM reloj.agentes WHERE legajo = '$legajo'";
            $res = toba::db('ctrl_asis')->consultar_fila($sql); 
            if(!empty($res['tipo_sexo'])){
                return $res['tipo_sexo'];
            }
        }


    function get_vista_familiares($filtro=array())
    {
        $where = array();
        if (isset($filtro['legajo'])) {
                $where[] = "nro_legaj = '".$filtro['legajo']."'";
        }
        if (isset($filtro['desc_nombre'])) {
                $where[] = "desc_nombre ILIKE ".quote("%{$filtro['desc_nombre']}%");
        }

        $sql = "SELECT nro_legaj, codc_paren, sino_menor, tipo_sexo, desc_apell, desc_nombre, fec_nacim, tipo_docum, nro_docum, codc_estcv, domicilio, cuil
				FROM uncu.datos_familiares";

        if (count($where)>0) {
                $sql = sql_concatenar_where($sql, $where);
        }

        return toba::db('mapuche')->consultar($sql); 
    }

    function get_vista_domicilio($filtro=array())
    {
        $where = array();
        if (isset($filtro['legajo'])) {
                $where[] = "legajo = '".$filtro['legajo']."'";
        }
        if (isset($filtro['desc_nombre'])) {
                $where[] = "desc_nombre ILIKE ".quote("%{$filtro['desc_nombre']}%");
        }

        $sql = "SELECT legajo, pais, provincia, codigo_postal, localidad, codc_cara_manzana, 
                       zona_paraje_barrio, calle, numero, piso, dpto_oficina, telefono, 
                       telefono_celular
                  FROM uncu.domicilio";

        if (count($where)>0) {
                $sql = sql_concatenar_where($sql, $where);
        }

        return toba::db('mapuche')->consultar($sql); 
    }

        static function get_domicilio($legajo)
        {
            $sql = "SELECT legajo, calle, zona_paraje_barrio, numero, piso, localidad, codigo_postal 
                     FROM reloj.domicilio 
                     WHERE legajo = '$legajo'";
            $res = toba::db('ctrl_asis')->consultar_fila($sql); 

            if(!empty($res['legajo'])){

                
                $domicilio =  ''; 

                $zona_paraje_barrio = str_replace(' ', '', $res['zona_paraje_barrio']);
                if(!empty($zona_paraje_barrio)){
                $domicilio.=  $res['zona_paraje_barrio'].'. ';
                }

                $calle = str_replace(' ', '', $res['calle']);
                if(!empty($calle)){
                $domicilio.=  $res['calle'].' ';
                }

                $numero = str_replace(' ', '', $res['numero']);
                if(!empty($numero)){
                $domicilio.=  $res['numero'].' ';
                }

                $piso = str_replace(' ', '', $res['piso']);
                if(!empty($piso)){
                $domicilio.=  'Piso: '.$res['piso'].' ';
                }

                $dpto = str_replace(' ', '', $res['dpto_oficina']);
                if(!empty($dpto)){
                $domicilio.=  'Dpto.: '.$res['dpto_oficina'];
                }

                return $domicilio;
            }
        } 

        static function get_localidad($legajo)
        {
            $sql = "SELECT legajo, codigo_postal, localidad
                     FROM reloj.domicilio 
                     WHERE legajo = '$legajo'";
            $res = toba::db('ctrl_asis')->consultar_fila($sql); 

            if(!empty($res['legajo'])){

                
                $localidad =  ''; 

                $loc = str_replace(' ', '', $res['localidad']);
                if(!empty($loc)){
                $localidad.=  $res['localidad'].' ';
                }

                $codigo_postal = str_replace(' ', '', $res['codigo_postal']);
                if(!empty($codigo_postal)){
                $localidad.=  ' CP: '.$res['codigo_postal'];
                }
                
                return $localidad;
            }
        }

    static function get_vista_dependencia_completa($filtro=array())
    {
        $where = array();
		//$filtro['cod_depcia'] =4;
        if (isset($filtro['cod_depcia'])) {
            $where[] = "cod_depcia = '".$filtro['cod_depcia']."'";
        }

        if (isset($filtro['desc_depcia'])) {
            $where[] = "desc_depcia ILIKE ".quote("%{$filtro['desc_depcia']}%");
        }

        $sql = "SELECT cod_depcia, cod_depcia as dependencia, desc_depcia, cod_depcia||'. '||desc_depcia as descripcion
                  FROM uncu.dependencia 
                  ORDER BY cod_depcia";

        if (count($where)>0) {
                $sql = sql_concatenar_where($sql, $where);
        }

        return toba::db('mapuche')->consultar($sql); 
    }

    static function get_vista_dependencia_con_damsu($filtro=array())
    {

        $sql = "SELECT cod_depcia, cod_depcia as dependencia, desc_depcia, cod_depcia||'. '||desc_depcia as descripcion
                  FROM uncu.dependencia 

                UNION 

                SELECT '64', '64' as dependencia, 'DAMSU', '64. DAMSU' as descripcion

                  ORDER BY cod_depcia";

        return toba::db('mapuche')->consultar($sql); 
    }

    static function get_vista_dependencia($filtro=array())
    {
        $where = array();
        if(!empty($_SESSION['dependencia'])){

            if($_SESSION['dependencia'] == 53 and $_SESSION['operador'] == true ){ //sec. gestion

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
                $where[] = "cod_depcia IN ('01','15','21','22','24','25','27','28','29','31','32','33','34','35','37','42','43','44','46','50','53','57','60','68','92','94')";

            }elseif($_SESSION['dependencia'] == 32 and $_SESSION['operador'] == true ){ //sec. extension universitaria
                $where[] = "cod_depcia IN ('15','32')"; //organismos artisiticos y sec. extension universitaria

            }elseif($_SESSION['dependencia'] == 33 and $_SESSION['operador'] == true ){ //sec. extension universitaria
                $where[] = "cod_depcia IN ('31','33','34','24')"; //organismos artisiticos y sec. extension universitaria

            }
            elseif($_SESSION['dependencia'] == 21 and $_SESSION['operador'] == true ){ //obras
                $where[] = "cod_depcia IN ('21','29')"; //obras

            }elseif($_SESSION['dependencia'] == 64){ //DAMSU
                $sql = "SELECT '64' as cod_depcia, '64' as dependencia, 'DAMSU' as desc_depcia, '64. DAMSU' as descripcion";
                return toba::db('mapuche')->consultar($sql);
            }else{
                $where[] = "cod_depcia = '".$_SESSION['dependencia']."'";
            }
            
        }elseif(isset($filtro['cod_depcia'])) {

            if($filtro['cod_depcia'] == '64'){
                $sql = "SELECT '64' as cod_depcia, '64' as dependencia, 'DAMSU' as desc_depcia, '64. DAMSU' as descripcion";
                return toba::db('mapuche')->consultar($sql);
            }else{
                $where[] = "cod_depcia = '".$filtro['cod_depcia']."'";
            }          
        }

        if (isset($filtro['desc_depcia'])) {
                $where[] = "desc_depcia ILIKE ".quote("%{$filtro['desc_depcia']}%");
        }

        $sql = "SELECT cod_depcia, cod_depcia as dependencia, desc_depcia, cod_depcia||'. '||desc_depcia as descripcion
                  FROM uncu.dependencia ORDER BY cod_depcia";

        if (count($where)>0) {
                $sql = sql_concatenar_where($sql, $where);
        }

        return toba::db('mapuche')->consultar($sql); 
    }

    function get_dependencias_legajo_por_estado_cargo($legajo, $cargos_todos, $cod_depcia=null)
    {
        return self::get_dependencias_legajo($legajo, $cod_depcia, $cargos_todos);
    }
    static function get_dependencias_legajo($legajo, $cod_depcia = null, $cargos_todos=null)
        {
            $where = array();
            if(isset($cod_depcia)){
                 $where[] = "t_l.cod_depcia = '".$cod_depcia."'";
            }

            //Si necesita buscar legajos por cargos inactivos (1)
            $nombre_tabla = self::get_nombre_tabla_legajos_por_estado_cargo($cargos_todos);

            $sql = "SELECT t_l.legajo, 
                           t_l.cod_depcia, t_d.desc_depcia, 
                           t_l.agrupamiento, t_a.codagrup, t_a.descagrup 
                      FROM $nombre_tabla t_l, uncu.dependencia as t_d, uncu.agrupamientos as t_a
                     WHERE t_l.legajo =  '$legajo'
                       AND t_d.cod_depcia = t_l.cod_depcia 
                       AND t_a.codagrup = t_l.agrupamiento 
                     ORDER BY t_d.cod_depcia ASC, t_a.descagrup ASC";
        
            if (count($where)>0) {
                    $sql = sql_concatenar_where($sql, $where);
            }

            return toba::db('mapuche')->consultar($sql); 
        }

    static function get_dependencias_por_legajo_sin_repetir_cargos($legajo, $cod_depcia = null)
        {
            $where = array();
            if(isset($cod_depcia)){
                 $where[] = "t_l.cod_depcia = '".$cod_depcia."'";
            }

            $sql = "SELECT DISTINCT t_l.legajo, 
                    t_l.cod_depcia,
                    t_l.agrupamiento
                FROM uncu.legajo t_l, uncu.dependencia as t_d, uncu.agrupamientos as t_a
                WHERE t_l.legajo  =  '$legajo'
                AND t_d.cod_depcia = t_l.cod_depcia 
                AND t_a.codagrup = t_l.agrupamiento 
                ORDER BY t_l.legajo, t_l.cod_depcia ASC";
        
            if (count($where)>0) {
                    $sql = sql_concatenar_where($sql, $where);
            }

            return toba::db('mapuche')->consultar($sql); 
        }

        static function get_dependencia($cod_depcia)
        {

            if($cod_depcia < 10){ $cod_depcia = '0'.$cod_depcia; }

            $sql = "SELECT t_d.cod_depcia, t_d.desc_depcia, t_d.desc_depcia as dependencia
                      FROM uncu.dependencia as t_d
                     WHERE t_d.cod_depcia =  '$cod_depcia'";
            
             return toba::db('mapuche')->consultar_fila($sql); 
        }      

    function get_agrupamientos($filtro=array())
    {
        $where = array();
        if (isset($filtro['codagrup'])) {
                $where[] = "codagrup = '".$filtro['codagrup']."'";
        }
        if (isset($filtro['descagrup'])) {
                $where[] = "descagrup ILIKE ".quote("%{$filtro['descagrup']}%");
        }

        $sql = "SELECT codagrup, descagrup
                  FROM uncu.agrupamientos";

        if (count($where)>0) {
                $sql = sql_concatenar_where($sql, $where);
        }

        return toba::db('mapuche')->consultar($sql); 
    }             

    function get_datos_agente($filtro=array())
    {
        $where = array();
       
        //$nombre_tabla = "uncu.legajo";
        if (isset($filtro['legajo'])) {
                $where[] = "t_l.legajo = '".$filtro['legajo']."'";
        }
        //if (isset($filtro['cargos_todos']) && $filtro['cargos_todos'] == "1") {
                $nombre_tabla = "reloj.agentes";
        //}

        $sql = "SELECT t_l.legajo, t_l.apellido, t_l.nombre, t_l.fec_nacim, t_l.dni, t_l.fecha_ingreso, t_l.estado_civil, 
                       t_l.caracter, t_l.categoria, t_l.agrupamiento, t_l.escalafon, 
                       t_l.fec_nacim as fecha_nacimiento, t_l.cuil,
                       t_d.pais, t_d.provincia, t_d.codigo_postal, t_d.localidad, t_d.manzana, 
                       t_d.zona_paraje_barrio, t_d.calle, t_d.numero, t_d.piso, t_d.dpto_oficina, t_d.telefono, 
                       t_d.telefono_celular
                  FROM $nombre_tabla as t_l LEFT JOIN reloj.domicilio as t_d
                  ON t_l.legajo = t_d.legajo";

        if (count($where)>0) {
                $sql = sql_concatenar_where($sql, $where);
        }
       
        return toba::db('ctrl_asis')->consultar($sql); 
    }

    static function get_nombre_tabla_legajos_por_estado_cargo($cargos_todos){
        return $cargos_todos == "1" ? "uncu.legajo_cargos" : "uncu.legajo";
    }
	static function get_legajos_email ($legajo){
	//	ei_arbol($legajo);
		$sql = "SELECT  legajo||' - '||apellido||', '||nombre as descripcion , email
                    FROM reloj.agentes 
                    WHERE legajo = '$legajo'
                    ORDER BY apellido, legajo ASC";
		return toba::db('ctrl_asis')->consultar($sql); 
	}
     static function get_legajos_autoridad($legajo)
        {
            $sql = "SELECT legajo, apellido, nombre 
                    FROM reloj.agentes 
                    WHERE legajo =$legajo
                    --and  ESCALAFON =  'AUTO'
                   -- ORDER BY apellido, legajo ASC";

            return toba::db('ctrl_asis')->consultar($sql); 
        }

      static function get_legajos_jefes_fca ($legajo){
        
        $sql = "SELECT  distinct legajo  as superior , apellido||', '||nombre as descripcion 
                    FROM reloj.agentes
                    WHERE legajo = $legajo
                  --  ORDER BY apellido, legajo ASC";
        return toba::db('ctrl_asis')->consultar($sql); 
        }   
      static function get_legajos_fca_cargos ($legajo){
        $sql = "SELECT  distinct legajo , apellido||', '||nombre as descripcion 
                    FROM reloj.agentes
                    WHERE legajo = $legajo
                  --  ORDER BY apellido, legajo ASC";
        return toba::db('ctrl_asis')->consultar($sql); 
        }     

}

?>