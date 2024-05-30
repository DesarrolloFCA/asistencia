<?php
/**
 * Tipo de p�gina pensado para pantallas de login, presenta un logo y un pie de p�gina b�sico
 *
 * @package SalidaGrafica
 */
class tp_login extends toba_tp_basico
{
               
     
        function inicio_barra_superior()
             {
                echo "<div class='login-titulo'>". toba_recurso::imagen_proyecto("logo_grande.gif",true);
                echo "</div>";
                //echo "<div align='center' class='cuerpo' style='margin-top:30px'>";
        }

        function pre_contenido()
        {
                //echo "<div class='login-titulo'>". toba_recurso::imagen_proyecto("logo_login.png",true);
                //echo "</div>";
               echo "\n<div align='center' class='cuerpo' style='margin-top:30px'>\n";
        }
        
              
        function footer()
        {
                echo "</div>";
                echo "<div class='login-pie'>";
                echo "<div>Desarrollado por <strong>CAIFCA // Facultad de Ciencias Agrarias UNCuyo</strong></div>";
                //echo " <div>"date('Y')."</div>";
                echo "<div> 2024 </div>";
                echo "</div>";
              
        }
}
?>