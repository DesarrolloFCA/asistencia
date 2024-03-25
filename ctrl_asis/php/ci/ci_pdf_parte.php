<?php
class ci_pdf_parte extends toba_ci
{
	function vista_pdf(toba_vista_pdf $salida)
	{

        //----- Obtengo datos necesarios ------------------------------------------------------------

        $datos = toba::tabla('parte')->get_parte($_SESSION['id_parte']);

        if($datos['t_manana'] == 1) { $datos['t_manana_vista'] = 'Si'; }else{ $datos['t_manana_vista'] = 'No';  }
        if($datos['t_tarde']  == 1) { $datos['t_tarde_vista']  = 'Si'; }else{ $datos['t_tarde_vista']  = 'No';  }
        if($datos['t_noche']  == 1) { $datos['t_noche_vista']  = 'Si'; }else{ $datos['t_noche_vista']  = 'No';  }

        $dependencia = $this->dep('mapuche')->get_dependencia($datos['cod_depcia']);
        $datos['desc_depcia'] = $dependencia['desc_depcia'];

        $datos['cuil'] = $this->dep('mapuche')->get_cuil($datos['legajo']);

        list($y,$m,$d)=explode("-",$datos['fecha_nacimiento']); //2011-03-31
        $datos['fecha_nacimiento'] = $d."/".$m."/".$y;

        $dias_calculo = $datos['dias'] - 1;
        $dias = '+'.$dias_calculo.' day';
        $datos['fecha_fin_licencia']    = date ( 'Y-m-d' , strtotime ( $dias , strtotime ( $datos['fecha_inicio_licencia'] ) )  ); //sumamos N dias a la fecha de inicio licencia
        list($y,$m,$d)=explode("-",$datos['fecha_fin_licencia']); //2011-03-31
        $datos['fecha_fin_licencia'] = $d."/".$m."/".$y;

        list($y,$m,$d)=explode("-",$datos['fecha_inicio_licencia']); //2011-03-31
        $datos['fecha_inicio_licencia'] = $d."/".$m."/".$y;

        list($f,$h)=explode(" ",$datos['fecha_alta']);
        list($y,$m,$d)=explode("-",$f); //2015-04-17 08:49:03
        $datos['fecha_alta'] = $d."/".$m."/".$y." ".$h;

        list($f,$h)=explode(" ",$datos['fecha_cierre']);
        list($y,$m,$d)=explode("-",$f); //2015-04-17 08:49:03
        $datos['fecha_cierre'] = $d."/".$m."/".$y." ".$h;

        $datos['usuario_alta_nombre']   = $this->dep('datos')->tabla('apex_usuario')->get_nombre_usuario($datos['usuario_alta']);
        $datos['usuario_cierre_nombre'] = $this->dep('datos')->tabla('apex_usuario')->get_nombre_usuario($datos['usuario_cierre']);
        $datos['usuario_medico_nombre'] = $this->dep('datos')->tabla('apex_usuario')->get_nombre_usuario($datos['usuario_medico']);

        $datos['id_motivo_vista']       = toba::tabla('motivo')->get_descripcion($datos['id_motivo']);
        $datos['id_decreto_vista']      = toba::tabla('decreto')->get_descripcion($datos['id_decreto']);
        if(!empty($datos['id_articulo']) ) { $datos['id_articulo_vista']     = toba::tabla('articulo')->get_descripcion($datos['id_articulo']); }

        if($datos['estado'] == 'C'){ $datos['estado'] = "Cerrado/Levantado"; }
        if(empty($datos['observaciones'])){ $datos['observaciones'] = "-"; }
        if(empty($datos['observaciones_cierre'])){ $datos['observaciones_cierre'] = "-"; }

        if($datos['id_decreto'] == 3){  $accion = "No concederle";   }else{  $accion = 'Concederle'; }
     
        //En caso de que sea un parte de vacaciones
        if(!empty($datos['id_motivo']) && $datos['id_motivo'] == 35){
            $parte_anio = toba::tabla('parte')->get_parte_anio($datos['id_parte']);
            $datos['anio']  = $parte_anio['anio'];
        }else{
            $datos['anio']  = "-";
        }

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

        
        //------ PIE DE PAGINA ----------------------------------------------------------------------
        #$formato = utf8_decode('Fecha impresión: '.date("d/m/Y").'                                                                                                                       Página {PAGENUM} de {TOTALPAGENUM}');
        #$pdf->ezStartPageNumbers(500, 20, 8, 'left', $formato, 1);    //x, y, size, pos, texto, pagina inicio


        //------ CUERPO DE CONTENIDOS  --------------------------------------------------------------

        //DEFINICION GENERAL DEL PROCESO
        #$pdf->ezSetY($this->puntos_cm(23.5));

        $linea = 26.4;
        $pdf->ezSetY($this->puntos_cm($linea));
        $pdf->ezText('<b>DATOS DEL AGENTE</b> ', 10, array('justification' => 'left', 'left'=> $this->puntos_cm(0.4)));
        
        $linea = $linea-0.55; //25
        $pdf->ezSetY($this->puntos_cm($linea));
        $pdf->ezText('<b>Legajo:</b> ', 9, array('justification' => 'left', 'left'=> $this->puntos_cm(0.4)));
        $pdf->ezSetY($this->puntos_cm($linea));
        $pdf->ezText($datos['legajo'], 9, array('justification' => 'left', 'left'=> $this->puntos_cm(5)));

        $pdf->ezSetY($this->puntos_cm($linea));
        $pdf->ezText('<b>C.U.I.L.:</b> ', 9, array('justification' => 'left', 'left'=> $this->puntos_cm(10.4)));
        $pdf->ezSetY($this->puntos_cm($linea));
        $pdf->ezText($datos['cuil'], 9, array('justification' => 'left', 'left'=> $this->puntos_cm(12.0)));

        $linea = $linea-0.55;
        $pdf->ezSetY($this->puntos_cm($linea)); 
        $pdf->ezText('<b>Apellido y Nombre:</b> ', 9, array('justification' => 'left', 'left'=> $this->puntos_cm(0.4)));
        $pdf->ezSetY($this->puntos_cm($linea)); 
        $pdf->ezText($datos['nombre_completo'], 9, array('justification' => 'left', 'left'=> $this->puntos_cm(5)));

        $linea = $linea-0.55;
        $pdf->ezSetY($this->puntos_cm($linea)); 
        $pdf->ezText('<b>Fecha de nacimiento:</b> ', 9, array('justification' => 'left', 'left'=> $this->puntos_cm(0.4)));
        $pdf->ezSetY($this->puntos_cm($linea)); 
        $pdf->ezText($datos['fecha_nacimiento'], 9, array('justification' => 'left', 'left'=> $this->puntos_cm(5)));
   
        $pdf->ezSetY($this->puntos_cm($linea)); 
        $pdf->ezText('<b>Edad:</b> ', 9, array('justification' => 'left', 'left'=> $this->puntos_cm(10.4)));
        $pdf->ezSetY($this->puntos_cm($linea)); 
        $pdf->ezText($datos['edad'], 9, array('justification' => 'left', 'left'=> $this->puntos_cm(12)));

        $pdf->ezSetY($this->puntos_cm($linea)); 
        $pdf->ezText('<b>Estado civil:</b> ', 9, array('justification' => 'left', 'left'=> $this->puntos_cm(14)));
        $pdf->ezSetY($this->puntos_cm($linea)); 
        $pdf->ezText($datos['estado_civil'], 9, array('justification' => 'left', 'left'=> $this->puntos_cm(16.6)));

        $linea = $linea-0.55;
        $pdf->ezSetY($this->puntos_cm($linea)); 
        $pdf->ezText('<b>Domicilio:</b>', 9, array('justification' => 'left', 'left'=> $this->puntos_cm(0.4)));
        $pdf->ezSetDy($this->puntos_cm(0.4),'makeSpace');
        $pdf->ezText($datos['domicilio'], 9, array('justification' => 'left', 'left'=> $this->puntos_cm(5)));

        $linea = $linea-0.55;
        $pdf->ezSetY($this->puntos_cm($linea)); 
        $pdf->ezText('<b>Localidad:</b> ', 9, array('justification' => 'left', 'left'=> $this->puntos_cm(0.4)));
        $pdf->ezSetDy($this->puntos_cm(0.4),'makeSpace');
        $pdf->ezText(utf8_decode($datos['localidad']), 9, array('justification' => 'left', 'left'=> $this->puntos_cm(5)));

        $linea = $linea-0.55;
        $pdf->ezSetY($this->puntos_cm($linea)); 
        $pdf->ezText('<b>Observaciones:</b> ', 9, array('justification' => 'left', 'left'=> $this->puntos_cm(0.4)));
        $pdf->ezSetDy($this->puntos_cm(0.4),'makeSpace');
        $pdf->ezText($datos['observaciones'], 9, array('justification' => 'left', 'left'=> $this->puntos_cm(5)));

        
        $linea = $linea-0.75; //25
        $pdf->ezSetY($this->puntos_cm($linea)); // $pdf->ezSetDy(-$this->puntos_cm(0.6),'makeSpace');
        $pdf->ezText(utf8_decode('<b>DATOS DEL INSTITUTO</b>'), 10, array('justification' => 'left', 'left'=> $this->puntos_cm(0.4)));        
   
        $linea = $linea-0.55; //25
        $pdf->ezSetY($this->puntos_cm($linea)); 
        $pdf->ezText('<b>Instituto/Dependencia:</b> ', 9, array('justification' => 'left', 'left'=> $this->puntos_cm(0.4)));
        $pdf->ezSetY($this->puntos_cm($linea)); 
        $pdf->ezText(utf8_decode($datos['desc_depcia']), 9, array('justification' => 'left', 'left'=> $this->puntos_cm(5)));

        $pdf->ezSetY($this->puntos_cm($linea)); 
        $pdf->ezText('<b>Agrupamiento:</b> ', 9, array('justification' => 'left', 'left'=> $this->puntos_cm(10.4)));
        $pdf->ezSetY($this->puntos_cm($linea)); 
        $pdf->ezText($datos['agrupamiento'], 9, array('justification' => 'left', 'left'=> $this->puntos_cm(14)));

        /*$pdf->ezSetDy(-$this->puntos_cm(0.4),'makeSpace');
        $pdf->ezText(utf8_decode('<b>Turno mañana:</b>'), 10, array('justification' => 'left', 'left'=> $this->puntos_cm(0.4)));
        $pdf->ezSetDy($this->puntos_cm(0.45),'makeSpace');
        $pdf->ezText($datos['t_manana_vista'], 11, array('justification' => 'left', 'left'=> $this->puntos_cm(5)));
        $pdf->ezSetDy(-$this->puntos_cm(0.2),'makeSpace');
        $pdf->ezText(utf8_decode('<b>Turno tarde:</b>'), 10, array('justification' => 'left', 'left'=> $this->puntos_cm(0.4)));
        $pdf->ezSetDy($this->puntos_cm(0.45),'makeSpace');
        $pdf->ezText($datos['t_tarde_vista'], 11, array('justification' => 'left', 'left'=> $this->puntos_cm(5)));      
        $pdf->ezSetDy(-$this->puntos_cm(0.2),'makeSpace');
        $pdf->ezText(utf8_decode('<b>Turno noche:</b>'), 10, array('justification' => 'left', 'left'=> $this->puntos_cm(0.4)));
        $pdf->ezSetDy($this->puntos_cm(0.45),'makeSpace');
        $pdf->ezText($datos['t_noche_vista'], 11, array('justification' => 'left', 'left'=> $this->puntos_cm(5)));*/
 
        $linea = $linea-0.75; 
        $pdf->ezSetY($this->puntos_cm($linea)); 
        $pdf->ezText(utf8_decode('<b>DATOS DEL PARTE</b>'), 10, array('justification' => 'left', 'left'=> $this->puntos_cm(0.4)));        
        
        /*$pdf->ezSetDy(-$this->puntos_cm(0.4),'makeSpace');
        $pdf->ezText('<b>Estado del parte:</b>', 10, array('justification' => 'left', 'left'=> $this->puntos_cm(0.4)));
        $pdf->ezSetDy($this->puntos_cm(0.45),'makeSpace');
        $pdf->ezText($datos['estado'], 11, array('justification' => 'left', 'left'=> $this->puntos_cm(5)));*/

        $linea = $linea-0.55; 
        $pdf->ezSetY($this->puntos_cm($linea)); 
        $pdf->ezText(utf8_decode('<b>Fecha recepción:</b>'), 9, array('justification' => 'left', 'left'=> $this->puntos_cm(0.4)));
        $pdf->ezSetY($this->puntos_cm($linea)); 
        $pdf->ezText($datos['fecha_alta'], 9, array('justification' => 'left', 'left'=> $this->puntos_cm(5)));

        $pdf->ezSetY($this->puntos_cm($linea)); 
        $pdf->ezText('<b>Usuario alta:</b>', 9, array('justification' => 'left', 'left'=> $this->puntos_cm(10.4)));
        $pdf->ezSetY($this->puntos_cm($linea)); 
        $pdf->ezText($datos['usuario_alta_nombre'], 9, array('justification' => 'left', 'left'=> $this->puntos_cm(14)));

        $linea = $linea-0.55;
        $pdf->ezSetY($this->puntos_cm($linea)); 
        $pdf->ezText('<b>Fecha cierre:</b>', 9, array('justification' => 'left', 'left'=> $this->puntos_cm(0.4)));
        $pdf->ezSetY($this->puntos_cm($linea)); 
        $pdf->ezText('<b>'.$datos['fecha_cierre'].'</b>', 9, array('justification' => 'left', 'left'=> $this->puntos_cm(5)));

        $pdf->ezSetY($this->puntos_cm($linea)); 
        $pdf->ezText('<b>Usuario cierre:</b>', 9, array('justification' => 'left', 'left'=> $this->puntos_cm(10.4)));
        $pdf->ezSetY($this->puntos_cm($linea)); 
        $pdf->ezText($datos['usuario_cierre_nombre'], 9, array('justification' => 'left', 'left'=> $this->puntos_cm(14)));

        $linea = $linea-0.55;
        $pdf->ezSetY($this->puntos_cm($linea)); 
        $pdf->ezText('<b>Fecha Inicio Licencia:</b>', 10, array('justification' => 'left', 'left'=> $this->puntos_cm(0.4)));
        $pdf->ezSetY($this->puntos_cm($linea)); 
        $pdf->ezText('<b>'.$datos['fecha_inicio_licencia'].'</b>', 10, array('justification' => 'left', 'left'=> $this->puntos_cm(5)));

        $pdf->ezSetY($this->puntos_cm($linea)); 
        $pdf->ezText(utf8_decode('<b>Días de licencia:</b>'), 9, array('justification' => 'left', 'left'=> $this->puntos_cm(10.4)));
        $pdf->ezSetY($this->puntos_cm($linea)); 
        $pdf->ezText($datos['dias'], 9, array('justification' => 'left', 'left'=> $this->puntos_cm(14)));

        $linea = $linea-0.55;
        $pdf->ezSetDy(-$this->puntos_cm(0.2),'makeSpace');
        $pdf->ezText('<b>Fecha Fin Licencia:</b>', 10, array('justification' => 'left', 'left'=> $this->puntos_cm(0.4)));
        $pdf->ezSetY($this->puntos_cm($linea));
        $pdf->ezText('<b>'.$datos['fecha_fin_licencia'].'</b>', 10, array('justification' => 'left', 'left'=> $this->puntos_cm(5)));

        $pdf->ezSetY($this->puntos_cm($linea)); 
        $pdf->ezText('<b>Motivo:</b> ', 9, array('justification' => 'left', 'left'=> $this->puntos_cm(10.4)));
        $pdf->ezSetY($this->puntos_cm($linea)); 
        $pdf->ezText($datos['id_motivo_vista'], 9, array('justification' => 'left', 'left'=> $this->puntos_cm(14)));
        
        if(!empty($datos['anio'])){
            $linea = $linea-0.55;
            $pdf->ezSetY($this->puntos_cm($linea)); 
            $pdf->ezText('<b>Corresponde a:</b>', 9, array('justification' => 'left', 'left'=> $this->puntos_cm(0.4)));
            $pdf->ezSetY($this->puntos_cm($linea)); 
            $pdf->ezText($datos['anio'], 9, array('justification' => 'left', 'left'=> $this->puntos_cm(5)));    
        }
        
        $linea = $linea-0.55;
        $pdf->ezSetY($this->puntos_cm($linea)); 
        $pdf->ezText('<b>Decreto:</b> ', 9, array('justification' => 'left', 'left'=> $this->puntos_cm(0.4)));
        $pdf->ezSetY($this->puntos_cm($linea)); 
        $pdf->ezText(utf8_decode($datos['id_decreto_vista']), 9, array('justification' => 'left', 'left'=> $this->puntos_cm(5)));
   
        $pdf->ezSetY($this->puntos_cm($linea)); 
        $pdf->ezText(utf8_decode('<b>Artículo/Inciso:</b>'), 9, array('justification' => 'left', 'left'=> $this->puntos_cm(10.4)));
        $pdf->ezSetY($this->puntos_cm($linea)); 
        $pdf->ezText(utf8_decode($datos['id_articulo_vista']), 9, array('justification' => 'left', 'left'=> $this->puntos_cm(14)));
        
        $linea = $linea-0.55;
        $pdf->ezSetY($this->puntos_cm($linea)); 
        $pdf->ezText('<b>Profesional Interviniente:</b>', 9, array('justification' => 'left', 'left'=> $this->puntos_cm(0.4)));
        $pdf->ezSetY($this->puntos_cm($linea)); 
        $pdf->ezText($datos['usuario_medico_nombre'], 9, array('justification' => 'left', 'left'=> $this->puntos_cm(5)));

        $linea = $linea-0.55;
        $pdf->ezSetY($this->puntos_cm($linea)); 
        $pdf->ezText('<b>Observaciones:</b>', 9, array('justification' => 'left', 'left'=> $this->puntos_cm(0.4)));
        $pdf->ezSetY($this->puntos_cm($linea));
        $pdf->ezText($datos['observaciones_cierre'], 9, array('justification' => 'left', 'left'=> $this->puntos_cm(5)));


        //**************************************************************************************************
        //ENCABEZADO ----------------------------------------------------------
        //$pdf = $salida->get_pdf();
        foreach ($pdf->ezPages as $pageNum=>$id){

                $pdf->reopenObject($id);
                //------------------------------------------
                # Rectángulo Superior Encabezado              
                $pdf->rectangle($this->puntos_cm(1),$this->puntos_cm(26.8),$this->puntos_cm(11),$this->puntos_cm(2));  //Lado iquierdo
                $pdf->rectangle($this->puntos_cm(12),$this->puntos_cm(26.8),$this->puntos_cm(8),$this->puntos_cm(2));  //Lado redecho

                # Rectángulo Superior Encabezado
                //$pdf->rectangle($this->puntos_cm(1),$this->puntos_cm(23.8),$this->puntos_cm(9.5),$this->puntos_cm(4));//Lado iquierdo 
                //$pdf->rectangle($this->puntos_cm(10.5),$this->puntos_cm(23.8),$this->puntos_cm(9.5),$this->puntos_cm(4));//Lado redecho


                //Encabezado derecho
                $TOP = 26;// + 3.0; //27.8
                $pdf->ezSetY($this->puntos_cm(28.6));
                $pdf->ezText(utf8_decode('<b>Parte de Inasistencia Nro. '.$datos['id_parte'].'</b>'), 12, array('justification' => 'left', 'left'=> $this->puntos_cm(11.4))); 

                $pdf->ezSetDy(-$this->puntos_cm(0.1),'makeSpace');
                $pdf->ezText(utf8_decode('Fecha: 15/04/2015'), 9, array('justification' => 'left', 'left'=> $this->puntos_cm(11.4))); //Cód: RE_DGM_006   Rev: 01   
                $pdf->ezSetDy(-$this->puntos_cm(0.1),'makeSpace');
                $pdf->ezText(utf8_decode('Fecha impresión: ').date("d/m/Y"), 9, array('justification' => 'left', 'left'=> $this->puntos_cm(11.4)));              

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

        //----------------------------------
        $tmp = $pdf->ezOutput(0);
        header('Cache-Control: private');
        header('Content-type: application/pdf');
        header('Content-Length: '.strlen(ltrim($tmp)));
        header('Content-Disposition: attachment; filename="parte.pdf"');
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
}
?>