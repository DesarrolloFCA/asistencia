<?php
class ci_dependencia_agente extends ctrl_asis_ci
{
	//-----------------------------------------------------------------------------------
	//---- form -------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------
function evt__form__modificacion($datos)
	{
            //eliminar duplicados
                $sql = "SELECT DISTINCT
reloj.agentes.legajo,
reloj.agentes.agente_id
FROM
reloj.agentes
GROUP BY
reloj.agentes.legajo,
reloj.agentes.agente_id" ;
                $resp_a = consultar_fuente($sql);
                $primero = 0;
                foreach($resp_a as $r)
                {
                    
                    
                        $sql = "DELETE FROM reloj.agentes WHERE reloj.agentes.legajo = ". $r['legajo'] ." AND  reloj.agentes.agente_id <> " . $r['agente_id'];
                        consultar_fuente($sql);
                    
                    
                }
                
                
                
            }
    
	/*function evt__form__modificacion($datos)
	{
            //aisgnacion de dependencia y area a legajos//
            $sql = "SELECT
                        uncu.legajo.*
                        FROM
                        uncu.legajo";
            $resp = toba::db('mapuche')->consultar($sql);
            foreach($resp as $r)
            {
                $sql = "SELECT
                        reloj.conf_areas.area_id,
                        reloj.conf_areas.dependencia_id
                        FROM
                        reloj.conf_areas
                        WHERE 
                        reloj.conf_areas.area_id = " . $r['cod_depcia'] ;
                $resp_a = consultar_fuente($sql);
                if(count($resp_a))
                {
                    $sql = "UPDATE reloj.agentes
                                      SET dependencia_id=".$resp_a[0]['dependencia_id'].", area_id=".$resp_a[0]['area_id']."
                                        WHERE legajo = " . $r['legajo'];
                
                    consultar_fuente($sql);
                }  else
                {
                    $sql = "UPDATE reloj.agentes
                                      SET dependencia_id=".$r['cod_depcia'].", area_id=2
                                        WHERE legajo = " . $r['legajo'];
                    consultar_fuente($sql);

                }    
                ei_arbol($sql);
                
                
                
            }
	}
	/*function evt__form__modificacion($datos)
	{
            //aisgnacion de dependencia y area a legajos//
            $sql = "SELECT
                        uncu.legajo.*
                        FROM
                        uncu.legajo";
            $resp = toba::db('mapuche')->consultar($sql);
            foreach($resp as $r)
            {
                $sql = "SELECT
                        reloj.conf_areas.area_id,
                        reloj.conf_areas.dependencia_id
                        FROM
                        reloj.conf_areas
                        WHERE 
                        reloj.conf_areas.area_id = " . $r['cod_depcia'] ;
                $resp_a = consultar_fuente($sql);
                if(count($resp_a))
                {
                    $sql = "UPDATE reloj.agentes
                                      SET dependencia_id=".$resp_a[0]['dependencia_id'].", area_id=".$resp_a[0]['area_id']."
                                        WHERE legajo = " . $r['legajo'];
                
                    consultar_fuente($sql);
                }  else
                {
                    $sql = "UPDATE reloj.agentes
                                      SET dependencia_id=".$r['cod_depcia'].", area_id=2
                                        WHERE legajo = " . $r['legajo'];
                    consultar_fuente($sql);

                }    
                ei_arbol($sql);
                
                
                
            }
	}*/

}

?>