<?php
class ci_inasistencia extends ctrl_asis_ci
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
			$cuadro->set_datos($this->dep('datos')->tabla('parte')->get_listado_fca($this->s__datos_filtro));
		} else {
			$cuadro->set_datos($this->dep('datos')->tabla('parte')->get_listado_fca());
		}
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
			$form->set_solo_lectura('id_decreto');
			$form->set_solo_lectura('id_motivo');
			$form->set_solo_lectura('id_articulo');
			$form->set_datos($this->dep('datos')->tabla('parte')->get());
		} else {
			$this->pantalla()->eliminar_evento('eliminar');
		}
	}

	function evt__formulario__modificacion($datos)
	{
		$legajo = $datos['legajo'];
		$mail = $this->dep('datos2')->tabla('agentes_mail')->get_legajo_mail($legajo);
		ei_arbol($mail);
		//$this->dep('datos')->tabla('parte')->set($datos);

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
	function enviar_correos($correo)
	{
		require_once('mail/tobamail.php');

		$datos = $this->s__datos;

		$asunto = 'Esto es un correo de prueba';
		//Para enviar un correo formateado en HTML lo cargamos con la siguiente función. Si no, puedes meterle directamente una cadena de texto.
		//$mail->MsgHTML(file_get_contents('correomaquetado.html'), dirname(ruta_al_archivo));
		//Y por si nos bloquean el contenido HTML (algunos correos lo hacen por seguridad) una versión alternativa en texto plano (también será válida para lectores de pantalla)
		$mail->IsHTML(true); //el mail contiene html
		$fecha = date('d/m/Y', strtotime($datos['fecha']));

		$cuerpo = '<table>
						El/la agente  <b>' . $datos['agente'] . '</b> perteneciente a la catedra/oficina/ direccion <b>' . $datos['catedra'] . '</b>.<br/>
						Solicita comision de servicio con motivo de ' . $datos['motivo'] . ' a realizarse el dia ' . $fecha . ' 
						en ' . $datos['lugar'] . ' a partir de la hora ' . $datos['horario'] . '. Teniendo en cuenta las siguientes Observaciones: ' . $datos['observaciones'] . '
											
			</table>'; //date("d/m/y",$fecha)
		
		//Enviamos el correo
		$mail = new TobaMail($hacia, $asunto, $cuerpo, $desde);

		// Agregar un archivo adjunto
		//$mail->agregarAdjunto('nombre_archivo.pdf', '/ruta/al/archivo/nombre_archivo.pdf');

		try {
			$mail->ejecutar();
			echo "Correo enviado exitosamente.<br>";
		} catch (Exception $e) {
			echo "Error al enviar el correo: " . $e->getMessage();
		}
		

	}
}