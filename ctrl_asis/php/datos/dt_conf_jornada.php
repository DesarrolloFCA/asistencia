<?php
class dt_conf_jornada extends toba_datos_tabla
{
	function get_listado($filtro=array())
	{
		$where = array();

		if (isset($filtro['legajo'])) {
			$where[] = "t_cj.legajo = ".quote($filtro['legajo']);
		}

		if (isset($filtro['estado'])) {
			if($filtro['estado'] == 'activo'){
				$where[] = "t_cj.fecha_fin IS NULL";
			}elseif($filtro['estado'] == 'inactivo'){
				$where[] = "t_cj.fecha_fin IS NOT NULL";
			}
			
		}

		if (count($filtro['jornada'])>0) {
			foreach($filtro['jornada'] as $jornada){
				
				switch ($jornada) {
					case 'normal':
						$where[] = "t_cj.normal = 1";
						break;
					case 'lunes':
						$where[] = "t_cj.lunes = 1";
						break;
					case 'martes':
						$where[] = "t_cj.martes = 1";
						break;	
					case 'miercoles':
						$where[] = "t_cj.miercoles = 1";
						break;	
					case 'jueves':
						$where[] = "t_cj.jueves = 1";
						break;	
					case 'viernes':
						$where[] = "t_cj.viernes = 1";
						break;	
					case 'sabado':
						$where[] = "t_cj.sabado = 1";
						break;
					case 'domingo':
						$where[] = "t_cj.domingo = 1";
						break;					
				}
			}
		}

		$sql = "SELECT 
			t_cj.legajo,
			t_cj.normal,
			t_cj.h1,
			t_cj.h2,
			t_cj.h3,
			t_cj.h4,
			t_cj.lunes,
			t_cj.lunesh1,
			t_cj.lunesh2,
			t_cj.lunesh3,
			t_cj.lunesh4,
			t_cj.martes,
			t_cj.martesh1,
			t_cj.martesh2,
			t_cj.martesh3,
			t_cj.martesh4,
			t_cj.miercoles,
			t_cj.miercolesh1,
			t_cj.miercolesh2,
			t_cj.miercolesh3,
			t_cj.miercolesh4,
			t_cj.jueves,
			t_cj.juevesh1,
			t_cj.juevesh2,
			t_cj.juevesh3,
			t_cj.juevesh4,
			t_cj.viernes,
			t_cj.viernesh1,
			t_cj.viernesh2,
			t_cj.viernesh3,
			t_cj.viernesh4,
			t_cj.sabado,
			t_cj.sabadoh1,
			t_cj.sabadoh2,
			t_cj.sabadoh3,
			t_cj.sabadoh4,
			t_cj.domingo,
			t_cj.domingoh1,
			t_cj.domingoh2,
			t_cj.domingoh3,
			t_cj.domingoh4,
			t_cj.fecha_ini,
			t_cj.fecha_fin,
			t_cj.id_jornada
		FROM
			conf_jornada as t_cj 
		ORDER BY  t_cj.legajo ASC, t_cj.fecha_ini DESC";

		/*ALTER TABLE reloj.conf_jornada DROP CONSTRAINT pk_conf_jornada;ALTER TABLE reloj.conf_jornada
  ADD CONSTRAINT pk_conf_jornada PRIMARY KEY(legajo, fecha_ini);
*/

		if (count($where)>0) {
			$sql = sql_concatenar_where($sql, $where);
		}

		return toba::db('ctrl_asis')->consultar($sql);
	}

	function get_jornada_agente($legajo){
		$filtro['legajo'] = $legajo;
		$filtro['estado'] = 'activo';
		$datos = $this->get_listado($filtro);
		if(count($datos)>0){
			return $datos[0]; //retorna sola la que quene fecha inicio mayor
		}else{
			return false;
		}
	}
	function get_horas_diarias($legajo){
		$sql = "Select max(fecha_ini), date_part('hours',h2-h1) horas, a.legajo from reloj.conf_jornada a
inner join  reloj.agentes_mail b on a.legajo =b.legajo 
where date_part('hours',h2-h1) <> 6
and a.legajo =" .$legajo. "
group by h1,h2, a.legajo;" ;
return toba::db('ctrl_asis')->consultar($sql);


	}

}

?>