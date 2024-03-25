<?php

class cod_error {
    //put your code here
    
    function error_mensaje($e)
    {
        switch($e)
        {
            case 'coderr1': return 'No hay disponibilida de días para el periodo.';
        }
    }
}
