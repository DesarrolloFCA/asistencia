INSERT INTO apex_revision (revision, creacion) VALUES ('DESCONOCIDA', '2024-04-11 12:33:57');
INSERT INTO apex_revision (revision, creacion) VALUES ('DESCONOCIDA', '2024-05-21 16:38:42');
INSERT INTO apex_instancia (instancia, version, institucion, observaciones, administrador_1, administrador_2, administrador_3, creacion) VALUES ('desarrollo', '3.3.26', NULL, NULL, NULL, NULL, NULL, '2024-04-11 12:33:57');
INSERT INTO apex_checksum_proyectos (checksum, proyecto) ( SELECT '0e077ca6c02b9155788ee7b72f7ae4eb005e397ad1cee2bec88d08a3137bf731', 'comision' WHERE NOT EXISTS ( SELECT 1  FROM apex_checksum_proyectos WHERE  checksum = '0e077ca6c02b9155788ee7b72f7ae4eb005e397ad1cee2bec88d08a3137bf731'  AND  proyecto = 'comision' ));
