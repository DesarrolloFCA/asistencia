<?php
class dt_agentes extends comision_datos_tabla
{
    function get_agentes_sub ($legajo) {
		//ei_arbol($legajo);
        $sql = "SELECT apellido||', '|| nombre as descripcion from reloj.agentes
		WHERE legajo = $legajo";
		return toba::db('comision')->consultar($sql);
	}
	static function lista_agente()
	{
	//	include("usuario_logueado.php");
	//	$legajo_1 = usuario_logueado::get_legajo(toba::usuario()->get_id());
	//	$legajo = $legajo_1[0]['legajo'];
	 $usuario = toba::usuario()-> get_id();
	   	$sql = "SELECT a.legajo legajo,apellido,nombre from reloj.agentes_mail a
			inner join reloj.agentes b on a.legajo = b.legajo
				WHERE a.email = '$usuario' ";
		$legajo_1 =toba::db('comision')->consultar_fila($sql);	
		$legajo = $legajo_1['legajo'];
	//	ei_arbol($legajo);
		$sql = "SELECT  legajo, legajo||' - '||apellido||', '||nombre as descripcion 
		from reloj.agentes
		WHERE legajo in (SELECT distinct legajo from reloj.catedras_agentes
						Where id_catedra in (SELECT id_catedra from reloj.catedras_agentes
											where legajo = $legajo)
						)
		or legajo in (SELECT distinct legajo from reloj.vw_directores
					where legajo_dir = $legajo)
		order by apellido, nombre";
	//	ei_arbol(toba::db('comision')->consultar($sql));
		return toba::db('comision')->consultar($sql);

	}
}

?>