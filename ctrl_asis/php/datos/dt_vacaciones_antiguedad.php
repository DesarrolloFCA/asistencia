<?php
class dt_vacaciones_antiguedad extends toba_datos_tabla
{
    function get_descripciones()
    {
        $sql = "SELECT t_va.id_vacaciones_antiguedad, t_va.descripcion, t_va.antiguedad_min, t_va.antiguedad_max, t_va.dias, t_va.orden, 
        t_vr.id_vac_rango, t_vr.agrupamientos
        FROM reloj.vacaciones_antiguedad as t_va, reloj.vacaciones_rangos as t_vr 
        WHERE t_va.id_vac_rango = t_vr.id_vac_rango 
        ORDER BY t_va.orden"; //t_vr.vac_rango_descripcion, 

        return toba::db('ctrl_asis')->consultar($sql);
    }

    //funcion que recibe la fecha de inicio, y retorna arreglo con dias de vacaciones corresponientes por antiguedad
    function get_array_antiguedad($fecha_incio, $agrupamiento, $anio = null)
    {
       // ei_arbol($fecha_incio,$agrupamiento);
        $fecha = time() - strtotime($fecha_incio);
        if($anio != null && $anio != date("Y")){
            $fecha = mktime(0, 0, 0, 12, 31, $anio) - strtotime($fecha_incio);
        }
        $diferencia = (($fecha / 3600) / 24) / 360;
        $array = array('antiguedad' => '', 'dias' => 0);
        $datos = @$this->get_descripciones();
        if(count($datos)>0){
            foreach($datos as $key=>$dato){
                
                if($diferencia >= $dato['antiguedad_min'] and $diferencia <= $dato['antiguedad_max']){
           
                    $agrupamientos = ' '.$dato['agrupamientos'];

                    if(strpos($agrupamientos, $agrupamiento) > 0){
                    
                        $array = array(
                            'descripcion'=> $dato['descripcion'],
                            'antiguedad' => $diferencia, 
                            'dias' => $dato['dias']);

                        break;
                    }
                }
            }
        }

        return $array;
    }

    //funcion que recibe la fecha de inicio y año que se quiere calcular la antiguedad, y retorna arreglo con dias de vacaciones corresponientes por antiguedad
    function get_array_antiguedad_por_anio($fecha_incio, $agrupamiento, $anio)
    {   
        $fecha = time() - strtotime($fecha_incio);
        if($anio != date("Y")){
            $fecha = mktime(0, 0, 0, 12, 31, $anio) - strtotime($fecha_incio);
            var_dump($fecha);
        }        
        $diferencia = (($fecha / 3600) / 24) / 360;
        $array = array('antiguedad' => '', 'dias' => 0);
      
        $datos = @$this->get_descripciones();

        if(count($datos)>0){
            foreach($datos as $key=>$dato){
                
                if($diferencia >= $dato['antiguedad_min'] and $diferencia <= $dato['antiguedad_max']){
           
                    $agrupamientos = ' '.$dato['agrupamientos'];

                    if(strpos($agrupamientos, $agrupamiento) > 0){
                    
                        $array = array(
                            'descripcion'=> $dato['descripcion'],
                            'antiguedad' => $diferencia, 
                            'dias' => $dato['dias']);

                        break;
                    }
                }
            }
        }

        return $array;
    }

}
?>