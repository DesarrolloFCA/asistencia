<?php
include_once 'datos/consultas_agentes.php';
include_once 'libs/datos_fijos.php';
class cn_control extends ctrl_asis_cn
{
	function ini()
	{
            
	}
        
        function get_agentes($f)
        {
           // $f['legajo']  = 28983;
            return consultas_agentes::get_agentes($f);
        }
        
        function get_detalle_agente($l)
        {
            $resp = consultas_agentes::get_agentes($l);
            $vac = consultas_agentes::get_vacaciones($l);
            $detalle_vac = '';
            foreach($vac as $v)
            {
                $detalle_vac .= '<td align="center" valign="middle">'.$v['periodo'].'</td>';
                $detalle_vac .= '<td align="center" valign="middle">' . $v['dias_disponibles'] . '/' . $v['dias_totales'] . '</td>';
            }
            $salida = $resp[0];
            $salida['vacaciones'] = datos_fijos::vac_html_header().$detalle_vac.datos_fijos::vac_html_footer();
            $salida['agente_id'] = consultas_agentes::recuperar_id_x_legajo($resp[0]['legajo']);
            $salida['promedio_horas'] = consultas_agentes::horas_promedio($salida);
            return $salida;
        }
        
        function get_marcas_agente()
        {
            return consultas_agentes::recuperar_marcas(toba::memoria()->get_dato('agente'), '');
        }
    

}

?>