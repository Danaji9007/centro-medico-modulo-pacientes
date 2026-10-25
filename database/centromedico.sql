-- Script de creacion de la base de datos y las tablas del centro medico.
-- Evidencia GA7-220501096-AA2-EV01 - Codificacion y pruebas del proyecto.
--
-- Ejecutar en MySQL/MariaDB con un usuario con permisos de administracion:
--     mysql -u root -p < database/centromedico.sql

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP DATABASE IF EXISTS centromedico;

CREATE DATABASE centromedico
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE centromedico;

-- Tabla de roles del sistema.
CREATE TABLE roles (
    id_rol INT AUTO_INCREMENT PRIMARY KEY,
    nombre_rol VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de usuarios del sistema.
CREATE TABLE usuarios (
    idUsuario INT AUTO_INCREMENT PRIMARY KEY,
    usuLogin VARCHAR(50) NOT NULL UNIQUE,
    usuPassword VARCHAR(255) NOT NULL,
    usuEstado VARCHAR(20) NOT NULL DEFAULT 'Activo',
    usuRol VARCHAR(50) NOT NULL,
    roles_id_rol INT NULL,
    CONSTRAINT fk_usuarios_roles
        FOREIGN KEY (roles_id_rol)
        REFERENCES roles (id_rol)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de pacientes.
CREATE TABLE pacientes (
    idPaciente INT AUTO_INCREMENT PRIMARY KEY,
    pacIdentificacion VARCHAR(20) NOT NULL UNIQUE,
    pacNombres VARCHAR(100) NOT NULL,
    pacApellidos VARCHAR(100) NOT NULL,
    pacFechaNacimiento DATE NULL,
    pacSexo CHAR(1) NULL,
    usuarios_idUsuario INT NULL,
    CONSTRAINT fk_pacientes_usuarios
        FOREIGN KEY (usuarios_idUsuario)
        REFERENCES usuarios (idUsuario)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de medicos.
CREATE TABLE medicos (
    idMedico INT AUTO_INCREMENT PRIMARY KEY,
    medIdentificacion VARCHAR(20) NOT NULL UNIQUE,
    medNombres VARCHAR(100) NOT NULL,
    medApellidos VARCHAR(100) NOT NULL,
    medEspecialidad VARCHAR(100) NOT NULL,
    medTelefono VARCHAR(20) NULL,
    medCorreo VARCHAR(100) NULL,
    usuarios_idUsuario INT NULL,
    CONSTRAINT fk_medicos_usuarios
        FOREIGN KEY (usuarios_idUsuario)
        REFERENCES usuarios (idUsuario)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Datos iniciales: roles basicos del sistema.
INSERT INTO roles (nombre_rol) VALUES
    ('Administrador'),
    ('Medico'),
    ('Recepcionista'),
    ('Paciente');

SET FOREIGN_KEY_CHECKS = 1;
