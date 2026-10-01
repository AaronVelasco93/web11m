<?php 
    include('db.php');
    if($_SERVER['REQUEST_METHOD'] === 'POST' ){
        $nombre = $_POST['nombre'];
        $email = $_POST['correo'];
        $telefono = $_POST['telefono'];

        $sql="INSERT INTO usuarios (nombre, email, telefono) VALUES ('$nombre','$email','$telefono')";

        if($conn->query($sql) === TRUE){
            header ('Location: index.php');
            exit();
        }else{
            echo "Error".$sql."<br>".$conn->error;
        }

    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Crear usuario</h1>
    <form action="create.php" method="POST">
        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" required placeholder="Ingresa tu nombre" maxlength="100">
        <br>
        <label for="correo">Correo:</label>
        <input type="email" name="correo" required placeholder="Ingresa tu correo" maxlength="100">
        <br>
        <label for="telefono">Telefono:</label>
        <input type="text" name="telefono" required placeholder="Ingresa tu numero de telefono" maxlength="15" >
        <br>
        <button type="submit">Enviar registro</button>
    </form>
    <a href="index.php">Inicio de sistema</a>    

</body>
</html>