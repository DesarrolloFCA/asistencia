<?php
class creacion_usuario extends ctrl_asis_ci
{
	protected $s__datos;
	protected $s__datos_filtro;
    protected $s__usuario;
    const clave_falsa = "xS34Io9gF2JD"; //La clave no se envia al cliente
	//-----------------------------------------------------------------------------------
	//---- cuadro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro(ctrl_asis_ei_cuadro $cuadro)
	{
		$filtro = s__datos_filtro;
		$where = array( );
		if (isset($filtro['nombre'])) {
                        $where[] = "t_au.nombre ILIKE ".quote("%{$filtro['nombre']}%");
                }
        $sql = "SELECT t_au.usuario,nombre,email,bloqueado,forzar_cambio_pwd FROM desarrollo.apex_usuario t_au
        		INNER JOIN desarrollo.apex_usuario_proyecto b ON t_au.usuario =b.usuario
        		WHERE proyecto = 'comision'
        		AND usuario_grupo_acc  = 'personal' ";
        if (count($where)>0) {
                        $sql = sql_concatenar_where($sql, $where);
                }

        $datos = toba::db('toba')->consultar($sql);  
        $cuadro->set_datos($datos);     

	}

	function evt__cuadro__seleccion($seleccion)
	{
		$this->s__usuario = $datos['usuario'];
                $this->set_pantalla('pant_edicion');
	}

	//-----------------------------------------------------------------------------------
	//---- filtro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__filtro(ctrl_asis_ei_filtro $filtro)
	{
		if (isset($this->s__datos_filtro)) {
                        $filtro->set_datos($this->s__datos_filtro);
                }
	}

	function evt__filtro__filtrar($datos)
	{
		 if (array_no_nulo($datos)) {
                        $this->s__datos_filtro = $datos;
                } else {
                        toba::notificacion()->agregar('El filtro no posee valores');
                }
	}

	function evt__filtro__cancelar()
	{
		unset($this->s__datos_filtro);
	}

	//-----------------------------------------------------------------------------------
	//---- formulario -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__formulario(ctrl_asis_ei_formulario $form)
	{
		if (isset($this->s__usuario)) { 
		
			$usuario = $this->s__usuario;
			$sql = "SELECT t_au.usuario usuario ,nombre,email,bloqueado,forzar_cambio_pwd FROM desarrollo.apex_usuario t_au
        		INNER JOIN desarrollo.apex_usuario_proyecto b ON t_au.usuario =b.usuario
        		WHERE proyecto = 'comision'
        		AND usuario_grupo_acc  = 'personal' 
        		AND t_au.usuario = $usuario ";
        	$datos = toba::db('toba')->consultar($sql);
			$form->set_datos($datos);

        } else { //Va a agregar
        
        	$this->pantalla()->eliminar_evento('eliminar');
        }

	}

	function evt__formulario__alta($datos)
	{
	}

	function evt__formulario__baja()
	{
	}

	function evt__formulario__modificacion($datos)
	{
	}

	function evt__formulario__cancelar()
	{
	}

}
?>