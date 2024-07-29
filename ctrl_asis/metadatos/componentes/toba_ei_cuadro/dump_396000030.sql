------------------------------------------------------------
--[396000030]--  Control de asistencia - cuadro_resumen 
------------------------------------------------------------

------------------------------------------------------------
-- apex_objeto
------------------------------------------------------------

--- INICIO Grupo de desarrollo 396
INSERT INTO apex_objeto (proyecto, objeto, anterior, identificador, reflexivo, clase_proyecto, clase, punto_montaje, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion, posicion_botonera) VALUES (
	'ctrl_asis', --proyecto
	'396000030', --objeto
	NULL, --anterior
	NULL, --identificador
	NULL, --reflexivo
	'toba', --clase_proyecto
	'toba_ei_cuadro', --clase
	'4000021', --punto_montaje
	'extension_cuadro_multiple', --subclase
	'ci/extension_cuadro_multiple.php', --subclase_archivo
	NULL, --objeto_categoria_proyecto
	NULL, --objeto_categoria
	'Control de asistencia - cuadro_resumen', --nombre
	NULL, --titulo
	'0', --colapsable
	NULL, --descripcion
	NULL, --fuente_datos_proyecto
	NULL, --fuente_datos
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
	'2015-06-26 11:53:41', --creacion
	'abajo'  --posicion_botonera
);
--- FIN Grupo de desarrollo 396

------------------------------------------------------------
-- apex_objeto_eventos
------------------------------------------------------------

--- INICIO Grupo de desarrollo 396
INSERT INTO apex_objeto_eventos (proyecto, evento_id, objeto, identificador, etiqueta, maneja_datos, sobre_fila, confirmacion, estilo, imagen_recurso_origen, imagen, en_botonera, ayuda, orden, ci_predep, implicito, defecto, display_datos_cargados, grupo, accion, accion_imphtml_debug, accion_vinculo_carpeta, accion_vinculo_item, accion_vinculo_objeto, accion_vinculo_popup, accion_vinculo_popup_param, accion_vinculo_target, accion_vinculo_celda, accion_vinculo_servicio, es_seleccion_multiple, es_autovinculo) VALUES (
	'ctrl_asis', --proyecto
	'396000028', --evento_id
	'396000030', --objeto
	'seleccion', --identificador
	NULL, --etiqueta
	'1', --maneja_datos
	'1', --sobre_fila
	NULL, --confirmacion
	NULL, --estilo
	'apex', --imagen_recurso_origen
	'doc.gif', --imagen
	'0', --en_botonera
	NULL, --ayuda
	'1', --orden
	NULL, --ci_predep
	NULL, --implicito
	NULL, --defecto
	NULL, --display_datos_cargados
	NULL, --grupo
	'V', --accion
	NULL, --accion_imphtml_debug
	'396000011', --accion_vinculo_carpeta
	'396000012', --accion_vinculo_item
	NULL, --accion_vinculo_objeto
	'1', --accion_vinculo_popup
	'width: 750px, height: 600px, scrollbars: 1, resizable: 1', --accion_vinculo_popup_param
	NULL, --accion_vinculo_target
	'popup', --accion_vinculo_celda
	NULL, --accion_vinculo_servicio
	'0', --es_seleccion_multiple
	'0'  --es_autovinculo
);
--- FIN Grupo de desarrollo 396

--- INICIO Grupo de desarrollo 26960396
INSERT INTO apex_objeto_eventos (proyecto, evento_id, objeto, identificador, etiqueta, maneja_datos, sobre_fila, confirmacion, estilo, imagen_recurso_origen, imagen, en_botonera, ayuda, orden, ci_predep, implicito, defecto, display_datos_cargados, grupo, accion, accion_imphtml_debug, accion_vinculo_carpeta, accion_vinculo_item, accion_vinculo_objeto, accion_vinculo_popup, accion_vinculo_popup_param, accion_vinculo_target, accion_vinculo_celda, accion_vinculo_servicio, es_seleccion_multiple, es_autovinculo) VALUES (
	'ctrl_asis', --proyecto
	'26960396000001', --evento_id
	'396000030', --objeto
	'enviar', --identificador
	NULL, --etiqueta
	'1', --maneja_datos
	'1', --sobre_fila
	NULL, --confirmacion
	NULL, --estilo
	'apex', --imagen_recurso_origen
	'mail.png', --imagen
	'0', --en_botonera
	NULL, --ayuda
	'2', --orden
	NULL, --ci_predep
	NULL, --implicito
	NULL, --defecto
	NULL, --display_datos_cargados
	NULL, --grupo
	NULL, --accion
	NULL, --accion_imphtml_debug
	NULL, --accion_vinculo_carpeta
	NULL, --accion_vinculo_item
	NULL, --accion_vinculo_objeto
	NULL, --accion_vinculo_popup
	NULL, --accion_vinculo_popup_param
	NULL, --accion_vinculo_target
	NULL, --accion_vinculo_celda
	NULL, --accion_vinculo_servicio
	'0', --es_seleccion_multiple
	'0'  --es_autovinculo
);
INSERT INTO apex_objeto_eventos (proyecto, evento_id, objeto, identificador, etiqueta, maneja_datos, sobre_fila, confirmacion, estilo, imagen_recurso_origen, imagen, en_botonera, ayuda, orden, ci_predep, implicito, defecto, display_datos_cargados, grupo, accion, accion_imphtml_debug, accion_vinculo_carpeta, accion_vinculo_item, accion_vinculo_objeto, accion_vinculo_popup, accion_vinculo_popup_param, accion_vinculo_target, accion_vinculo_celda, accion_vinculo_servicio, es_seleccion_multiple, es_autovinculo) VALUES (
	'ctrl_asis', --proyecto
	'26960396000002', --evento_id
	'396000030', --objeto
	'multiple', --identificador
	'<input type=''checkbox'' onclick=''seleccion_total("multiple")'' />', --etiqueta
	'1', --maneja_datos
	'1', --sobre_fila
	NULL, --confirmacion
	NULL, --estilo
	'apex', --imagen_recurso_origen
	NULL, --imagen
	'0', --en_botonera
	NULL, --ayuda
	'3', --orden
	NULL, --ci_predep
	NULL, --implicito
	NULL, --defecto
	NULL, --display_datos_cargados
	NULL, --grupo
	NULL, --accion
	NULL, --accion_imphtml_debug
	NULL, --accion_vinculo_carpeta
	NULL, --accion_vinculo_item
	NULL, --accion_vinculo_objeto
	NULL, --accion_vinculo_popup
	NULL, --accion_vinculo_popup_param
	NULL, --accion_vinculo_target
	NULL, --accion_vinculo_celda
	NULL, --accion_vinculo_servicio
	'1', --es_seleccion_multiple
	'0'  --es_autovinculo
);
--- FIN Grupo de desarrollo 26960396

