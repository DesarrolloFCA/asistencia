<?php
class form_parte extends comision_ei_formulario
{
	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		echo "
		//---- Validacion de EFs -----------------------------------
		
		{$this->objeto_js}.evt__fecha_inicio_licencia__validar = function()
		{
		//alert('Entrando en la función de validación de fecha'); // Alerta para confirmar la ejecuci�n
				var fechaAlta = this.ef('fecha_inicio_licencia').fecha(); // Obtiene la fecha ingresada
				var fechaActual = new Date();
				fechaAlta.setHours(0, 0, 0, 0);
				fechaActual.setHours(0, 0, 0, 0);
				// Asegúrate de convertir fechaAlta a un objeto Date si no lo es ya
				if (fechaAlta < fechaActual) {
		    				this.ef('fecha_inicio_licencia').set_error('La fecha de inicio debe ser mayor o igual a la fecha actual.');
		    				//alert('La fecha debe ser mayor o igual a la fecha actual. Fecha ingresada: ' + fechaAlta);
		    				return false;
				}
				return true;
		}
		//---- Eventos ---------------------------------------------
		
		{$this->objeto_js}.evt__alta = function()
		{
		}
		";
	}


}
?>