<?php
class tp_referencia extends toba_tp_normal
{
        protected $titulo;
        protected $alto_cabecera = "60px";

        protected function plantillas_css()
        {
                if (isset($this->menu)) {
                        $estilo = $this->menu->plantilla_css();
                        if ($estilo != '') {
                                echo toba_recurso::link_css($estilo, 'screen', false);
                        }
                }
                //parent::plantillas_css();

                #echo toba_recurso::link_css('toba', 'screen');
                echo '
                <link media="screen" type="text/css" rel="stylesheet" href="/toba_2.7/css/toba.css?av="></link>
                <link media="screen" type="text/css" rel="stylesheet" href="/ctrl_asis/1.0/skins/cit/toba.css?av="></link>
                <link media="screen" type="text/css" rel="stylesheet" href="/ctrl_asis/1.0/css/toba.css?av="></link>
                ';

                echo toba_recurso::link_css('toba_impr', 'print');
                $ico = toba_recurso::imagen_proyecto('favicon.ico');
                echo '<link rel="icon" href="'.$ico.'" type="image/x-icon" /><link rel="shortcut icon" href="'.$ico.'" type="image/x-icon" />';                 
        }
        
        function titulo_item()
        {
                if (! isset($this->titulo)) {
                        $info['basica'] = toba::solicitud()->get_datos_item();
                        $item = new toba_item_info($info);
                        $item->cargar_rama();

                        //Se recorre la rama
                        $camino = $item->get_nombre();
                        while ($item->get_padre() != null) {
                                $item = $item->get_padre();
                                if (! $item->es_raiz()) {
                                        $camino = '<span style="font-weight:normal;">'.$item->get_nombre().' > </span>'.$camino;
                                }
                        }
                        $this->titulo = $camino;
                }
                return $this->titulo;
        }


        protected function info_usuario()
        {
                echo '<div class="enc-usuario">';
                $grupo_acceso = toba::usuario()->get_grupos_acceso();

                foreach($grupo_acceso as $ga){

                        switch ($ga){
                        case "admin":
                        $perfil = "<br>Administrador Toba";
                        break;
                        case  "administrador":
                        $perfil = "<br>Administrador Sistema";
                        break;
                        case  "operador":
                        $perfil = "<br>Operador";
                        break;
                        case  "dependencia":
                        $perfil = "<br>Operador Dependencia";
                        break;
                        case  "agente":
                        $perfil = "<br>Agente";
                        break;
                        default:
                        $perfil = "";
                        }
                        
                        break;
                }
                echo "<span class='enc-usuario-nom'>".texto_plano(toba::usuario()->get_nombre())."$perfil</span>";
                echo '</div>';
        }

        protected function mostrar_logo()
        {
                echo toba_recurso::imagen_proyecto('logo.png', true);
        }
}
?>