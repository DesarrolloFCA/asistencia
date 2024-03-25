<?php
class dt_catedra extends comision_datos_tabla
{
	
	function get_catedra_agentes ($legajo)
	{
		//ei_arbol ($legajo);
		
		$sql= "SELECT legajo,a.id_catedra, nombre_catedra   from reloj.catedras a
				left join reloj.catedras_agentes b on a.id_catedra =b.id_catedra
				where legajo = '$legajo'";

		$catedra=  toba::db('comision')->consultar($sql);
		//ei_arbol(count($catedra));
		if (count($catedra)<= 0) {
			$catedra [0]['legajo']= $legajo;
			$catedra [0]['id_catedra'] = 0;
			$catedra [0]['nombre_catedra'] = 'Sin catedra/oficina/direccion';
			}
		 return $catedra;
	

		/*if(!empty($res['nombre_catedra'])){
					return $res['nombre_catedra'];
				}*/

	}
	
	function autoridades ($legajo) {
		//ei_arbol ($legajo);
		if ($legajo < 10 ) {
			$sql = "SELECT   legajo , autoridad as legajo_aut
                    FROM reloj.legajos_autoridad 
                    WHERE legajo = $legajo";
       		$autoridad = toba::db('comision')->consultar ($sql);
      	//	ei_arbol($autoridad);
      		 // return  $d;

			return $autoridad;
		}else{
		$sql = "SELECT legajo_autoridad legajo_aut from reloj.autoridades
		WHERE legajo_subalterno = $legajo";
		$autoridad = toba::db('comision')->consultar($sql);
	//	ei_arbol($autoridad);
		if (count($autoridad)> 0) {
			$leg= $autoridad [0]['legajo_aut'];
			if ($leg < 10){
			$sql = "SELECT   legajo , autoridad as legajo_aut
                    FROM reloj.legajos_autoridad 
                    WHERE legajo = $leg";
       		$autoridad = toba::db('comision')->consultar ($sql);
      		 //ei_arbol($autoridad);
      		 // return  $d;

			return $autoridad;
			} else {
			$sql = "SELECT   legajo , apellido||', '||nombre as legajo_aut
                    FROM reloj.agentes
                    WHERE legajo = $leg";
             $autoridad = toba::db('comision')->consultar ($sql);
			//ei_arbol($autoridad);
			return $autoridad ;        

			}
		} else{
				
				$sql = "SELECT DISTINCT legajo_dir legajo_jefe FROM reloj.departamento_director a
						INNER JOIN reloj.departamentos b on a.id_departamento =b.id_departamento
						INNER JOIN reloj.catedras c on c.id_departamento = b.id_departamento
						INNER JOIN reloj.catedras_agentes d on d.id_catedra =c.id_catedra
						WHERE jefe = true
						AND d.legajo = $legajo;"; 
				$jefe= toba::db('comision')->consultar($sql);
				//ei_arbol($jefe);
				$legajo = $jefe[0]['legajo_jefe'];
				$sql = "SELECT legajo_autoridad  from reloj.autoridades
				WHERE legajo_subalterno = $legajo";
		$temp = toba::db('comision')->consultar($sql);
		$leg = $temp[0]['legajo_autoridad'];
		if ($leg < 10){
			$sql = "SELECT   legajo , autoridad as legajo_aut
                    FROM reloj.legajos_autoridad 
                    WHERE legajo = $leg";
       		$autoridad = toba::db('comision')->consultar ($sql);
       //ei_arbol($autoridad);
       // return  $d;
			return $autoridad;
			} else {
			$sql = "SELECT   legajo , apellido||', '||nombre as legajo_aut
                    FROM reloj.agentes 
                    WHERE legajo = $leg";
       		$autoridad = toba::db('comision')->consultar ($sql);
			//ei_arbol($autoridad);
			return $autoridad ;
			}
		}
		//return $leg;
	}
	}
	function jefes ($legajo) {
		$sql = "SELECT jefe from reloj.catedras_agentes b 
		 		WHERE legajo = $legajo;";
		 $es_jefe = toba::db('comision')->consultar($sql);	

		 if ($es_jefe [0]['jefe']) {
		 	$sql = "SELECT * from reloj.departamento_director b 
		 		WHERE legajo_dir = $legajo;";
		 	$auto = toba::db('comision')->consultar($sql);	
		 	$hay = count($auto);
		// 	ei_arbol($auto);
		 	if ($hay > 0) {
		 		$jefe[0]['legajo_jefe']= null;

		 		}else{
		 		$sql = "SELECT DISTINCT legajo_dir legajo_jefe FROM reloj.departamento_director a
						INNER JOIN reloj.departamentos b on a.id_departamento =b.id_departamento
						INNER JOIN reloj.catedras c on c.id_departamento = b.id_departamento
						INNER JOIN reloj.catedras_agentes d on d.id_catedra =c.id_catedra
						WHERE jefe = true
						AND d.legajo = $legajo;"; 
				$jefe= toba::db('comision')->consultar($sql);
		 		} 
		 	}else {		
			$sql ="SELECT legajo legajo_jefe from reloj.catedras_agentes b 
			where id_catedra in (( Select id_catedra from reloj.catedras_agentes
			where legajo = $legajo)) and jefe = true;";
			$jefe= toba::db('comision')->consultar($sql);
		}	
		return $jefe;
		//$legajo_jefe =   toba::db('comision')->consultar($sql);
		//ei_arbol($legajo_jefe);
	}

