-- View: reloj.vm_detalle_pres

-- DROP MATERIALIZED VIEW IF EXISTS reloj.vm_detalle_pres;

CREATE MATERIALIZED VIEW IF NOT EXISTS reloj.vm_detalle_pres
TABLESPACE pg_default
AS
 SELECT DISTINCT a.legajo,
    (TRIM(BOTH FROM d.apellido) || ', '::text) || TRIM(BOTH FROM d.nombre) AS ayn,
    d.agrupamiento,
    d.categoria,
    b.nombre_catedra,
    a.fecha,
        CASE
            WHEN SUBSTRING(d.categoria FROM 4 FOR 4) = '1'::text THEN '05:36'::text
            WHEN SUBSTRING(d.categoria FROM 4 FOR 4) = '2'::text THEN '02:48'::text
            WHEN SUBSTRING(d.categoria FROM 4 FOR 4) = '3'::text THEN '01:24'::text
            ELSE '06:00'::text
        END::time without time zone AS horas_requeridad,
    a.hora_entrada,
    a.hora_salida,
    a.horas_trabajadas,
    c.descripcion,
        CASE
            WHEN f.feriado IS NOT NULL THEN f.feriado::text
            ELSE a.condicion
        END AS estado
   FROM reloj.vm_pres_aus_jus a
     LEFT JOIN reloj.catedras_agentes e ON a.legajo = e.legajo
     LEFT JOIN reloj.catedras b ON e.id_catedra = b.id_catedra
     LEFT JOIN reloj.motivo c ON a.id_motivo = c.id_motivo
     LEFT JOIN reloj.agentes d ON a.legajo = d.legajo
     LEFT JOIN reloj.vw_feriados f ON a.fecha = f.generate_series AND (f.agru = 'Todos'::text OR f.agru = d.agrupamiento::text)
  WHERE d.nombre IS NOT NULL
  ORDER BY a.legajo, a.fecha DESC
WITH DATA;

ALTER TABLE IF EXISTS reloj.vm_detalle_pres
    OWNER TO postgres;