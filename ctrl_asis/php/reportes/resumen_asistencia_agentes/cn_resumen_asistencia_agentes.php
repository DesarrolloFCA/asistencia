<?php
include_once 'datos/consultas_agentes.php';
include_once 'libs/datos_fijos.php';
class cn_resumen_asistencia_agentes extends ctrl_asis_cn
{
	function ini()
	{
	}
        
        function get_agentes($f)
        {
            
            
                $resp =  consultas_agentes::get_agentes($f);
                
                $salida = array();
                $i=0;
                foreach($resp as $r)
                {
                  //  $r['agente_id'] = consultas_agentes::recuperar_id_x_legajo($r['legajo']);
                    $r['promedio'] = consultas_agentes::horas_promedio_por_periodo($r, $f);
                    $r['inasistencias'] = consultas_agentes::cant_ausentes_por_periodo($r, $f);
                    $r['inasistencias_p'] = consultas_agentes::cant_ausentes_por_periodo($r, datos_fijos::primer_ultimo(($f)));
                    $salida[] = $r;
                   // if($i++ == 10) break;
                }
                return $salida;
        }

}

?>