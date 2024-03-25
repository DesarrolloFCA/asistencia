<?php
class ci_cargar_datos extends ctrl_asis_ci
{
    function evt__formulario__generar()
	{

        $this->execute_sql();


	}

    function generar_parte_anio_viejos(){
        $filtro = array();
        $fecha_desde = '2016-12-25';
        $anio_vacaciones = 2016;
        $motivo = 35;
        $filtro['fecha_desde'] = $fecha_desde;
        $filtro['id_motivo'] = 35;
        $this->execute_sql();
        $partes = toba::tabla('parte')->get_listado($filtro);
        foreach ($partes as $parte){
            toba::tabla('parte_anio')->generar_parte_anio($parte["id_parte"], $anio_vacaciones);   
        }  
    }

    function execute_sql(){
        $sql = "SELECT DISTINCT t_l.legajo
        FROM uncu.legajo as t_l
        ORDER BY t_l.legajo";
        $legajos = toba::db('mapuche')->consultar($sql); 
        $anios = [2010,2011,2012,2013,2014,2015];
        foreach ($legajos as $legajo){
            $cargos = $this->dep('mapuche')->get_dependencias_por_legajo_sin_repetir_cargos($legajo['legajo']);
            foreach ($cargos as $cargo){
                foreach($anios as $anio){
                    toba::tabla('vacaciones_restantes')->generar_vacaciones_restantes($cargo["legajo"], $cargo["cod_depcia"], $cargo["agrupamiento"], $anio, 0);
                }
            }  
        }

    }
}

?>