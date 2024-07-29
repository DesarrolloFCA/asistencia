------------------------------------------------------------
--[26960396000004]--  Vistas Hander 
------------------------------------------------------------

------------------------------------------------------------
-- apex_item
------------------------------------------------------------

--- INICIO Grupo de desarrollo 26960396
INSERT INTO apex_item (item_id, proyecto, item, padre_id, padre_proyecto, padre, carpeta, nivel_acceso, solicitud_tipo, pagina_tipo_proyecto, pagina_tipo, actividad_buffer_proyecto, actividad_buffer, actividad_patron_proyecto, actividad_patron, nombre, descripcion, punto_montaje, actividad_accion, menu, orden, solicitud_registrar, solicitud_obs_tipo_proyecto, solicitud_obs_tipo, solicitud_observacion, solicitud_registrar_cron, prueba_directorios, zona_proyecto, zona, zona_orden, zona_listar, imagen_recurso_origen, imagen, parametro_a, parametro_b, parametro_c, publico, redirecciona, usuario, exportable, creacion, retrasar_headers) VALUES (
	NULL, --item_id
	'ctrl_asis', --proyecto
	'26960396000004', --item
	NULL, --padre_id
	'ctrl_asis', --padre_proyecto
	'396000009', --padre
	'0', --carpeta
	'0', --nivel_acceso
	'web', --solicitud_tipo
	'ctrl_asis', --pagina_tipo_proyecto
	'tp_referencia', --pagina_tipo
	NULL, --actividad_buffer_proyecto
	NULL, --actividad_buffer
	NULL, --actividad_patron_proyecto
	NULL, --actividad_patron
	'Vistas Hander', --nombre
	NULL, --descripcion
	'4000021', --punto_montaje
	NULL, --actividad_accion
	'1', --menu
	'3', --orden
	'0', --solicitud_registrar
	NULL, --solicitud_obs_tipo_proyecto
	NULL, --solicitud_obs_tipo
	NULL, --solicitud_observacion
	NULL, --solicitud_registrar_cron
	NULL, --prueba_directorios
	NULL, --zona_proyecto
	NULL, --zona
	NULL, --zona_orden
	'0', --zona_listar
	'apex', --imagen_recurso_origen
	NULL, --imagen
	NULL, --parametro_a
	NULL, --parametro_b
	NULL, --parametro_c
	'0', --publico
	NULL, --redirecciona
	NULL, --usuario
	'0', --exportable
	'2015-09-02 09:26:25', --creacion
	'0'  --retrasar_headers
);
--- FIN Grupo de desarrollo 26960396

------------------------------------------------------------
-- apex_item_objeto
------------------------------------------------------------
INSERT INTO apex_item_objeto (item_id, proyecto, item, objeto, orden, inicializar) VALUES (
	NULL, --item_id
	'ctrl_asis', --proyecto
	'26960396000004', --item
	'26960396000009', --objeto
	'0', --orden
	NULL  --inicializar
);
