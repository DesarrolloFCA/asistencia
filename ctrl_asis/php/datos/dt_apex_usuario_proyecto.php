<?php
class dt_apex_usuario_proyecto extends toba_datos_tabla
{
    function get_listado()
    {
        $sql = "SELECT
                t_aup.proyecto,
                t_aup.usuario_grupo_acc,
                t_aup.usuario,
                t_aup.usuario_perfil_datos
        FROM desarrollo.apex_usuario_proyecto as t_aup
        WHERE t_aup.proyecto = 'ctrl_asis'
        ORDER BY usuario_perfil_datos";

        return toba::db('toba')->consultar($sql);
    }

    function alta($datos){
    
            ei_arbol($datos, 'Datos para insertar en apex_usuario_proyecto');
    }

    function get_descripciones($filtro=array())
    {
        $where = array();
        if (isset($filtro['usuario'])) {
                $where[] = "usuario = '".$filtro['usuario']."'";
        }

        if (isset($filtro['proyecto'])) {
                $where[] = "proyecto = '".$filtro['proyecto']."'";
        }
        
        $sql = "SELECT usuario, usuario_grupo_acc 
        FROM desarrollo.apex_usuario_proyecto
        ORDER BY usuario_grupo_acc DESC";
        
        if (count($where)>0) {
                $sql = sql_concatenar_where($sql, $where);
        }
        
        return toba::db('toba')->consultar($sql);
    }       
        
    function agregar ($datos)
    {        
        //Nota: parametro_a guarda la empresa
        $sql = "INSERT INTO desarrollo.apex_usuario_proyecto (
                        proyecto,
                        usuario_grupo_acc,
                        usuario
                )
                VALUES (
                        '".$datos['proyecto']."',
                        '".$datos['usuario_grupo_acc']."',
                        '".$datos['usuario']."'
                )";
        
        toba::db('toba')->ejecutar($sql);
        return true;
    }
        
    function modificar ($datos)
    {

        $sql = "UPDATE desarrollo.apex_usuario_proyecto 
           SET usuario_grupo_acc   = '".$datos['usuario_grupo_acc']."'
         WHERE usuario = '".$datos['usuario']."';";
                                         
        toba::db('toba')->ejecutar($sql);
        return true;
            
    }
        
    function eliminar_agregar($datos)
    {
        $usuario = $datos['usuario'];
        $grupos = $datos['grupos'];

        $this->eliminar_grupos_usuario($usuario);
        $this->insertar_grupos_usuario($usuario,$grupos);

        return true;
    }
    
    //------------
        

    private function eliminar_grupos_usuario($usuario)
    {
        if(!empty($usuario)){
            $sql="DELETE FROM desarrollo.apex_usuario_proyecto WHERE usuario='$usuario';";
            toba::db('toba')->ejecutar($sql);
        }
    }

    private function insertar_grupos_usuario($usuario,$grupos=array())
    {
        if(count($grupos)>0){
            foreach ($grupos as $usuario_grupo_acc){
                $sql = "INSERT INTO desarrollo.apex_usuario_proyecto (
                        proyecto,
                        usuario_grupo_acc,
                        usuario
                )
                VALUES (
                       'ctrl_asis',
                       '$usuario_grupo_acc',
                       '$usuario'
                )";

                toba::db('toba')->ejecutar($sql);
            }
        }
    }

}
?>