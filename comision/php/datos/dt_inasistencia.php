<?php
class dt_inasistencia extends comision_datos_tabla
{
    
    function get_inasistencia_sub($legajo_cat, $legajo_dep, $filtro)
{
    
    // 1. Armar condición de fecha
    if (isset($filtro['fecha_desde'])) {
        if ($filtro['fecha_desde']['condicion'] == 'BETWEEN') {
            
            $valor_fecha = "fecha ". $filtro['fecha_desde']['condicion'] . " '" . $filtro['fecha_desde']['valor']['desde'] . "' AND '" . $filtro['fecha_desde']['valor']['hasta'] . "'";
        } else {
            $valor_fecha = "fecha ". $filtro['fecha_desde']['condicion'] . " '" . $filtro['fecha_desde']['valor']. "'";
        }
    } else {
        $valor_fecha = "fecha >= CURRENT_DATE - INTERVAL '30 days'";
    }

    $resultado_cat = [];
    $resultado_dep = [];
	
		
    // 2. Si existe filtro['legajo'], usarlo
    if (isset($filtro['legajo']) && !empty($filtro['legajo']['valor'])) {
        if ($filtro['legajo']['condicion'] == '(') {
            $valores_leg = implode(',', $filtro['legajo']['valor']);
            $in = "($valores_leg)";
        } else {
            $in = "(" . $filtro['legajo']['valor'][0] . ")";
        }
        
        // Solo una consulta para todos los legajos del filtro
        $resultado_cat =$this->obtener_resultado_legajos($in, $valor_fecha,$legajo_cat[0]['nombre_catedra']);
    } else {
        // 3. Si NO hay filtro, usar legajo_cat
        if (!empty($legajo_cat)) {
            $legajos = array_column($legajo_cat, 'legajo');
            $in = "(" . implode(',', $legajos) . ")";
            $resultado_cat = $this->obtener_resultado_legajos($in, $valor_fecha, $legajo_cat[0]['nombre_catedra']);

        }

        // 4. Y también usar legajo_dep
        if (!empty($legajo_dep)) {
            $legajos = array_column($legajo_dep, 'legajo');
            $in = "(" . implode(',', $legajos) . ")";
            $resultado_dep = $this->obtener_resultado_legajos($in, $valor_fecha, $legajo_cat[0]['departamento']);
        }
    }

    // 5. Unir resultados
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
   
    // Consulta de horas
    $sql = "SELECT legajo,
                   COUNT(*) AS cuenta,
                   to_char(SUM(horas_requeridad), 'HH24:MI') AS horas_requeridas_prom,
                   to_char(SUM(horas_trabajadas), 'HH24:MI') AS horas_totales,
                   to_char(AVG(horas_trabajadas), 'HH24:MI') AS horas_promedio
            FROM (
                SELECT DISTINCT legajo, fecha, horas_requeridad, horas_trabajadas
                FROM reloj.vm_detalle_pres
                WHERE legajo in $in
                  AND $valor_fecha
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
            WHERE legajo in $in
              AND $valor_fecha
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