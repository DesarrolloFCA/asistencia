<?php
class ci_usuarios extends toba_ci
{
        protected $s__datos_filtro;
        protected $s__usuario;
        protected $s__datos;
        const clave_falsa = "xS34Io9gF2JD"; //La clave no se envia al cliente


        //-----------------------------------------------------------------------------------
        //---- Configuraciones --------------------------------------------------------------
        //-----------------------------------------------------------------------------------
        
        //---- Filtro -----------------------------------------------------------------------
        
        function conf__filtro(toba_ei_formulario $filtro)
        {
                if (isset($this->s__datos_filtro)) {
                        $filtro->set_datos($this->s__datos_filtro);
                }
        }
        
        function evt__filtro__filtrar($datos)
        {
                if (array_no_nulo($datos)) {
                        $this->s__datos_filtro = $datos;
                } else {
                        toba::notificacion()->agregar('El filtro no posee valores');
                }
        }
        
        function evt__filtro__cancelar()
        {
                unset($this->s__datos_filtro);
        }
        
        //---- Cuadro -----------------------------------------------------------------------
        
        function conf__cuadro(toba_ei_cuadro $cuadro)
        {
                if (isset($this->s__datos_filtro)) { 
                        $datos = $this->dep('datos')->tabla('apex_usuario')->get_listado($this->s__datos_filtro);
                }else{
                        $datos = $this->dep('datos')->tabla('apex_usuario')->get_listado();
                }

                $cuadro->set_datos($datos);
        }
        
        function evt__cuadro__seleccion($datos)
        {
                $this->s__usuario = $datos['usuario'];
                $this->set_pantalla('pant_edicion');
        }
        
        //---- Formulario -------------------------------------------------------------------
        
        function conf__formulario(toba_ei_formulario $form)
        {
                if (isset($this->s__usuario)) { //Va a modificar
                        $datos = $this->dep('datos')->tabla('apex_usuario')->get_datos($this->s__usuario);
                        if (isset($datos)) {
                                $datos['clave'] = self::clave_falsa;
                        }
                        
                        //---------------------------------------------------
                        //Grupos
                        $filtro['usuario']= $this->s__usuario;
                        $grupos = $this->dep('datos')->tabla('apex_usuario_proyecto')->get_descripciones($filtro);

                        $array_seleccionados = array();
                        if(count($grupos)>0){
                                foreach ($grupos as $g){
                                        $array_seleccionados[] = $g['usuario_grupo_acc'];
                                }
                        }

                        $datos['grupos'] = $array_seleccionados;
                        
                        //----------------------------------------------------

                        $form->set_datos($datos);

                } else { //Va a agregar
                        $this->pantalla()->eliminar_evento('eliminar');
                }
        }
        
        function evt__formulario__modificacion($datos)
        {
                //Seteo datos fijos
                $datos['proyecto']='ctrl_asis';
                #$datos['usuario_grupo_acc']='empresa';
                if ($datos['clave'] == self::clave_falsa ) {
                        unset($datos['clave']);
                }
                $this->s__datos = $datos;
        }
        
        function resetear()
        {
                $this->dep('datos')->resetear();
                unset($this->s__datos);
                unset($this->s__usuario);
                $this->set_pantalla('pant_seleccion');
        }
        
        //---- EVENTOS CI -------------------------------------------------------------------
        
        function evt__agregar()
        {
                $this->set_pantalla('pant_edicion');
        }
        
        function evt__volver()
        {
                $this->resetear();
        }
        
        function evt__eliminar()
        {
                $this->dep('datos')->tabla('apex_usuario')->eliminar($this->s__usuario);
                $this->resetear();
        }
        
        function evt__guardar()
        {
                //Modificar
                if (isset($this->s__usuario)) {
                        
                        if ( $this->dep('datos')->tabla('apex_usuario')->modificar($this->s__datos) ){
                                if ( $this->dep('datos')->tabla('apex_usuario_proyecto')->eliminar_agregar($this->s__datos) ){
                                        $this->resetear();
                                }else{
                                        toba::notificacion()->agregar("Error: Falla en la modificación del perfil del usuario. Contacte al administrador del sistema.", "info");
                                }
                        }else{     toba::notificacion()->agregar("Error: Falla en la modificación del usuario. Contacte al administrador del sistema.", "info");                  }
                        
                        //Agregar
                } else {
                        
                        if ( $this->dep('datos')->tabla('apex_usuario')->agregar($this->s__datos) ){
                                if ( $this->dep('datos')->tabla('apex_usuario_proyecto')->eliminar_agregar($this->s__datos) ){
                                        $this->resetear();
                                }else{
                                        toba::notificacion()->agregar("Falla en el alta del perfil del usuario. Contacte al administrador del sistema.", "error");
                                }
                        }else{     toba::notificacion()->agregar("Ya existe el usuario ".$this->s__datos['usuario'].". Por favor, seleccione otro nombre de usuario.", "info");     }
                }
                

        }

}
?>