------------------------------------------------------------
-- apex_objeto_cuadro
------------------------------------------------------------
INSERT INTO apex_objeto_cuadro (objeto_cuadro_proyecto, objeto_cuadro, titulo, subtitulo, sql, columnas_clave, columna_descripcion, clave_dbr, archivos_callbacks, ancho, ordenar, paginar, tamano_pagina, tipo_paginado, mostrar_total_registros, eof_invisible, eof_customizado, siempre_con_titulo, exportar_paginado, exportar, exportar_rtf, pdf_propiedades, pdf_respetar_paginacion, asociacion_columnas, ev_seleccion, ev_eliminar, dao_nucleo_proyecto, dao_nucleo, dao_metodo, dao_parametros, desplegable, desplegable_activo, scroll, scroll_alto, cc_modo, cc_modo_anidado_colap, cc_modo_anidado_totcol, cc_modo_anidado_totcua) VALUES (
	'ctrl_asis', --objeto_cuadro_proyecto
	'396000030', --objeto_cuadro
	NULL, --titulo
	NULL, --subtitulo
	NULL, --sql
	'legajo', --columnas_clave
	NULL, --columna_descripcion
	'0', --clave_dbr
	NULL, --archivos_callbacks
	NULL, --ancho
	'1', --ordenar
	'1', --paginar
	'20', --tamano_pagina
	'P', --tipo_paginado
	'1', --mostrar_total_registros
	'0', --eof_invisible
	NULL, --eof_customizado
	'0', --siempre_con_titulo
	'0', --exportar_paginado
	'0', --exportar
	'0', --exportar_rtf
	NULL, --pdf_propiedades
	NULL, --pdf_respetar_paginacion
	NULL, --asociacion_columnas
	NULL, --ev_seleccion
	NULL, --ev_eliminar
	NULL, --dao_nucleo_proyecto
	NULL, --dao_nucleo
	NULL, --dao_metodo
	NULL, --dao_parametros
	NULL, --desplegable
	NULL, --desplegable_activo
	'0', --scroll
	NULL, --scroll_alto
	't', --cc_modo
	'0', --cc_modo_anidado_colap
	NULL, --cc_modo_anidado_totcol
	NULL  --cc_modo_anidado_totcua
);

------------------------------------------------------------
-- apex_objeto_ei_cuadro_columna
------------------------------------------------------------

