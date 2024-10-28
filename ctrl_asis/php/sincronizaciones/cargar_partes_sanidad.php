<?php
    //Este procedimiento busca partes de sanidad cerrados y eliminados integrandolos a la base
    // de asistencia para su posterior uso en reportes
 
    $filtro_sanidad['fecha_desde'] = date("2019-01-01", strtotime("-1 month"));
    $filtro_sanidad['fecha_hasta'] = date("2024-10-01");
    $filtro_sanidad['estado'] = 'C';
    $filtro_sanidad['motivos_sincronizacion'] = 1;
    $partes_cerrados = toba::componente('vistas_sanidad')->get_listado($filtro_sanidad);
    //ei_arbol($partes_cerrado);
    foreach ($partes_cerrados as $key => $parte_cerrado) {
        toba::componente('dt_parte')->guardar_parte_desde_sanidad($parte_cerrado);
    }

    $filtro_sanidad['estado'] = 'E';
    $filtro_sanidad['eliminados'] = 1;
    $partes_eliminado = toba::componente('vistas_sanidad')->get_listado($filtro_sanidad);
    foreach ($partes_eliminado as $key => $parte_eliminado) {
        toba::componente('dt_parte')->guardar_parte_desde_sanidad($parte_eliminado);
    } 

    echo("Sincronización con éxito");                                                                                                           
?>