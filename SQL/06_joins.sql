-- =====================================================================
-- 06_joins.sql  |  Los seis tipos de JOIN sobre el modelo Helpdesk
-- Ejecutar DESPUES de 05_datos_apoyo_joins.sql.
-- Entrar a psql y correr:   \i 06_joins.sql   (o una consulta a la vez)
-- Para capturas mas legibles:  \pset border 2   y   \x off
-- =====================================================================

-- ---------------------------------------------------------------------
-- 1) INNER JOIN: solo filas con coincidencia en TODAS las tablas.
--    El acta sin acuerdo (PL-00469) NO aparece.
-- ---------------------------------------------------------------------
SELECT u.nombre_completo, e.placa_equipo, ar.fecha_firma, a.fecha_entrega
FROM actas_entrega a
INNER JOIN usuarios u                  ON u.id_usuario  = a.id_usuario
INNER JOIN equipos e                   ON e.id_equipo   = a.id_equipo
INNER JOIN acuerdos_responsabilidad ar ON ar.id_acuerdo = a.id_acuerdo
ORDER BY a.fecha_entrega;

-- ---------------------------------------------------------------------
-- 2) LEFT JOIN: todos los usuarios, tengan o no equipo asignado.
--    Pedro Prueba aparece con placa NULL.
-- ---------------------------------------------------------------------
SELECT u.nombre_completo, e.placa_equipo, a.fecha_entrega
FROM usuarios u
LEFT JOIN actas_entrega a ON a.id_usuario = u.id_usuario
LEFT JOIN equipos e       ON e.id_equipo  = a.id_equipo
ORDER BY u.id_usuario;

-- Variante util para auditoria: SOLO los usuarios sin equipo
SELECT u.nombre_completo
FROM usuarios u
LEFT JOIN actas_entrega a ON a.id_usuario = u.id_usuario
WHERE a.id_acta IS NULL;

-- ---------------------------------------------------------------------
-- 3) RIGHT JOIN: todos los equipos, esten o no entregados.
--    PL-00468 aparece sin usuario (inventario libre).
-- ---------------------------------------------------------------------
SELECT e.placa_equipo, e.observaciones, u.nombre_completo, a.fecha_entrega
FROM actas_entrega a
RIGHT JOIN equipos e ON e.id_equipo  = a.id_equipo
LEFT  JOIN usuarios u ON u.id_usuario = a.id_usuario
ORDER BY e.id_equipo;

-- ---------------------------------------------------------------------
-- 4) FULL OUTER JOIN: todo de ambos lados, coincida o no.
--    Muestra un acuerdo sin acta (Pedro Prueba) y un acta sin acuerdo (PL-00469).
-- ---------------------------------------------------------------------
SELECT ar.id_acuerdo, ar.fecha_firma, a.id_acta, a.fecha_entrega
FROM acuerdos_responsabilidad ar
FULL OUTER JOIN actas_entrega a ON a.id_acuerdo = ar.id_acuerdo
ORDER BY ar.id_acuerdo NULLS LAST;

-- ---------------------------------------------------------------------
-- 5) CROSS JOIN: producto cartesiano (todas las combinaciones).
--    Util como matriz de posibles asignaciones usuario-equipo.
-- ---------------------------------------------------------------------
SELECT COUNT(*) AS combinaciones
FROM usuarios u
CROSS JOIN equipos e;

SELECT u.nombre_completo, e.placa_equipo
FROM usuarios u
CROSS JOIN equipos e
ORDER BY u.id_usuario, e.id_equipo
LIMIT 12;

-- ---------------------------------------------------------------------
-- 6) SELF JOIN: una tabla contra si misma.
--    Pares de equipos del mismo modelo (e1.id < e2.id evita duplicados).
-- ---------------------------------------------------------------------
SELECT e1.placa_equipo AS equipo_1,
       e2.placa_equipo AS equipo_2,
       e1.observaciones AS modelo
FROM equipos e1
JOIN equipos e2
  ON e1.observaciones = e2.observaciones
 AND e1.id_equipo < e2.id_equipo
ORDER BY e1.observaciones;
