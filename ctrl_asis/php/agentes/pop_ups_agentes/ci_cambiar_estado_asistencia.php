<?php

class ci_cambiar_estado_asistencia extends ctrl_asis_ci {

    protected $s__agente;
    protected $s__periodo;

    function ini()
    {
        $this->s__agente = toba::memoria()->get_dato('agente');
        $this->s__agente['agente_id'] = consultas_agentes::recuperar_id_x_legajo($this->s__agente['legajo']);
        
        
    }

    //-----------------------------------------------------------------------------------
    //---- form_asistencia --------------------------------------------------------------
    //-----------------------------------------------------------------------------------

    function conf__form_asistencia(ctrl_asis_ei_formulario $form)
    {
        
        return $this->cn()->get_agentes($this->s__agente);
        
    }

    //-----------------------------------------------------------------------------------
    //---- ml_estados -------------------------------------------------------------------
    //-----------------------------------------------------------------------------------

    function conf__ml_estados(ctrl_asis_ei_formulario_ml $form_ml)
    {
        
    }

    function evt__ml_estados__modificacion($datos)
    {
        $this->cn()->modificar_estado_asistencia($datos);
    }

}

?>