<?php
class ci_mi_cuenta extends ctrl_asis_ci
{
    protected $s__datos;
    protected $s__usuario;

    const clave_falsa = "xS34Io9gF2JD"; //La clave no se envia al cliente

	//-----------------------------------------------------------------------------------
	//---- Configuraciones --------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf()
	{
		$this->s__usuario = toba::usuario()->get_id();
	}

	//-----------------------------------------------------------------------------------
	//---- formulario -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__formulario(ctrl_asis_ei_formulario $form)
	{
		$datos = $this->dep('datos')->tabla('apex_usuario')->get_datos( $this->s__usuario );
        if (isset($datos)) {
                $datos['clave'] = self::clave_falsa;
        }
                        
        //---------------------------------------------------
        //Grupos
        $filtro['usuario']  = $this->s__usuario;
        $filtro['proyecto'] = 'ctrl_asis';
        $grupos = $this->dep('datos')->tabla('apex_usuario_proyecto')->get_descripciones($filtro);

        if(count($grupos)>0){
            foreach ($grupos as $k=>$g){

            	if($k==0){
            		$grupos = $g['usuario_grupo_acc'];
            	}else{
            		$grupos.= ', '.$g['usuario_grupo_acc'];
            	}
            }
        }

        $datos['grupos'] = $grupos;
        
        //----------------------------------------------------

		$form->set_datos($datos);
	}

        function evt__formulario__modificacion($datos)
        {
                //Seteo datos fijos
                $datos['proyecto'] = 'ctrl_asis';
                #$datos['usuario_grupo_acc']='empresa';
                if ($datos['clave'] == self::clave_falsa ) {
                        unset($datos['clave']);
                }
                $this->s__datos = $datos;
        }

	//-----------------------------------------------------------------------------------
	//---- FUNCIONES --------------------------------------------------------------------
	//-----------------------------------------------------------------------------------
    
    function resetear()
    {
            $this->dep('datos')->resetear();
            unset($this->s__usuario);
            unset($this->s__datos);
            $this->set_pantalla('pant_inicial');
    }
        
	//-----------------------------------------------------------------------------------
	//---- EVENTOS CI -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

    function evt__guardar()
    {
                    
        if ( $this->dep('datos')->tabla('apex_usuario')->modificar($this->s__datos) ){
                
        		toba::notificacion()->agregar("Cambios guardardos correcamtente. En tu pr&oacute;ximo acceso ser ver&aacute;n reflejados los cambios", "info");
                $this->resetear();

        }else{     

        	toba::notificacion()->agregar("Error: Falla en la modificaci&oacute;n del usuario. Contacte al administrador del sistema", "error");                 

        }
        
    }

}

?>