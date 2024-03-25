<?php 
ini_set('memory_limit','512M');
class vistas_hander extends toba_datos_relacion
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

    //Las descripción del estado del EF se cargan a partir del resultado de este método. El formato de retorno es una cadena simple. 
    static function get_fichada($filtro=array())
    {

        $where = array();

        if (isset($filtro['fecha'])) {
            $where[] = " CONVERT(varchar(10), C.CHECKTIME, 120) = '".$filtro['fecha']."'";
        }

        if (isset($filtro['fecha_desde'])) {
                list($y,$m,$d)=explode("-",$filtro['fecha_desde']); //2011-03-31
                $fecha_desde = $y."-".$m."-".$d;
                $where[] = "CONVERT(varchar(10), FechaHora, 120) >= ".quote($fecha_desde);
        }
        if (isset($filtro['fecha_hasta'])) {
                list($y,$m,$d)=explode("-",$filtro['fecha_hasta']); //2011-03-31
                $fecha_hasta = $y."-".$m."-".$d; //." 23:59:59";
                $where[] = "CONVERT(varchar(10), FechaHora, 120) <= ".quote($fecha_hasta);
        }        

        #if (isset($filtro['legajo'])) {
        #    $where[] = "U.Badgenumber = ".quote($filtro['legajo']);
        #}

        if (isset($filtro['badgenumber'])) {
            $where[] = "U.Badgenumber = ".quote($filtro['badgenumber']);
        }        

        if (isset($filtro['id_dispositivo'])) {
            $where[] = "M.ID = ".quote($filtro['id_dispositivo']);
        }

        $sql = "SELECT id, Convert(Int, Tarjeta) as Tarjeta, Evento, 
        CONVERT(varchar(19), FechaHora, 120) as FechaHora,  Reloj, 
        CONVERT(varchar(19), Exportacion, 120) as Exportacion 
        FROM  Fichada ORDER BY Tarjeta, id ASC"; 

        if (count($where)>0) {
            $sql = sql_concatenar_where($sql, $where);
        }
        //-------------------------------------------
        //$connectionInfo = array( "UID"=>"citreloj", "PWD"=>"reloj2015", "Database"=>"Hander");
        //$conn = sqlsrv_connect( '172.22.32.27\SQLESPRESS,2523', $connectionInfo);
        $conf_hander = file_get_contents('../php/datos/conf_hander.txt');
        list($UID,$PWD,$DB,$HOST)=explode(';',$conf_hander); //UID:sa,PWD:CitReloj2015,DB:access,HOST:CIT-RELOJ\ASISTENCIA
        $connectionInfo = array( "UID"=>$UID, "PWD"=>$PWD, "Database"=>$DB); //$connectionInfo = array( "UID"=>"citreloj", "PWD"=>"reloj2015", "Database"=>"Hander");
        $conn = sqlsrv_connect($HOST, $connectionInfo); //$conn = sqlsrv_connect( '172.22.32.27\SQLESPRESS,2523', $connectionInfo);

        if( $conn === false ){
        echo "No es posible conectarse al servidor Hander.</br>";
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
        

        return $array;

    }

    function get_marcas($filtro=array()){
        
        $filtro['sin_errores'] = true;
        $array = $this->get_CHECKINOUT($filtro);
        $datos = array();

        if(count($array)>0){ 
            
            $cont = 0;
            $row_anterior = array();
            
            foreach($array as $key=>$row){

                switch ($row['CHECKTYPE']) {
                    case 'I': //Entrada
                    case '1': //Entrada interna

                        //si no el mismo usuario que la marca anterior y la anterior fue entrada, incremetamos contador antes de setear entrada
                        /*if($array[$key_anterior]['USERID'] <> $row['USERID'] and $array[$key_anterior]['CHECKTYPE'] =='I'){ 
                            $cont++;
                        }*/

                        //si la marca anterior es del es el mismo usuario, no es del mismo dia, y no es salida,  ponemos marca de salida a las 24 del mismo dia
                        /*if( $array[$key_anterior]['USERID'] == $row['USERID'] and $array[$key_anterior]['fecha'] <> $row['fecha'] and $array[$key_anterior]['CHECKTYPE'] <> 'O'){ 
                            //set salida final de dia
                            $datos[$cont-1]['salida'] = '23:59:59';
                            #$cont++;
                        
                        }*/


                        //set entrada
                        $datos[$cont]= array(
                            'marca_id'    => $row['LOGID'],
                             #'legajo'    => $row['Badgenumber'],
                            'badgenumber' => $row['Badgenumber'],
                            'fecha'       => $row['fecha'],
                            'entrada'     => substr($row['CHECKTIME'],11,8),
                             #'salida'    =>,
                            'reloj_i'     => $row['device_id']
                            );

                        $row_anterior = $row;

                        break;
                    
                    case 'O': //Salida
                    case '0': //Salida interna


                        //si la marca anterior es del es el mismo usuario, no es del mismo dia, y no es salida,  ponemos marca de salida a las 24 del mismo dia
                        /*if($row['fecha'] =='2015-07-15'){
                            file_put_contents('C:/TEMP/row.txt',print_r($row,true));
                            file_put_contents('C:/TEMP/key_actual.txt',print_r($array[$key],true));
                            file_put_contents('C:/TEMP/key_anterior.txt',print_r($array[$key_anterior],true));
                        }
                        if( $array[$key_anterior]['USERID'] == $row['USERID'] and $array[$key_anterior]['fecha'] <> $row['fecha'] and $array[$key_anterior]['CHECKTYPE'] <> 'O'){ 
                            //set salida final de dia
                            $datos[$cont-1]['salida'] = '23:59:59';
                            #$cont++;
                        
                        }else{*/

                        $datos[$cont]['reloj_o'] = $row['device_id'];
                        $datos[$cont]['fecha']   = $row['fecha'];
                        $datos[$cont]['salida']  = substr($row['CHECKTIME'],11,8);
                        $cont++;

                        //}

                        //si el anteriores el mismo usuario, su marca es de otro dia, agrego entrada hora 0 antes de la salida
                        /*$key_anterior = $key - 1;
                        if(($array[$key_anterior]['USERID'] == $row['USERID']) and ($array[$key_anterior]['fecha'] <> $row['fecha'])){ 

                            //set entrada comienzo de dia
                            $datos[$cont]= array(
                                'marca_id' => $row['LOGID'],
                                #'legajo'   => $row['Badgenumber'],
                                'badgenumber' => $row['Badgenumber'],
                                'fecha'    => substr($row['CHECKTIME'],0,10),
                                'entrada'  => '00:00:00',
                                #'salida'   =>,
                                'reloj'    => $row['device_id']
                                );
                            $cont++;
                        }*/

                        $row_anterior = $row;


                        break;


                    case '#Error':
                    default:


                        break;           
                }

                /*if($key > 0){ //no es el primero 
                    $key_anterior = $key - 1;
                    //si no el mismo usuario que la marca anterior o es es salida, incremetamos contador
                    if($array[$key_anterior]['USERID'] <> $row['USERID'] or $row['CHECKTYPE'] =='O'){ 
                        $cont++;
                    }
                } */

                $row_anterior = $row;

            }
        }
        //-----------------------
        if(isset($filtro['calcular_horas']) and count($datos) > 0){
            foreach($datos as $key=>$dato){
                $datos[$key]['horas']     = $this->restar_horas($dato['entrada'],$dato['salida']);
            }
        }
        return $datos;
    }


    /*static function get_dispositivos()
    {

        $sql = "SELECT M.ID as id_dispositivo,
        M.MachineAlias,
        M.area_id,
        A.areaid, A.areaid as cod_depcia, 
        A.areaname
        FROM Machines as M, personnel_area as A
        WHERE A.id = M.area_id "; 

        //-------------------------------------------
        $connectionInfo = array( "UID"=>"sa", "PWD"=>"reloj2015", "Database"=>"access");
        $conn = sqlsrv_connect( 'citreloj', $connectionInfo);

        if( $conn === false ){
        echo "No es posible conectarse al servidor Hander.</br>";
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


    }*/

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

    /*static function get_dispositivos_dependencia($cod_depcia)
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
        $connectionInfo = array( "UID"=>"sa", "PWD"=>"reloj2015", "Database"=>"access");
        $conn = sqlsrv_connect( 'citreloj', $connectionInfo);

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
            $array[] = $row;
        }

        sqlsrv_free_stmt($result);
        sqlsrv_close($conn);
        //-------------------------------------------

        return $array;

    }*/


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

    function get_lista_resumen($agentes, $filtro=array())
    {

        $fecha_desde = $filtro['fecha_desde'];
        $fecha_hasta = $filtro['fecha_hasta'];

        if (isset($filtro['id_dispositivo'])) {
            $filtro_marca['id_dispositivo'] = $filtro['id_dispositivo'];
        }

        if(count($agentes)>0){
            /*
            Bucle por agente, para calcular presentismo, y razones de los ausentes
            */
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

                //seteamos datos de vacaciones -----------------------------------------------
                /*if(!empty($agente['fec_ingreso'])){
                    $antiguedad = toba::tabla('vacaciones_antiguedad')->get_array_antiguedad($agente['fec_ingreso']);
                    $agentes[$key]['dias_vac_antiguedad']   = $antiguedad['dias']; //utf8_decode($antiguedad['dias'].' días');
                    $agentes[$key]['antiguedad']            = utf8_decode($antiguedad['antiguedad'].' años');


                    //obtenemos dias tomados---------------------------------------
                    $dias_tomados = 0;
                    list($y,$m,$d) = explode('-',$fecha_desde);
                    $filtro_parte['legajo']      = $agente['legajo']; 
                    $filtro_parte['id_motivo']   = '35'; //Vacaciones 
                    $filtro_parte['anio']        = $y; 
                    for ($i=0; $i < 12; $i++) {  // bucle en todos los meses hasta el ultimo mes del año
                        $mes = $i+1;
                        if($mes<10){ $filtro_parte['mes']  = "0".$mes;  }else{  $filtro_parte['mes']  = $mes; }
                        
                        $partes = toba::tabla('parte')->get_listado($filtro_parte);
                        if(count($partes)>0){
                            foreach ($partes as $parte) {
                                $dias_tomados = $dias_tomados + $parte['dias_mes'];
                            }
                        }
                    }

                    $agentes[$key]['dias_vac_tomadas']     = $dias_tomados;
                    $agentes[$key]['dias_vac_disponibles'] = $antiguedad['dias'] - $dias_tomados;

                }*/
                //-----------------------------------------------------------------------------
                    

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

                //--------------------------------------------------------------------------------------------
                //--------------------------------------------------------------------------------------------
                //reviso fecha desde 
                if($fecha_desde < $jornada['fecha_ini']){
                    $fecha_desde = $jornada['fecha_ini'];
                }
                //reviso fecha hasta 
                if(!empty($jornada['fecha_fin']) and $fecha_hasta > $jornada['fecha_fin']){
                    $fecha_hasta = $jornada['fecha_fin'];
                }elseif($fecha_hasta > date("Y-m-d")){ 
                    $fecha_hasta = date("Y-m-d");
                }                   

                //recorremos todos los dias entre fecha_desde y fecha_hasta
                $fechaInicio = strtotime($fecha_desde);
                $fechaFin    = strtotime($fecha_hasta);
        
                for($i=$fechaInicio; $i<=$fechaFin; $i+=86400){

                    $dia = date("Y-m-d", $i);

                    if(toba::tabla('conf_feriados')->hay_feriado($dia)){ //revisamos el día solo si no es feriado

                        $agentes[$key]['feriados']++;

                    }else{

                        $datos_dia = getdate($i);

                        switch ($datos_dia['wday']) { //0 (para Domingo) hasta 6 (para Sábado)
                            
                            case 1: //lunes

                                if($jornada['normal']==1 or $jornada['lunes']==1 ) {
                                    $this->calculo_dia ('lunes', 'lunes', $key, $agentes, $array_marcas, $contador_marcas, $dia);
                                }
                                break;

                            case 2: //martes

                                if($jornada['normal']==1 or $jornada['martes']==1 ) {
                                    $this->calculo_dia ('martes', 'martes', $key, $agentes, $array_marcas, $contador_marcas, $dia);
                                }
                                break;

                            case 3: //miercoles

                                if($jornada['normal']==1 or $jornada['miercoles']==1 ) {
                                    $this->calculo_dia ('miercoles', 'miercoles', $key, $agentes, $array_marcas, $contador_marcas, $dia);
                                }
                                break;

                            case 4: //jueves

                                if($jornada['normal']==1 or $jornada['jueves']==1 ) {
                                    $this->calculo_dia ('jueves', 'jueves', $key, $agentes, $array_marcas, $contador_marcas, $dia);
                                }
                                break;

                            case 5: //viernes

                                if($jornada['normal']==1 or $jornada['viernes']==1 ) {
                                    $this->calculo_dia ('viernes', 'viernes', $key, $agentes, $array_marcas, $contador_marcas, $dia);
                                }
                                break;

                            case 6: //sabado

                                if($jornada['sabado']==1) {
                                    $this->calculo_dia ('sabado', 'sabado', $key, $agentes, $array_marcas, $contador_marcas, $dia);
                                }
                                break;

                            case 0: //domingo

                                if($jornada['domingo']==1) {
                                    $this->calculo_dia ('domingo', 'domingo', $key, $agentes, $array_marcas, $contador_marcas, $dia);
                                }
                                break;

                        }//fin switch

                    }//fin no es feriado

                }//fin recorremos todos los dias entre fecha_desde y fecha_hasta


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

                            //calculamos horas 
                            $horas         = $this->restar_horas($array_marcas[$m]['entrada'],$array_marcas[$m]['salida']);
                            $horas_totales = $this->sumar_horas($horas,$horas_totales);
                            $prom_acum = $this->dividir_horas($horas_totales,$marca['contador_marcas']);//dividendo,divisor    

                        }elseif($marca['descripcion'] == 'Ausente'){

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

                $agentes[$key]['horas_requeridas_prom']      = '06:00';

                if($agentes[$key]['laborables'] > 0){
                    $agentes[$key]['horas_totales']       = $horas_totales;
                    $agentes[$key]['horas_promedio']      = $prom_acum;
                }else{
                    $agentes[$key]['horas_promedio']      = '0:00:00';
                }

                if($agentes[$key]['horas_promedio'] < $agentes[$key]['horas_requeridas_prom']){ //dif negativa
                    $desviacion_horario =  $this->restar_horas($agentes[$key]['horas_promedio'],$agentes[$key]['horas_requeridas_prom']);
                    $agentes[$key]['desviacion_horario']    = "<p style='background-color: #DD4B39; color:#FFFFFF; padding: 2px;'>$desviacion_horario</p>";
                }else{ //dif positiva
                    $desviacion_horario =  $this->restar_horas($agentes[$key]['horas_requeridas_prom'],$agentes[$key]['horas_promedio']);
                    $agentes[$key]['desviacion_horario']    = "<p style='background-color: #00A65A; color:#FFFFFF; padding: 2px;'>$desviacion_horario</p>";
                }
              
                //--------------------------------------------------------------------------------------------
                //--------------------------------------------------------------------------------------------

            }//fin bucle agentes

        }

        /*if (isset($filtro['con_marcas']) and $filtro['con_marcas']==1) {
            $array = array();
            if(count($agentes)>0){
                foreach ($agentes as $key => $value) {
                    if($value['horas_totales']>0){
                        $array[]=$value;
                    }
                }
            }
            return $array;
        }*/


        return $agentes;
    }


    function calculo_dia ($dia_ref, $dia_leyenda, $key, &$agentes, &$array_marcas, &$contador_marcas, $dia)
    {
        $agente = $agentes[$key];
        //------------------------

        $agentes[$key]['laborables']++;
                      
        $id_parte = toba::tabla('parte')->tiene_parte($agente['legajo'], $dia); 
        $id_parte_sanidad = toba::tabla('parte')->tiene_parte_sanidad($agente['legajo'], $dia);     
            
        if($id_parte > 0){ 
            $agente['partes']++; 
            $justificado = true;

            $array_marcas[] = array(
                'legajo'    => $agente['legajo'],
                'fecha'        => $dia,
                'dia'       => $dia_leyenda,
                'descripcion'  => 'Parte' # '.$parte['id_parte'].': '.$parte['motivo']
                 );

        }elseif($id_parte_sanidad > 0){  
            $agentes[$key]['partes_sanidad']++;
            $justificado = true;

            $array_marcas[] = array(
                'legajo'    => $agente['legajo'],
                'fecha'        => $dia,
                'dia'       => $dia_leyenda,
                'descripcion'  => 'Parte' # sanidad '.$parte['id_parte'].': '.$parte['motivo']
                 );

        }else{ //buscamos marcas

            $filtro_marca['badgenumber'] = $agente['dni']; 
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

                $contador_marcas++;
                $array_marcas[] = array(
                    'legajo'    => $agente['legajo'],
                    'fecha'        => $dia,
                    'dia'       => $dia_leyenda,
                    'descripcion'  =>'Ausente',
                    'contador_marcas' =>$contador_marcas,
                     #'prom_acum' => $this->dep('access')->dividir_horas($horas_totales,$contador_marcas)
                     ); 

                //verificar justificacion ----------------------
                $justificado = false;
                
                if($justificado){
                    $agentes[$key]['justificados']++;
                }else{
                    $agentes[$key]['injustificados']++;
                }
                //-----------------------------------------------
            }
        }

    }

}

?>