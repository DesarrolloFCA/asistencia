-- View: reloj.vm_pres_aus_jus

-- DROP MATERIALIZED VIEW IF EXISTS reloj.vm_pres_aus_jus;

CREATE MATERIALIZED VIEW IF NOT EXISTS reloj.vm_pres_aus_jus
TABLESPACE pg_default
AS
 SELECT a.legajo,
    a.fecha,
    a.min AS hora_entrada,
    a.max AS hora_salida,
    a.max - a.min AS horas_trabajadas,
    NULL::integer AS id_parte,
    NULL::integer AS id_motivo,
    'Presente'::text AS condicion
   FROM relojes.marcacion_m a
UNION
 SELECT b.legajo,
    b.fecha,
    NULL::time without time zone AS hora_entrada,
    NULL::time without time zone AS hora_salida,
    NULL::interval AS horas_trabajadas,
    b.id_parte,
    b.id_motivo,
    'Ausente Justificado'::text AS condicion
   FROM reloj.vw_inas_m b
     LEFT JOIN relojes.marcacion_m a ON b.legajo = a.legajo AND b.fecha = a.fecha
  WHERE a.legajo IS NULL
UNION
 SELECT b.legajo,
    b.fecha_ AS fecha,
    NULL::time without time zone AS hora_entrada,
    NULL::time without time zone AS hora_salida,
    NULL::interval AS horas_trabajadas,
    NULL::integer AS id_parte,
    NULL::integer AS id_motivo,
    b.condicion
   FROM reloj.vw_ausentes_m b
     LEFT JOIN relojes.marcacion_m a ON b.legajo = a.legajo AND b.fecha_ = a.fecha
     LEFT JOIN reloj.vw_inas_m c ON b.legajo = c.legajo AND b.fecha_ = c.fecha
     LEFT JOIN reloj.vw_ausentes_trab_sab d ON b.legajo = d.legajo AND b.fecha_ = d.fecha_
  WHERE a.legajo IS NULL AND c.legajo IS NULL AND d.legajo IS NULL
  ORDER BY 1, 2 DESC
WITH DATA;

ALTER TABLE IF EXISTS reloj.vm_pres_aus_jus
    OWNER TO postgres;


CREATE UNIQUE INDEX inndx_paj
    ON reloj.vm_pres_aus_jus USING btree
    (legajo, fecha, condicion COLLATE pg_catalog."default")
    TABLESPACE pg_default;