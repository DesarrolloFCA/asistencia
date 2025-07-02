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
		$sql = "SELECT * FROM reloj.departamento_director
		where legajo_dir = $legajo";
		$director = toba::db('comision')->consultar($sql);

		if (count($jefe)> 0 or count($director)> 0) {
			return true;
		}else{
			return false;
		}

	}
	public function get_legajo_jefe($legajo){
		$legajo_sub = null;
		$sql = "SELECT id_catedra FROM reloj.catedras_agentes
		where legajo = $legajo
		and jefe = true";
		$catedra = toba::db('comision')->consultar_fila($sql);
	//	ei_arbol (!isset($catedra));
		if(isset($catedra)){
		$id_catedra = $catedra['id_catedra'];
		
		$sql = "SELECT legajo FROM reloj.vw_agente_catedra
		where id_catedra = $id_catedra";
		$legajo_sub = toba::db('comision')->consultar($sql);
		return $legajo_sub;
		}
		else {
			return $legajo_sub;
		}
	}
	public function get_legajo_dir($legajo ) {
		$sql = "SELECT legajo , departamento FROM reloj.vw_directores
			where legajo_dir = $legajo";
		$legajo_sub = toba::db('comision')->consultar($sql);
		return $legajo_sub;
	}
}
?>