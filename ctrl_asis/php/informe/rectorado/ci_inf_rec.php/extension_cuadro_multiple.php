<?php
class extension_cuadro_multiple extends toba_ei_cuadro
{
	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		echo "
		var estado_seleccion_total = false;
		function seleccion_total(evento)
		{
			if (estado_seleccion_total) {
				{$this->objeto_js}.deseleccionar_todos(evento);
				estado_seleccion_total = false;
			} else {
				{$this->objeto_js}.seleccionar_todos(evento);
				estado_seleccion_total = true;
			}
		};";
	}

}
?>