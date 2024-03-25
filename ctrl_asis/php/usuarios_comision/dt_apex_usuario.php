<?php
class dt_apex_usuario extends toba_datos_tabla
{
        function get_listado($filtro=array())
        {
                $where = array();
                if (isset($filtro['usuario'])) {
                        $where[] = "t_au.usuario ILIKE ".quote("%{$filtro['usuario']}%");
                }
                if (isset($filtro['nombre'])) {
                        $where[] = "t_au.nombre ILIKE ".quote("%{$filtro['nombre']}%");
                }
                if (isset($filtro['parametro_a'])) {
                        $where[] = "t_au.parametro_a = '".$filtro['parametro_a']."'";
                }
                
                #$and = '';
                if (isset($filtro['usuario_grupo_acc'])) {
                        $where[] = "t_aup.usuario_grupo_acc = '".$filtro['usuario_grupo_acc']."'";
                }else{
                        $where[] = "t_aup.usuario_grupo_acc <> 'admin'";
                }

                $sql = "SELECT DISTINCT
                        t_au.usuario,
                        t_au.clave,
                        t_au.nombre,
                        t_au.email,
                        t_au.autentificacion,
                        t_au.bloqueado,
                        t_au.parametro_a,
                        t_au.parametro_b,
                        t_au.parametro_c,
                        t_au.solicitud_registrar,
                        t_au.solicitud_obs_tipo_proyecto,
                        t_au.solicitud_obs_tipo,
                        t_au.solicitud_observacion,
                        t_aut.descripcion as usuario_tipodoc_nombre,
                        t_au.pre,
                        t_au.ciu,
                        t_au.suf,
                        t_au.telefono,
                        t_au.vencimiento,
                        t_au.dias,
                        t_au.hora_entrada,
                        t_au.hora_salida,
                        t_au.ip_permitida 
                        FROM
                        desarrollo.apex_usuario as t_au
                        LEFT OUTER JOIN desarrollo.apex_usuario_tipodoc as t_aut ON (t_au.usuario_tipodoc = t_aut.usuario_tipodoc),
                        desarrollo.apex_usuario_proyecto as t_aup
                        WHERE t_aup.proyecto = 'ctrl_asis'
                        AND t_au.usuario = t_aup.usuario
                        AND t_au.usuario <> 'toba'
                        ORDER BY t_au.nombre ";

                if (count($where)>0) {
                        $sql = sql_concatenar_where($sql, $where);
                }

                $datos = toba::db('toba')->consultar($sql);
                
                if (count($datos)>0) {
                        foreach ($datos as $key=>$dato) {
                                $sql = "SELECT
                                t_aup.proyecto,
                                t_aup.usuario_grupo_acc,
                                t_aup.usuario,
                                t_aup.usuario_perfil_datos
                                FROM desarrollo.apex_usuario_proyecto as t_aup
                                WHERE t_aup.proyecto = 'ctrl_asis'
                                AND t_aup.usuario = '".$dato['usuario']."'
                                ORDER BY usuario_perfil_datos";
                                
                                $grupos = toba::db('toba')->consultar($sql);
                                
                                $primero = true;
                                if(count($grupos)>0){
                                        foreach($grupos as $grupo){
                                                if($primero){
                                                        $primero = false;
                                                        $string_grupos = $grupo['usuario_grupo_acc'];
                                                }else{
                                                        $string_grupos.= ', '.$grupo['usuario_grupo_acc'];
                                                }
                                        }
                                        $datos[$key]['grupos']   = $string_grupos;
                                }else{
                                        $datos[$key]['grupos']   = 'sin perfil';
                                }
                        }
                }
                
                return $datos;

                //---------------------------------------------------------
        }

        function get_descripciones()
        {
                $sql = "SELECT usuario, nombre, email FROM desarrollo.apex_usuario ORDER BY nombre";
                return toba::db('toba')->consultar($sql);
        }

        function get_nombre_usuario($usuario)
        {
                $sql = "SELECT usuario, nombre FROM desarrollo.apex_usuario WHERE usuario = '$usuario'";
                $dato = toba::db('toba')->consultar_fila($sql);
                if(!empty($dato['nombre'])){
                    return $dato['nombre'];
                }else{
                    return $usuario;
                }

                
        }

