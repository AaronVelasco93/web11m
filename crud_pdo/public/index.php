<?php
require_once __DIR__ . "/../config/db.php";
//consultar los registros

$query= "SELECT id, nombre, email, created_at FROM alumnos ORDER BY id DESC";
$stmt = $pdo->query($query);
$alumnos=$stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Alumnos</title>
    <link rel="stylesheet" href="css/estilos.css">
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
            <?php if(count($alumnos) === 0): ?>
                <tr>
                    <td colspan="5">No hay registros</td>
                </tr>
            <?php else:?>
                <?php foreach($alumnos as $a):?>
                    <tr>
                        <td><?= htmlspecialchars($a["id"])?></td>
                        <td><?= htmlspecialchars($a['nombre'])?></td>
                        <td><?= htmlspecialchars($a['email'])?></td>
                        <td><?= htmlspecialchars($a['created_at'])?></td>
                        <td>
                            <a href="edit.php?id=<?=htmlspecialchars($a["id"])?>">Editar</a>
                            <a href="delete.php?id=<?=htmlspecialchars($a["id"])?>" onclick="return confirm('Quieres eliminar el registro')" >Eliminar</a>
                        </td>
                    </tr> 
                <?php endforeach; ?> 
            <?php endif;?>  
        </tbody>
    </table>
</body>
</html>
