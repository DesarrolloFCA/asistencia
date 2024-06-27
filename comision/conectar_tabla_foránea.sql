CREATE EXTENSION postgres_fdw;
 CREATE SCHEMA esquema_destino;
 
DROP SERVER IF EXISTS servidor_bd_remota CASCADE;

CREATE SERVER servidor_bd_remota 
 FOREIGN DATA WRAPPER postgres_fdw
 OPTIONS (host '172.22.8.59', dbname 'db_zkt_v1', port '49111');

CREATE USER MAPPING FOR CURRENT_USER SERVER servidor_bd_remota 
 OPTIONS (USER 'postgres', password '123--,.qaz098--xsw--123');
 
 IMPORT FOREIGN SCHEMA public
FROM SERVER servidor_bd_remota INTO reloj;

SELECT * FROM relojes.user_attendance;