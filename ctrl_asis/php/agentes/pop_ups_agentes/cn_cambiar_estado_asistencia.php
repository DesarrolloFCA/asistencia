<?php
include_once 'datos/consultas_agentes.php';
include_once 'libs/datos_fijos.php';

class cn_cambiar_estado_asistencia extends ctrl_asis_cn
{
    protected $s__agente;

    function ini()
    {
        $this->s__agente = toba::memoria()->get_dato('agente');
        $this->s__agente['agente_id'] = consultas_agentes::recuperar_id_x_legajo($this->s__agente['legajo']);
    }
    function get_agentes($f)
    {

        $resp = (consultas_agentes::get_agentes($f));
        return $resp[0];
    }
    
    function modificar_estado_asistencia($d)
    {
        
        foreach ($d as $d1)
        {
            for($i=0;$i<$d1['dias'];$i++)
            {
                
                $fecha = $this->nueva_fecha($d1['fecha'], $i);
                
                if(consultas_agentes::tiene_marca($fecha, $this->s__agente['agente_id']))//si esta la marca la actualizo 
                {
                    $sql = "UPDATE reloj.marca
                            SET estado_marca_id=".$d1['estado_marca_id'].", 
                                observaciones='".$d1['observaciones']."'
                          WHERE agente_id = " . $this->s__agente['agente_id'] . " AND fecha = '".$fecha."'";
                    
                }else //si no esta la marca la creo con el estado 
                {
                    $sql = "INSERT INTO reloj.marca(
                                    agente_id, fecha, entrada, salida, estado_marca_id, 
                                    observaciones, reloj)
                            VALUES (".$this->s__agente['agente_id'].", '".$fecha."', '00:00', '00:00', ".$d1['estado_marca_id'].", 
                                   '".$d1['observaciones']."', '000');";
                    
                }
               
                consultar_fuente($sql);
            }
            toba::notificacion()->agregar('Registros actualizados. Ud. puede cerrar esta ventana','info');
        }
    }
    
    function nueva_fecha($fecha, $d)
    {
        $nuevafecha = strtotime ( '+'.$d.' day' , strtotime ( $fecha ) ) ;
        $nuevafecha = date ( 'Y-m-j' , $nuevafecha );
        return $nuevafecha;
    }
}

?>