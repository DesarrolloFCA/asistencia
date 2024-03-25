<?php
include_once 'datos/consultas_agentes.php';
class ci_arbol_personal extends ctrl_asis_ci
{
    protected $s__resp;//responsable
    protected $s__cmpl;//responsable
	function ini()
	{
            $this->s__resp = $this->cn()->ini();
            
	}
        //-----------------------------------------------------------------------------------
	//---- form_agente_resp -------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_agente_resp(ctrl_asis_ei_formulario $form)
	{
            return $this->s__resp;
	}	

	//-----------------------------------------------------------------------------------
	//---- ml_agentes -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__ml_agentes__modificacion($datos)
	{
            $this->cn()->actualizar($datos);
	}

	function conf__ml_agentes(ctrl_asis_ei_formulario_ml $form_ml)
	{

            return $this->cn()->subordinados($this->s__resp);
	}
        
        function get_nombres_agente($d)
        {
            
            $resp =  $this->cn()->nombres_agente($d);
            if(strlen($resp)< 4)
                ei_arbol($d);
            return $resp;
            
            }

}
?>