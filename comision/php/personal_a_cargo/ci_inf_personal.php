<?php
class inf_personal extends comision_ci
{
	//-----------------------------------------------------------------------------------
	//---- cuadro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro(comision_ei_cuadro $cuadro)
	{
		include("usuario_logueado.php");
		$legajo = usuario_logueado::get_legajo(toba::usuario()->get_id());
		$legajo = $legajo[0]['legajo'];
		if (usuario_logueado::get_jefe($legajo)) {
			$legajo_cat = usuario_logueado::get_legajo_jefe($legajo);
			
			$legajo_dep = usuario_logueado::get_legajo_dir($legajo);
			if (!isset($legajo_cat)){
				for ($i =0;$i<count($legajo_cat)-1;$i++){
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
				COUNT(CASE WHEN estado = 'Asuente Justicado Sanidad' THEN 1 END) AS partes_sanidad
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
			$resultado_cat = array_values($resultado);
		}
		 if(!isset($legajo_dep)){
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
				$sql1 = "SELECT  distinct cuil, legajo, ayn nombre_completo, agrupamiento , categoria, nombre_catedra, escalafon,caracter,
    			COUNT(CASE WHEN estado = 'Ausente' THEN 1 END) AS injustificados,
    			COUNT(CASE WHEN estado = 'Presente' THEN 1 END) AS presentes,
    			COUNT(CASE WHEN estado = 'Ausente Justificado' THEN 1 END) AS partes,
				COUNT(CASE WHEN estado = 'Asuente Justicado Sanidad' THEN 1 END) AS partes_sanidad
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
			$resultado_dep = array_values($resultado);
		 }
		 $resultado_final = array_values(
   		 empty($resultado_cat) ? 
        (empty($resultado_dep) ? [] : $resultado_dep) :
        (empty($resultado_dep) ? $resultado_cat : array_merge($resultado_cat, $resultado_dep)));
		}
		
		
	}
 
	//-----------------------------------------------------------------------------------
	//---- filtro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__filtro(comision_ei_filtro $filtro)
	{
	}

}
?>