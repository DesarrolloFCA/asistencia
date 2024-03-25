<?php

include_once 'datos/consultas_agentes.php';
include_once 'datos/consultas_areas.php';
include_once 'datos/consultas_dependencias.php';

class cn_arbol_personal extends ctrl_asis_cn {
protected $s__resp;
protected $s__sub;

    function ini()
    {
        $this->s__resp = $this->datos_usuario();
        if(count($this->s__resp)<0)
        {
            toba::notificacion()->agregar('El usuario no se encuentra como responsable de ningún área', 'info');
        }else
        {
            return $this->s__resp;
        }
    }

    function actualizar($dto)
    {
        ei_arbol($dto);
        $i=0;
        $aux = $dto;
        foreach($dto as $d)
        {
            if($d['apex_ei_analisis_fila'] == 'A')//nueva fila
            {
                $sql = "UPDATE reloj.agentes
                            SET dependencia_id=".$this->s__resp['dependencia_id'].", area_id=".$this->s__resp['area_id']."
                          WHERE legajo =  " . $d['agente'];
                ei_arbol($sql);
                consultar_fuente($sql);
            }else if($d['apex_ei_analisis_fila'] == 'B')//nueva fila
            {
                $sql = "UPDATE reloj.agentes
                            SET dependencia_id=0, area_id=2
                          WHERE legajo =  " . $this->s__sub[$i]['legajo'];
                consultar_fuente($sql);
            }
            $i++;
        }
    }

    function datos_usuario()
    {
        $resp = consultas_agentes::get_nombres_x_usuario(toba::usuario()->get_id());
        $resp['dependencia_id'] = consultas_agentes::get_dep_responsable($resp['agente_id']);
        $resp['dependencia'] = consultas_dependencias::get_nombre(array('dependencia_id' => $resp['dependencia_id']));
        $resp['area_id'] = consultas_agentes::get_area_responsable($resp['agente_id']);
        $resp['area'] = consultas_areas::get_nombre(array('area_id' => $resp['area_id']));
        return $resp;
    }
    
    function subordinados($r)
    {
  
        $this->s__sub = consultas_agentes::get_subordinados($r);
      //  ei_arbol($resp);
        return $this->s__sub;
    }
    function nombres_agente($a)
    {
        return consultas_agentes::get_nombres_x_id(array('agente_id' => $a));
    }

}

?>