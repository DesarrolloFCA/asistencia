<?php
class form_ci_comision extends comision_ei_formulario
{
    function extender_objeto_js()
	{
		echo "
		//---- Validacion de EFs -----------------------------------
		
		{$this->objeto_js}.evt__fecha__validar = function()
		{
			
				//alert('Entrando en la funci�n de validaci�n de fecha'); // Alerta para confirmar la ejecuci�n
				var fechaAlta = this.ef('fecha').fecha(); // Obtiene la fecha ingresada
				var fechaActual = new Date();
				fechaAlta.setHours(0, 0, 0, 0);
				fechaActual.setHours(0, 0, 0, 0);
				// Asegúrate de convertir fechaAlta a un objeto Date si no lo es ya
				if (fechaAlta < fechaActual) {
    				this.ef('fecha').set_error('La fecha de inicio debe ser mayor o igual a la fecha actual.');
    				//alert('La fecha debe ser mayor o igual a la fecha actual. Fecha ingresada: ' + fechaAlta);
    				return false;
				}
				return true;

		}
		{$this->objeto_js}.evt__fecha_fin__validar = function()
		{
            var fechaAlta = this.ef('fecha').fecha(); // Obtiene la fecha ingresada
			var fechaFin = this.ef('fecha_fin').fecha();
            if (fechaAlta > fechaFin) {
    				this.ef('fecha_fin').set_error('La fecha de final debe ser mayor o igual a la fecha inicio.');
    				//alert('La fecha debe ser mayor o igual a la fecha actual. Fecha ingresada: ' + fechaAlta);
    				return false;
				}
				return true;


		}
			
			
		";
	}

}
?>