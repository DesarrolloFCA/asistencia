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
	public function get_agentes ($legajo){
		$sql = "SELECT apellido||', '|| nombre as descripcion from reloj.agentes
		WHERE legajo = $legajo";
		return toba::db('comision')->consultar($sql);
	}
	public function get_jefe ($legajo){
		$sql = "SELECT * FROM reloj.catedras_agentes
		where legajo = $legajo
		and jefe = true";
		$jefe = toba::db('comision')->consultar($sql);
		if (count($jefe)> 0) {
			return true;
		}else{
			return false;
		}

	}
}
?>