-- View: reloj.vw_marca_parte

-- DROP MATERIALIZED VIEW IF EXISTS reloj.vw_marca_parte;

CREATE MATERIALIZED VIEW IF NOT EXISTS reloj.vw_marca_parte
TABLESPACE pg_default
AS
 SELECT COALESCE(m.legajo, p.legajo) AS legajo,
    COALESCE(m.fecha, p.fecha) AS fecha_,
        CASE
            WHEN p.id_motivo > 0 THEN NULL::time without time zone
            ELSE m.min
        END AS hora_entrada,
        CASE
            WHEN p.id_motivo > 0 THEN NULL::time without time zone
            ELSE m.max
        END AS hora_salida,
    p.id_motivo,
    p.id_parte,
        CASE
            WHEN p.id_motivo > 0 THEN 'Ausente Justificado'::text
            ELSE 'Presente'::text
        END AS condicion
   FROM relojes.marcaciones m
     FULL JOIN reloj.vw_inasistencias p ON m.legajo = p.legajo AND m.fecha = p.fecha
  WHERE (m.legajo IN ( SELECT agentes.legajo
           FROM reloj.agentes))
WITH DATA;

ALTER TABLE IF EXISTS reloj.vw_marca_parte
    OWNER TO postgres;


CREATE UNIQUE INDEX idx_mp
    ON reloj.vw_marca_parte USING btree
    (legajo, fecha_, hora_entrada, id_parte)
    TABLESPACE pg_default;