<?php
/**
 * Tipo de p�gina pensado para pantallas de login, presenta un logo y un pie de p�gina b�sico
 *
 * @package SalidaGrafica
 */
class tp_login extends toba_tp_logon
{
        /*function barra_superior()
        {
                echo "
                        <style type='text/css'>
                                .cuerpo {

                                }

                                //.ei-barra-sup {
                                //background-image: -moz-linear-gradient(center top , #F30E00 50%, #E2041A 50%);
                                //}
                        </style>
                ";
                echo "<div id='barra-superior' class='barra-superior-login'>\n";
        }
        */
        function pre_contenido()
        {
                //echo "<div class='login-titulo'>". toba_recurso::imagen_proyecto("logo_login.png",true);
                //echo "</div>";
                //echo "\n<div align='center' class='cuerpo' style='margin-top:30px'>\n";
        }

        function post_contenido()
        {
                //echo "</div>";
                echo "<div class='login-pie'>";
                echo "<div>Desarrollado por <strong><a target='_blank' href='#'>CAIFCA // Facultad de Ciencias Agrarias</a> UNCuyo</strong></div>";
                echo " <div>"date('Y')."</div>";
                echo "</div>";
        }
}
?>