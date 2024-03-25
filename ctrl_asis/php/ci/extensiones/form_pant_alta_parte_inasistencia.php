<?php
class form_pant_alta_parte_inasistencia extends ctrl_asis_ei_formulario
{
	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		echo "
		//---- Validacion de EFs -----------------------------------
					
		{$this->objeto_js}.evt__id_motivo__validar = function()
		{
					if(this.ef('id_motivo').get_estado() == 35)
					{
						this.ef('anio').mostrar();
					}else
					{
						this.ef('anio').ocultar();
					}
					return true;
		}
		";
	}


}
?>