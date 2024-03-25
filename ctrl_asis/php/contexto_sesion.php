<?php
class contexto_sesion extends toba_sesion
{

    /*Atrapa el inicio de la sesión del usuario en la instancia (unica vez en toda la sesión)*/
    function conf__inicial($datos=null){

        $grupos_acceso = toba::usuario()->get_grupos_acceso( toba::usuario()->get_id(), toba::proyecto()->get_id() );
        $_SESSION['dependencia']     = 0;   $_SESSION['operador'] = 0; 
        $_SESSION['agente']          = 0;
        $_SESSION['admin']           = 0; 

        foreach ($grupos_acceso as $grupo){ 

            if($grupo == 'dependencia' or $grupo == 'operador')    {                
                $sql = "SELECT  t_au.parametro_c FROM desarrollo.apex_usuario as t_au WHERE t_au.usuario = '".toba::usuario()->get_id()."'";
                $usuario = toba::db('toba')->consultar_fila($sql);
                $_SESSION['dependencia']  = $usuario['parametro_c'];
                //break;  

                if($grupo == 'operador')    {                
                $_SESSION['operador']   = true;
                }
            }

            if($grupo == 'agente')    {                
                $sql = "SELECT t_au.parametro_b FROM desarrollo.apex_usuario as t_au WHERE t_au.usuario = '".toba::usuario()->get_id()."'";
                $usuario = toba::db('toba')->consultar_fila($sql);
                $_SESSION['agente']  = $usuario['parametro_b'];
                //break;  
            }

            if($grupo == 'admin' or $grupo == 'administrador')    {                
                $_SESSION['admin']  = true;
                //break;  
            }


        }
    }

    function conf__final()
    {
        unset($_SESSION['dependencia']); unset($_SESSION['operador']);
        unset($_SESSION['agente']);
        unset($_SESSION['admin']);
    }

}
?>