<?php

include_once 'libs/datos_fijos.php';
include_once 'datos/consultas_agentes.php';
include_once 'datos/consultas_agentes.php';

class cn_importar_marcas extends ctrl_asis_cn {

    function importar_marca()
    {
        //Importa las marcas del archivo de texto 
        for ($i = 66; $i <= 136; $i++)
        {
            //$file = fopen(datos_fijos::ruta_a_marca(), "r");
            $file = fopen('./2015/1 (' . $i . ').txt', "r");
            while (!feof($file))
            {
                $linea = split(' ', fgets($file));
//                ei_arbol($linea);
                $aux = split('/', $linea[1]);
                $linea[1] = $aux[2].'-'.$aux[1].'-'.$aux[0];
                $linea['agente_id'] = consultas_agentes::recuperar_id_x_legajo(substr($linea[0], -5));
                $linea['marca_id'] = consultas_agentes::crear_marca($linea);
                $entrada_salida = consultas_agentes::entrada_o_salida($linea, $linea['agente_id']);

                if ($linea['agente_id'] == '' || $linea['marca_id'] == '')
                {
                    //  ei_arbol($linea);
                    continue;
                }


                if ($entrada_salida == 'E')
                {
                    $sql = "UPDATE reloj.marca
                    SET agente_id='" . $linea['agente_id'] . "', fecha='" . $linea[1] . "', entrada='" . $linea[2] . "', estado_marca_id = 0, 
                    reloj='" . $linea[4] . "'
                    WHERE marca_id = " . $linea['marca_id'];
                } else
                {
                    $sql = "UPDATE reloj.marca
                    SET agente_id='" . $linea['agente_id'] . "', fecha='" . $linea[1] . "', salida='" . $linea[2] . "', estado_marca_id = ".datos_fijos::marca_present().", 
                    reloj='" . $linea[4] . "'
                    WHERE marca_id = " . $linea['marca_id'];
                }
                consultar_fuente($sql);
//                echo $sql."------><br>";
//                ei_arbol($linea);
            }
            toba::notificacion()->agregar('Marcas agregadas.', 'info');
            fclose($file);
         
        }
        //Hace una verificacion si todos los agentes en el periodo actual tiene las marcas
        //si no debe generar los ausentes
        consultas_agentes::generar_ausentes_globales(date('m/Y'));
        
        
    }

    /*  // ei_arbol($linea);
      //  return;
      if ((strlen(substr($linea[0], -5)) == 5))
      {
      $agente_id = consultas_agentes::recuperar_id_x_legajo(substr($linea[0], -5));
      var_dump($agente_id);
      if ($agente_id != false)
      {
      $entrada_salida = consultas_agentes::entrada_o_salida($linea, $agente_id);
      if($entrada_salida == 'E')
      {
      $sql = "INSERT INTO reloj.marca(
      agente_id, fecha, entrada, estado_marca_id,
      observaciones, reloj)
      VALUES ('" . $agente_id . "', '".$linea[1]."', '".$linea[2].":00', 0,
      '', '".$linea[4]."');
      ";

      }else
      {
      $sql = "INSERT INTO reloj.marca(
      agente_id, fecha, salida, estado_marca_id,
      observaciones, reloj)
      VALUES ('" . $agente_id . "', '".$linea[1]."', '".$linea[2].":00', 0,
      '', '".$linea[4]."');
      ";

      }
      consultar_fuente($sql);
      }else{

      }
      } */
}

?>