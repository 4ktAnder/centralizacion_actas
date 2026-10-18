-- =====================================================================
-- 01_crear_tablas.sql  |  Proyecto Helpdesk - Base de datos PostgreSQL
-- Listado 1: estructura de las cuatro tablas del modelo
-- =====================================================================

CREATE TABLE usuarios (
    id_usuario          SERIAL PRIMARY KEY,
    nombre_completo     VARCHAR(255) NOT NULL,
    correo              VARCHAR(255) NOT NULL,
    documento_identidad VARCHAR(50) UNIQUE
);

CREATE TABLE equipos (
    id_equipo     SERIAL PRIMARY KEY,
    placa_equipo  VARCHAR(100) NOT NULL,
    serial_equipo VARCHAR(150),
    observaciones TEXT,
    estado        BOOLEAN DEFAULT TRUE
);

CREATE TABLE acuerdos_responsabilidad (
    id_acuerdo        SERIAL PRIMARY KEY,
    id_usuario        INT NOT NULL,
    fecha_firma       DATE,
    evidencia_firmada VARCHAR(255),
    estado            BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);

CREATE TABLE actas_entrega (
    id_acta             SERIAL PRIMARY KEY,
    id_usuario          INT NOT NULL,
    id_equipo           INT NOT NULL,
    id_acuerdo          INT,
    fecha_entrega       TIMESTAMP NOT NULL,
    fecha_devolucion    TIMESTAMP DEFAULT NULL,
    cargo_al_momento    VARCHAR(150),
    gerencia_al_momento VARCHAR(150),
    evidencia_firmada   VARCHAR(255),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario),
    FOREIGN KEY (id_equipo)  REFERENCES equipos(id_equipo),
    FOREIGN KEY (id_acuerdo) REFERENCES acuerdos_responsabilidad(id_acuerdo)
);
