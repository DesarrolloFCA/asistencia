<?php
class ci_comisiones extends ctrl_asis_ci
{
	protected $s__datos_filtro;


	//---- Filtro -----------------------------------------------------------------------

	function conf__filtro(toba_ei_formulario $filtro)
	{
		if (isset($this->s__datos_filtro)) {
			$filtro->set_datos($this->s__datos_filtro);
		}
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
		if (isset($this->s__datos_filtro)) {
			$listado=$this->dep('datos')->tabla('comision')->get_listado($this->s__datos_filtro);
			
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
				
		//$listado = $this->dep('datos')->tabla('comision')->get_listado();
			//$usuario_alta  = toba::usuario()->get_id();
			
			
			$cuadro->set_datos($listado);	
			
		}
		
		
		//$cuadro->set_datos($listado);
	}

	function evt__cuadro__seleccion($datos)
	{
		$this->dep('datos')->cargar($datos);
		$this->set_pantalla('pant_edicion');
	}

	//---- Formulario -------------------------------------------------------------------

	function conf__formulario(toba_ei_formulario $form)
	{
		
		
		if ($this->dep('datos')->esta_cargada()) {
			$filtro= $this->s__datos_filtro;
			$listado=$this->dep('datos')->tabla('comision')->get();
			$legajo=$listado['legajo'];
			$leg_sup=$listado['legajo_sup'];
			$leg_aut=$listado['legajo_aut'];
			$agente = $this->dep('mapuche')->get_legajos_email($legajo);
			$sup = $this->dep('mapuche')->get_legajos_email($leg_sup);
			$aut =$this->dep('mapuche')->get_legajos_email($leg_aut);
			$listado['agente_nom']=substr($agente[0]['descripcion'],8);
			$listado['sup_nom']=substr($sup[0]['descripcion'],8);
			$listado['aut_nom']=substr($aut[0]['descripcion'],8);
			//ei_arbol($filtro);
			//ei_arbol($leg_sup);
			//if ($filtro['legajo_sup'] == $leg_sup){
			//$form->set_solo_lectura('autoriza_aut',true);
			
			$form->desactivar_efs('autoriza_aut');
			
			//}
			//if ($filtro['legajo_aut'] == $leg_sup){
			//$form->set_solo_lectura('autoriza_aut',true);
			$form->desactivar_efs('autoriza_sup');
			//}
			$form->set_datos($listado);
			//$form->set_datos($this->dep('datos')->tabla('comision')->get());
		} else {
			$this->pantalla()->eliminar_evento('eliminar');
		}
	}

	function evt__formulario__modificacion($datos)
	{
		$this->dep('datos')->tabla('comision')->set($datos);
	}

	function resetear()
	{
		$this->dep('datos')->resetear();
		$this->set_pantalla('pant_seleccion');
	}

	//---- EVENTOS CI -------------------------------------------------------------------

	function evt__agregar()
	{
		$this->set_pantalla('pant_edicion');
	}

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
		$this->dep('datos')->sincronizar();
		$this->resetear();
	}

}

?>