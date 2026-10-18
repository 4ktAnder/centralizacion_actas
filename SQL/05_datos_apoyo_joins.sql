-- =====================================================================
-- 05_datos_apoyo_joins.sql  |  Filas extra para que los JOIN externos
-- muestren diferencias. Es seguro ejecutarlo una sola vez.
-- Agrega:
--   * Un usuario SIN actas ni acuerdos        (Pedro Prueba)
--   * Un equipo libre, SIN actas              (PL-00468)
--   * Un equipo entregado con acta SIN acuerdo(PL-00469 -> Carlos Gomez)
--   * Un acuerdo SIN acta                     (de Pedro Prueba)
-- =====================================================================

INSERT INTO usuarios (nombre_completo, correo, documento_identidad)
SELECT 'Pedro Prueba', 'p.prueba@empresa.co', '1020304060'
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE documento_identidad = '1020304060');

INSERT INTO equipos (placa_equipo, serial_equipo, observaciones)
SELECT 'PL-00468', 'LN14T00033', 'Portátil Lenovo ThinkPad T14'
WHERE NOT EXISTS (SELECT 1 FROM equipos WHERE placa_equipo = 'PL-00468');

INSERT INTO equipos (placa_equipo, serial_equipo, observaciones)
SELECT 'PL-00469', 'DL7740A003', 'Portátil Dell Latitude 7440'
WHERE NOT EXISTS (SELECT 1 FROM equipos WHERE placa_equipo = 'PL-00469');

-- Acta sin acuerdo (id_acuerdo queda NULL)
INSERT INTO actas_entrega (id_usuario, id_equipo, id_acuerdo, fecha_entrega, cargo_al_momento)
SELECT u.id_usuario, e.id_equipo, NULL, '2026-10-05 10:00', 'Desarrollador'
FROM usuarios u, equipos e
WHERE u.documento_identidad = '1020304052'
  AND e.placa_equipo = 'PL-00469'
  AND NOT EXISTS (SELECT 1 FROM actas_entrega a WHERE a.id_equipo = e.id_equipo);

-- Acuerdo sin acta
INSERT INTO acuerdos_responsabilidad (id_usuario, fecha_firma, evidencia_firmada)
SELECT u.id_usuario, '2026-10-10', '/evidencias/acuerdo_p_prueba.pdf'
FROM usuarios u
WHERE u.documento_identidad = '1020304060'
  AND NOT EXISTS (SELECT 1 FROM acuerdos_responsabilidad ar WHERE ar.id_usuario = u.id_usuario);
