------------------------------------------------------------
--[396000073]--  Parte - CI - datos 
------------------------------------------------------------

------------------------------------------------------------
-- apex_objeto
------------------------------------------------------------

--- INICIO Grupo de desarrollo 396
INSERT INTO apex_objeto (proyecto, objeto, anterior, identificador, reflexivo, clase_proyecto, clase, punto_montaje, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion, posicion_botonera) VALUES (
	'ctrl_asis', --proyecto
	'396000073', --objeto
	NULL, --anterior
	NULL, --identificador
	NULL, --reflexivo
	'toba', --clase_proyecto
	'toba_datos_relacion', --clase
	'4000021', --punto_montaje
	NULL, --subclase
	NULL, --subclase_archivo
	NULL, --objeto_categoria_proyecto
	NULL, --objeto_categoria
	'Parte - CI - datos', --nombre
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
	'2015-07-29 10:29:11', --creacion
	NULL  --posicion_botonera
);
--- FIN Grupo de desarrollo 396

------------------------------------------------------------
-- apex_objeto_datos_rel
------------------------------------------------------------
INSERT INTO apex_objeto_datos_rel (proyecto, objeto, debug, clave, ap, punto_montaje, ap_clase, ap_archivo, sinc_susp_constraints, sinc_orden_automatico, sinc_lock_optimista) VALUES (
	'ctrl_asis', --proyecto
	'396000073', --objeto
	'0', --debug
	NULL, --clave
	'2', --ap
	'4000021', --punto_montaje
	NULL, --ap_clase
	NULL, --ap_archivo
	'0', --sinc_susp_constraints
	'1', --sinc_orden_automatico
	'1'  --sinc_lock_optimista
);

------------------------------------------------------------
-- apex_objeto_dependencias
------------------------------------------------------------

--- INICIO Grupo de desarrollo 396
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'ctrl_asis', --proyecto
	'396000074', --dep_id
	'396000073', --objeto_consumidor
	'396000048', --objeto_proveedor
	'parte', --identificador
	'1', --parametros_a
	'1', --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'1'  --orden
);
--- FIN Grupo de desarrollo 396

--- INICIO Grupo de desarrollo 35736730
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'ctrl_asis', --proyecto
	'35736730000003', --dep_id
	'396000073', --objeto_consumidor
	'35736730000001', --objeto_proveedor
	'parte_anio', --identificador
	'0', --parametros_a
	'1', --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	'2'  --orden
);
--- FIN Grupo de desarrollo 35736730

------------------------------------------------------------
-- apex_objeto_datos_rel_asoc
------------------------------------------------------------

--- INICIO Grupo de desarrollo 35736730
INSERT INTO apex_objeto_datos_rel_asoc (proyecto, objeto, asoc_id, identificador, padre_proyecto, padre_objeto, padre_id, padre_clave, hijo_proyecto, hijo_objeto, hijo_id, hijo_clave, cascada, orden) VALUES (
	'ctrl_asis', --proyecto
	'396000073', --objeto
	'35736730000001', --asoc_id
	NULL, --identificador
	'ctrl_asis', --padre_proyecto
	'396000048', --padre_objeto
	'parte', --padre_id
	NULL, --padre_clave
	'ctrl_asis', --hijo_proyecto
	'35736730000001', --hijo_objeto
	'parte_anio', --hijo_id
	NULL, --hijo_clave
	NULL, --cascada
	'1'  --orden
);
--- FIN Grupo de desarrollo 35736730

------------------------------------------------------------
-- apex_objeto_rel_columnas_asoc
------------------------------------------------------------
INSERT INTO apex_objeto_rel_columnas_asoc (proyecto, objeto, asoc_id, padre_objeto, padre_clave, hijo_objeto, hijo_clave) VALUES (
	'ctrl_asis', --proyecto
	'396000073', --objeto
	'35736730000001', --asoc_id
	'396000048', --padre_objeto
	'396000218', --padre_clave
	'35736730000001', --hijo_objeto
	'35736730000005'  --hijo_clave
);
