<?php

class datos_fijos {

    static function vac_html_header()
    {
        return '<table width="6%" border="1"><tr>';
    }

    static function vac_html_footer()
    {
        return '</tr></table>';
    }

    static function ruta_a_marca()
    {
        return "./2014/1 (2).txt";
    }

    static function periodos_meses()
    {
        $salida = array();
        for ($i = 2013; $i <= date('Y'); $i++)
        {
            for ($j = 1; $j <= 12; $j++)
            {
                $periodo = $j . "/" . $i;
                $salida[] = array('periodo' => $periodo, 'descripcion' => $periodo);
            }
        }
        return $salida;
    }

    static function periodos_anos()
    {
        $salida = array();
        for ($i = 2013; $i <= date('Y') + 1; $i++)
        {
            $periodo = $i;
            $salida[] = array('periodo' => $periodo, 'descripcion' => $periodo);
        }
        return $salida;
    }

    static function marca_present()
    {
        //id correspondiente al estado presente
        return 2;
    }

    static function primer_ultimo($f = null)
    {

        if ($f['periodo'] == null)
            return null;
        $auxf = explode('/', $f['periodo']);
        $month = $auxf[0];
        $year = $auxf[1];
        $day = date("d", mktime(0, 0, 0, $month + 1, 0, $year));

        $ultimo_dia = $year . "-" . $month . "-" . $day;
        $primer_dia = $year . "-" . $month . "-01";

        return array('primer' => $primer_dia, 'ultimo' => $ultimo_dia);
    }

}