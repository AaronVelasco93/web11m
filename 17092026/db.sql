-- crear la base de datos
CREATE DATABASE crud_app;
USE crud_app;
-- crear latabla
CREATE TABLE usuarios(
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR (150) NOT NULL,
    email VARCHAR (150) NOT NULL,
    telefono VARCHAR (15) NOT NULL
);
