<?php
class ci_control_asistencia extends ctrl_asis_ci
{
    protected $s__datos_filtro;
    protected $s__datos;

    protected $s__seleccion;
    protected $s__limite_envio_masivo = 5;

	//-----------------------------------------------------------------------------------
	//---- Configuraciones --------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf()
	{
	}

    //---- Filtro -----------------------------------------------------------------------

    function conf__filtro(toba_ei_formulario $filtro)
    {
        if (isset($this->s__datos_filtro)) {
            $filtro->set_datos($this->s__datos_filtro);
        }else{
            $f['anio'] = date("Y");
            $f['mes']  = date("m");
            if(!empty($_SESSION['dependencia'])){ 
                $f['cod_depcia']  = $_SESSION['dependencia'];
            }
            if(!empty($_SESSION['agente'])){ 
                $f['legajo']  = $_SESSION['agente'];
            }
            $filtro->set_datos($f);
        }
    }

    function evt__filtro__filtrar($datos)
    {
        $this->s__datos_filtro = $datos;
    }

    function evt__filtro__cancelar()
    {
        unset($this->s__datos_filtro);
    }	


    //---- Cuadro resumen -------------------------------------------------------------------

    function conf__cuadro_resumen(toba_ei_cuadro $cuadro)
    {
        if (isset($this->s__datos_filtro)) {

            if (isset($this->s__datos_filtro['anio'])) {
                $y = $this->s__datos_filtro['anio'];
                $m = $this->s__datos_filtro['mes'];
                $d = date("d",(mktime(0,0,0,$m+1,1,$y)-1));

                $this->s__datos_filtro['fecha_desde'] = $y."-".$m."-01";
                $this->s__datos_filtro['fecha_hasta'] = $y."-".$m."-".$d;
            }

            $_SESSION['fecha_desde'] = $this->s__datos_filtro['fecha_desde'];
            $_SESSION['fecha_hasta'] = $this->s__datos_filtro['fecha_hasta'];
            $_SESSION['basedatos']   = $this->s__datos_filtro['basedatos'];  
            
            $agentes =  $this->dep('mapuche')->get_agentes_control_asistencia($this->s__datos_filtro);

            $filtro['fecha_desde'] = $this->s__datos_filtro['fecha_desde'];
            $filtro['fecha_hasta'] = $this->s__datos_filtro['fecha_hasta'];
            if (isset($this->s__datos_filtro['basedatos'])) {
            $filtro['basedatos'] = $this->s__datos_filtro['basedatos'];
            }
            $this->s__datos = $this->dep('access')->get_lista_resumen($agentes,$filtro);
            $cuadro->set_datos($this->s__datos);   

            list($y,$m,$d) = explode('-', $this->s__datos_filtro['fecha_desde']);
            $fecha_desde = "$d/$m/$y";
            list($y,$m,$d) = explode('-', $this->s__datos_filtro['fecha_hasta']);
            $fecha_hasta = "$d/$m/$y";  
            $cuadro->set_titulo("Asistencia desde el ".$fecha_desde.", hasta el ".$fecha_hasta);
        
        }
    }

    function evt__cuadro_resumen__multiple($seleccion)
    {
        $this->s__seleccion = $seleccion;
    }
  
    function evt__cuadro_resumen__enviar($datos)
    {
        $this->s__seleccion[0]['legajo'] = $datos['legajo'];
        $this->enviar_asistencia($this->s__seleccion);
    }

        function conf_evt__cuadro_resumen__enviar($evento, $fila)
        {
            if (empty($this->s__datos[$fila]['email'])) {  $evento->anular();   }
        }
        function conf_evt__cuadro_resumen__multiple($evento, $fila)
        {
            if (empty($this->s__datos[$fila]['email'])) {  $evento->anular();   }
        }  

    /*function evt__cuadro__seleccion($datos)
    {
            $this->dep('datos')->cargar($datos);
            $this->set_pantalla('pant_edicion');
    }*/

    //---- Cuadro resumen -------------------------------------------------------------------

    function conf__cuadro_imprimir(toba_ei_cuadro $cuadro)
    {
        if (isset($this->s__datos)) {
            $cuadro->set_datos($this->s__datos);   

            list($y,$m,$d) = explode('-', $this->s__datos_filtro['fecha_desde']);
            $fecha_desde = "$d-$m-$y";
            list($y,$m,$d) = explode('-', $this->s__datos_filtro['fecha_hasta']);
            $fecha_hasta = "$d-$m-$y";  
            $cuadro->set_titulo("Asistencia desde el ".$fecha_desde.", hasta el ".$fecha_hasta);
        }
    }

