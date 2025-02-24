<?php

use JpGraph\Graph;
use JpGraph\BarPlot;
use Laminas\Validator\InArray;

class ci_incio extends comision_ci
{
	protected $s__datos;
	protected $s__cargo;
	//-----------------------------------------------------------------------------------
	//---- cuadro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro(comision_ei_cuadro $cuadro)
	{
		include("usuario_logueado.php");
		$legajo = usuario_logueado::get_legajo(toba::usuario()->get_id());
		$legajo = $legajo[0]['legajo'];
		$sql = "SELECT count(*) cant from reloj.agentes
				WHERE legajo = $legajo ";
		$cargos = toba::db('comision')->consultar_fila($sql);
		$this->s__cargo = $cargos['cant'];
		/*$agente = $this->legajo_cargo();
		$legajo = $agente['legajo'];*/
		//ei_arbol($agente);

		$sql = "SELECT Distinct  fecha,hora_entrada,hora_salida,horas_trabajadas,horas_requeridad,descripcion,estado 
		from reloj.vm_detalle_pres
		where legajo = $legajo
		and fecha >= CURRENT_DATE - INTERVAL '30 days'";

		$presentismo = toba::db('comision')->consultar($sql);
		$this->s__datos = $presentismo;
		$cuadro->set_datos($presentismo);
	}
	function conf__cuadrograf(comision_ei_cuadro $cuadro)
	{
		$j = count($this->s__datos);



		//ei_arbol ($agente);
		for ($i = 0; $i < $j; $i++) {
			if ($this->s__datos[$i]['estado'] <> 'Ausente Justificado') {
				list($horas, $minutos, $segundos) = explode(":", $this->s__datos[$i]['horas_trabajadas']);
				$minu = intval($horas * 60) + (intval($minutos));
				$datos_1[] = round($minu / 60, 2);
			}
		}

		$prom_hora = round(array_sum($datos_1) / (count($datos_1) - 1), 2);

		list($hora, $minuto, $segundos) = explode(":", $this->s__datos[0]['horas_requeridad']);
		$minut = intval($hora * 60) + intval($minuto);
		$horas_requ = round($minut / 60, 2);
		//$horas_cumpli = ($prom_hora/$horas_requ) *100;
		$max = intval($horas_requ) + 2;

		$majorTicks = [];
		for ($i = 0; $i <= $max; $i++) {
			$majorTicks[] = (string)$i;
		}
		$majorTicksJson = json_encode($majorTicks);

		$script = "<html>
  <head>
   <script type='text/javascript' src='https://www.gstatic.com/charts/loader.js'></script>
   <script type='text/javascript'>
      google.charts.load('current', {'packages':['gauge']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {

        var data = google.visualization.arrayToDataTable([
          ['Label', 'Value'],
          ['Prom. Horas', 0],
          ]);

		var options = {
          width: 400, height: 220,
          greenFrom: $horas_requ, greenTo: $max ,
          yellowFrom:$horas_requ - 1, yellowTo: $horas_requ,
		  max : $max,
		//  redFrom:80, redTo: 0
          minorTicks: 6,
		  majorTicks: $majorTicksJson,
		  animation:{
        duration: 4000,
        easing: 'out',}
        };

        var chart = new google.visualization.Gauge(document.getElementById('chart_div'));

        chart.draw(data, options);
		data.setValue(0, 1, $prom_hora);
        chart.draw(data, options);

        //setInterval(function() {
        //  data.setValue(0, 1, 40 + Math.round(60 * Math.random()));
        //  chart.draw(data, options);
        //}, 1300);
        // setInterval(function() {
        //   data.setValue(1, 1, 40 + Math.round(60 * Math.random()));
        //   chart.draw(data, options);
        // }, 5000);
        //setInterval(function() {
         // data.setValue(2, 1, 60 + Math.round(20 * Math.random()));
         // chart.draw(data, options);
        //}, 260);
      }
    </script>
  </head>
  <body>
    <div id='chart_div' style='width: 400px; height: 220px;'></div>
  </body>
</html>";
		$datos[0]['grafico'] = $script;
		$cuadro->set_datos($datos);
	}

	//-----------------------------------------------------------------------------------
	//---- grafico ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------


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
			$minut = intval($hora * 60) + intval($minuto);
			$datos_2[] = round($minut / 60, 2);
			list($anio, $mes, $dia) = explode("-", $this->s__datos[$i]['fecha']);
			$dias[] = $dia; //.'/' . $mes;
		}

		$canvas = new Graph(900, 400);
		$canvas->SetScale("textlin", 0, 12);

		$majorTickPositions = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 12); // Posiciones principales
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
		$canvas->legend->SetPos(0.83, 0.15, 'left', 'bottom');
		$graficob->conf()->canvas__set($canvas);
	}
}
