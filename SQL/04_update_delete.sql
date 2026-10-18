-- =====================================================================
-- 04_update_delete.sql  |  UPDATE y DELETE sobre un equipo de prueba
-- Se crea un equipo temporal para no tocar los datos reales.
-- =====================================================================

-- 1) INSERT de un equipo de prueba
INSERT INTO equipos (placa_equipo, serial_equipo, observaciones)
VALUES ('PL-99999', 'PRUEBA-0001', 'Equipo de prueba para UPDATE/DELETE');
SELECT * FROM equipos WHERE placa_equipo = 'PL-99999';

-- 2) UPDATE: marcarlo como dado de baja
UPDATE equipos
SET observaciones = 'Dado de baja', estado = FALSE
WHERE placa_equipo = 'PL-99999';
SELECT * FROM equipos WHERE placa_equipo = 'PL-99999';

-- 3) DELETE: eliminarlo (solo es posible si no tiene actas asociadas)
DELETE FROM equipos WHERE placa_equipo = 'PL-99999';
SELECT * FROM equipos WHERE placa_equipo = 'PL-99999';   -- (0 rows)
