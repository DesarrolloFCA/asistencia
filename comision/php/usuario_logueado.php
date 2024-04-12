<?php
class usuario_logueado
{
    public function get_legajo($usuario){
       $usuario = toba::usuario()-> get_id();
	   	$sql = "SELECT a.legajo legajo,apellido,nombre from reloj.agentes_mail a
			inner join reloj.agentes b on a.legajo = b.legajo
				WHERE a.email = '$usuario' ";
		$leg_usu = toba::db('comision')->consultar($sql);
		return $leg_usu;
    }
}
?>