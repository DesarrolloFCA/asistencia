-- FOREIGN TABLE: relojes.user_attendance

-- DROP FOREIGN TABLE IF EXISTS relojes.user_attendance;

CREATE FOREIGN TABLE IF NOT EXISTS relojes.user_attendance(
    id integer OPTIONS (column_name 'id') NOT NULL,
    legajo integer OPTIONS (column_name 'legajo') NOT NULL,
    fecha date OPTIONS (column_name 'fecha') NOT NULL,
    hora time without time zone OPTIONS (column_name 'hora') NOT NULL
)
    SERVER servidor_bd_remota
    OPTIONS (schema_name 'public', table_name 'user_attendance');

ALTER FOREIGN TABLE relojes.user_attendance
    OWNER TO postgres;


-- Trigger: trefresh

-- DROP TRIGGER IF EXISTS trefresh ON relojes.user_attendance;

CREATE OR REPLACE TRIGGER trefresh
    AFTER INSERT
    ON relojes.user_attendance
    FOR EACH ROW
    EXECUTE FUNCTION relojes.trefresh();

ALTER TABLE relojes.user_attendance
    ENABLE ALWAYS TRIGGER trefresh;