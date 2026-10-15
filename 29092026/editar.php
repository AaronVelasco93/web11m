<?php
include ('db.php');
$id = $_GET['id'];
// consulta en SQL
$sql = "SELECT * FROM usuarios WHERE id = $id";
$result=$conn->query($sql);
$row = $result->fetch_assoc();



?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
<h1>Editar</h1>
<form  method="POST" action="editar2.php?id=<?php echo $id;?>">
    <label for="nombre">Nombre</label>
    <input type="text" name="nombre" value="<?php echo $row['nombre']; ?>">
    <br>
    <label for="email">Correo</label>
    <input type="email" name="correo" value="<?php echo $row['email'];?>">
    <br>
    <label for="telefono">Telefono</label>
    <input type="text" name="telefono" value="<?php echo $row['telefono'];?>">
    <br>
    <button type="submit" >Actualizar</button>
    <a href="index.php">Inicio</a>
</form>
</body>
</html>