--- INICIO Grupo de desarrollo 396
INSERT INTO apex_objeto_ei_cuadro_columna (objeto_cuadro_proyecto, objeto_cuadro, objeto_cuadro_col, clave, orden, titulo, estilo_titulo, estilo, ancho, formateo, vinculo_indice, no_ordenar, mostrar_xls, mostrar_pdf, pdf_propiedades, desabilitado, total, total_cc, usar_vinculo, vinculo_carpeta, vinculo_item, vinculo_popup, vinculo_popup_param, vinculo_target, vinculo_celda, vinculo_servicio, permitir_html, grupo, evento_asociado) VALUES (
	'ctrl_asis', --objeto_cuadro_proyecto
	'396000030', --objeto_cuadro
	'396000058', --objeto_cuadro_col
	'ausentes', --clave
	'19', --orden
	'Ausentes', --titulo
	'ei-cuadro-col-tit', --estilo_titulo
	'col-cen-s1', --estilo
	NULL, --ancho
	'1', --formateo
	NULL, --vinculo_indice
	'0', --no_ordenar
	NULL, --mostrar_xls
	NULL, --mostrar_pdf
	NULL, --pdf_propiedades
	NULL, --desabilitado
	'0', --total
	NULL, --total_cc
	'0', --usar_vinculo
	NULL, --vinculo_carpeta
	NULL, --vinculo_item
	NULL, --vinculo_popup
	NULL, --vinculo_popup_param
	NULL, --vinculo_target
	NULL, --vinculo_celda
	NULL, --vinculo_servicio
	'0', --permitir_html
	'Asistencia', --grupo
	NULL  --evento_asociado
);
INSERT INTO apex_objeto_ei_cuadro_columna (objeto_cuadro_proyecto, objeto_cuadro, objeto_cuadro_col, clave, orden, titulo, estilo_titulo, estilo, ancho, formateo, vinculo_indice, no_ordenar, mostrar_xls, mostrar_pdf, pdf_propiedades, desabilitado, total, total_cc, usar_vinculo, vinculo_carpeta, vinculo_item, vinculo_popup, vinculo_popup_param, vinculo_target, vinculo_celda, vinculo_servicio, permitir_html, grupo, evento_asociado) VALUES (
	'ctrl_asis', --objeto_cuadro_proyecto
	'396000030', --objeto_cuadro
	'396000060', --objeto_cuadro_col
	'injustificados', --clave
	'20', --orden
	'Injustificados', --titulo
	'ei-cuadro-col-tit', --estilo_titulo
	'col-cen-s1', --estilo
	NULL, --ancho
	'1', --formateo
	NULL, --vinculo_indice
	'0', --no_ordenar
	NULL, --mostrar_xls
	NULL, --mostrar_pdf
	NULL, --pdf_propiedades
	NULL, --desabilitado
	'0', --total
	NULL, --total_cc
	'0', --usar_vinculo
	NULL, --vinculo_carpeta
	NULL, --vinculo_item
	NULL, --vinculo_popup
	NULL, --vinculo_popup_param
	NULL, --vinculo_target
	NULL, --vinculo_celda
	NULL, --vinculo_servicio
	'0', --permitir_html
	'Ausentes', --grupo
	NULL  --evento_asociado
);
INSERT INTO apex_objeto_ei_cuadro_columna (objeto_cuadro_proyecto, objeto_cuadro, objeto_cuadro_col, clave, orden, titulo, estilo_titulo, estilo, ancho, formateo, vinculo_indice, no_ordenar, mostrar_xls, mostrar_pdf, pdf_propiedades, desabilitado, total, total_cc, usar_vinculo, vinculo_carpeta, vinculo_item, vinculo_popup, vinculo_popup_param, vinculo_target, vinculo_celda, vinculo_servicio, permitir_html, grupo, evento_asociado) VALUES (
	'ctrl_asis', --objeto_cuadro_proyecto
	'396000030', --objeto_cuadro
	'396000061', --objeto_cuadro_col
	'partes_sanidad', --clave
	'22', --orden
	'Con Parte sanidad', --titulo
	'ei-cuadro-col-tit', --estilo_titulo
	'col-cen-s1', --estilo
	NULL, --ancho
	'1', --formateo
	NULL, --vinculo_indice
	'0', --no_ordenar
	NULL, --mostrar_xls
	NULL, --mostrar_pdf
	NULL, --pdf_propiedades
	NULL, --desabilitado
	'0', --total
	NULL, --total_cc
	'0', --usar_vinculo
	NULL, --vinculo_carpeta
	NULL, --vinculo_item
	NULL, --vinculo_popup
	NULL, --vinculo_popup_param
	NULL, --vinculo_target
	NULL, --vinculo_celda
	NULL, --vinculo_servicio
	'0', --permitir_html
	'Ausentes', --grupo
	NULL  --evento_asociado
);
INSERT INTO apex_objeto_ei_cuadro_columna (objeto_cuadro_proyecto, objeto_cuadro, objeto_cuadro_col, clave, orden, titulo, estilo_titulo, estilo, ancho, formateo, vinculo_indice, no_ordenar, mostrar_xls, mostrar_pdf, pdf_propiedades, desabilitado, total, total_cc, usar_vinculo, vinculo_carpeta, vinculo_item, vinculo_popup, vinculo_popup_param, vinculo_target, vinculo_celda, vinculo_servicio, permitir_html, grupo, evento_asociado) VALUES (
	'ctrl_asis', --objeto_cuadro_proyecto
	'396000030', --objeto_cuadro
	'396000108', --objeto_cuadro_col
	'presentes', --clave
	'18', --orden
	'Presentes', --titulo
	'ei-cuadro-col-tit', --estilo_titulo
	'col-cen-s1', --estilo
	NULL, --ancho
	'1', --formateo
	NULL, --vinculo_indice
	'0', --no_ordenar
	NULL, --mostrar_xls
	NULL, --mostrar_pdf
	NULL, --pdf_propiedades
	NULL, --desabilitado
	'0', --total
	NULL, --total_cc
	'0', --usar_vinculo
	NULL, --vinculo_carpeta
	NULL, --vinculo_item
	NULL, --vinculo_popup
	NULL, --vinculo_popup_param
	NULL, --vinculo_target
	NULL, --vinculo_celda
	NULL, --vinculo_servicio
	'0', --permitir_html
	'Asistencia', --grupo
	NULL  --evento_asociado
);
INSERT INTO apex_objeto_ei_cuadro_columna (objeto_cuadro_proyecto, objeto_cuadro, objeto_cuadro_col, clave, orden, titulo, estilo_titulo, estilo, ancho, formateo, vinculo_indice, no_ordenar, mostrar_xls, mostrar_pdf, pdf_propiedades, desabilitado, total, total_cc, usar_vinculo, vinculo_carpeta, vinculo_item, vinculo_popup, vinculo_popup_param, vinculo_target, vinculo_celda, vinculo_servicio, permitir_html, grupo, evento_asociado) VALUES (
	'ctrl_asis', --objeto_cuadro_proyecto
	'396000030', --objeto_cuadro
	'396000109', --objeto_cuadro_col
	'horas_promedio', --clave
	'13', --orden
	'Promedio', --titulo
	'ei-cuadro-col-tit', --estilo_titulo
	'col-cen-s1', --estilo
	NULL, --ancho
	'1', --formateo
	NULL, --vinculo_indice
	'0', --no_ordenar
	NULL, --mostrar_xls
	NULL, --mostrar_pdf
	NULL, --pdf_propiedades
	NULL, --desabilitado
	'0', --total
	NULL, --total_cc
	'0', --usar_vinculo
	NULL, --vinculo_carpeta
	NULL, --vinculo_item
	NULL, --vinculo_popup
	NULL, --vinculo_popup_param
	NULL, --vinculo_target
	NULL, --vinculo_celda
	NULL, --vinculo_servicio
	'0', --permitir_html
	'Horas', --grupo
	NULL  --evento_asociado
);
INSERT INTO apex_objeto_ei_cuadro_columna (objeto_cuadro_proyecto, objeto_cuadro, objeto_cuadro_col, clave, orden, titulo, estilo_titulo, estilo, ancho, formateo, vinculo_indice, no_ordenar, mostrar_xls, mostrar_pdf, pdf_propiedades, desabilitado, total, total_cc, usar_vinculo, vinculo_carpeta, vinculo_item, vinculo_popup, vinculo_popup_param, vinculo_target, vinculo_celda, vinculo_servicio, permitir_html, grupo, evento_asociado) VALUES (
	'ctrl_asis', --objeto_cuadro_proyecto
	'396000030', --objeto_cuadro
	'396000111', --objeto_cuadro_col
	'laborables', --clave
	'17', --orden
	'Laborables', --titulo
	'ei-cuadro-col-tit', --estilo_titulo
	'col-cen-s1', --estilo
	NULL, --ancho
	'1', --formateo
	NULL, --vinculo_indice
	'0', --no_ordenar
	NULL, --mostrar_xls
	NULL, --mostrar_pdf
	NULL, --pdf_propiedades
	NULL, --desabilitado
	'0', --total
	NULL, --total_cc
	'0', --usar_vinculo
	NULL, --vinculo_carpeta
	NULL, --vinculo_item
	NULL, --vinculo_popup
	NULL, --vinculo_popup_param
	NULL, --vinculo_target
	NULL, --vinculo_celda
	NULL, --vinculo_servicio
	'0', --permitir_html
	'Días', --grupo
	NULL  --evento_asociado
);
INSERT INTO apex_objeto_ei_cuadro_columna (objeto_cuadro_proyecto, objeto_cuadro, objeto_cuadro_col, clave, orden, titulo, estilo_titulo, estilo, ancho, formateo, vinculo_indice, no_ordenar, mostrar_xls, mostrar_pdf, pdf_propiedades, desabilitado, total, total_cc, usar_vinculo, vinculo_carpeta, vinculo_item, vinculo_popup, vinculo_popup_param, vinculo_target, vinculo_celda, vinculo_servicio, permitir_html, grupo, evento_asociado) VALUES (
	'ctrl_asis', --objeto_cuadro_proyecto
	'396000030', --objeto_cuadro
	'396000112', --objeto_cuadro_col
	'feriados', --clave
	'16', --orden
	'Feriados', --titulo
	'ei-cuadro-col-tit', --estilo_titulo
	'col-cen-s1', --estilo
	NULL, --ancho
	'1', --formateo
	NULL, --vinculo_indice
	'0', --no_ordenar
	NULL, --mostrar_xls
	NULL, --mostrar_pdf
	NULL, --pdf_propiedades
	NULL, --desabilitado
	'0', --total
	NULL, --total_cc
	'0', --usar_vinculo
	NULL, --vinculo_carpeta
	NULL, --vinculo_item
	NULL, --vinculo_popup
	NULL, --vinculo_popup_param
	NULL, --vinculo_target
	NULL, --vinculo_celda
	NULL, --vinculo_servicio
	'0', --permitir_html
	'Días', --grupo
	NULL  --evento_asociado
);
INSERT INTO apex_objeto_ei_cuadro_columna (objeto_cuadro_proyecto, objeto_cuadro, objeto_cuadro_col, clave, orden, titulo, estilo_titulo, estilo, ancho, formateo, vinculo_indice, no_ordenar, mostrar_xls, mostrar_pdf, pdf_propiedades, desabilitado, total, total_cc, usar_vinculo, vinculo_carpeta, vinculo_item, vinculo_popup, vinculo_popup_param, vinculo_target, vinculo_celda, vinculo_servicio, permitir_html, grupo, evento_asociado) VALUES (
	'ctrl_asis', --objeto_cuadro_proyecto
	'396000030', --objeto_cuadro
	'396000150', --objeto_cuadro_col
	'legajo', --clave
	'3', --orden
	'Legajo', --titulo
	'ei-cuadro-col-tit', --estilo_titulo
	'col-cen-s1', --estilo
	NULL, --ancho
	'1', --formateo
	NULL, --vinculo_indice
	'0', --no_ordenar
	NULL, --mostrar_xls
	NULL, --mostrar_pdf
	NULL, --pdf_propiedades
	NULL, --desabilitado
	'0', --total
	NULL, --total_cc
	'0', --usar_vinculo
	NULL, --vinculo_carpeta
	NULL, --vinculo_item
	NULL, --vinculo_popup
	NULL, --vinculo_popup_param
	NULL, --vinculo_target
	NULL, --vinculo_celda
	NULL, --vinculo_servicio
	'0', --permitir_html
	'Agente', --grupo
	NULL  --evento_asociado
);
INSERT INTO apex_objeto_ei_cuadro_columna (objeto_cuadro_proyecto, objeto_cuadro, objeto_cuadro_col, clave, orden, titulo, estilo_titulo, estilo, ancho, formateo, vinculo_indice, no_ordenar, mostrar_xls, mostrar_pdf, pdf_propiedades, desabilitado, total, total_cc, usar_vinculo, vinculo_carpeta, vinculo_item, vinculo_popup, vinculo_popup_param, vinculo_target, vinculo_celda, vinculo_servicio, permitir_html, grupo, evento_asociado) VALUES (
	'ctrl_asis', --objeto_cuadro_proyecto
	'396000030', --objeto_cuadro
	'396000158', --objeto_cuadro_col
	'horas_requeridas_prom', --clave
	'11', --orden
	'Req. Mensual', --titulo
	'ei-cuadro-col-tit', --estilo_titulo
	'col-cen-s1', --estilo
	NULL, --ancho
	'1', --formateo
	NULL, --vinculo_indice
	'0', --no_ordenar
	NULL, --mostrar_xls
	NULL, --mostrar_pdf
	NULL, --pdf_propiedades
	NULL, --desabilitado
	'0', --total
	NULL, --total_cc
	'0', --usar_vinculo
	NULL, --vinculo_carpeta
	NULL, --vinculo_item
	NULL, --vinculo_popup
	NULL, --vinculo_popup_param
	NULL, --vinculo_target
	NULL, --vinculo_celda
	NULL, --vinculo_servicio
	'0', --permitir_html
	'Horas', --grupo
	NULL  --evento_asociado
);
INSERT INTO apex_objeto_ei_cuadro_columna (objeto_cuadro_proyecto, objeto_cuadro, objeto_cuadro_col, clave, orden, titulo, estilo_titulo, estilo, ancho, formateo, vinculo_indice, no_ordenar, mostrar_xls, mostrar_pdf, pdf_propiedades, desabilitado, total, total_cc, usar_vinculo, vinculo_carpeta, vinculo_item, vinculo_popup, vinculo_popup_param, vinculo_target, vinculo_celda, vinculo_servicio, permitir_html, grupo, evento_asociado) VALUES (
	'ctrl_asis', --objeto_cuadro_proyecto
	'396000030', --objeto_cuadro
	'396000159', --objeto_cuadro_col
	'horas_totales', --clave
	'12', --orden
	'Trabajadas', --titulo
	'ei-cuadro-col-tit', --estilo_titulo
	'col-cen-s1', --estilo
	NULL, --ancho
	'1', --formateo
	NULL, --vinculo_indice
	'0', --no_ordenar
	NULL, --mostrar_xls
	NULL, --mostrar_pdf
	NULL, --pdf_propiedades
	NULL, --desabilitado
	'0', --total
	NULL, --total_cc
	'0', --usar_vinculo
	NULL, --vinculo_carpeta
	NULL, --vinculo_item
	NULL, --vinculo_popup
	NULL, --vinculo_popup_param
	NULL, --vinculo_target
	NULL, --vinculo_celda
	NULL, --vinculo_servicio
	'1', --permitir_html
	'Horas', --grupo
	NULL  --evento_asociado
);
INSERT INTO apex_objeto_ei_cuadro_columna (objeto_cuadro_proyecto, objeto_cuadro, objeto_cuadro_col, clave, orden, titulo, estilo_titulo, estilo, ancho, formateo, vinculo_indice, no_ordenar, mostrar_xls, mostrar_pdf, pdf_propiedades, desabilitado, total, total_cc, usar_vinculo, vinculo_carpeta, vinculo_item, vinculo_popup, vinculo_popup_param, vinculo_target, vinculo_celda, vinculo_servicio, permitir_html, grupo, evento_asociado) VALUES (
	'ctrl_asis', --objeto_cuadro_proyecto
	'396000030', --objeto_cuadro
	'396000161', --objeto_cuadro_col
	'foto', --clave
	'1', --orden
	NULL, --titulo
	'ei-cuadro-col-tit', --estilo_titulo
	'4', --estilo
	NULL, --ancho
	'1', --formateo
	NULL, --vinculo_indice
	'0', --no_ordenar
	NULL, --mostrar_xls
	NULL, --mostrar_pdf
	NULL, --pdf_propiedades
	NULL, --desabilitado
	'0', --total
	NULL, --total_cc
	'0', --usar_vinculo
	NULL, --vinculo_carpeta
	NULL, --vinculo_item
	NULL, --vinculo_popup
	NULL, --vinculo_popup_param
	NULL, --vinculo_target
	NULL, --vinculo_celda
	NULL, --vinculo_servicio
	'1', --permitir_html
	'Agente', --grupo
	NULL  --evento_asociado
);
INSERT INTO apex_objeto_ei_cuadro_columna (objeto_cuadro_proyecto, objeto_cuadro, objeto_cuadro_col, clave, orden, titulo, estilo_titulo, estilo, ancho, formateo, vinculo_indice, no_ordenar, mostrar_xls, mostrar_pdf, pdf_propiedades, desabilitado, total, total_cc, usar_vinculo, vinculo_carpeta, vinculo_item, vinculo_popup, vinculo_popup_param, vinculo_target, vinculo_celda, vinculo_servicio, permitir_html, grupo, evento_asociado) VALUES (
	'ctrl_asis', --objeto_cuadro_proyecto
	'396000030', --objeto_cuadro
	'396000162', --objeto_cuadro_col
	'partes', --clave
	'21', --orden
	'Con Parte', --titulo
	'ei-cuadro-col-tit', --estilo_titulo
	'col-cen-s1', --estilo
	NULL, --ancho
	'1', --formateo
	NULL, --vinculo_indice
	'0', --no_ordenar
	NULL, --mostrar_xls
	NULL, --mostrar_pdf
	NULL, --pdf_propiedades
	NULL, --desabilitado
	'0', --total
	NULL, --total_cc
	'0', --usar_vinculo
	NULL, --vinculo_carpeta
	NULL, --vinculo_item
	NULL, --vinculo_popup
	NULL, --vinculo_popup_param
	NULL, --vinculo_target
	NULL, --vinculo_celda
	NULL, --vinculo_servicio
	'0', --permitir_html
	'Ausentes', --grupo
	NULL  --evento_asociado
);
INSERT INTO apex_objeto_ei_cuadro_columna (objeto_cuadro_proyecto, objeto_cuadro, objeto_cuadro_col, clave, orden, titulo, estilo_titulo, estilo, ancho, formateo, vinculo_indice, no_ordenar, mostrar_xls, mostrar_pdf, pdf_propiedades, desabilitado, total, total_cc, usar_vinculo, vinculo_carpeta, vinculo_item, vinculo_popup, vinculo_popup_param, vinculo_target, vinculo_celda, vinculo_servicio, permitir_html, grupo, evento_asociado) VALUES (
	'ctrl_asis', --objeto_cuadro_proyecto
	'396000030', --objeto_cuadro
	'396000169', --objeto_cuadro_col
	'nombre_completo', --clave
	'4', --orden
	'Nombre', --titulo
	'ei-cuadro-col-tit', --estilo_titulo
	'4', --estilo
	NULL, --ancho
	'1', --formateo
	NULL, --vinculo_indice
	'0', --no_ordenar
	NULL, --mostrar_xls
	NULL, --mostrar_pdf
	NULL, --pdf_propiedades
	NULL, --desabilitado
	'0', --total
	NULL, --total_cc
	'0', --usar_vinculo
	NULL, --vinculo_carpeta
	NULL, --vinculo_item
	NULL, --vinculo_popup
	NULL, --vinculo_popup_param
	NULL, --vinculo_target
	NULL, --vinculo_celda
	NULL, --vinculo_servicio
	'0', --permitir_html
	'Agente', --grupo
	NULL  --evento_asociado
);
INSERT INTO apex_objeto_ei_cuadro_columna (objeto_cuadro_proyecto, objeto_cuadro, objeto_cuadro_col, clave, orden, titulo, estilo_titulo, estilo, ancho, formateo, vinculo_indice, no_ordenar, mostrar_xls, mostrar_pdf, pdf_propiedades, desabilitado, total, total_cc, usar_vinculo, vinculo_carpeta, vinculo_item, vinculo_popup, vinculo_popup_param, vinculo_target, vinculo_celda, vinculo_servicio, permitir_html, grupo, evento_asociado) VALUES (
	'ctrl_asis', --objeto_cuadro_proyecto
	'396000030', --objeto_cuadro
	'396000171', --objeto_cuadro_col
	'catedra', --clave
	'6', --orden
	'Área', --titulo
	'ei-cuadro-col-tit', --estilo_titulo
	'col-cen-s1', --estilo
	NULL, --ancho
	'1', --formateo
	NULL, --vinculo_indice
	'0', --no_ordenar
	NULL, --mostrar_xls
	NULL, --mostrar_pdf
	NULL, --pdf_propiedades
	NULL, --desabilitado
	'0', --total
	NULL, --total_cc
	'0', --usar_vinculo
	NULL, --vinculo_carpeta
	NULL, --vinculo_item
	NULL, --vinculo_popup
	NULL, --vinculo_popup_param
	NULL, --vinculo_target
	NULL, --vinculo_celda
	NULL, --vinculo_servicio
	'0', --permitir_html
	'Agente', --grupo
	NULL  --evento_asociado
);
INSERT INTO apex_objeto_ei_cuadro_columna (objeto_cuadro_proyecto, objeto_cuadro, objeto_cuadro_col, clave, orden, titulo, estilo_titulo, estilo, ancho, formateo, vinculo_indice, no_ordenar, mostrar_xls, mostrar_pdf, pdf_propiedades, desabilitado, total, total_cc, usar_vinculo, vinculo_carpeta, vinculo_item, vinculo_popup, vinculo_popup_param, vinculo_target, vinculo_celda, vinculo_servicio, permitir_html, grupo, evento_asociado) VALUES (
	'ctrl_asis', --objeto_cuadro_proyecto
	'396000030', --objeto_cuadro
	'396000236', --objeto_cuadro_col
	'cod_depcia_destino', --clave
	'10', --orden
	'Adsc.', --titulo
	'ei-cuadro-col-tit', --estilo_titulo
	'col-cen-s1', --estilo
	NULL, --ancho
	'1', --formateo
	NULL, --vinculo_indice
	'0', --no_ordenar
	NULL, --mostrar_xls
	NULL, --mostrar_pdf
	NULL, --pdf_propiedades
	NULL, --desabilitado
	'0', --total
	NULL, --total_cc
	'0', --usar_vinculo
	NULL, --vinculo_carpeta
	NULL, --vinculo_item
	NULL, --vinculo_popup
	NULL, --vinculo_popup_param
	NULL, --vinculo_target
	NULL, --vinculo_celda
	NULL, --vinculo_servicio
	'0', --permitir_html
	'Agente', --grupo
	NULL  --evento_asociado
);
--- FIN Grupo de desarrollo 396

