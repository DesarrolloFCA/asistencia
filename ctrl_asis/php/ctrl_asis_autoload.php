<?php
/**
 * Esta clase fue y será generada automáticamente. NO EDITAR A MANO.
 * @ignore
 */
class ctrl_asis_autoload 
{
	static function existe_clase($nombre)
	{
		return isset(self::$clases[$nombre]);
	}

	static function cargar($nombre)
	{
		if (self::existe_clase($nombre)) { 
			 require_once(dirname(__FILE__) .'/'. self::$clases[$nombre]); 
		}
	}

	static protected $clases = array(
		'ctrl_asis_ci' => 'extension_toba/componentes/ctrl_asis_ci.php',
		'ctrl_asis_cn' => 'extension_toba/componentes/ctrl_asis_cn.php',
		'ctrl_asis_datos_relacion' => 'extension_toba/componentes/ctrl_asis_datos_relacion.php',
		'ctrl_asis_datos_tabla' => 'extension_toba/componentes/ctrl_asis_datos_tabla.php',
		'ctrl_asis_ei_arbol' => 'extension_toba/componentes/ctrl_asis_ei_arbol.php',
		'ctrl_asis_ei_archivos' => 'extension_toba/componentes/ctrl_asis_ei_archivos.php',
		'ctrl_asis_ei_calendario' => 'extension_toba/componentes/ctrl_asis_ei_calendario.php',
		'ctrl_asis_ei_codigo' => 'extension_toba/componentes/ctrl_asis_ei_codigo.php',
		'ctrl_asis_ei_cuadro' => 'extension_toba/componentes/ctrl_asis_ei_cuadro.php',
		'ctrl_asis_ei_esquema' => 'extension_toba/componentes/ctrl_asis_ei_esquema.php',
		'ctrl_asis_ei_filtro' => 'extension_toba/componentes/ctrl_asis_ei_filtro.php',
		'ctrl_asis_ei_firma' => 'extension_toba/componentes/ctrl_asis_ei_firma.php',
		'ctrl_asis_ei_formulario' => 'extension_toba/componentes/ctrl_asis_ei_formulario.php',
		'ctrl_asis_ei_formulario_ml' => 'extension_toba/componentes/ctrl_asis_ei_formulario_ml.php',
		'ctrl_asis_ei_grafico' => 'extension_toba/componentes/ctrl_asis_ei_grafico.php',
		'ctrl_asis_ei_mapa' => 'extension_toba/componentes/ctrl_asis_ei_mapa.php',
		'ctrl_asis_servicio_web' => 'extension_toba/componentes/ctrl_asis_servicio_web.php',
		'ctrl_asis_comando' => 'extension_toba/ctrl_asis_comando.php',
		'ctrl_asis_modelo' => 'extension_toba/ctrl_asis_modelo.php',
	);
}
?>