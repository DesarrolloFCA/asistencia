<?php
include_once 'datos/consultas_agentes.php';
class ci_busquedad_de_agentes extends ctrl_asis_ci {

    protected $s__filtro;
    //-----------------------------------------------------------------------------------
    //---- filtro -----------------------------------------------------------------------
    //-----------------------------------------------------------------------------------

    function conf__filtro(ctrl_asis_ei_formulario $form)
    {
        if(isset($this->s__filtro)) return $this->s__filtro;
    }

    function evt__filtro__filtrar($datos)
    {
        $this->s__filtro = $datos;
    }

    function evt__filtro__cancelar()
    {
        unset($this->s__filtro);
    }

    //-----------------------------------------------------------------------------------
    //---- form_resultado ---------------------------------------------------------------
    //-----------------------------------------------------------------------------------

    function conf__form_resultado(ctrl_asis_ei_cuadro $cuadro)
    {
        $cuadro->desactivar_modo_clave_segura();
        if(isset($this->s__filtro))
        {
            return consultas_agentes::get_agentes($this->s__filtro);
        }
    }

    function evt__form_resultado__seleccion($seleccion)
    {
        return $seleccion;
    }

}

?>