--- INICIO Grupo de desarrollo 26960396
INSERT INTO apex_objeto_ei_cuadro_columna (objeto_cuadro_proyecto, objeto_cuadro, objeto_cuadro_col, clave, orden, titulo, estilo_titulo, estilo, ancho, formateo, vinculo_indice, no_ordenar, mostrar_xls, mostrar_pdf, pdf_propiedades, desabilitado, total, total_cc, usar_vinculo, vinculo_carpeta, vinculo_item, vinculo_popup, vinculo_popup_param, vinculo_target, vinculo_celda, vinculo_servicio, permitir_html, grupo, evento_asociado) VALUES (
	'ctrl_asis', --objeto_cuadro_proyecto
	'396000030', --objeto_cuadro
	'26960396000004', --objeto_cuadro_col
	'email', --clave
	'5', --orden
	'E-mail', --titulo
	'ei-cuadro-col-tit', --estilo_titulo
	'4', --estilo
	NULL, --ancho
	'1', --formateo
	NULL, --vinculo_indice
	'0', --no_ordenar
	NULL, --mostrar_xls
	NULL, --mostrar_pdf
	NULL, --pdf_propiedades
	NULL, --desabilitado
	'0', --total
	NULL, --total_cc
	'0', --usar_vinculo
	NULL, --vinculo_carpeta
	NULL, --vinculo_item
	NULL, --vinculo_popup
	NULL, --vinculo_popup_param
	NULL, --vinculo_target
	NULL, --vinculo_celda
	NULL, --vinculo_servicio
	'0', --permitir_html
	'Agente', --grupo
	NULL  --evento_asociado
);
--- FIN Grupo de desarrollo 26960396

