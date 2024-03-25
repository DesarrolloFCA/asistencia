<?php

/**
 * Description of consultas_dependencias
 *
 * @author Ing. SW. Sebastián Eula
 */
class consultas_dependencias {

    function get_listado($filtro = array())
    {
        $where = array();
        if (isset($filtro['dependencia']))
        {
            $where[] = "dependencia ILIKE " . quote("%{$filtro['dependencia']}%");
        }
        if (isset($filtro['dependencia_id']))
        {
            $where[] = "dependencia_id = " . quote($filtro['dependencia_id']);
        }
        $sql = "SELECT
			t_cd.dependencia_id,
			t_cd.dependencia
		FROM
			conf_dependencia as t_cd
		ORDER BY dependencia";
        if (count($where) > 0)
        {
            $sql = sql_concatenar_where($sql, $where);
        }
        return toba::db('ctrl_asis')->consultar($sql);
    }
    
        function get_nombre($filtro = array())
    {
        $where = array();
        if (isset($filtro['dependencia_id']))
        {
            $where[] = "dependencia_id = " . quote($filtro['dependencia_id']);
        }
        $sql = "SELECT
			t_cd.dependencia_id,
			t_cd.dependencia
		FROM
			conf_dependencia as t_cd
		ORDER BY dependencia";
        if (count($where) > 0)
        {
            $sql = sql_concatenar_where($sql, $where);
        }
        $resp =  toba::db('ctrl_asis')->consultar($sql);
        return $resp[0]['dependencia'];
    }

}
