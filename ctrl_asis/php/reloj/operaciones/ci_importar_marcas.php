<?php

class ci_importar_marcas extends ctrl_asis_ci {

    //-----------------------------------------------------------------------------------
    //---- form_importar ----------------------------------------------------------------
    //-----------------------------------------------------------------------------------

    function conf__form_importar(ctrl_asis_ei_formulario $form)
    {
        
    }

    function evt__form_importar__importar($datos)
    {
        return $this->cn()->importar_marca($datos);
    }

}

?>