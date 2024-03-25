<?php
class dt_agrupacion_decreto extends toba_datos_tabla
{
        function get_listado($filtro=array())
        {
                $where = array();
                if (isset($filtro['codagrup'])) {
                        $where[] = "t_ad.codagrup= ".quote($filtro['codagrup']);
                }
                $sql = "SELECT
                        t_d.id_decreto,
                        t_d.descripcion
                        FROM
                        agrupacion_decreto as t_ad, decreto as t_d
                        WHERE t_ad.id_decreto = t_d.id_decreto    
                        ORDER BY t_d.descripcion ASC";
                if (count($where)>0) {
                        $sql = sql_concatenar_where($sql, $where);
                }
                return toba::db('ctrl_asis')->consultar($sql);
        }
        
        function eliminar_fila_contacto($id)
        {
                if(!empty($id)){
                        $sql="DELETE FROM agrupacion_decreto WHERE codagrup='$id';";
                        toba::db('ctrl_asis')->ejecutar($sql);
                }
        }
        
        function insertar_fila_contacto($codagrup,$decretos=array())
        {
                if(count($decretos)>0){
                        foreach ($decretos as $id_decreto ){
                                $sql="INSERT INTO agrupacion_decreto (codagrup,id_decreto) 
                                              VALUES ('$codagrup','$id_decreto');";
                                toba::db('ctrl_asis')->ejecutar($sql);
                        }
                }
        }
        
        function eliminar_fila_decreto($id)
        {
                if(!empty($id)){
                        $sql="DELETE FROM agrupacion_decreto WHERE id_decreto='$id';";
                        toba::db('ctrl_asis')->ejecutar($sql);
                }
        }

        function insertar_fila_decreto($id_decreto,$contactos=array())
        {
                if(count($contactos)>0){
                        foreach ($contactos as $codagrup ){
                                $sql="INSERT INTO agrupacion_decreto (codagrup,id_decreto)
                                        VALUES ('$codagrup','$id_decreto');";
                                toba::db('ctrl_asis')->ejecutar($sql);
                        }
                }
        }

}
?>