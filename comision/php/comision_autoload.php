<?php
/**
 * Esta clase fue y será generada automáticamente. NO EDITAR A MANO.
 * @ignore
 */
class comision_autoload 
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
		'comision_comando' => 'extension_toba/comision_comando.php',
		'comision_modelo' => 'extension_toba/comision_modelo.php',
		'comision_ci' => 'extension_toba/componentes/comision_ci.php',
		'comision_cn' => 'extension_toba/componentes/comision_cn.php',
		'comision_datos_relacion' => 'extension_toba/componentes/comision_datos_relacion.php',
		'comision_datos_tabla' => 'extension_toba/componentes/comision_datos_tabla.php',
		'comision_ei_arbol' => 'extension_toba/componentes/comision_ei_arbol.php',
		'comision_ei_archivos' => 'extension_toba/componentes/comision_ei_archivos.php',
		'comision_ei_calendario' => 'extension_toba/componentes/comision_ei_calendario.php',
		'comision_ei_codigo' => 'extension_toba/componentes/comision_ei_codigo.php',
		'comision_ei_cuadro' => 'extension_toba/componentes/comision_ei_cuadro.php',
		'comision_ei_esquema' => 'extension_toba/componentes/comision_ei_esquema.php',
		'comision_ei_filtro' => 'extension_toba/componentes/comision_ei_filtro.php',
		'comision_ei_firma' => 'extension_toba/componentes/comision_ei_firma.php',
		'comision_ei_formulario' => 'extension_toba/componentes/comision_ei_formulario.php',
		'comision_ei_formulario_ml' => 'extension_toba/componentes/comision_ei_formulario_ml.php',
		'comision_ei_grafico' => 'extension_toba/componentes/comision_ei_grafico.php',
		'comision_ei_mapa' => 'extension_toba/componentes/comision_ei_mapa.php',
		'comision_servicio_web' => 'extension_toba/componentes/comision_servicio_web.php',
	);
}
?>