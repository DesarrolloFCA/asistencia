
------------------------------------------------------------
-- apex_usuario_grupo_acc
------------------------------------------------------------
INSERT INTO apex_usuario_grupo_acc (proyecto, usuario_grupo_acc, nombre, nivel_acceso, descripcion, vencimiento, dias, hora_entrada, hora_salida, listar, permite_edicion, menu_usuario) VALUES (
	'comision', --proyecto
	'admin', --usuario_grupo_acc
	'Administrador', --nombre
	'0', --nivel_acceso
	'Accede a toda la funcionalidad', --descripcion
	NULL, --vencimiento
	NULL, --dias
	NULL, --hora_entrada
	NULL, --hora_salida
	NULL, --listar
	'1', --permite_edicion
	NULL  --menu_usuario
);

------------------------------------------------------------
-- apex_usuario_grupo_acc_miembros
------------------------------------------------------------
INSERT INTO apex_usuario_grupo_acc_miembros (proyecto, usuario_grupo_acc, usuario_grupo_acc_pertenece) VALUES (
	'comision', --proyecto
	'admin', --usuario_grupo_acc
	'personal'  --usuario_grupo_acc_pertenece
);

------------------------------------------------------------
-- apex_usuario_grupo_acc_item
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'comision', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'1'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'comision', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'2'  --item
);
--- FIN Grupo de desarrollo 0

--- INICIO Grupo de desarrollo 1842
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'comision', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'1842000002'  --item
);
--- FIN Grupo de desarrollo 1842

--- INICIO Grupo de desarrollo 24234
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'comision', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'24234000001'  --item
);
--- FIN Grupo de desarrollo 24234

--- INICIO Grupo de desarrollo 35736730
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'comision', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'35736730000045'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'comision', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'35736730000046'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'comision', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'35736730000053'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'comision', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'35736730000054'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'comision', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'35736730000055'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'comision', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'35736730000073'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'comision', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'35736730000074'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'comision', --proyecto
	'admin', --usuario_grupo_acc
	NULL, --item_id
	'35736730000098'  --item
);
--- FIN Grupo de desarrollo 35736730
