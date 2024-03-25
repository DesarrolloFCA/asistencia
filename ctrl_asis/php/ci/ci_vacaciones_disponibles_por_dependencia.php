<?php
class ci_vacaciones_disponibles_por_dependencia extends ctrl_asis_ci
{
	protected $s__datos_filtro;
	protected $s__datos;
	//-----------------------------------------------------------------------------------
	//---- cuadro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro(ctrl_asis_ei_cuadro $cuadro)
	{
		if (isset($this->s__datos_filtro)) {
			$resumen = array();
			$anio_inicio = 2010;            
			$cod_depcia = $this->s__datos_filtro['cod_depcia'];

			$legajos = $this->dep('mapuche')->get_legajos_depencencia($cod_depcia);
			foreach ($legajos as $legajo){
				$legajo = $legajo["legajo"];
				//Buscamos información de dependecia y agrupamiento
				$datos_legajo = $this->dep('mapuche')->get_dependencias_legajo($legajo, $cod_depcia);
				//Buscamos los datos personales del agente
				$filtro['legajo'] = $legajo;
				$datos_agente = $this->dep('mapuche')->get_datos_agente($filtro);
				$apellido = $datos_agente[0]['apellido'];
				$nombre = $datos_agente[0]['nombre'];
				//Buscamos la información de agrupamiento
				$cargos = $this->dep('mapuche')->get_dependencias_por_legajo_sin_repetir_cargos($legajo, $cod_depcia);
				foreach ($cargos as $cargo){
					$agrupamiento = $cargo["agrupamiento"];
					//seteamos fecha ingreso de tabla antiguedad --------------------------------
					$dato_antiguedad = toba::tabla('antiguedad')->get_antiguedad($legajo);
					if(!empty($dato_antiguedad['fecha_ingreso'])){
						$agente['fec_ingreso'] = $dato_antiguedad['fecha_ingreso'];
					}else{                
						$sql = "SELECT fec_ingreso FROM uncu.legajo WHERE legajo = '$legajo' and agrupamiento = '$agrupamiento' ";
						$agente =  toba::db('mapuche')->consultar_fila($sql); 
					}
					//hay que iterar por los años vigentes
					//obtenemos dias tomados---------------------------------------
					if(!empty($agente['fec_ingreso'])){
						
						for ($i=$anio_inicio; $i <= date("Y") ; $i++){
							$dias_tomados = 0;
							$anio = $i;
							//Se busca antiguedad por año
							$antiguedad = toba::tabla('vacaciones_antiguedad')->get_array_antiguedad($agente['fec_ingreso'],$agrupamiento, $anio);
							$filtro['legajo']              = $legajo;
							$filtro['id_motivo']           = 35; //vacaciones
							$filtro['agrupamiento']     = $agrupamiento;
							$filtro['parte_anio']          = $anio; //ano seleccionado por vacaciones
							$filtro['no_anio_fechas']   = true;
							$filtro['anio']                = $anio;        
							
							$partes = toba::tabla('parte')->get_listado($filtro);
							if(count($partes)>0){
								foreach ($partes as $parte) {
									$dias_tomados = $dias_tomados + $parte['dias'];
								}
							}

							$vacaciones_restantes = toba::tabla('vacaciones_restantes')->get_dias($legajo, $anio, $agrupamiento);
							if (is_null($vacaciones_restantes)){
								$dias_disponibles = $antiguedad['dias'] - $dias_tomados;
							}else{
								$dias_disponibles = $vacaciones_restantes - $dias_tomados;
							}
							//Si tiene dias disponibles, lo mostramos en el listado
							if($dias_disponibles > 0){
								$respuesta["legajo"] = $legajo;
								$respuesta["apellido"] = $apellido;
								$respuesta["nombre"] = $nombre;                        
								$respuesta["anio"] = $anio;
								$respuesta["total_dias"] = $dias_disponibles;
								$respuesta["dependencia"] = $datos_legajo[0]["desc_depcia"];
								$respuesta["agrupamiento"] = $datos_legajo[0]["descagrup"];
								array_push($resumen, $respuesta);
							}
						}
					}
				}
			}
			$this->s__datos = $resumen;
			$cuadro->set_datos($this->s__datos);
		}        
	}

