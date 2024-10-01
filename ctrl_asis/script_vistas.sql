-- View: reloj.vw_inas_m

-- DROP MATERIALIZED VIEW IF EXISTS reloj.vw_inas_m;

CREATE MATERIALIZED VIEW IF NOT EXISTS reloj.vw_inas_m
TABLESPACE pg_default
AS
 SELECT parte.legajo,
    parte.fecha_inicio_licencia,
    generate_series(parte.fecha_inicio_licencia::timestamp with time zone, reloj.calcular_dia_final(parte.fecha_inicio_licencia, parte.dias)::timestamp with time zone, '1 day'::interval)::date AS fecha,
    parte.id_motivo,
    parte.id_parte,
    parte.id_parte_sanidad
   FROM reloj.parte
  WHERE parte.estado = 'C'::bpchar
WITH DATA;

ALTER TABLE IF EXISTS reloj.vw_inas_m
    OWNER TO postgres;

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
    b.id_motivo,
    NULL::integer AS id_parte_sanidad,
    'Presente'::text AS condicion
   FROM reloj.marcacion_m a
     LEFT JOIN reloj.parte b ON a.legajo = b.legajo AND a.fecha = b.fecha_inicio_licencia
UNION
 SELECT b.legajo,
    b.fecha,
    NULL::time without time zone AS hora_entrada,
    NULL::time without time zone AS hora_salida,
    NULL::interval AS horas_trabajadas,
    b.id_parte,
    b.id_motivo,
    b.id_parte_sanidad,
    'Ausente Justificado'::text AS condicion
   FROM reloj.vw_inas_m b
     LEFT JOIN reloj.marcacion_m a ON b.legajo = a.legajo AND b.fecha = a.fecha
  WHERE a.legajo IS NULL
UNION
 SELECT b.legajo,
    b.fecha_ AS fecha,
    NULL::time without time zone AS hora_entrada,
    NULL::time without time zone AS hora_salida,
    NULL::interval AS horas_trabajadas,
    NULL::integer AS id_parte,
    NULL::integer AS id_motivo,
    NULL::integer AS id_parte_sanidad,
    b.condicion
   FROM reloj.vw_ausentes_m b
     LEFT JOIN reloj.marcacion_m a ON b.legajo = a.legajo AND b.fecha_ = a.fecha
     LEFT JOIN reloj.vw_inas_m c ON b.legajo = c.legajo AND b.fecha_ = c.fecha
     LEFT JOIN reloj.vw_ausentes_trab_sab d ON b.legajo = d.legajo AND b.fecha_ = d.fecha_
  WHERE a.legajo IS NULL AND c.legajo IS NULL AND d.legajo IS NULL
  ORDER BY 1, 2 DESC
WITH DATA;

ALTER TABLE IF EXISTS reloj.vm_pres_aus_jus
    OWNER TO postgres;

-- View: reloj.vm_detalle_pres

-- DROP MATERIALIZED VIEW IF EXISTS reloj.vm_detalle_pres;

CREATE MATERIALIZED VIEW IF NOT EXISTS reloj.vm_detalle_pres
TABLESPACE pg_default
AS
 SELECT DISTINCT d.cuil,
    a.legajo,
    (btrim(d.apellido::text) || ', '::text) || btrim(d.nombre::text) AS ayn,
    d.agrupamiento,
    d.categoria,
    d.escalafon,
    d.caracter,
    b.nombre_catedra,
    a.fecha,
    h.horas_requeridad,
    LEAST(a.hora_entrada::time with time zone, g.horario) AS hora_entrada,
    GREATEST(a.hora_salida::time with time zone, g.horario_fin) AS hora_salida,
    GREATEST(a.hora_salida, g.horario_fin::time without time zone) - LEAST(a.hora_entrada, g.horario::time without time zone) AS horas_trabajadas,
        CASE
            WHEN a.id_motivo = 58 THEN 'Permiso Horario'::character varying
            WHEN a.legajo = g.legajo AND a.fecha = g.fecha THEN ((('Comision de servicio desde '::text || g.horario) || ' hasta '::text) || g.horario_fin)::character varying
            ELSE c.descripcion
        END AS descripcion,
        CASE
            WHEN a.id_parte_sanidad IS NOT NULL THEN 'Asuente Justicado Sanidad'::text
            WHEN f.feriado IS NOT NULL THEN 'Feriado'::text
            WHEN a.id_motivo = 56 THEN 'Presente'::text
            WHEN g.fecha IS NOT NULL THEN 'Presente'::text
            ELSE a.condicion
        END AS estado
   FROM reloj.vm_pres_aus_jus a
     LEFT JOIN reloj.catedras_agentes e ON a.legajo = e.legajo
     LEFT JOIN reloj.catedras b ON e.id_catedra = b.id_catedra
     LEFT JOIN reloj.motivo c ON a.id_motivo = c.id_motivo
     LEFT JOIN reloj.agentes d ON a.legajo = d.legajo
     LEFT JOIN reloj.vw_feriados f ON a.fecha = f.generate_series AND (f.agru = 'Todos'::text OR f.agru = d.agrupamiento::text)
     LEFT JOIN reloj.vw_comision g ON a.legajo = g.legajo AND a.fecha = g.fecha
     LEFT JOIN reloj.vw_agentes_horas_req h ON a.legajo = h.legajo
  WHERE d.nombre IS NOT NULL
  GROUP BY d.cuil, a.legajo, d.apellido, d.nombre, d.agrupamiento, d.categoria, d.escalafon, d.caracter, b.nombre_catedra, a.fecha, a.hora_salida, a.hora_entrada, g.horario, g.horario_fin, a.id_motivo, c.descripcion, f.feriado, a.condicion, a.id_parte_sanidad, g.legajo, g.fecha, h.horas_requeridad
  ORDER BY a.legajo, a.fecha DESC
WITH DATA;

ALTER TABLE IF EXISTS reloj.vm_detalle_pres
    OWNER TO postgres;   