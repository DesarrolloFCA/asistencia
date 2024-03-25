<?php
class ci_vacacionesyrp extends ctrl_asis_ci
{
	protected $s__datos_filtro;
	protected $s__datos;
	//-----------------------------------------------------------------------------------
	//---- filtro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__filtro(ctrl_asis_ei_filtro $filtro)
	{

	}

	function evt__filtro__filtrar($datos)
	{
		$this->s__datos_filtro = $datos;

	}

	function evt__filtro__cancelar()
	{
		unset($this->s__datos_filtro);
	}

	function ini__operacion()
	{
		//$this->dep('datos')->cargar();
	}

	function evt__guardar()
	{
		//ei_arbol($datos);

		$this->dep('datos')->sincronizar();
		$this->dep('datos')->resetear();
		$this->dep('datos')->cargar();
	}

	function evt__formulario__modificacion($datos)
	{
		
		//ei_arbol($datos);
		//$datos = $this->s__datos;

		//ei_arbol($datos);
		$cant = count($datos);
		for($i=0;$i<$cant;$i++){
			$id = $datos[$i]['id_inasistencia'];
			$a_sup=$datos[$i]['auto_sup'];
			$a_aut=$datos[$i]['auto_aut'];
			$obs = $datos[$i]['observaciones'];
			$datos[$i]['fecha_inicio']=$this->s__datos[$i]['fecha_inicio'];

		//ei_arbol($datos[$i]['apex_ei_analisis_fila']);
			if ($datos[$i]['apex_ei_analisis_fila'] == 'M' ){
			$sql = "UPDATE reloj.inasistencias
					Set auto_sup = $a_sup, auto_aut = $a_aut, observaciones = '$obs'
					where id_inasistencia = $id";
		//    ei_arbol($sql);
			toba::db('ctrl_asis')->ejecutar ($sql);    

			} 
		}
		//    ei_arbol($temp);
		
		//$this->dep('datos')->procesar_filas($datos);
		
	}

	function conf__formulario(toba_ei_formulario_ml $componente)
	{
		
		//$datos= $this->dep('datos')->get_filas();

		$sql = "Select * from reloj.inasistencias

			where estado = 'A'
			and id_motivo in (30,35,57)
			order by fecha_inicio, fecha_fin, legajo";
		$datos = toba::db('ctrl_asis')->consultar($sql);
		
		$filtro = $this->s__datos_filtro;
		

	/*    if (isset($filtro['legajo_sup']['valor'])){
			$legajo_superior = $filtro['legajo_sup']['valor'];
			$componente->desactivar_efs('auto_aut');
		
		$sql = "SELECT * from reloj.inasistencias
		where leg_sup = $legajo_superior

		--And auto_sup = false
		And estado ='A' ";

		$datos = toba::db('ctrl_asis')->consultar($sql);
			}*/
			//ei_arbol($filtro);
		$where = array();
		if (isset($filtro['legajo_aut']['valor'])){
			$legajo_superior = $filtro['legajo_aut']['valor'];
			$componente->set_solo_lectura('auto_sup',true);
			$sql = "SELECT * from reloj.inasistencias
				where leg_aut = $legajo_superior
				And estado ='A'";

			if(isset($filtro['id_catedra']['valor'])){
			$id_catedra=$filtro['id_catedra']['valor'];
			$where  []= " id_catedra=".quote($id_catedra);
			//	AND id_catedra=$id_catedra";
			}

			if (isset($filtro['id_motivo']['valor'])){

				$id_motivo =$filtro['id_motivo']['valor'];
				$where  []= " id_motivo=$id_motivo";
			}
			$sql = sql_concatenar_where($sql, $where);	
		}
		

		$datos = toba::db('ctrl_asis')->consultar($sql);
		//ei_arbol($sql);
				
		//ei_arbol ($datos);        
		$cant = count($datos);
		
		/*for($i=0;$i<$cant;$i++){
		$temp = array_filter( $datos, function( $temp ) {
					return ( $datos[$i]['legajo_sup']== $filtro['legajo_sup']['valor']);    
				});    
		}
		//ei_arbol($temp);*/

		for($i=0;$i<$cant;$i++){
		
		$catedra = $datos[$i]['id_catedra'];
		$motivo = $datos[$i]['id_motivo'];
		$agente= $this->dep('mapuche')->get_legajos_jefes_fca ($datos[$i]['legajo']);
		$datos[$i]['descripcion']= $agente[0]['descripcion'];    
		$sql = "SELECT nombre_catedra from reloj.catedras
		where id_catedra =$catedra";
		$cat=toba::db('ctrl_asis')->consultar($sql);
		$datos[$i]['catedra'] = $cat[0]['nombre_catedra'];
		$agente= $this->dep('mapuche')->get_legajos_jefes_fca ($datos[$i]['leg_sup']);
		$datos[$i]['nombre_sup']= $agente[0]['descripcion'];
		$agente= $this->dep('mapuche')->get_legajos_jefes_fca ($datos[$i]['leg_aut']);
		$datos[$i]['nombre_aut']= $agente[0]['descripcion'];
		$sql = "SELECT descripcion from reloj.motivo
		where id_motivo =$motivo";
		$cat=toba::db('ctrl_asis')->consultar($sql);
		$datos[$i]['motivo'] = $cat[0]['descripcion'];



		}
		//ei_arbol($datos);
		$this->s__datos =$datos;

		$componente ->set_datos($datos);
		//$componente->set_datos($this->dep('datos')->get_filas());
	}

	
}
?>