-- View: reloj.vw_ausentes_trab_sab

-- DROP VIEW reloj.vw_ausentes_trab_sab;

CREATE OR REPLACE VIEW reloj.vw_ausentes_trab_sab
 AS
 SELECT vw_ausentes.legajo,
    vw_ausentes.fecha_,
    vw_ausentes.condicion
   FROM reloj.vw_ausentes
  WHERE (vw_ausentes.legajo IN ( SELECT conf_jornada.legajo
           FROM reloj.conf_jornada
          WHERE conf_jornada.sabado IS NOT NULL)) AND date_part('dow'::text, vw_ausentes.fecha_) = 6::double precision;

ALTER TABLE reloj.vw_ausentes_trab_sab
    OWNER TO postgres;