    //---- FUNCIONES -------------------------------------------------------------------

    function resetear()
    {
        #$this->dep('datos')->resetear();
        unset($this->s__datos);
        unset($this->s__seleccion);
        $this->set_pantalla('pant_seleccion');
    }

    function vista_excel(toba_vista_excel $salida)
    {
        $excel = $salida->get_excel();
        $excel->setActiveSheetIndex(0);
        $excel->getActiveSheet()->setTitle('Control de asistencia');
        $this->dependencia('cuadro_imprimir')->vista_excel($salida);
    }

    function enviar_asistencia($seleccion)
    {
        require_once('3ros/phpmailer/class.phpmailer.php');

        if(count($seleccion)>0){
            ei_arbol($seleccion,'seleccion');
        foreach($seleccion as $s){

            foreach($this->s__datos as $d){

                //set dato a enviar
                if($s['legajo'] == $d['legajo']){
                    $datos = $d;
                    break;
                }
            }

            if(!empty($datos['email'])){
                //---------------------------------------------------------------------

                //Completamos parametros que se envian con la funcion de envio de mensajes por email -----------------    
                $email_destino =    $datos['email'];                         
                $parametros['correo_destino']           = $email_destino; 
                #$parametros['reply_email']              = $vendedor['email_contacto']; 
                #$parametros['reply_nombre']             = $vendedor['razon_vendedor']; 

                list($y,$m,$d) = explode('-', $this->s__datos_filtro['fecha_desde']);
                $fecha_desde = "$d-$m-$y";
                list($y,$m,$d) = explode('-', $this->s__datos_filtro['fecha_hasta']);
                $fecha_hasta = "$d-$m-$y";  
                $parametros['asunto']                   = $datos['nombre_completo'].' - Asistencia desde el '.$fecha_desde.', hasta el '.$fecha_hasta; 
                $parametros['contenido_mensaje']        = '<div>
                                        <p></p> 
                                        <p>DETALLE ASISTENCIA:</p>
                                        <p></p> 
                            </div>';

                $parametros['encabezado_mensaje']      = 'Asistencia desde el '.$fecha_desde.', hasta el '.$fecha_hasta;
                $parametros['encabezado_mensaje_txt']  = strip_tags($parametros['encabezado_mensaje']);                                                                
                $parametros['contenido_mensaje_txt']   = strip_tags($parametros['contenido_mensaje']);

                #$parametros['adjunto1']                = $path.$nombre_fichero;
                #$parametros['correo_copia']           = $vendedor['email_contacto'];
                $parametros['correo_copia_oculta']     = $vendedor['email_contacto'];

                try {
                    $this->enviar_mail($parametros);
                    toba::notificacion()->agregar("El mensaje se ha enviado correctamente al correo ".$email_destino.".", "info");

                } catch (Exception $e) {

                    $error = 'Excepción capturada: '.$e->getMessage();
                    toba::notificacion()->agregar($error, "error");

                    $error = "Problemas enviando correo electrónico.<br/>".$mail->ErrorInfo;
                    toba::notificacion()->agregar($error, "error");
                }

                //---------------------------------------------------------------------------------------------------
            }

        }
        }else{
            toba::notificacion()->agregar('No hay selección', "info");

        }

    }

     function enviar_mail($parametros,$uso='predeterminado'){

        //Obtengo datos necesarios para mandar correo
        #$datos_correo =   toba::tabla('correo_envio')->get_correo_envio_por_uso($uso);
        /*
        correo_destino
        asunto
        encabezado_mensaje
        encabezado_mensaje_txt
        contenido_mensaje
        contenido_mensaje_txt
        reply_email y reply_nombre opcionales
        */
        require('enviar_mail.php');
        //return true;
    }

    //---- EVENTOS CI -------------------------------------------------------------------

    function evt__envio_seleccion()
    {  
        $this->enviar_asistencia($this->s__seleccion);
    }

    function evt__envio_masivo()
    {

        if(count($this->s__datos)>0){
            unset($this->s__seleccion);
            $cont = 0;
            $limite = $this->s__limite_envio_masivo;
            foreach($this->s__datos as $dato){
                if( !empty($dato['email']) and $cont < $limite ){
                    $this->s__seleccion[]['legajo'] = $dato['legajo'];
                    $cont++;
                }
            }
        }

        if(count($this->s__seleccion)>0){
            $this->enviar_asistencia($this->s__seleccion);
        }else{
            toba::notificacion()->agregar("No hay datos para enviar.", "error");
        }
    }

}

?>