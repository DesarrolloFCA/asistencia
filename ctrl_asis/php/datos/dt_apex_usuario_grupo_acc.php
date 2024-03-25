<?php
class dt_apex_usuario_grupo_acc extends toba_datos_tabla
{
        function get_listado_proyecto()
        {

                $sql = "SELECT usuario_grupo_acc, nombre
                        FROM desarrollo.apex_usuario_grupo_acc
                        WHERE proyecto = 'ctrl_asis'
                        AND usuario_grupo_acc <> 'admin'";
                return toba::db('toba')->consultar($sql);
        }

}
?>