        function get_administradores()
        {
                $sql = "SELECT t_au.usuario, t_au.nombre,
                        t_au.email,t_au.parametro_b,t_au.parametro_c,t_au.telefono,t_au.hora_entrada,t_au.hora_salida
                        FROM desarrollo.apex_usuario as t_au,  desarrollo.apex_usuario_proyecto as t_aup
                        WHERE t_aup.proyecto = 'ctrl_asis'
                        AND t_aup.usuario_grupo_acc = 'administrador'
                        AND t_au.usuario = t_aup.usuario
                        ORDER BY t_au.nombre";
                return toba::db('toba')->consultar($sql);
        }


        function get_operadores()
        {
                $sql = "SELECT t_au.usuario, t_au.nombre,
                        t_au.email,t_au.parametro_b,t_au.parametro_c,t_au.telefono,t_au.hora_entrada,t_au.hora_salida
                        FROM desarrollo.apex_usuario as t_au,  desarrollo.apex_usuario_proyecto as t_aup
                        WHERE t_aup.proyecto = 'ctrl_asis'
                        AND t_aup.usuario_grupo_acc = 'operador'
                        AND t_au.usuario = t_aup.usuario
                        ORDER BY t_au.nombre";
                return toba::db('toba')->consultar($sql);
        }

        function get_medicos()
        {
                $sql = "SELECT t_au.usuario,  t_au.usuario as usuario_personal, t_au.nombre, t_au.email, t_au.parametro_a, t_au.parametro_b
                        FROM desarrollo.apex_usuario as t_au,  desarrollo.apex_usuario_proyecto as t_aup
                        WHERE t_aup.proyecto = 'ctrl_asis'
                        AND t_aup.usuario_grupo_acc = 'medico'
                        AND t_au.usuario = t_aup.usuario
                        ORDER BY t_au.nombre";
                return toba::db('toba')->consultar($sql);
        }

        function existe()
        {
                $sql = "SELECT count(t_au.usuario) as existe
                        FROM desarrollo.apex_usuario as t_au,  desarrollo.apex_usuario_proyecto as t_aup
                        WHERE t_aup.proyecto = 'ctrl_asis'
                        AND t_au.usuario = t_aup.usuario "; //AND t_aup.usuario_grupo_acc = 'cliente' 
                $dato = toba::db('toba')->consultar($sql);
                return $dato['existe'];
        }

        function get_datos($usuario)
        {
                $sql = "SELECT
                        t_au.usuario,
                        t_au.clave,
                        t_au.nombre,
                        t_au.email,
                        t_au.autentificacion,
                        t_au.bloqueado,
                        t_au.parametro_a,
                        t_au.parametro_b,
                        t_au.parametro_c,
                        t_au.solicitud_registrar,
                        t_au.solicitud_obs_tipo_proyecto,
                        t_au.solicitud_obs_tipo,
                        t_au.solicitud_observacion,
                        t_aut.descripcion as usuario_tipodoc_nombre,
                        t_au.pre,
                        t_au.ciu,
                        t_au.suf,
                        t_au.telefono,
                        t_au.vencimiento,
                        t_au.dias,
                        t_au.hora_entrada,
                        t_au.hora_salida,
                        t_au.ip_permitida,
                        t_aup.usuario_grupo_acc

                        FROM
                        desarrollo.apex_usuario as t_au
                        LEFT OUTER JOIN desarrollo.apex_usuario_tipodoc as t_aut ON (t_au.usuario_tipodoc = t_aut.usuario_tipodoc),
                        desarrollo.apex_usuario_proyecto as t_aup
                        WHERE t_aup.proyecto = 'ctrl_asis'
                        AND t_au.usuario = t_aup.usuario
                        AND t_au.usuario = '$usuario' ";

                return toba::db('toba')->consultar_fila($sql);
        }

