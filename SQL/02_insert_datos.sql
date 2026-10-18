-- =====================================================================
-- 02_insert_datos.sql  |  Datos de prueba (Listados 2 y 3 + acuerdos/actas)
-- Ejecutar solo en una base vacia (despues de 01_crear_tablas.sql).
-- Las actas se reconstruyeron a partir de la aplicacion web; ajusta
-- cargos/fechas si difieren de tu base real.
-- =====================================================================

INSERT INTO usuarios (nombre_completo, correo, documento_identidad) VALUES
('Juan Pablo Rodríguez', 'jp.rodriguez@empresa.co', '1020304050'),
('María Fernanda López', 'mf.lopez@empresa.co',     '1020304051'),
('Carlos Andrés Gómez',  'ca.gomez@empresa.co',     '1020304052'),
('Laura Camila Torres',  'lc.torres@empresa.co',    '1020304053'),
('Diego Alejandro Ruiz', 'da.ruiz@empresa.co',      '1020304054'),
('Valentina Herrera',    'v.herrera@empresa.co',    '1020304055'),
('Santiago Morales',     's.morales@empresa.co',    '1020304056'),
('Daniela Castro',       'd.castro@empresa.co',     '1020304057'),
('Felipe Ramírez',       'f.ramirez@empresa.co',    '1020304058'),
('Camila Ortiz',         'c.ortiz@empresa.co',      '1020304059');

INSERT INTO equipos (placa_equipo, serial_equipo, observaciones) VALUES
('PL-00458', '5CG1234XYZ', 'Portátil HP ProBook, cargador incluido'),
('PL-00459', '5CG1234XZA', 'Portátil HP ProBook, cargador incluido'),
('PL-00460', 'DL7740A001', 'Portátil Dell Latitude 7440'),
('PL-00461', 'DL7740A002', 'Portátil Dell Latitude 7440'),
('PL-00462', 'LN14T00031', 'Portátil Lenovo ThinkPad T14'),
('PL-00463', 'LN14T00032', 'Portátil Lenovo ThinkPad T14'),
('PL-00464', 'AS15X00107', 'Portátil Asus VivoBook 15'),
('PL-00465', 'AS15X00108', 'Portátil Asus VivoBook 15'),
('PL-00466', 'MB14P00221', 'MacBook Pro 14, con funda'),
('PL-00467', 'MB14P00222', 'MacBook Pro 14, con funda');

INSERT INTO acuerdos_responsabilidad (id_usuario, fecha_firma, evidencia_firmada) VALUES
(1,  '2026-01-12', '/evidencias/acuerdo_jp_rodriguez.pdf'),
(2,  '2026-02-03', '/evidencias/acuerdo_mf_lopez.pdf'),
(3,  '2026-02-24', '/evidencias/acuerdo_ca_gomez.pdf'),
(4,  '2026-03-18', '/evidencias/acuerdo_lc_torres.pdf'),
(5,  '2026-04-09', '/evidencias/acuerdo_da_ruiz.pdf'),
(6,  '2026-05-21', '/evidencias/acuerdo_v_herrera.pdf'),
(7,  '2026-06-15', '/evidencias/acuerdo_s_morales.pdf'),
(8,  '2026-07-07', '/evidencias/acuerdo_d_castro.pdf'),
(9,  '2026-08-26', '/evidencias/acuerdo_f_ramirez.pdf'),
(10, '2026-09-30', '/evidencias/acuerdo_c_ortiz.pdf');

INSERT INTO actas_entrega (id_usuario, id_equipo, id_acuerdo, fecha_entrega, fecha_devolucion, cargo_al_momento) VALUES
(1,  1,  1,  '2026-01-13 09:00', NULL,               'Analista'),
(2,  2,  2,  '2026-02-04 09:00', NULL,               'Coordinadora'),
(3,  3,  3,  '2026-02-25 09:00', NULL,               'Desarrollador'),
(4,  4,  4,  '2026-03-19 09:00', '2026-08-15 16:00', 'Contadora'),
(5,  5,  5,  '2026-04-10 09:00', NULL,               'Analista de datos'),
(6,  6,  6,  '2026-05-22 09:00', NULL,               'Jefe de talento'),
(7,  7,  7,  '2026-06-16 09:00', NULL,               'Ejecutivo de ventas'),
(8,  8,  8,  '2026-07-08 09:00', NULL,               'Auxiliar contable'),
(9,  9,  9,  '2026-08-27 09:00', NULL,               'Diseñador'),
(10, 10, 10, '2026-10-01 09:00', NULL,               'Asistente legal');
