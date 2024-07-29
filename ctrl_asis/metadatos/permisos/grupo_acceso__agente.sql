
------------------------------------------------------------
-- apex_usuario_grupo_acc
------------------------------------------------------------
INSERT INTO apex_usuario_grupo_acc (proyecto, usuario_grupo_acc, nombre, nivel_acceso, descripcion, vencimiento, dias, hora_entrada, hora_salida, listar, permite_edicion, menu_usuario) VALUES (
	'ctrl_asis', --proyecto
	'agente', --usuario_grupo_acc
	'Agente', --nombre
	NULL, --nivel_acceso
	'Perfil de usuarios autogestión', --descripcion
	NULL, --vencimiento
	NULL, --dias
	NULL, --hora_entrada
	NULL, --hora_salida
	NULL, --listar
	'0', --permite_edicion
	NULL  --menu_usuario
);

------------------------------------------------------------
-- apex_usuario_grupo_acc_item
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'ctrl_asis', --proyecto
	'agente', --usuario_grupo_acc
	NULL, --item_id
	'1'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'ctrl_asis', --proyecto
	'agente', --usuario_grupo_acc
	NULL, --item_id
	'2'  --item
);
--- FIN Grupo de desarrollo 0

--- INICIO Grupo de desarrollo 396
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'ctrl_asis', --proyecto
	'agente', --usuario_grupo_acc
	NULL, --item_id
	'396000012'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'ctrl_asis', --proyecto
	'agente', --usuario_grupo_acc
	NULL, --item_id
	'396000022'  --item
);
--- FIN Grupo de desarrollo 396

--- INICIO Grupo de desarrollo 26960396
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'ctrl_asis', --proyecto
	'agente', --usuario_grupo_acc
	NULL, --item_id
	'26960396000005'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'ctrl_asis', --proyecto
	'agente', --usuario_grupo_acc
	NULL, --item_id
	'26960396000006'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'ctrl_asis', --proyecto
	'agente', --usuario_grupo_acc
	NULL, --item_id
	'26960396000010'  --item
);
--- FIN Grupo de desarrollo 26960396

--- INICIO Grupo de desarrollo 35736730
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'ctrl_asis', --proyecto
	'agente', --usuario_grupo_acc
	NULL, --item_id
	'35736730000058'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'ctrl_asis', --proyecto
	'agente', --usuario_grupo_acc
	NULL, --item_id
	'35736730000059'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'ctrl_asis', --proyecto
	'agente', --usuario_grupo_acc
	NULL, --item_id
	'35736730000061'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'ctrl_asis', --proyecto
	'agente', --usuario_grupo_acc
	NULL, --item_id
	'35736730000063'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'ctrl_asis', --proyecto
	'agente', --usuario_grupo_acc
	NULL, --item_id
	'35736730000072'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'ctrl_asis', --proyecto
	'agente', --usuario_grupo_acc
	NULL, --item_id
	'35736730000075'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'ctrl_asis', --proyecto
	'agente', --usuario_grupo_acc
	NULL, --item_id
	'35736730000076'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'ctrl_asis', --proyecto
	'agente', --usuario_grupo_acc
	NULL, --item_id
	'35736730000083'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'ctrl_asis', --proyecto
	'agente', --usuario_grupo_acc
	NULL, --item_id
	'35736730000084'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'ctrl_asis', --proyecto
	'agente', --usuario_grupo_acc
	NULL, --item_id
	'35736730000085'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'ctrl_asis', --proyecto
	'agente', --usuario_grupo_acc
	NULL, --item_id
	'35736730000087'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'ctrl_asis', --proyecto
	'agente', --usuario_grupo_acc
	NULL, --item_id
	'35736730000096'  --item
);
--- FIN Grupo de desarrollo 35736730

------------------------------------------------------------
-- apex_grupo_acc_restriccion_funcional
------------------------------------------------------------
INSERT INTO apex_grupo_acc_restriccion_funcional (proyecto, usuario_grupo_acc, restriccion_funcional) VALUES (
	'ctrl_asis', --proyecto
	'agente', --usuario_grupo_acc
	'26960396000002'  --restriccion_funcional
);
