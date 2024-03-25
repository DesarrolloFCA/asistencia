<?php
class ci_comision_de_servicio extends ctrl_asis_ci
{
	protected $s__datos_filtro;
	protected $s__id_comision;


	//---- Filtro -----------------------------------------------------------------------

	function conf__filtro(ctrl_asis_ei_filtro  $filtro)
	{
		/*if (isset($this->s__datos_filtro)) {
			$filtro->set_datos($this->s__datos_filtro);
		}*/
	}

	function evt__filtro__filtrar($datos)
	{
		
		$this->s__datos_filtro = $datos;
		
	}

	function evt__filtro__cancelar()
	{
		unset($this->s__datos_filtro);
	}

	//---- Cuadro -----------------------------------------------------------------------

	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		$filtro =$this->s__datos_filtro;
		//ei_arbol($filtro);
		if (isset($this->s__datos_filtro)) {
			$listado =$this->dep('datos')->tabla('comision')->get_listado($this->s__datos_filtro);
		} else {
			$listado =$this->dep('datos')->tabla('comision')->get_listado();
		}
		
	//	$listado = $this->dep('datos')->tabla('comision')->get_listado();
		$lim = count($listado);
		for ($i=0;$i<$lim;$i++){
			
			$legajo=$listado[$i]['legajo'];
			$leg_sup=$listado[$i]['legajo_sup'];
			$leg_aut=$listado[$i]['legajo_aut'];
			
			$agente = $this->dep('mapuche')->get_legajos_email($legajo);
			$sup = $this->dep('mapuche')->get_legajos_email($leg_sup);
			$aut =$this->dep('mapuche')->get_legajos_email($leg_aut);
			$listado[$i]['agente_nom']=substr($agente[0]['descripcion'],8);
			$listado[$i]['sup_nom']=substr($sup[0]['descripcion'],8);
			$listado[$i]['aut_nom']=substr($aut[0]['descripcion'],8);			
		}
		
