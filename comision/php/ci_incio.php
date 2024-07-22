<?php

use JpGraph\Graph;
use JpGraph\BarPlot;

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
		$sql = "SELECT max(ncargo) cant from reloj.agentes
				WHERE legajo = $legajo ";
		$cargos = toba::db('comision')->consultar_fila($sql);
		//ei_arbol($cargos);
		if ($cargos['cant'] == 0){
		$sql= "SELECT Distinct  fecha,hora_entrada,hora_salida,horas_trabajadas,horas_requeridad,descripcion,estado 
		from reloj.vm_detalle_pres
		where legajo = $legajo
		and fecha >= CURRENT_DATE - INTERVAL '30 days'";
		} else {
		$sql= "SELECT fecha, hora_entrada, hora_salida, horas_trabajadas, SUM(horas_requeridad)::time AS horas_requeridad, 
		 descripcion,estado
		FROM  reloj.vm_detalle_pres
		WHERE   legajo = $legajo
		AND fecha >= CURRENT_DATE - INTERVAL '30 days'
		GROUP BY fecha, hora_entrada, hora_salida, horas_trabajadas, descripcion,estado";
		}
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

		$j = count($this->s__datos);
		for ($i = 0; $i < $j; $i++) {
			list($horas, $minutos, $segundos) = explode(":", $this->s__datos[$i]['horas_trabajadas']);
			$minu = intval($horas * 60) + (intval($minutos));
			$datos_1[] = round($minu / 60, 2);
			list($hora, $minuto, $segundos) = explode(":", $this->s__datos[$i]['horas_requeridad']);
			$minut = intval($hora * 60) + (intval($minuto));
			$datos_2[] = round($minut / 60, 2);
			list($año, $mes, $dia) = explode("-", $this->s__datos[$i]['fecha']);
			$dias[] = $dia; //.'/' . $mes;
		}

		$grafico->conf()->canvas__set_titulo('Horas Trabajadas');

		$grafico->conf()
			->serie__agregar('Horas Trabajadas', $datos_1)
			->serie__set_color('green')
			->serie__set_leyenda('Horas cumplidas');

		$grafico->conf()
			->serie__agregar('Horas Requeridas', $datos_2)
			->serie__set_color('red')
			->serie__set_leyenda('Horas Requeridas');

		$serie = $grafico->conf()->serie('Horas Requeridas')->SetWeight(3);
		$serie = $grafico->conf()->serie('Horas Trabajadas')->SetWeight(3);
		$grafico->conf()->canvas()->xaxis->SetTickLabels($dias);
		$grafico->conf()->canvas()->ygrid->SetFill(true, '#EFEFEF@0.8', '#BBCCFF@0.1');
	}
	//-----------------------------------------------------------------------------------
	//---- grafico barras----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__graficob(toba_ei_grafico $graficob)
	{
		require_once(toba_dir() . "/php/3ros/jpgraph/jpgraph.php");
		require_once(toba_dir() . '/php/3ros/jpgraph/jpgraph_bar.php');

		//$graficob->conf()->canvas__set_titulo("Barras!");
		//$datos = array(13, 5, 3, 15, 10);
		$j = count($this->s__datos);
		for ($i = 0; $i < $j; $i++) {
			list($horas, $minutos, $segundos) = explode(":", $this->s__datos[$i]['horas_trabajadas']);
			$minu = intval($horas * 60) + (intval($minutos));
			$datos_1[] = round($minu / 60, 2);
			list($hora, $minuto, $segundos) = explode(":", $this->s__datos[$i]['horas_requeridad']);
			$minut = intval($hora * 60) + (intval($minuto));
			$datos_2[] = round($minut / 60, 2);
			list($año, $mes, $dia) = explode("-", $this->s__datos[$i]['fecha']);
			$dias[] = $dia; //.'/' . $mes;
		}

		$canvas = new Graph(900, 400);
		$canvas->SetScale("textlin", 0, 10);
		
		$majorTickPositions = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10); // Posiciones principales
		$canvas->yaxis->SetTickPositions($majorTickPositions);
		// Configurar los títulos
		$canvas->title->Set("Horas Trabajadas vs Horas Requeridas");
		//$canvas->title->SetFont(FF_ARIAL, FS_BOLD, 14);
		$canvas->xaxis->title->Set("Días");
		$canvas->yaxis->title->Set("Horas");

		// Configurar las etiquetas del eje X con los días
		$canvas->xaxis->SetTickLabels($dias);

		//$canvas->title->SetFont(FF_ARIAL,FS_BOLD,14);

		// Ajustar los márgenes del gráfico (izquierda, derecha, arriba, abajo)
		$canvas->SetMargin(50, 30, 50, 50);

		// Crear los objetos de barra
		$bplot1 = new BarPlot($datos_1);
		$bplot1->SetLegend('Horas Trabajadas');
		$bplot2 = new BarPlot($datos_2);
		$bplot2->SetLegend('Horas Requeridas');



		// Configurar colores
		$bplot1->SetFillColor('blue');
		$bplot2->SetFillColor('red');

		// Añadir las barras al gráfico
		//$gbplot = new GroupBarPlot(array($bplot1));
		$canvas->Add($bplot2);
		$canvas->Add($bplot1);


		//$canvas->legend->SetLayout(LEGEND_HOR);
		
		//$canvas->legend->SetFont(FF_ARIAL, FS_NORMAL, 12);
		//$canvas->legend->SetFillColor('white');
		
		//$canvas->legend->SetColumns(1);
		//$canvas->graph_theme = null;
		$canvas->SetFrame(true, 'black', 1);
		$canvas->legend->SetPos(0.83,0.15,'left','bottom');
		$graficob->conf()->canvas__set($canvas);
	}
}
