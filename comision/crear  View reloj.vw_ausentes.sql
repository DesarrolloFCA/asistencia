-- View: reloj.vw_ausentes

-- DROP VIEW reloj.vw_ausentes;

CREATE OR REPLACE VIEW reloj.vw_ausentes
 AS
 SELECT fechas_totales.legajo,
    fechas_totales.fecha_,
    'Ausente'::text AS condicion
   FROM ( SELECT vista_marcaciones_y_partes.legajo,
            generate_series('2024-01-01'::date::timestamp with time zone, CURRENT_DATE::timestamp with time zone, '1 day'::interval) AS fecha_
           FROM reloj.vista_marcaciones_y_partes
          GROUP BY vista_marcaciones_y_partes.legajo) fechas_totales
     LEFT JOIN reloj.vista_marcaciones_y_partes a ON fechas_totales.legajo = a.legajo AND fechas_totales.fecha_ = a.fecha_
  WHERE a.legajo IS NULL AND (fechas_totales.legajo IN ( SELECT agentes.legajo
           FROM reloj.agentes)) AND NOT (fechas_totales.fecha_ IN ( SELECT a_1.generate_series
           FROM reloj.vw_feriados a_1));

ALTER TABLE reloj.vw_ausentes
    OWNER TO postgres;

