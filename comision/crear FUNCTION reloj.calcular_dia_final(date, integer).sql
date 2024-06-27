-- FUNCTION: reloj.calcular_dia_final(date, integer)

-- DROP FUNCTION IF EXISTS reloj.calcular_dia_final(date, integer);

CREATE OR REPLACE FUNCTION reloj.calcular_dia_final(
	fecha_inicio date,
	dias integer)
    RETURNS date
    LANGUAGE 'plpgsql'
    COST 100
    VOLATILE PARALLEL UNSAFE
AS $BODY$
DECLARE
    fecha_final date;
BEGIN
    fecha_final := fecha_inicio + dias - 1;
    RETURN fecha_final;
END;
$BODY$;

ALTER FUNCTION reloj.calcular_dia_final(date, integer)
    OWNER TO postgres;