		$cuadro->set_datos($listado);
	}

	function evt__cuadro__seleccion($datos)
	{
		
		$this->dep('datos')->cargar($datos);
		$this->set_pantalla('pant_edicion');
	}

	//---- Formulario -------------------------------------------------------------------

	function conf__formulario(toba_ei_formulario $form)
	{
		//$listado=$this->dep('datos')->tabla('comision')->get();
		//ei_arbol($listado);
		$filtro =$this->s__datos_filtro;
		
		$where = array();
		if (isset($filtro['fecha'])){
			$fecha_filtrada =$filtro['fecha']['valor'] ;
			if($filtro['fecha']['condicion'] == 'desde') {
				$where[]= "fecha >= to_date('$fecha_filtrada','YYYY-MM-DD') " ;
			} else if ($filtro['fecha']['condicion'] == 'hasta') {
				$where[]= "fecha <= to_date('$fecha_filtrada','YYYY-MM-DD') ";
				} else if ($filtro['fecha']['condicion'] == 'entre'){
				$fecha_inicial =$filtro['fecha']['valor']['desde'] ;
				$fecha_final =$filtro['fecha']['valor']['hasta'] ;
				$where[] = "fecha BETWEEN to_date('$fecha_inicial','YYYY-MM-DD') AND to_date('$fecha_final','YYYY-MM-DD')";
					
				} else if ($filtro['fecha']['condicion'] == 'es_igual_a'){
					$where[]= "fecha = to_date('$fecha_filtrada','YYYY-MM-DD') ";	
				} else {
					$where[]= "fecha <> to_date('$fecha_filtrada','YYYY-MM-DD') ";
				}

		}
		if (isset($filtro['legajo_aut'])){
			$legajo_filtro = $filtro['legajo_aut']['valor'];
			if($filtro['legajo_aut']['condicion'] == 'es_igual_a'){
				$where[] = "legajo = $legajo_filtro";
			}else{
				$where[] = "legajo <> $legajo_filtro";
			}
		}
		if (isset($filtro['catedra'])){
			$catedra_filtro = $filtro['catedra']['valor'];
			if ($filtro['catedra']['condicion'] == 'es_igual_a'){
				$where[]="catedra = $catedra";
			} else {
				$where[]="catedra <> $catedra";
			}
		}
		
			$sql = "SELECT  *  FROM reloj.comision
					WHERE pasada is null
					
					";
			if (count($where)>0) {
		
			$sql = sql_concatenar_where($sql, $where);
			
			}		
			
			$listado = toba::db('ctrl_asis')->consultar($sql);		

		//	ei_arbol($listado);
		//$cant_comision = count($listado);
		// for ($i=0;$i<=$cant_comision;$i++){
		// 	$datos =
		// }

			$form->set_datos($listado);
		/*if ($this->dep('datos')->esta_cargada()) {
			//$listado=$this->dep('datos')->tabla('comision')->get();
			
			$listado['fecha']=date('d/m/Y',strtotime($listado['fecha']) );
			$listado['fecha_fin']=date('d/m/Y',strtotime($listado['fecha_fin']) );
			$listado['horario']=substr($listado['horario'],0,5);
			$listado['horario_fin']=substr($listado['horario_fin'],0,5);
			$this->s__id_comision =$listado['id_comision'];
			
			ei_arbol($listado);
			$legajo=$listado['legajo'];
			$leg_sup=$listado['legajo_sup'];
			$leg_aut=$listado['legajo_aut'];
			$agente = $this->dep('mapuche')->get_legajos_email($legajo);
			$sup = $this->dep('mapuche')->get_legajos_email($leg_sup);
			$aut =$this->dep('mapuche')->get_legajos_email($leg_aut);
			$listado['agente_nom']=substr($agente[0]['descripcion'],8);
			$listado['sup_nom']=substr($sup[0]['descripcion'],8);
			$listado['aut_nom']=substr($aut[0]['descripcion'],8);	
			$form->set_datos($listado);
			//$form->set_datos($this->dep('datos')->tabla('comision')->get());
		} else {
			//$this->pantalla()->eliminar_evento('eliminar');
		}*/
	}

	function evt__formulario__modificacion($datos)
	{
		//$this->dep('datos')->tabla('comision')->set($datos);
	//ei_arbol($datos);
	
		//$id = ($this->s__id_comision);
		$cant = count($datos); 
		for($i=0;$i<$cant;$i++){
			$id= $datos[$i]['id_comision'];
			$a_sup=$datos[$i]['autoriza_sup'];
			//ei_arbol($sup);
		/*	if ($sup == 1) {
				$a_sup = True;
			}else {
				$a_sup= False;
			}*/
			$a_aut=$datos[$i]['autoriza_aut'];
			/*if ($aut == 1) {
				$a_aut = True;
			} else
			{
				$a_aut = False;
			}*/
			$obs = $datos[$i]['observaciones'];
		//	ei_arbol(is_bool($a_sup));
			if ($datos[$i]['apex_ei_analisis_fila'] == 'M' ){
			$sql = "UPDATE reloj.comision
					Set autoriza_sup = $a_sup, autoriza_aut = $a_aut, observaciones = '$obs'
					where id_comision = $id";
		   // ei_arbol($sql);
		toba::db('ctrl_asis')->ejecutar ($sql);    

			} 
			}

		/*if ($datos['autoriza_aut']== 1)
		{
		$sql = "SELECT legajo, apellido, nombre, fec_nacim, dni, fec_ingreso, estado_civil, 
							caracter, categoria, agrupamiento, escalafon, cod_depcia, cuil
						FROM uncu.legajo WHERE legajo = '".$datos['legajo']."
						'";
		$agente =  toba::db('mapuche')->consultar_fila($sql); 
		$antiguedad = toba::tabla('vacaciones_antiguedad')->get_array_antiguedad($agente['fec_ingreso'],$agente['agrupamiento']);
				//$agente['dias_vac_antiguedad']   = utf8_decode($antiguedad['dias'].' días');
				$agente['antiguedad']            = utf8_decode(intval($antiguedad['antiguedad']).' años');		
	
		$legajo=$datos['legajo'];
		$edad = $this->dep('mapuche')->get_edad($agente['legajo']);
		$domicilio =$this->dep('mapuche')->get_domicilio($agente['legajo']);
		$localidad =$this->dep('mapuche')->get_localidad($agente['legajo']);
		$agrupamiento= $agente['agrupamiento'];
		$fecha_nacimiento= $agente['fec_nacim'];
		$apellido = $agente['apellido'];
		$nombre=$agente['nombre'];
		$estado_civil = $agente['estado_civil'];
		$fecha_alta=date('d/m/Y',strtotime($datos['fecha']));
	
		$observaciones = $datos['observaciones'];
		//$legajo=$datos['legajo'];
		//$fecha_alta= $datos['fecha'];
		$usuario_alta  = toba::usuario()->get_id();
		$estado= 'C';
		$fecha_inicio_licencia=$fecha_alta;
			
			if ($fecha_alta == $datos['fecha_fin'])
			{
			$dias = 1;
			} else
			{
			$fecha1= strtotime($datos['fecha']);
			$fecha2= strtotime($datos['fecha_fin']);
			$dias = $fecha2 - $fecha1 + 1;
			}
	
		$sql="INSERT INTO reloj.parte(
		legajo, edad, fecha_alta, usuario_alta, estado, fecha_inicio_licencia, dias, cod_depcia, domicilio, localidad, agrupamiento, fecha_nacimiento, apellido, nombre, estado_civil, observaciones, id_motivo) --, id_articulo, observaciones_cierre, fecha_baja, usuario_baja, tipo_sexo, usuario_cierre, fecha_cierre, dias2, dias3, dias4, dias5, dias6, dias7, dias8, dias9, id_parte_sanidad)
		VALUES ($legajo, $edad, '$fecha_alta', '$usuario_alta', '$estado', '$fecha_inicio_licencia', $dias, '04', '$domicilio', '$localidad', '$agrupamiento','$fecha_nacimiento', '$apellido', '$nombre', '$estado_civil' ,'$observaciones' , 56) ;--, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);
		UPDATE reloj.comision set pasada = true
		where id_comision = $id ;";
	
		toba::db('ctrl_asis')->ejecutar($sql); 
		}
		else
		{
		toba::notificacion()->agregar('Falta la autorizacion de la autoridad correspondiente', 'info');
		}*/
	}
	
	function resetear()
	{
		$this->dep('datos')->resetear();
		$this->set_pantalla('pant_seleccion');
	}

	//---- EVENTOS CI -------------------------------------------------------------------

	/*function evt__agregar()
	{
		$this->set_pantalla('pant_edicion');
	}*/

	function evt__volver()
	{
		$this->resetear();
	}

	function evt__eliminar()
	{
		$this->dep('datos')->eliminar_todo();
		$this->resetear();
	}

	function evt__guardar()
	{
		//$this->dep('datos')->sincronizar();
		//$this->resetear();
		$this->dep('datos')->sincronizar();
		$this->dep('datos')->resetear();
		$this->dep('datos')->cargar();
	}
	function obtener_edad_segun_fecha($fecha_nacimiento)
	{
    $nacimiento = new DateTime($fecha_nacimiento);
    $ahora = new DateTime(date("Y-m-d"));
    $diferencia = $ahora->diff($nacimiento);
    return $diferencia->format("%y");
	}

	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		echo "
		//---- Eventos ---------------------------------------------
		
		{$this->objeto_js}.evt__guardar = function()
		{
		}
		";
	}

}
?>