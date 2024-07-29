------------------------------------------------------------
--[26960396000045]--  info_complementaria 
------------------------------------------------------------

------------------------------------------------------------
-- apex_objeto
------------------------------------------------------------

--- INICIO Grupo de desarrollo 26960396
INSERT INTO apex_objeto (proyecto, objeto, anterior, identificador, reflexivo, clase_proyecto, clase, punto_montaje, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion, posicion_botonera) VALUES (
	'ctrl_asis', --proyecto
	'26960396000045', --objeto
	NULL, --anterior
	NULL, --identificador
	NULL, --reflexivo
	'toba', --clase_proyecto
	'toba_datos_tabla', --clase
	'4000021', --punto_montaje
	'dt_info_complementaria', --subclase
	'datos/dt_info_complementaria.php', --subclase_archivo
	NULL, --objeto_categoria_proyecto
	NULL, --objeto_categoria
	'info_complementaria', --nombre
	NULL, --titulo
	NULL, --colapsable
	NULL, --descripcion
	'ctrl_asis', --fuente_datos_proyecto
	'ctrl_asis', --fuente_datos
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
	'2017-05-22 10:12:38', --creacion
	NULL  --posicion_botonera
);
--- FIN Grupo de desarrollo 26960396

------------------------------------------------------------
-- apex_objeto_db_registros
------------------------------------------------------------
INSERT INTO apex_objeto_db_registros (objeto_proyecto, objeto, max_registros, min_registros, punto_montaje, ap, ap_clase, ap_archivo, tabla, tabla_ext, alias, modificar_claves, fuente_datos_proyecto, fuente_datos, permite_actualizacion_automatica, esquema, esquema_ext) VALUES (
	'ctrl_asis', --objeto_proyecto
	'26960396000045', --objeto
	NULL, --max_registros
	NULL, --min_registros
	'4000021', --punto_montaje
	'1', --ap
	NULL, --ap_clase
	NULL, --ap_archivo
	'info_complementaria', --tabla
	NULL, --tabla_ext
	NULL, --alias
	'0', --modificar_claves
	'ctrl_asis', --fuente_datos_proyecto
	'ctrl_asis', --fuente_datos
	'0', --permite_actualizacion_automatica
	NULL, --esquema
	'reloj'  --esquema_ext
);

------------------------------------------------------------
-- apex_objeto_db_registros_col
------------------------------------------------------------

--- INICIO Grupo de desarrollo 26960396
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'ctrl_asis', --objeto_proyecto
	'26960396000045', --objeto
	'26960396000008', --col_id
	'id_info_complementaria', --columna
	'E', --tipo
	'1', --pk
	'info_complementaria_id_info_complementaria_seq', --secuencia
	NULL, --largo
	NULL, --no_nulo
	'1', --no_nulo_db
	'0', --externa
	NULL  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'ctrl_asis', --objeto_proyecto
	'26960396000045', --objeto
	'26960396000016', --col_id
	'agrupamiento', --columna
	'C', --tipo
	'0', --pk
	'', --secuencia
	'4', --largo
	NULL, --no_nulo
	'1', --no_nulo_db
	'0', --externa
	'info_complementaria'  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'ctrl_asis', --objeto_proyecto
	'26960396000045', --objeto
	'26960396000017', --col_id
	'fecha_alta', --columna
	'T', --tipo
	'0', --pk
	'', --secuencia
	NULL, --largo
	NULL, --no_nulo
	'1', --no_nulo_db
	'0', --externa
	'info_complementaria'  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'ctrl_asis', --objeto_proyecto
	'26960396000045', --objeto
	'26960396000018', --col_id
	'usuario_alta', --columna
	'C', --tipo
	'0', --pk
	'', --secuencia
	'60', --largo
	NULL, --no_nulo
	'1', --no_nulo_db
	'0', --externa
	'info_complementaria'  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'ctrl_asis', --objeto_proyecto
	'26960396000045', --objeto
	'26960396000019', --col_id
	'legajo', --columna
	'E', --tipo
	'0', --pk
	'', --secuencia
	NULL, --largo
	NULL, --no_nulo
	'1', --no_nulo_db
	'0', --externa
	'info_complementaria'  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'ctrl_asis', --objeto_proyecto
	'26960396000045', --objeto
	'26960396000020', --col_id
	'entrada', --columna
	'T', --tipo
	'0', --pk
	'', --secuencia
	NULL, --largo
	NULL, --no_nulo
	'1', --no_nulo_db
	'0', --externa
	'info_complementaria'  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'ctrl_asis', --objeto_proyecto
	'26960396000045', --objeto
	'26960396000021', --col_id
	'salida', --columna
	'T', --tipo
	'0', --pk
	'', --secuencia
	NULL, --largo
	NULL, --no_nulo
	'1', --no_nulo_db
	'0', --externa
	'info_complementaria'  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'ctrl_asis', --objeto_proyecto
	'26960396000045', --objeto
	'26960396000022', --col_id
	'observaciones', --columna
	'C', --tipo
	'0', --pk
	'', --secuencia
	'250', --largo
	NULL, --no_nulo
	'0', --no_nulo_db
	'0', --externa
	'info_complementaria'  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'ctrl_asis', --objeto_proyecto
	'26960396000045', --objeto
	'26960396000023', --col_id
	'cod_depcia', --columna
	'C', --tipo
	'0', --pk
	'', --secuencia
	'2', --largo
	NULL, --no_nulo
	'1', --no_nulo_db
	'0', --externa
	'info_complementaria'  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'ctrl_asis', --objeto_proyecto
	'26960396000045', --objeto
	'26960396000024', --col_id
	'apellido', --columna
	'C', --tipo
	'0', --pk
	'', --secuencia
	'50', --largo
	NULL, --no_nulo
	'0', --no_nulo_db
	'0', --externa
	'info_complementaria'  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'ctrl_asis', --objeto_proyecto
	'26960396000045', --objeto
	'26960396000025', --col_id
	'nombre', --columna
	'C', --tipo
	'0', --pk
	'', --secuencia
	'50', --largo
	NULL, --no_nulo
	'0', --no_nulo_db
	'0', --externa
	'info_complementaria'  --tabla
);
--- FIN Grupo de desarrollo 26960396
