<?php
class dt_inasistencia extends comision_datos_tabla
{
    
    function get_inasistencia_sub($legajo_cat, $legajo_dep, $filtro)
    {
        $resultado_cat = [];
        $resultado_dep = [];
       if (!empty($legajo_cat)) {
            $legajos = array_column($legajo_cat, 'legajo');
       }
       if (!empty($legajo_dep)) {
            $legajos1 = array_column($legajo_dep, 'legajo');
            $leg =array_merge($legajos,$legajos1);
        } else {
            $leg=$legajos;
        }
        
        $in =  $in = " AND legajo in (" . implode(',', $leg) . ")";
      
        if (isset($filtro)) {
      
             if (stripos($filtro, 'in') !== false) {
                $bandera =true; 
                $in = null;
             } else {
                $bandera = false;
             } 
      
            if(stripos($filtro,'fecha') == false) {
                if ($bandera){
                    $filtro = $filtro ." AND fecha >= CURRENT_DATE - INTERVAL '30 days'";
                
                 }else {
                $filtro = "fecha >= CURRENT_DATE - INTERVAL '30 days'";
                }
                
            }
        
        // Solo una consulta para todos los legajos del filtro
        $resultado_cat =$this->obtener_resultado_legajos($in, $filtro,$legajo_cat[0]['nombre_catedra']);
    } else {
        $filtro = "fecha >= CURRENT_DATE - INTERVAL '30 days'";
      $resultado_cat =$this->obtener_resultado_legajos($in, $filtro,$legajo_cat[0]['nombre_catedra']);
        }

    
    $resultado_final = array_values(
        empty($resultado_cat) ?
            (empty($resultado_dep) ? [] : $resultado_dep) :
            (empty($resultado_dep) ? $resultado_cat : array_merge($resultado_cat, $resultado_dep))
    );
   
   
    return $resultado_final;
}

// Función auxiliar para evitar duplicar código
function obtener_resultado_legajos($in, $valor_fecha, $campo_catedra)
{
    $db = toba::db('ctrl_asis');
    $filtro =$valor_fecha . $in;
    // Consulta de horas
    $sql = "SELECT legajo,
                   COUNT(*) AS cuenta,
                   to_char(AVG(horas_requeridad), 'HH24:MI') AS horas_requeridas_prom,
                   to_char(SUM(horas_trabajadas), 'HH24:MI') AS horas_totales,
                   to_char(AVG(horas_trabajadas), 'HH24:MI') AS horas_promedio
            FROM (
                SELECT DISTINCT legajo, fecha, horas_requeridad, horas_trabajadas
                FROM reloj.vm_detalle_pres
                WHERE $filtro

            ) AS sub
            GROUP BY legajo
            ORDER BY legajo";

    $horas = $db->consultar($sql);

    // Consulta de asistencia
    $sql1 = "SELECT DISTINCT cuil, legajo, ayn nombre_completo, agrupamiento, categoria, 
        case when nombre_catedra = '$campo_catedra' then nombre_catedra else departamento END AS nombre_catedra, escalafon, caracter,
                   COUNT(CASE WHEN estado = 'Ausente' THEN 1 END) AS injustificados,
                   COUNT(CASE WHEN estado = 'Presente' THEN 1 END) AS presentes,
                   COUNT(CASE WHEN estado = 'Ausente Justificado' THEN 1 END) AS partes,
                   COUNT(CASE WHEN estado = 'Asuente Justicado Sanidad' THEN 1 END) AS partes_sanidad,
                   COUNT(CASE WHEN estado = 'Ausente Justificado' OR estado = 'Asuente Justicado Sanidad' THEN 1 END) AS justificado
            FROM reloj.vm_detalle_pres
            WHERE $filtro
            GROUP BY legajo, ayn, agrupamiento, categoria, nombre_catedra, departamento , cuil, escalafon, caracter";
    $condicion = $db->consultar($sql1);

    // Combinar ambos arrays
    $resultado = [];
    $combinado = array_merge($horas, $condicion);
    foreach ($combinado as $elemento) {
        $legajo = $elemento['legajo'];
        if (!isset($resultado[$legajo])) {
            $resultado[$legajo] = [];
        }
        $resultado[$legajo] = array_merge($resultado[$legajo], $elemento);
    }

   
   
    return array_values($resultado);
}

}

?>