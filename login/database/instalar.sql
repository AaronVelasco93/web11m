-- INSTALACIÓN MANUAL: importar este archivo desde la pestaña Importar de phpMyAdmin.
-- No incluye credenciales, usuarios iniciales, DELETE ni DROP.
-- Si tu hosting utiliza otro nombre de base, cambia las dos referencias a login_mvc
-- o selecciona tu base e importa únicamente el bloque CREATE TABLE de este archivo.

-- CREATE DATABASE prepara el contenedor de las tablas; IF NOT EXISTS evita recrearlo.
-- utf8mb4 admite Unicode. La colación define cómo se comparan y ordenan los textos.
CREATE DATABASE IF NOT EXISTS login_mvc CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Selecciona la base en la que se ejecutará la siguiente instrucción CREATE TABLE.
USE login_mvc;

-- La tabla representa las cuentas. El modelo Usuario es quien consulta e inserta sus filas.
CREATE TABLE IF NOT EXISTS usuarios (
    -- Identificador numérico positivo, generado por MariaDB/MySQL al insertar una fila.
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    -- NOT NULL obliga a incluir el campo; las validaciones de contenido ocurren en el controlador.
    nombre VARCHAR(100) NOT NULL,
    -- El controlador normaliza el correo a minúsculas antes de guardarlo o buscarlo.
    correo VARCHAR(190) NOT NULL,
    -- Almacena el resultado de password_hash(), nunca la contraseña original.
    -- 255 deja espacio para algoritmos de hash cuyos resultados sean más largos.
    password_hash VARCHAR(255) NOT NULL,
    -- La conexión fija UTC; CURRENT_TIMESTAMP registra automáticamente la fecha de alta.
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    -- La clave primaria identifica una única fila y permite búsquedas por su ID.
    PRIMARY KEY (id),
    -- La clave única impide registrar dos veces el mismo correo, incluso en solicitudes simultáneas.
    UNIQUE KEY uq_usuarios_correo (correo)
-- InnoDB es el motor de almacenamiento. La tabla también utiliza Unicode y su colación.
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
