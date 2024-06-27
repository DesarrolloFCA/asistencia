-- View: reloj.vw_feriados

-- DROP VIEW reloj.vw_feriados;

CREATE OR REPLACE VIEW reloj.vw_feriados
 AS
 SELECT conf_feriados.feriado,
    generate_series(conf_feriados.feriado_fecha::timestamp with time zone, conf_feriados.feriado_fecha_fin::timestamp with time zone, '1 day'::interval) AS generate_series,
    conf_feriados.tipo_feriado,
        CASE
            WHEN conf_feriados.agrupamiento::text = 'Docentes'::text THEN 'DOCE'::text
            WHEN conf_feriados.agrupamiento::text = 'Personal de Apoyo'::text THEN 'NODO'::text
            ELSE 'Todos'::text
        END AS agru
   FROM reloj.conf_feriados;

ALTER TABLE reloj.vw_feriados
    OWNER TO postgres;

