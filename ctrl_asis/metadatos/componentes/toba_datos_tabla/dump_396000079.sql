------------------------------------------------------------
--[396000079]--  DT - apex_usuario_proyecto 
------------------------------------------------------------

------------------------------------------------------------
-- apex_objeto
------------------------------------------------------------

--- INICIO Grupo de desarrollo 396
INSERT INTO apex_objeto (proyecto, objeto, anterior, identificador, reflexivo, clase_proyecto, clase, punto_montaje, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion, posicion_botonera) VALUES (
	'ctrl_asis', --proyecto
	'396000079', --objeto
	NULL, --anterior
	NULL, --identificador
	NULL, --reflexivo
	'toba', --clase_proyecto
	'toba_datos_tabla', --clase
	'4000021', --punto_montaje
	'dt_apex_usuario_proyecto', --subclase
	'datos/dt_apex_usuario_proyecto.php', --subclase_archivo
	NULL, --objeto_categoria_proyecto
	NULL, --objeto_categoria
	'DT - apex_usuario_proyecto', --nombre
	NULL, --titulo
	NULL, --colapsable
	NULL, --descripcion
	'ctrl_asis', --fuente_datos_proyecto
	'toba', --fuente_datos
	NULL, --solicitud_registrar
	NULL, --solicitud_obj_obs_tipo
	NULL, --solicitud_obj_observacion
	NULL, --parametro_a
	NULL, --parametro_b
	NULL, --parametro_c
	NULL, --parametro_d
	NULL, --parametro_e
	NULL, --parametro_f
	NULL, --usuario
	'2015-07-29 11:09:16', --creacion
	NULL  --posicion_botonera
);
--- FIN Grupo de desarrollo 396

------------------------------------------------------------
-- apex_objeto_db_registros
------------------------------------------------------------
INSERT INTO apex_objeto_db_registros (objeto_proyecto, objeto, max_registros, min_registros, punto_montaje, ap, ap_clase, ap_archivo, tabla, tabla_ext, alias, modificar_claves, fuente_datos_proyecto, fuente_datos, permite_actualizacion_automatica, esquema, esquema_ext) VALUES (
	'ctrl_asis', --objeto_proyecto
	'396000079', --objeto
	NULL, --max_registros
	NULL, --min_registros
	'4000021', --punto_montaje
	'1', --ap
	NULL, --ap_clase
	NULL, --ap_archivo
	'apex_usuario_proyecto', --tabla
	NULL, --tabla_ext
	NULL, --alias
	'0', --modificar_claves
	'ctrl_asis', --fuente_datos_proyecto
	'toba', --fuente_datos
	'1', --permite_actualizacion_automatica
	'desarrollo', --esquema
	'desarrollo'  --esquema_ext
);

------------------------------------------------------------
-- apex_objeto_db_registros_col
------------------------------------------------------------

--- INICIO Grupo de desarrollo 396
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'ctrl_asis', --objeto_proyecto
	'396000079', --objeto
	'396000280', --col_id
	'proyecto', --columna
	'C', --tipo
	'1', --pk
	'', --secuencia
	'15', --largo
	NULL, --no_nulo
	'1', --no_nulo_db
	NULL, --externa
	'apex_usuario_proyecto'  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'ctrl_asis', --objeto_proyecto
	'396000079', --objeto
	'396000281', --col_id
	'usuario_grupo_acc', --columna
	'C', --tipo
	'1', --pk
	'', --secuencia
	'30', --largo
	NULL, --no_nulo
	'1', --no_nulo_db
	NULL, --externa
	'apex_usuario_proyecto'  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'ctrl_asis', --objeto_proyecto
	'396000079', --objeto
	'396000282', --col_id
	'usuario', --columna
	'C', --tipo
	'1', --pk
	'', --secuencia
	'60', --largo
	NULL, --no_nulo
	'1', --no_nulo_db
	NULL, --externa
	'apex_usuario_proyecto'  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'ctrl_asis', --objeto_proyecto
	'396000079', --objeto
	'396000283', --col_id
	'usuario_perfil_datos', --columna
	'X', --tipo
	'0', --pk
	'', --secuencia
	NULL, --largo
	NULL, --no_nulo
	'0', --no_nulo_db
	NULL, --externa
	'apex_usuario_proyecto'  --tabla
);
--- FIN Grupo de desarrollo 396
