<?php
class dias_cor_mot 
{
function dias_motivos_legajo($legajo,$id_motivo)
{
	$anio = date("Y");
	switch ($id_motivo) {
		case 35 :
			$sql= "SELECT min(dias) dias_v from reloj.antiguedad
				where legajo = $legajo";
			$dias = toba::db('comision')->consultar($sql); // Vacaciones correspondientes por antigüedad
			
			if(isset($dias)){
				$sql = "SELECT sum(dias_adelanto) dias_a from reloj.vacaciones_adelantadas
					where legajo = $legajo and anio = $anio";
				$dias_ad = toba::db('comision')->consultar($sql);// Vacaciones adelantadas
				if (isset($dias_ad)){
					$dias_totales = $dias[0]['dias_v'] -$dias_ad[0]['dias_a'];
				} else{
					$dias_totales = $dias[0]['dias_v'];
				}

			

			} else{
				$dias_totales= 0 ;
			}
			//ei_arbol ($dias_totales);
			if ($dias_totales >= 30) {
				$lim = $dias_totales - 30;
				$dias_in = 30 ;
				for ($i=0;$i<=$lim; $i++){
					$dias_lic[$i]['dias']=$dias_in;
					$dias_in ++;
				} 
			} else {
				$dias_lic[0]['dias']=$dias_totales;
			}
			
			break;
		case 57:
			$filtro ['legajo'] = $legajo;
			$filtro['id_motivo'] = 35;
			$filtro['anio'] = $anio;
			$anio_anterior = $anio - 1;
			//ei_arbol($id_motivo);
			$dias_tomados=0;
			/*$sql = "SELECT dias FROM reloj.parte 
			WHERE  legajo = $legajo
			and id_motivo = 35
			and fecha_inicio_licencia between '$anio_anterior-12-01' AND '$anio-11-30'" ;
			$partes = toba::db('comision')->consultar($sql);*/
			//ei_arbol($partes);
			$partes = toba::tabla('parte')->get_listado($filtro);
					if(count($partes)>0){
						foreach ($partes as $parte) {
							$dias_tomados = $dias_tomados + $parte['dias'];
						}
					}
				//ei_arbol($dias_tomados);	
			
			$sql= "SELECT min(dias) dias_v from reloj.antiguedad
				where legajo = $legajo";
			$dias = toba::db('comision')->consultar($sql); // Vacaciones correspondientes por antigüedad
		
			//Vacaciones por antigüedad
			$sql = "SELECT
			sum(t_vt.dias) as dias_restantes																																																	
			FROM reloj.vacaciones_restantes as t_vt 
			where t_vt.legajo = '$legajo' 
		and t_vt.anio = '$anio'		";

		$datos = toba::db('comision')->consultar_fila($sql);
		if(is_numeric($datos['dias_restantes'])){
			$vacaciones_restantes= $datos['dias_restantes'];
		}else{
			$vaciones_restantes= NULL;
		}
					if (is_null($vacaciones_restantes)){
						$dias_disponibles = $dias[0]['dias_v'] - $dias_tomados;
					}else{
						$dias_disponibles = $vacaciones_restantes - $dias_tomados;
					}
				
					//Si tiene dias disponibles, lo mostramos en el listado
				
					if($dias_disponibles > 0){
						$dias_totales=$dias_disponibles;
					} else {
						$dias_totales = 0;
					}	

				$dias_in = 0; 
				if ($dias_totales >0) {
		 		for ($i=0;$i<=$dias_totales; $i++){
					$dias_lic[$i]['dias']=$dias_in;
					$dias_in ++;
					}  
				} else {
					$dias_lic[0]['dias'] = 0;
				}
				break;	


		default:
		 		$dias_in = 0; 
		 		for ($i=0;$i<=10; $i++){
					$dias_lic[$i]['dias']=$dias_in;
					$dias_in ++;
				}  ;


	}
	//ei_arbol ($dias_lic);
	return $dias_lic; 
}
 function fecha_inicio_vac($id_motivo)
    {
    	
    	if ($id_motivo == 35){
    	$anio = date("Y");	

    	$fecha_vacaciones = '26/12/'.$anio;
    	} else {
    	$fecha_vacaciones = date("d/m/Y");
    	}
    	//ei_arbol ('26/12/2023');
    	return $fecha_vacaciones;
    }  
}
?>