-- View: relojes.marcaciones

-- DROP VIEW relojes.marcaciones;

CREATE OR REPLACE VIEW relojes.marcaciones
 AS
 SELECT user_attendance.legajo,
    user_attendance.fecha,
    min(user_attendance.hora) AS min,
    max(user_attendance.hora) AS max
   FROM relojes.user_attendance
  GROUP BY user_attendance.legajo, user_attendance.fecha;

ALTER TABLE relojes.marcaciones
    OWNER TO postgres;

