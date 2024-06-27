-- View: relojes.marcacion_m

-- DROP MATERIALIZED VIEW IF EXISTS relojes.marcacion_m;

CREATE MATERIALIZED VIEW IF NOT EXISTS relojes.marcacion_m
TABLESPACE pg_default
AS
 SELECT user_attendance.legajo,
    user_attendance.fecha,
    min(user_attendance.hora) AS min,
    max(user_attendance.hora) AS max
   FROM relojes.user_attendance
  GROUP BY user_attendance.legajo, user_attendance.fecha
WITH DATA;

ALTER TABLE IF EXISTS relojes.marcacion_m
    OWNER TO postgres;


CREATE UNIQUE INDEX idx_marcaciones
    ON relojes.marcacion_m USING btree
    (legajo, fecha, min)
    TABLESPACE pg_default;