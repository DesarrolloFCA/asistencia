-- View: reloj.vw_ausentes_m

-- DROP MATERIALIZED VIEW IF EXISTS reloj.vw_ausentes_m;

CREATE MATERIALIZED VIEW IF NOT EXISTS reloj.vw_ausentes_m
TABLESPACE pg_default
AS
 SELECT fechas_totales.legajo,
    fechas_totales.fecha_,
    'Ausente'::text AS condicion
   FROM ( SELECT vw_marca_parte.legajo,
            generate_series('2018-12-26'::date::timestamp with time zone, CURRENT_DATE::timestamp with time zone, '1 day'::interval)::date AS fecha_
           FROM reloj.vw_marca_parte
          GROUP BY vw_marca_parte.legajo) fechas_totales
     LEFT JOIN reloj.vw_marca_parte a ON fechas_totales.legajo = a.legajo AND fechas_totales.fecha_ = a.fecha_
  WHERE a.legajo IS NULL AND (fechas_totales.legajo IN ( SELECT agentes.legajo
           FROM reloj.agentes)) AND NOT (fechas_totales.fecha_ IN ( SELECT vw_feriados.generate_series::date AS feriado
           FROM reloj.vw_feriados
          WHERE vw_feriados.agru IS NULL OR vw_feriados.agru = 'Todos'::text)) AND (date_part('dow'::text, fechas_totales.fecha_) <> ALL (ARRAY[0::double precision, 6::double precision]))
WITH DATA;

ALTER TABLE IF EXISTS reloj.vw_ausentes_m
    OWNER TO postgres;