	function vista_pdf(toba_vista_pdf $salida)
	{
		
		//----- Generales ---------------------------------------------------------------------------
		if(empty($salida)){
			require_once(toba_dir().'/php/3ros/ezpdf/class.ezpdf.php');
			$pdf = new Cezpdf('A4');  //'A4' (595.28,841.89  
		}else{
			$pdf = $salida->get_pdf();    
		}
		$pdf->selectFont(toba_dir() . '/php/3ros/ezpdf/fonts/Helvetica.afm');
		$pdf->ezSetMargins($this->puntos_cm(1),$this->puntos_cm(1),$this->puntos_cm(1),$this->puntos_cm(1)); //top,bottom,left,right      ,$this->puntos_cm(0.65)
		$pdf->setLineStyle(0.5); //grosor de linea
		
		
		
		//ENCABEZADO ----------------------------------------------------------
		//$pdf = $salida->get_pdf();
		foreach ($pdf->ezPages as $pageNum=>$id){

				$pdf->reopenObject($id);
				//------------------------------------------
				# RectÃ¡ngulo Superior Encabezado              
				$pdf->rectangle($this->puntos_cm(1),$this->puntos_cm(26.8),$this->puntos_cm(11),$this->puntos_cm(2));  //Lado iquierdo
				$pdf->rectangle($this->puntos_cm(12),$this->puntos_cm(26.8),$this->puntos_cm(8),$this->puntos_cm(2));  //Lado redecho

				# RectÃ¡ngulo Superior Encabezado
				//$pdf->rectangle($this->puntos_cm(1),$this->puntos_cm(23.8),$this->puntos_cm(9.5),$this->puntos_cm(4));//Lado iquierdo
				//$pdf->rectangle($this->puntos_cm(10.5),$this->puntos_cm(23.8),$this->puntos_cm(9.5),$this->puntos_cm(4));//Lado redecho


				//Encabezado derecho
				$TOP = 26;// + 3.0; //27.8
				$pdf->ezSetY($this->puntos_cm(28.6));
				$pdf->ezText(utf8_decode('<b>VACACIONES RESTANTES</b>'), 12, array('justification' => 'left', 'left'=> $this->puntos_cm(11.4)));

				$pdf->ezSetDy(-$this->puntos_cm(0.1),'makeSpace');
				$pdf->ezText('Fecha impresi�n: '.date("d/m/Y"), 9, array('justification' => 'left', 'left'=> $this->puntos_cm(11.4)));              

				//Encabezado izquierdo
				//------------------------------------------
				$imagen = toba::proyecto()->get_path().'/www/img/logo_pdf_2.png'; //logo_pdf.jpg   282 x 90 => 141, 45
				
				//Pasamos a cm para mostrarla
				$dpi = 96;
				list($ancho_orig, $alto_orig) = getimagesize($imagen);
				$alto      = $alto_orig * 2.54 / $dpi;
				$ancho     = $ancho_orig * 2.54 / $dpi;
				$alto      = number_format($alto, 2, '.', '');
				$ancho     = number_format($ancho, 2, '.', '');

				$pdf->ezSetY($this->puntos_cm(28.3));
				$pdf->ezImage($imagen, 8, $this->puntos_cm($ancho), 'none', 'left');
				
				$pdf->closeObject();
		}
		
		$salida -> separacion ();
		$salida -> separacion ();
		$this->dependencia('cuadro')->vista_pdf($salida);
		$salida -> separacion ();
		$salida -> separacion ();
		//----------------------------------
		$tmp = $pdf->ezOutput(0);
		header('Cache-Control: private');
		header('Content-type: application/pdf');
		header('Content-Length: '.strlen(ltrim($tmp)));
		header('Content-Disposition: attachment; filename="VacacionesPorDependecia.pdf"');
		header('Pragma: no-cache');
		header('Expires: 0');
		echo ltrim($tmp);
		
	}


	function puntos_cm ($medida, $resolucion=72)
	{
		//// 2.54 cm / pulgada
		return ($medida/(2.54))*$resolucion;
		//A4 29.7 x 21
	}

	//-----------------------------------------------------------------------------------
	//---- filtro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__filtro__filtrar($datos)
	{
		$this->s__datos_filtro = $datos;
	}

	function evt__filtro__cancelar()
	{
		unset($this->s__datos_filtro);
	}

	function conf__filtro(ctrl_asis_ei_formulario $filtro)
	{
		if (isset($this->s__datos_filtro)) {
			$filtro->set_datos($this->s__datos_filtro);
		}
	}

}
?>