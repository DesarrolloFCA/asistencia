<?php
class dt_inasistencia extends comision_datos_tabla
{
    
    function get_inasistencia_sub($legajo_cat,$legajo_dep){
           
			if (isset($legajo_cat)){
				for ($i =0;$i<count($legajo_cat);$i++){
					$legajo_agente = $legajo_cat[$i]['legajo'];
					if ($i==0 ){
					$in="($legajo_agente";
					}else {
					$in =$in . ", $legajo_agente";
				}
			}
				$in = $in . ")";	
				
				//horas		
				$sql = "SELECT  legajo,
    			COUNT(*) AS cuenta,
    			SUM(horas_requeridad) AS horas_requeridas_prom,
    			SUM(horas_trabajadas) AS horas_totales,
    			AVG(horas_trabajadas) AS horas_promedio
				FROM (
    					SELECT DISTINCT legajo, fecha, horas_requeridad, horas_trabajadas
    					FROM reloj.vm_detalle_pres
						WHERE legajo in  $in and 
						fecha >= CURRENT_DATE - INTERVAL '30 days'	
					) AS sub
				GROUP BY legajo
				ORDER BY legajo";
				$horas=  toba::db('ctrl_asis')->consultar($sql); 
                
			// Cuenta ausente justificados, presentes y ausentes
				$sql1 = "SELECT  distinct cuil, legajo, ayn nombre_completo, agrupamiento , categoria, nombre_catedra, escalafon,caracter,
    			COUNT(CASE WHEN estado = 'Ausente' THEN 1 END) AS injustificados,
    			COUNT(CASE WHEN estado = 'Presente' THEN 1 END) AS presentes,
    			COUNT(CASE WHEN estado = 'Ausente Justificado' THEN 1 END) AS partes,
				COUNT(CASE WHEN estado = 'Asuente Justicado Sanidad' THEN 1 END) AS partes_sanidad,
                COUNT(CASE WHEN estado = 'Ausente Justificado' OR estado = 'Asuente Justicado Sanidad' THEN 1 END) as justificado
				FROM reloj.vm_detalle_pres
				WHERE legajo in  $in and 
						fecha >= CURRENT_DATE - INTERVAL '30 days'	
				GROUP BY legajo, ayn, agrupamiento, categoria, nombre_catedra,cuil,escalafon,caracter";
				$condicion = toba::db('ctrl_asis')->consultar($sql1); 
							
			$resultado = [];

// Combinamos ambos arrays
$combinado = array_merge($horas, $condicion);

// Agrupamos por legajo
foreach ($combinado as $elemento) {
    $legajo = $elemento['legajo'];
    if (!isset($resultado[$legajo])) {
        $resultado[$legajo] = [];
    }
    $resultado[$legajo] = array_merge($resultado[$legajo], $elemento);
}

// 1) Contar cuántas veces aparece cada nombre_catedra
$contador_catedras = [];
foreach ($resultado as $elemento) {
    if (isset($elemento['nombre_catedra'])) {
        $nombre_catedra = $elemento['nombre_catedra'];
        if (!isset($contador_catedras[$nombre_catedra])) {
            $contador_catedras[$nombre_catedra] = 0;
        }
        $contador_catedras[$nombre_catedra]++;
    }
}

// 2) Obtener la más repetida
$nombre_catedra_mas_repetida = null;
$max_repeticiones = 0;
foreach ($contador_catedras as $catedra => $cuenta) {
    if ($cuenta > $max_repeticiones) {
        $max_repeticiones = $cuenta;
        $nombre_catedra_mas_repetida = $catedra;
    }
}

// 3) Colocar la más repetida en todos los elementos
foreach ($resultado as &$elemento) {
    $elemento['nombre_catedra'] = $nombre_catedra_mas_repetida;
}
unset($elemento); // buenas prácticas

// 4) Si quieres un array indexado:
$resultado_cat = array_values($resultado);

		}
		 if(isset($legajo_dep)){
			for ($i =0;$i<count($legajo_dep)-1;$i++){
					$legajo_agente = $legajo_dep[$i]['legajo'];
					if ($i==0 ){
					$in="($legajo_agente";
					}else {
					$in =$in . ", $legajo_agente";
				}
			}
				$in = $in . ")";	
				
				//horas		
				$sql = "SELECT  legajo,
    			COUNT(*) AS cuenta,
    			SUM(horas_requeridad) AS horas_requeridas_prom,
    			SUM(horas_trabajadas) AS horas_totales,
    			AVG(horas_trabajadas) AS horas_promedio
				FROM (
    					SELECT DISTINCT legajo, fecha, horas_requeridad, horas_trabajadas
    					FROM reloj.vm_detalle_pres
						WHERE legajo in  $in and 
						fecha >= CURRENT_DATE - INTERVAL '30 days'	
					) AS sub
				GROUP BY legajo
				ORDER BY legajo";
				$horas=  toba::db('ctrl_asis')->consultar($sql); 
			// Cuenta ausente justificados, presentes y ausentes
				$sql1 = "SELECT  distinct cuil, legajo, ayn nombre_completo, agrupamiento , categoria, departamento nombre_catedra, escalafon,caracter,
    			COUNT(CASE WHEN estado = 'Ausente' THEN 1 END) AS injustificados,
    			COUNT(CASE WHEN estado = 'Presente' THEN 1 END) AS presentes,
    			COUNT(CASE WHEN estado = 'Ausente Justificado' THEN 1 END) AS partes,
				COUNT(CASE WHEN estado = 'Asuente Justicado Sanidad' THEN 1 END) AS partes_sanidad,
                COUNT(CASE WHEN estado = 'Ausente Justificado' OR estado = 'Asuente Justicado Sanidad' THEN 1 END) as justificado
				FROM reloj.vm_detalle_pres
				WHERE legajo in  $in and 
						fecha >= CURRENT_DATE - INTERVAL '30 days'	
				GROUP BY legajo, ayn, agrupamiento, categoria, departamento,cuil,escalafon,caracter";
				$condicion = toba::db('ctrl_asis')->consultar($sql1); 
							
			$resultado = [];
    
   			 // Combinamos ambos arrays
   			$combinado = array_merge($horas, $condicion);
			
    // Agrupamos por legajo
    		foreach ($combinado as $elemento) {
        		$legajo = $elemento['legajo'];
       			if (!isset($resultado[$legajo])) {
            		$resultado[$legajo] = [];
        		}
        		$resultado[$legajo] = array_merge($resultado[$legajo], $elemento);
   			 }
			$resultado_dep = array_values($resultado);
		 }
		 $resultado_final = array_values(
   		 empty($resultado_cat) ? 
        (empty($resultado_dep) ? [] : $resultado_dep) :
        (empty($resultado_dep) ? $resultado_cat : array_merge($resultado_cat, $resultado_dep)));
         //   ei_arbol($resultado_final);
        return $resultado_final;
    }
}

?>