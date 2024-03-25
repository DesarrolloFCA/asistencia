<?php
class ci_justificacion_con_certificado extends ctrl_asis_ci
{
	protected $s__datos;
	function ini__operacion()
	{
		
		//$this->dep('datos')->cargar();
	}

	function evt__guardar()
	{
		$this->dep('datos')->sincronizar();
		$this->dep('datos')->resetear();
		$this->dep('datos')->cargar();
	}

	function evt__formulario__modificacion($datos)
	{
		$cant = count($datos);
		for($i=0;$i<$cant;$i++){
			$id = $datos[$i]['id_inasistencia'];
			$obs = $datos[$i]['observaciones'];
			$datos[$i]['fecha_inicio']=$this->s__datos[$i]['fecha_inicio'];
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
		//ei_arbol($datos[$i]['apex_ei_analisis_fila']);
			if ($datos[$i]['apex_ei_analisis_fila'] == 'M' ){
			$sql = "UPDATE reloj.inasistencias
					Set auto_sup = $aut_sup, auto_aut = $aut_aut, observaciones = '$obs'
					where id_inasistencia = $id";
		//    ei_arbol($sql);
			toba::db('ctrl_asis')->ejecutar ($sql);    

			} 
		}
		//$this->dep('datos')->procesar_filas($datos);
	}

	function conf__formulario(toba_ei_formulario_ml $componente)
	{
		$sql = "SELECT * FROM reloj.inasistencias
			WHERE  estado = 'A'
			AND id_motivo not in (30,35,57)
			Order by fecha_inicio, fecha_fin, legajo";
		$datos = toba::db('ctrl_asis')->consultar($sql);
		$this ->s__datos = $datos;
		$ruta = 'certificados/';
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

			$archivo = $datos[$i]['id_inasistencia'].$datos[$i]['fecha_inicio'].'.pdf';
			$datos[$i]['certificado'] = '<a href='.$ruta.$archivo.' target="_blank">Descargar Certificado</a>';
		}
		$this ->s__datos = $datos;
		$componente->set_datos($datos);
	}

}

?>