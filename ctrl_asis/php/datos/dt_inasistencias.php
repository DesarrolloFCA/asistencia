<?php
class dt_inasistencias extends ctrl_asis_datos_tabla
{

	function get_descripciones()
	{
		$sql = "SELECT id_inasistencia, observaciones FROM inasistencias ORDER BY observaciones";
		return toba::db('ctrl_asis')->consultar($sql);
	}
	function get_llenado ($anio){
		$where = array();

		$sql = "SELECT legajo,anio,a.observaciones obs,fecha_alta, descripcion,auto_aut FROM inasistencias a
		inner join motivo b on b.id_motivo = a.id_motivo order by legajo";
		 if (isset($anio)){
		 	$where[] = "anio = ". quote ($anio);  
		 }
		if (count($where) > 0)
        {
            $sql = sql_concatenar_where($sql, $where);
        }
		$ina = toba::db('ctrl_asis')->consultar($sql);

		$sql = "SELECT legajo from agentes_mail order by legajo";
		$tot= toba::db('ctrl_asis')->consultar($sql);
		$lim = count($tot);
		$limina= count($ina);
		for ($i=0;$i<$lim;$i++){
			$legajo = $tot[$i]['legajo'];
			if ($legajo > 10000){
				for ($j=0;$j<$limina;$j++){
					if($legajo== $ina[$j]['legajo']){
					$comp=true;
					$anio=$ina[$j]['anio'];
					$fecha=$ina[$j]['fecha_alta'];
					$descripcion=$ina[$j]['descripcion'];
					$obs = $ina[$j]['obs'];
					$auto_aut = $ina[$j]['auto_aut'];
					$j=$limina;
					}else
					{
					$anio=null;
					$fecha=null;
					$descripcion=null;
					$obs = null;
					$auto_aut = null;
					$comp = false; 
					}
				
				}
		
				$vacaciones [$i]['legajo']= $legajo;
				$vacaciones [$i]['completado'] = $comp;
				$vacaciones[$i]['anio']=$anio;
				$vacaciones[$i]['fecha_alta']=$fecha;
				$vacaciones[$i]['descripcion'] = $descripcion;
				$vacaciones[$i]['observaciones'] =$obs;
				$vacaciones[$i]['auto_aut']=$auto_aut;
			
			}
		}
	 return $vacaciones;
	}

}

?>