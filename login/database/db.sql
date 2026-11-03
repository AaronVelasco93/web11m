CREATE DATABASE IF NOT EXISTS login_mvc CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

USE login_mvc;
CREATE TABLE IF NOT EXISTS usuarios (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(250) NOT NULL,
    correo VARCHAR(250) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_usuarios_correo (correo)
);