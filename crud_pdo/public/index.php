<?php
require_once __DIR__ . "/../config/db.php";
//consultar los registros
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Alumnos</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <h1>CRUD con PDO (Alumnos)</h1>
    <a class="btn" href="create.php">+Nuevo alumno</a>
    <h2>Listado</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Creado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
</body>
</html>
