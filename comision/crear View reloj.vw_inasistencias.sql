-- View: reloj.vw_inasistencias

-- DROP VIEW reloj.vw_inasistencias;

CREATE OR REPLACE VIEW reloj.vw_inasistencias
 AS
 SELECT parte.legajo,
    parte.fecha_inicio_licencia,
    generate_series(parte.fecha_inicio_licencia::timestamp with time zone, reloj.calcular_dia_final(parte.fecha_inicio_licencia, parte.dias)::timestamp with time zone, '1 day'::interval)::date AS fecha,
    parte.id_motivo,
    parte.id_parte
   FROM reloj.parte
  WHERE parte.estado = 'C'::bpchar;

ALTER TABLE reloj.vw_inasistencias
    OWNER TO postgres;

