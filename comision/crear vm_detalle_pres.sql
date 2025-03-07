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
            WHEN a.id_motivo = 56 THEN 'Presente'::text
            WHEN a.id_motivo IS NOT NULL THEN 'Ausente Justicado'::text
            WHEN g.fecha IS NOT NULL THEN 'Presente'::text
            WHEN f.feriado IS NOT NULL THEN 'Feriado'::text
            ELSE a.condicion
        END AS estado
   FROM reloj.vm_pres_aus_jus a
     LEFT JOIN reloj.catedras_agentes e ON a.legajo = e.legajo
     LEFT JOIN reloj.catedras b ON e.id_catedra = b.id_catedra
     LEFT JOIN reloj.motivo c ON a.id_motivo = c.id_motivo
     LEFT JOIN reloj.agentes d ON a.legajo = d.legajo
     LEFT JOIN reloj.vw_feriados f ON a.fecha = f.generate_series AND (f.agru = 'Todos'::text OR f.agru = d.escalafon::text)
     LEFT JOIN reloj.vw_comision g ON a.legajo = g.legajo AND a.fecha = g.fecha
     LEFT JOIN reloj.vw_agentes_horas_req h ON a.legajo = h.legajo
  WHERE d.nombre IS NOT NULL
  GROUP BY d.cuil, a.legajo, d.apellido, d.nombre, d.agrupamiento, d.categoria, d.escalafon, d.caracter, b.nombre_catedra, a.fecha, a.hora_salida, a.hora_entrada, g.horario, g.horario_fin, a.id_motivo, c.descripcion, f.feriado, a.condicion, a.id_parte_sanidad, g.legajo, g.fecha, h.horas_requeridad
  ORDER BY a.legajo, a.fecha DESC
WITH DATA;

ALTER TABLE IF EXISTS reloj.vm_detalle_pres
    OWNER TO postgres;