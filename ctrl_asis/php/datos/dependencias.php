<?php
class dependencias {
    function get_dependencias()
    {
        $sql = "SELECT
                uncu.dependencia.cod_depcia,
                uncu.dependencia.desc_depcia
                FROM
                uncu.dependencia
                ORDER BY uncu.dependencia.desc_depcia ASC";
        return toba::db('mapuche')->consultar($sql);
    }
}
