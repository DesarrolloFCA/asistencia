<?php

class licencia_anual {
    function disponibilidad($a, $p, $d)//agenete, fecha desde, dias
    {   if (isset($l['agente_id']))
        {
            $where[] = "reloj.vacaciones_agentes.agente_id = " . quote($l['agente_id']);
        }
        if (isset($p['primer']))
        {
            $where[] = "reloj.vacaciones_agentes.periodo ilike '" . $p . "'";
        }

        $sql = "SELECT
                    reloj.vacaciones_agentes.vacaciones_agente_id,
                    reloj.vacaciones_agentes.agente_id,
                    reloj.vacaciones_agentes.periodo,
                    reloj.vacaciones_agentes.dias_totales,
                    reloj.vacaciones_agentes.dias_disponibles
                    FROM
                    reloj.vacaciones_agentes
                ";
         if (count($where) > 0)
        {
            $sql = sql_concatenar_where($sql, $where);
        }
        $s = consultar_fuente($sql);
        foreach($s as $sr)
        {
            if($sr['dias_disponibles'] < $d)
                return FALSE;
            else
                return TRUE;
        }
    }
    
    function set_distribucion_vacaciones($a, $s)
    {
        $sql = "INSERT INTO reloj.vacaciones_distribucion(
                            agente_id, periodo, fecha_desde, fecha_hasta, 
                            dias, asigna, fecha, hora)
                    VALUES (".$a['agente_id'].", '".$s['periodo']."', '".$s['fecha_desde']."', '".$s['fecha_hasta']."', '".$s['dias']."', 
                            ,'".toba::usuario()->get_nombre()."', '".date('Y-m-d')."', '".date('H:i:s')."')";
        consultar_fuente($sql);
    }
}