        function agregar ($datos)
        {
                //verificamos que no exista otro suario con ese nombre
                $sql = "SELECT count(t_au.usuario) as cantidad FROM desarrollo.apex_usuario as t_au
                        WHERE t_au.usuario = '".$datos['usuario']."'";
                $cant = toba::db('toba')->consultar_fila($sql);
                if ($cant['cantidad'] == 0 ){

                        $algoritmo = 'sha256';
                        $clave = $this->encriptar_con_sal($datos['clave'], $algoritmo);


                        $sql = "INSERT INTO desarrollo.apex_usuario (
                                usuario,
                                clave,
                                nombre,
                                email,
                                autentificacion,
                                bloqueado,
                                telefono,
                                ip_permitida,
                                parametro_a,
                                parametro_b,
                                parametro_c
                                )
                                VALUES (
                                '".$datos['usuario']."',
                                '$clave',
                                '".$datos['nombre']."',
                                '".$datos['email']."',
                                '$algoritmo',
                                '".$datos['bloqueado']."',
                                '".$datos['telefono']."',
                                '".$datos['ip_permitida']."',
                                '".$datos['parametro_a']."',
                                '".$datos['parametro_b']."',
                                '".$datos['parametro_c']."'
                                )";

                        return toba::db('toba')->ejecutar($sql);

                }else{
                        return false;
                }

        }

        function modificar ($datos)
        {

            if (isset($datos['clave']) ) { //edita clave, por lo que la encriptamos
                    $algoritmo = 'sha256';
                    $clave = $this->encriptar_con_sal($datos['clave'], $algoritmo);

                    $sql = "UPDATE desarrollo.apex_usuario
                        SET clave = '$clave',
                        autentificacion  = '$algoritmo' ";
                    if(!empty($datos['nombre'])){ $sql .=",nombre   = '".$datos['nombre']."' "; }
                    if(!empty($datos['email'])){ $sql .=",email   = '".$datos['email']."' "; }
                    if(!empty($datos['bloqueado'])){ $sql .=",bloqueado = '".$datos['bloqueado']."' "; }
                    if(!empty($datos['telefono'])){ $sql .=",telefono = '".$datos['telefono']."' "; } 
                    if(!empty($datos['ip_permitida'])){ $sql .=",ip_permitida = '".$datos['ip_permitida']."' "; }
                    if(!empty($datos['parametro_a'])){ $sql .=",parametro_a = '".$datos['parametro_a']."' "; }
                    if(!empty($datos['parametro_b'])){ $sql .=",parametro_b = '".$datos['parametro_b']."' "; }
                    if(!empty($datos['parametro_c'])){ $sql .=",parametro_c = '".$datos['parametro_c']."' "; }
                    
                    $sql .=" WHERE usuario = '".$datos['usuario']."';";

            }else{ //no edita clave
                    $sql = "UPDATE desarrollo.apex_usuario
                        SET
                        nombre   = '".$datos['nombre']."'";
                    if(!empty($datos['email'])){ $sql .=",email   = '".$datos['email']."' "; }
                    if(!empty($datos['bloqueado'])){ $sql .=",bloqueado = '".$datos['bloqueado']."' "; }
                    if(!empty($datos['telefono'])){ $sql .=",telefono = '".$datos['telefono']."' "; } 
                    if(!empty($datos['ip_permitida'])){ $sql .=",ip_permitida = '".$datos['ip_permitida']."' "; }
                    if(!empty($datos['parametro_a'])){ $sql .=",parametro_a = '".$datos['parametro_a']."' "; }
                    if(!empty($datos['parametro_b'])){ $sql .=",parametro_b = '".$datos['parametro_b']."' "; }
                    if(!empty($datos['parametro_c'])){ $sql .=",parametro_c = '".$datos['parametro_c']."' "; }
                    
                    $sql .=" WHERE usuario = '".$datos['usuario']."';";
            }

            return toba::db('toba')->ejecutar($sql);

        }

        function actualizar_correo ($usuario, $email)
        {

                $sql = "UPDATE desarrollo.apex_usuario
                        SET  email   = '".$email."' 
                        WHERE usuario = '".$usuario."'";

                return toba::db('toba')->ejecutar($sql);

        }


        function eliminar_usuario($usuario)
        {
                $sql = "DELETE FROM  desarrollo.apex_usuario
                        WHERE usuario = '".$usuario."'
                        AND usuario <> 'toba'";
                        
                return toba::db('toba')->ejecutar($sql);

        }

        function encriptar_con_sal($clave, $metodo, $sal=null)
        {
            if ($sal === null) {
                $sal = substr(md5(uniqid(rand(), true)), 0, 10);
            } else {
                $sal = substr($sal, 0, 10);
            }
            return $sal . hash($metodo, $sal . $clave);
        }

}
?>