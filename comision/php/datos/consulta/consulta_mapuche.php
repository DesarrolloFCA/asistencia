<?php
//php_referencia::instancia()->agregar(__FILE__);

class consulta_mapuche
{

	static function jefe ($legajo = null){
		$sql ="SELECT legajo as legajo_jefe from reloj.catedras_agentes b 
			where id_catedra in (( Select id_catedra from reloj.catedras_agentes
			where legajo = $legajo)) and jefe = true;";
		$jefe=	 toba::db('comision')->consultar($sql);	
		$cat =count($jefe);
		for ($i=0; $i<$cat;$i++){
			$legajo_j = $jefe [$i]['legajo_jefe'];
			$sql = "SELECT distinct apellido ||', '|| nombres as descricpion
			FROM uncu.legajo
			WHERE cod_depcia = '04'
            and legajo = '$legajo_j'";
            $jefe [$i]['nombre'] = toba::db('mapuche')->consultar($sql);
		}
		return $jefe;
	}
}
?>	