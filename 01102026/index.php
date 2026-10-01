<?php
    include('db.php');
    $consulta="SELECT * FROM usuarios";
    $result= $conn->query($consulta);
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de usuarios</title>
</head>
<body>
    <h1>Usuarios</h1>
    <a href="create.php">Agregar usuario</a>
    <table border="1" >
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Email</th>
            <th>Telefono</th>
            <th>Acciones</th>
        </tr>
        <?php  while($row=$result->fetch_assoc()){ ?>
        <tr>
            <td><?php echo $row['id'];?></td>
            <td><?php echo $row['nombre'];?></td>
            <td><?php echo $row['email'];?></td>
            <td><?php echo $row['telefono'];?></td>
            <td>
                <a href="edit.php?id=<?php echo $row['id'];?>">Editar</a>
                <a href="delete.php?id=<?php echo $row['id'];?>">Eliminar</a>
            </td>
        </tr>
      <?php } ?>  
    </table>

</body>
</html>