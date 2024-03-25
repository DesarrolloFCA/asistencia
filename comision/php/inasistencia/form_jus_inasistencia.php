<?php
class form_jus_inasistencia extends comision_ei_formulario
{
	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		echo "
		//---- Procesamiento de EFs --------------------------------
		
		{$this->objeto_js}.evt__id_motivo__procesar = function(es_inicial)
		{
			if (this.ef('id_motivo').get_estado() == 35){
		  		this.ef('observaciones').activar(false);
		}
		//---- Validacion general ----------------------------------
		
		{$this->objeto_js}.evt__validar_datos = function()
		{
			return true;
		}
		";
			}
	}

?>