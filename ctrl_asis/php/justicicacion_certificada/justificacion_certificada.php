<?php
class justificacion_certificada extends ctrl_asis_ci

{
	protected $s__datos;
	//-----------------------------------------------------------------------------------
	//---- cuadro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro(ctrl_asis_ei_cuadro $cuadro)
	{
		$sql = "SELECT * FROM reloj.inasistencias
			WHERE  estado = 'A'
			AND id_motivo not in (30,35,57)
			Order by fecha_inicio, fecha_fin, legajo";
		$datos = toba::db('ctrl_asis')->consultar($sql);
		$cuadro->set_datos($datos);
		$this ->s__datos = $datos;
		

	}

	function evt__cuadro__seleccion($seleccion)
	{
		
	}

	function evt__cuadro__aut_sup($seleccion)
	{
		ei_arbol($seleccion);
		if (isset($seleccion)) {
			$datos = $this->s__datos;
			$i = array_keys($seleccion);
			$datos[$i[0]]['auto_sup'] = true ;
			$this ->s__datos = $datos;	
		}
	
	}

	function evt__cuadro__aut_aut($seleccion)
	{
		$datos = $this->s__datos;
		if (isset($seleccion)) {
			
			$i = array_keys($seleccion);
			$datos[$i[0]]['auto_aut'] = true ;
			$this ->s__datos = $datos;
		} 
		
	}

	

	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__procesar()
	{
		$datos = $this->s__datos;

		$lim = count($datos);
		
		for ($i=0;$i<$lim;$i++){
			$id_ina= $datos[$i]['id_inasistencia'];
			if ($datos[$i]['auto_sup'] == 1){
				$aut_sup = 'true';
			}else{
				$aut_sup = 'false';
			}
			if ($datos[$i]['auto_aut'] == 1){
				$aut_aut = 'true';
			}else{
				$aut_aut = 'false';
			}
		$sql = "UPDATE reloj.inasistencias
				SET auto_sup = $aut_sup, auto_aut = $aut_aut
				WHERE id_inasistencia = $id_ina"	;
		toba::db('ctrl_asis')->ejecutar ($sql);		
		
		}

		
		
	}

	function conf_evt__cuadro__aut_sup(toba_evento_usuario $evento, $fila)
	{
		//ei_arbol($evento->esta_activado());
		$datos = $this->s__datos;
		ei_arbol($datos[$fila]['auto_sup']);
		if (($datos[$fila]['auto_sup'] == null) or ($datos[$fila]['auto_sup'] == 0)) {
			
			$evento->set_check_activo(false);
		} else {
			$evento->set_check_activo(true);

		}
	}
		


	function conf_evt__cuadro__aut_aut(toba_evento_usuario $evento, $fila)
	{
		$datos = $this->s__datos;
		ei_arbol($datos[$fila]['auto_aut']);
		if (($datos[$fila]['auto_aut'] == null) or ($datos[$fila]['auto_aut'] == 0)) {
			
			$evento->set_check_activo(false);
		} else {
			$evento->set_check_activo(true);

		}
	}

}
?>