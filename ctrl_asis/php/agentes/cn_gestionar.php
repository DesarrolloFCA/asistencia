<?php

include_once 'datos/consultas_agentes.php';
include_once 'datos/licencia_anual.php';
include_once 'libs/datos_fijos.php';
include_once 'libs/utiles.php';
include_once 'libs/cod_error.php';

class cn_gestionar extends ctrl_asis_cn {

    function get_agentes($f)
    {

        $resp = (consultas_agentes::get_agentes($f));
        return $resp[0];
    }

    function get_marcas($a, $p = null)
    {

        return consultas_agentes::recuperar_marcas($a, datos_fijos::primer_ultimo($p));
    }

    function estadisticas($a)
    {
        //informacion de los periodos del año en curso
        $salida = array();
        for ($i = 1; $i <= 12; $i++)
        {
            $p = array('periodo' => $i . '/' . date('Y'));
            $s['periodo'] = $p['periodo'];
            $s['promedio'] = consultas_agentes::horas_promedio_por_periodo($a, datos_fijos::primer_ultimo($p));
            $s['ausentes'] = consultas_agentes::cant_ausentes_por_periodo($a, datos_fijos::primer_ultimo($p));
            $salida[] = $s;
        }
        return $salida;
    }

    function procesar_licencia_anual($a, $d_ml)
    {
        foreach ($d_ml as $r)
        {
            $per = split('-', $r['fecha_desde']);
            if (licencia_anual::disponibilidad($a, $per[0], $r['dias']))
            {
                $salida = array('fecha_desde' => $r['fecha_desde'],
                'fecha_hasta' => utiles::dias_desde_fecha($r['fecha_desde'], $r['dias']), 'dias'=>$r['dias']);
            }else
            {
                toba::notificacion()->agregar('La cantidad de días ingresados es superior al disponible para el periodo ' . $per[0], 'info');
                return;
            }
            ///-///-/-/-/-/-/-/-/-/EJECUTO LA ACTUALIZACION DEL PERIODO-/--/-/-/-/-/-/-/-/-/-/-/
            
            
            ///-///-/-/-/-/-/-/-/-/-/--/-/-/-/-/-/-/-/-/-/-/
            
        }
        ei_arbol($salida);
        ei_arbol($a);
    }

}

?>