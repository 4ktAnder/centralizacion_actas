-- =====================================================================
-- 03_select.sql  |  Seleccion, proyeccion y union (Listados 4, 5 y 6)
-- =====================================================================

-- Listado 4: seleccion (filtra filas)
SELECT * FROM usuarios WHERE documento_identidad = '1020304050';

-- Listado 5: proyeccion (elige columnas)
SELECT nombre_completo, documento_identidad FROM usuarios;

-- Listado 6: union de cuatro tablas
SELECT u.nombre_completo,
       e.placa_equipo,
       e.serial_equipo,
       ar.fecha_firma,
       a.fecha_entrega,
       a.fecha_devolucion
FROM actas_entrega a
JOIN usuarios u                  ON u.id_usuario  = a.id_usuario
JOIN equipos e                   ON e.id_equipo   = a.id_equipo
JOIN acuerdos_responsabilidad ar ON ar.id_acuerdo = a.id_acuerdo
ORDER BY ar.fecha_firma;
