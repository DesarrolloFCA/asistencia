-- View: reloj.vw_inas_m

-- DROP MATERIALIZED VIEW IF EXISTS reloj.vw_inas_m;

CREATE MATERIALIZED VIEW IF NOT EXISTS reloj.vw_inas_m
TABLESPACE pg_default
AS
 SELECT parte.legajo,
    parte.fecha_inicio_licencia,
    generate_series(parte.fecha_inicio_licencia::timestamp with time zone, reloj.calcular_dia_final(parte.fecha_inicio_licencia, parte.dias)::timestamp with time zone, '1 day'::interval)::date AS fecha,
    parte.id_motivo,
    parte.id_parte
   FROM reloj.parte
  WHERE parte.estado = 'C'::bpchar
WITH DATA;

ALTER TABLE IF EXISTS reloj.vw_inas_m
    OWNER TO postgres;


CREATE UNIQUE INDEX idx_vw_inasistencia
    ON reloj.vw_inas_m USING btree
    (legajo, fecha, id_parte)
    TABLESPACE pg_default;