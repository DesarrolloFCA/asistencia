<?php



class ci_incio extends comision_ci
{
	protected $s__datos;
	//-----------------------------------------------------------------------------------
	//---- cuadro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro(comision_ei_cuadro $cuadro)
	{
		include("usuario_logueado.php");
		$legajo = usuario_logueado::get_legajo(toba::usuario()->get_id());
		$legajo = $legajo[0]['legajo'];
		$sql= "SELECT * from reloj.vm_detalle_pres
		where legajo = $legajo
		and fecha between '2024-05-01' and '2024-06-04'";
		$presentismo = toba::db('comision')->consultar($sql);
		$this->s__datos = $presentismo;
		
		//ei_arbol($presentismo);
		
		$cuadro->set_datos($presentismo);
	
	}

		
		
	
	//-----------------------------------------------------------------------------------
	//---- grafico ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__grafico(comision_ei_grafico $grafico)
	{
		    
      
    
	$j= count($this->s__datos);
	for($i=0; $i<$j;$i++){
		list($horas,$minutos,$segundos) = explode(":",$this->s__datos[$i]['horas_trabajadas']);
		$minu=intval($horas*60)+(intval($minutos));
		$datos_1[]= round($minu/60,2); 
		list($hora,$minuto,$segundos)=explode(":",$this->s__datos[$i]['horas_requeridad']);
		$minut= intval($hora*60)+(intval($minuto));
		$datos_2[]=round($minut/60,2);
		list($año,$mes,$dia) = explode("-",$this->s__datos[$i]['fecha']);
		$dias[]= $dia; //.'/' . $mes;
		
	}
	$datos_1 = array_reverse($datos_1);
	$dias = array_reverse($dias);
	//ei_arbol($mes);
	/*switch ($mes) {
		case '01': 
			$mes ='Enero';
			break;
		case '02': 
			$mes= 'Febrero';
			break;
		case '03':
			$mes = 'Marzo';
			break;
		case '04':
			$mes = 'Abril';
			break;
		case '05':
			$mes = 'Mayo';
			break;
		case '06':
			$mes = 'Junio';
			break;
		case '07':
			$mes = 'Julio';
			break;
		case '08':
			$mes= 'Agosto';
			break;
		case '09':
			$mes = 'Septiembre';
			break;
		case '10' :
			$mes = 'Octubre';
			break;
		case '11':
			$mes = 'Noviembre';
			break;
		default:
			$mes = 'Diciembre';
			break;
		}*/	
	
	$grafico->conf()->canvas__set_titulo('Horas Trabajadas del último mes del corriente año');
	
          $grafico->conf()
                 ->serie__agregar('Horas Trabajadas', $datos_1)
                 ->serie__set_color('green')
                 ->serie__set_leyenda('Horas cumplidas');
  
          
   
         
          $grafico->conf()
                  ->serie__agregar('Horas Requeridas', $datos_2)
                  ->serie__set_color('red')
                  ->serie__set_leyenda('Horas Requeridas');
   
          $serie = $grafico->conf()->serie('Horas Requeridas')->SetWeight(3);
			$grafico->conf()->canvas()->xaxis->SetTickLabels($dias);
		  $grafico->conf()->canvas()->ygrid->SetFill(true, '#EFEFEF@0.8', '#BBCCFF@0.1');
		  
		
	}

}
?>