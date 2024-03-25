<?php
class dt_antiguedad extends comision_datos_tabla
{
	static function get_antiguedad($legajo){
        
        $sql = "SELECT fecha_ingreso 
        		FROM reloj.antiguedad
        		WHERE legajo = $legajo";
        $facu=toba::db('ctrl_asis')->consultar($sql);
        $cant= count($facu);
        if($cant<>1) {
        	$sql = "SELECT fecha_ingreso 
        	FROM uncu.legajo_cargos
        	WHERE legajo = $legajo";
        	$uni=toba::db('ctrl_asis')->consultar($sql);
        	$fecha_ingreso = $uni[0]['fecha_ingreso'];	
        } else{

	       	$fecha_ingreso = $facu[0]['fecha_ingreso'];	
        }
  
        $fecha_inicio = date_create(date("Y-m-d",$fecha_ingreso)); 
        $hoy=date_create(date("Y-m-d"));
        $dia = date_diff($fecha_inicio , $hoy);
        $anti = $dia->format('%y');
        ei_arbol($anti);
        return $anti;
    }  
}

?>