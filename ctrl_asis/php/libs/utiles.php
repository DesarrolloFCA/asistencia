<?php
class utiles {
    
    function dias_desde_fecha($f, $d)
    {
        $nuevafecha = strtotime ( '+'.$d.' day' , strtotime ( $f ) ) ;
        $nuevafecha = date ( 'Y-m-j' , $nuevafecha );
        return $nuevafecha;
    }
    
    
}