--- INICIO Grupo de desarrollo 35736730
INSERT INTO apex_objeto_ei_cuadro_columna (objeto_cuadro_proyecto, objeto_cuadro, objeto_cuadro_col, clave, orden, titulo, estilo_titulo, estilo, ancho, formateo, vinculo_indice, no_ordenar, mostrar_xls, mostrar_pdf, pdf_propiedades, desabilitado, total, total_cc, usar_vinculo, vinculo_carpeta, vinculo_item, vinculo_popup, vinculo_popup_param, vinculo_target, vinculo_celda, vinculo_servicio, permitir_html, grupo, evento_asociado) VALUES (
	'ctrl_asis', --objeto_cuadro_proyecto
	'396000030', --objeto_cuadro
	'35736730000194', --objeto_cuadro_col
	'caracter', --clave
	'9', --orden
	'Caracter', --titulo
	'ei-cuadro-col-tit', --estilo_titulo
	'4', --estilo
	NULL, --ancho
	'1', --formateo
	NULL, --vinculo_indice
	'0', --no_ordenar
	NULL, --mostrar_xls
	NULL, --mostrar_pdf
	NULL, --pdf_propiedades
	NULL, --desabilitado
	'0', --total
	NULL, --total_cc
	'0', --usar_vinculo
	NULL, --vinculo_carpeta
	NULL, --vinculo_item
	NULL, --vinculo_popup
	NULL, --vinculo_popup_param
	NULL, --vinculo_target
	NULL, --vinculo_celda
	NULL, --vinculo_servicio
	'0', --permitir_html
	'Agente', --grupo
	NULL  --evento_asociado
);
INSERT INTO apex_objeto_ei_cuadro_columna (objeto_cuadro_proyecto, objeto_cuadro, objeto_cuadro_col, clave, orden, titulo, estilo_titulo, estilo, ancho, formateo, vinculo_indice, no_ordenar, mostrar_xls, mostrar_pdf, pdf_propiedades, desabilitado, total, total_cc, usar_vinculo, vinculo_carpeta, vinculo_item, vinculo_popup, vinculo_popup_param, vinculo_target, vinculo_celda, vinculo_servicio, permitir_html, grupo, evento_asociado) VALUES (
	'ctrl_asis', --objeto_cuadro_proyecto
	'396000030', --objeto_cuadro
	'35736730000195', --objeto_cuadro_col
	'categoria', --clave
	'8', --orden
	'Categoría', --titulo
	'ei-cuadro-col-tit', --estilo_titulo
	'4', --estilo
	NULL, --ancho
	'1', --formateo
	NULL, --vinculo_indice
	'0', --no_ordenar
	NULL, --mostrar_xls
	NULL, --mostrar_pdf
	NULL, --pdf_propiedades
	NULL, --desabilitado
	'0', --total
	NULL, --total_cc
	'0', --usar_vinculo
	NULL, --vinculo_carpeta
	NULL, --vinculo_item
	NULL, --vinculo_popup
	NULL, --vinculo_popup_param
	NULL, --vinculo_target
	NULL, --vinculo_celda
	NULL, --vinculo_servicio
	'0', --permitir_html
	'Agente', --grupo
	NULL  --evento_asociado
);
INSERT INTO apex_objeto_ei_cuadro_columna (objeto_cuadro_proyecto, objeto_cuadro, objeto_cuadro_col, clave, orden, titulo, estilo_titulo, estilo, ancho, formateo, vinculo_indice, no_ordenar, mostrar_xls, mostrar_pdf, pdf_propiedades, desabilitado, total, total_cc, usar_vinculo, vinculo_carpeta, vinculo_item, vinculo_popup, vinculo_popup_param, vinculo_target, vinculo_celda, vinculo_servicio, permitir_html, grupo, evento_asociado) VALUES (
	'ctrl_asis', --objeto_cuadro_proyecto
	'396000030', --objeto_cuadro
	'35736730000196', --objeto_cuadro_col
	'entrada', --clave
	'14', --orden
	'Entrada', --titulo
	'ei-cuadro-col-tit', --estilo_titulo
	'4', --estilo
	NULL, --ancho
	'1', --formateo
	NULL, --vinculo_indice
	'0', --no_ordenar
	NULL, --mostrar_xls
	NULL, --mostrar_pdf
	NULL, --pdf_propiedades
	NULL, --desabilitado
	'0', --total
	NULL, --total_cc
	'0', --usar_vinculo
	NULL, --vinculo_carpeta
	NULL, --vinculo_item
	NULL, --vinculo_popup
	NULL, --vinculo_popup_param
	NULL, --vinculo_target
	NULL, --vinculo_celda
	NULL, --vinculo_servicio
	'0', --permitir_html
	'Horas', --grupo
	NULL  --evento_asociado
);
INSERT INTO apex_objeto_ei_cuadro_columna (objeto_cuadro_proyecto, objeto_cuadro, objeto_cuadro_col, clave, orden, titulo, estilo_titulo, estilo, ancho, formateo, vinculo_indice, no_ordenar, mostrar_xls, mostrar_pdf, pdf_propiedades, desabilitado, total, total_cc, usar_vinculo, vinculo_carpeta, vinculo_item, vinculo_popup, vinculo_popup_param, vinculo_target, vinculo_celda, vinculo_servicio, permitir_html, grupo, evento_asociado) VALUES (
	'ctrl_asis', --objeto_cuadro_proyecto
	'396000030', --objeto_cuadro
	'35736730000197', --objeto_cuadro_col
	'salida', --clave
	'15', --orden
	'Salida', --titulo
	'ei-cuadro-col-tit', --estilo_titulo
	'4', --estilo
	NULL, --ancho
	'1', --formateo
	NULL, --vinculo_indice
	'0', --no_ordenar
	NULL, --mostrar_xls
	NULL, --mostrar_pdf
	NULL, --pdf_propiedades
	NULL, --desabilitado
	'0', --total
	NULL, --total_cc
	'0', --usar_vinculo
	NULL, --vinculo_carpeta
	NULL, --vinculo_item
	NULL, --vinculo_popup
	NULL, --vinculo_popup_param
	NULL, --vinculo_target
	NULL, --vinculo_celda
	NULL, --vinculo_servicio
	'0', --permitir_html
	'Horas', --grupo
	NULL  --evento_asociado
);
INSERT INTO apex_objeto_ei_cuadro_columna (objeto_cuadro_proyecto, objeto_cuadro, objeto_cuadro_col, clave, orden, titulo, estilo_titulo, estilo, ancho, formateo, vinculo_indice, no_ordenar, mostrar_xls, mostrar_pdf, pdf_propiedades, desabilitado, total, total_cc, usar_vinculo, vinculo_carpeta, vinculo_item, vinculo_popup, vinculo_popup_param, vinculo_target, vinculo_celda, vinculo_servicio, permitir_html, grupo, evento_asociado) VALUES (
	'ctrl_asis', --objeto_cuadro_proyecto
	'396000030', --objeto_cuadro
	'35736730000329', --objeto_cuadro_col
	'cuil', --clave
	'2', --orden
	'Cuil', --titulo
	'ei-cuadro-col-tit', --estilo_titulo
	'4', --estilo
	NULL, --ancho
	'1', --formateo
	NULL, --vinculo_indice
	'0', --no_ordenar
	NULL, --mostrar_xls
	NULL, --mostrar_pdf
	NULL, --pdf_propiedades
	NULL, --desabilitado
	'0', --total
	NULL, --total_cc
	'0', --usar_vinculo
	NULL, --vinculo_carpeta
	NULL, --vinculo_item
	NULL, --vinculo_popup
	NULL, --vinculo_popup_param
	NULL, --vinculo_target
	NULL, --vinculo_celda
	NULL, --vinculo_servicio
	'0', --permitir_html
	'Agente', --grupo
	NULL  --evento_asociado
);
INSERT INTO apex_objeto_ei_cuadro_columna (objeto_cuadro_proyecto, objeto_cuadro, objeto_cuadro_col, clave, orden, titulo, estilo_titulo, estilo, ancho, formateo, vinculo_indice, no_ordenar, mostrar_xls, mostrar_pdf, pdf_propiedades, desabilitado, total, total_cc, usar_vinculo, vinculo_carpeta, vinculo_item, vinculo_popup, vinculo_popup_param, vinculo_target, vinculo_celda, vinculo_servicio, permitir_html, grupo, evento_asociado) VALUES (
	'ctrl_asis', --objeto_cuadro_proyecto
	'396000030', --objeto_cuadro
	'35736730000330', --objeto_cuadro_col
	'escalafon', --clave
	'7', --orden
	'Tipo Escalafon', --titulo
	'ei-cuadro-col-tit', --estilo_titulo
	'4', --estilo
	NULL, --ancho
	'1', --formateo
	NULL, --vinculo_indice
	'0', --no_ordenar
	NULL, --mostrar_xls
	NULL, --mostrar_pdf
	NULL, --pdf_propiedades
	NULL, --desabilitado
	'0', --total
	NULL, --total_cc
	'0', --usar_vinculo
	NULL, --vinculo_carpeta
	NULL, --vinculo_item
	NULL, --vinculo_popup
	NULL, --vinculo_popup_param
	NULL, --vinculo_target
	NULL, --vinculo_celda
	NULL, --vinculo_servicio
	'0', --permitir_html
	'Agente', --grupo
	NULL  --evento_asociado
);
--- FIN Grupo de desarrollo 35736730
