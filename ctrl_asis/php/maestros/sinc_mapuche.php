<?php
	$sql = "TRUNCATE TABLE reloj.agentes";
	toba::db('ctrl_asis')->ejecutar($sql);
	$sql = "TRUNCATE TABLE reloj.domicilio";
	toba::db('ctrl_asis')->ejecutar($sql);
	$sql = "SELECT legajo from reloj.adscripcion";
	$adscripto = toba::db('ctrl_asis')->consultar($sql);
	if (isset($adscripto)){
		$lim = count($adscripto);
		for ($i=0;$i<$lim;$i++){
			$legajo = $adscripto[$i]['legajo'];
			if ($i== 0 ){
				$lis = "or legajo  in ( $legajo";
			} else {
				$lis =$lis.",$legajo";
			}
		}
		$lis =$lis.")";	
	}
	$sql = "SELECT legajo, apellido, nombre, fec_nacim, fec_ingreso, dni, estado_civil, caracter, categoria,escalafon, agrupamiento, cod_depcia, cuil, mayor_dedicacion, funcion_critica, tipo_sexo, email, telefono, codc_dedic, cant_horas, subrogancia
	FROM uncu.legajo
	where cod_depcia = '04'";
	$sql1 = "SELECT * FROM uncu.domicilio
			where legajo in ((Select distinct legajo from uncu.legajo where cod_depcia = '04'))";

	if (isset($lis)){
		$sql = $sql . $lis ;
		$sql1 = $sql1 . $lis ;

	}

	$sql = $sql . "ORDER BY legajo, agrupamiento,cant_horas DESC";
	$agentes_mapuche = toba::db('mapuche')->consultar($sql);
	$agentes_domicilio = toba::db('mapuche')->consultar($sql1);


	if (isset($agentes_local)){

	}else{
		$cant_mapuche = count($agentes_mapuche);
		$agentes_mapuche [0]['ncargo'] = 0;
		for ($i=0;$i<$cant_mapuche;$i++){
			$agentes_mapuche [$i]['ncargo'] = 0;
			if ($i>0){
				if($agentes_mapuche[$i]['legajo'] == $agentes_mapuche[$i-1]['legajo']){
					$agentes_mapuche [$i]['ncargo'] = $agentes_mapuche[$i-1]['ncargo'] +1;
				} 
			}
			$legajo = $agentes_mapuche[$i]['legajo'];
			$ncargo = $agentes_mapuche [$i]['ncargo'];
			$apellido = $agentes_mapuche [$i]['apellido'];
			$nombre = $agentes_mapuche [$i]['nombre'];
			$fec_nacim = $agentes_mapuche [$i]['fec_nacim'];
			$fecha_ingreso = $agentes_mapuche [$i]['fec_ingreso'];
			$dni = $agentes_mapuche [$i]['dni'];
			$estado_civil= $agentes_mapuche [$i]['estado_civil'];
			$caracter = $agentes_mapuche [$i]['caracter'];
			$categoria = $agentes_mapuche [$i]['categoria'];
			$agrupamiento = $agentes_mapuche [$i]['agrupamiento'];
			$escalafon = $agentes_mapuche[$i]['escalafon'];
			$cod_depcia = $agentes_mapuche [$i]['cod_depcia'];
			$cuil = intval($agentes_mapuche [$i]['cuil']);
			//ei_arbol($agentes_mapuche [$i]['mayor_dedicacion']== 'NO');

			$mayor_dedicacion = $agentes_mapuche [$i]['mayor_dedicacion'];
			$funcion_critica = $agentes_mapuche [$i]['funcion_critica'];
			$tipo_sexo = $agentes_mapuche [$i]['tipo_sexo'];
			$email = $agentes_mapuche [$i]['email'];
			$telefono = $agentes_mapuche [$i]['telefono'];
			$cod_dedic = $agentes_mapuche [$i]['codc_dedic'];
			$cant_horas = $agentes_mapuche [$i]['cant_horas'];
			$subrogancia = $agentes_mapuche [$i]['subrogancia'];
			$sql = "INSERT INTO reloj.agentes(
					legajo, ncargo, apellido, nombre, fec_nacim, dni, fecha_ingreso, estado_civil, caracter, categoria, agrupamiento, escalafon, cod_depcia, cuil, 
					mayor_dedicacion, funcion_critica, tipo_sexo, email, telefono, cod_dedic, cant_horas, subrogancia)
					VALUES
					($legajo,$ncargo,'$apellido','$nombre','$fec_nacim',$dni,'$fecha_ingreso','$estado_civil', '$caracter','$categoria','$agrupamiento','$escalafon','$cod_depcia',$cuil,
					'$mayor_dedicacion','$funcion_critica', '$tipo_sexo' , '$email' , '$telefono','$cod_dedic' , $cant_horas, '$subrogancia');";
			toba::db('ctrl_asis')->ejecutar($sql);

		}
		$cant = count($agentes_domicilio);	
		for ($j=0;$j<$cant;$j++){
			$legajo = $agentes_domicilio[$j]['legajo'];
			$pais = $agentes_domicilio[$j]['pais'];
			$provincia = $agentes_domicilio[$j]['provincia'];
			$cp=$agentes_domicilio[$j]['codigo_postal'];
			$localidad= $agentes_domicilio[$j]['localidad'];
			$manzana= $agentes_domicilio[$j]['codc_cara_manzana'];
			$zona= $agentes_domicilio[$j]['zona_paraje_barrio'];
			$calle= str_replace("'", "", $agentes_domicilio[$j]['calle'] );

			$numero= $agentes_domicilio[$j]['numero'];
			$piso = $agentes_domicilio[$j]['piso'];
			$oficina= $agentes_domicilio[$j]['oficina'];
			$telefono= $agentes_domicilio[$j]['telefono'];
			$telefono_celular= $agentes_domicilio[$j]['telefono_celular'];

			$sql= "INSERT INTO reloj.domicilio(
			legajo, pais, provincia, codigo_postal, localidad, manzana, zona_paraje_barrio, calle, numero, piso, dpto_oficina, telefono, telefono_celular)
			VALUES ($legajo,'$pais','$provincia' ,'$cp', '$localidad' , '$manzana', '$zona', '$calle' ,  '$numero', '$piso' , '$oficina',' $telefono' , '$telefono_celular')";
			toba::db('ctrl_asis')->ejecutar($sql);
		}

		//ei_arbol($sql);
		 echo("Sincronización con éxito"); 
	}



?>	