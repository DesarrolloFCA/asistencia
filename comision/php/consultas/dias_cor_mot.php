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