	function get_catedra_jefe ($legajo, $id_catedra)
	{
		//ei_arbol($legajo,$id_catedra);

		$sql = "SELECT jefe from reloj.catedras_agentes b 
		 		WHERE legajo = $legajo
		 		AND id_catedra = $id_catedra;";
		 $es_jefe = toba::db('comision')->consultar($sql);	
		 // ei_arbol($es_jefe);

		  if ($es_jefe [0]['jefe'] == 1) {
		 	
		 	if (($id_catedra >= 49 and $id_catedra <= 52) or ($id_catedra == 99) or ($id_catedra== 28 )or ($id_catedra== 83 )or($id_catedra==86)){
		 		$aut = self::get_autoridades($legajo,0);
		 		$jefe [0]['legajo'] = $aut[0]['legajo'];
		 		$jefe[0]['legajo_jefe']= $aut[0]['legajo_aut'];
		 		/*$jefe [0]['legajo'] = 0;
		 				$jefe[0]['legajo_jefe']= ' ';*/
		 		//ei_arbol($jefe);
		 	} else {
		 	$sql = "SELECT * from reloj.departamento_director b 
		 		WHERE legajo_dir = $legajo;";
		 	$auto = toba::db('comision')->consultar($sql);	
		 	$hay = count($auto);
		 	
		 	

		 		
		 		if ($hay <= 0  ) {
		 		
		 			$sql = "SELECT b.legajo_dir  FROM reloj.catedras_agentes a
							inner join reloj.catedras c on c.id_catedra = a.id_catedra
							inner join reloj.departamento_director b on b.id_departamento = c.id_departamento
							where a.legajo = $legajo and c.id_catedra = $id_catedra";
					$temp = toba::db('comision')->consultar($sql);	
					
					$conteo = count($temp);
					//ei_arbol($conteo);
					if ($conteo> 0) {
											
						$leg = $temp[0]['legajo_dir'];	
						$sql = "SELECT   legajo , apellido||', '||nombre as legajo_jefe
                    	FROM reloj.agentes 
                    	WHERE legajo = $leg";
       					$jefe = toba::db('comision')->consultar ($sql);
       					//ei_arbol ($sql);
			
					} else{
					
						$aut = self::get_autoridades($legajo,0);
						//ei_arbol($aut);
		 				$jefe [0]['legajo'] = $aut[0]['legajo'];
		 				$jefe[0]['legajo_jefe']= $aut[0]['legajo_aut'];

		 				/*$jefe [0]['legajo'] = 0;
		 				$jefe[0]['legajo_jefe']= ' ';*/
		 			}		
		 		}else
		 		{

		 				$sql = "SELECT DISTINCT legajo_dir  FROM reloj.departamento_director a
						INNER JOIN reloj.departamentos b on a.id_departamento =b.id_departamento
						INNER JOIN reloj.catedras c on c.id_departamento = b.id_departamento
						INNER JOIN reloj.catedras_agentes d on d.id_catedra =c.id_catedra
						WHERE jefe = true
						AND d.legajo = $legajo;";
						$temp= toba::db('comision')->consultar($sql);
										
							$leg = $temp[0]['legajo_dir'];
						if($leg <> $legajo and $leg<> null) {
							$sql = "SELECT   legajo , apellido||', '||nombre as legajo_jefe
                    		FROM reloj.agentes
                    		WHERE legajo = $leg";
       						$jefe = toba::db('comision')->consultar ($sql);
       						//ei_arbol($jefe);
       					} else {
       					
       					$aut = self::get_autoridades($legajo,0);
		 				
		 				$jefe [0]['legajo'] = $aut[0]['legajo'];
		 				$jefe[0]['legajo_jefe']= $aut[0]['legajo_aut'];
		 				//ei_arbol($jefe);
		 				/*$jefe [0]['legajo'] = 0;
		 				$jefe[0]['legajo_jefe']= ' ';*/
       					}
       					
       						 
       					


		 		}
		 	}	

		 }else {		
			$sql ="SELECT legajo legajo_dir from reloj.catedras_agentes b 
			where id_catedra  = $id_catedra and jefe = true;";
			$temp = toba::db('comision')->consultar($sql);
			//ei_arbol($temp);
			$leg = $temp[0]['legajo_dir'];

				if ($leg <> null or $leg<>'' ){
						$sql = "SELECT   legajo , apellido||', '||nombre as legajo_jefe
                    	FROM reloj.agentes 
                    	WHERE legajo = $leg";
       			$jefe = toba::db('comision')->consultar ($sql);
       			} else {
       				//Agregué la catedra en la consulta como esta en desarrollo (la 49) para que traiga bien el jefe de departamento - LEO
       				$sql = "SELECT b.legajo_dir  FROM reloj.catedras_agentes a
							inner join reloj.catedras c on c.id_catedra = a.id_catedra
							inner join reloj.departamento_director b on b.id_departamento = c.id_departamento
							where a.legajo = $legajo and c.id_catedra = $id_catedra";
					$temp = toba::db('comision')->consultar($sql);	
					
							if (count($temp)> 0) {
					
							$leg = $temp[0]['legajo_dir'];	
							$sql = "SELECT   legajo , apellido||', '||nombre as legajo_jefe
                    		FROM reloj.agentes
                    		WHERE legajo = $leg";
       						$jefe = toba::db('comision')->consultar ($sql);
							} else {
       			
       				$jefe [0]['legajo'] = $aut[0]['legajo'];
		 				$jefe[0]['legajo_jefe']= $aut[0]['legajo_aut'];}
       			}
		}	
		//ei_arbol($jefe);

		return $jefe;
		//return self::jefes($legajo);
		
		
	}
	function get_descripciones()
	{
		$sql = "SELECT id_catedra, nombre_catedra FROM catedras ORDER BY nombre_catedra";
		return toba::db('comision')->consultar($sql);
	}
	function get_autoridades ($legajo,$superior){
		
		//ei_arbol($legajo,$superior);
		if ($superior <> 0 ){
			$legajo_aut = self::autoridades ($superior);

		}else {
			$legajo_aut = self::autoridades ($legajo);
		}
		//ei_arbol($legajo_aut);
		return $legajo_aut;
	}
	function get_ayn_aut($legajo_aut){
		$sql = "SELECT   apellido||', '||nombre as ayn
                    FROM reloj.agentes 
                    WHERE legajo = $legajo_aut";
       $d = toba::db('comision')->consultar ($sql);
       //ei_arbol($d);
       return $d;
	}

}
?>