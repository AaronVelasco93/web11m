<?php
    include('db.php');
    
    $id= $_GET['id'];
    $sql= "SELECT * FROM usuarios WHERE id=$id";
    $result= $conn->query($sql);
    
    $row= $result->fetch_assoc();
    if( $_SERVER['REQUEST_METHOD'] === 'POST' ){
        $nombre= $_POST['nombre'];
        $email= $_POST['email'];
        $telefono= $_POST['telefono'];

        $sql= "UPDATE usuarios SET nombre='$nombre', email='$email', telefono='$telefono' WHERE id=$id";

        if($conn->query($sql) === TRUE){
            header('Location: index.php');
            exit();
        }else{
            echo "Error: ".$sql."<br>".$conn->error;
        }

    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar</title>
</head>
<body>
    <h1>Editar Usuario</h1>
    <form action="edit.php?id=<?php echo $id; ?>" method="post">

        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" id="nombre" value="<?php echo $row['nombre']; ?>">
        <br>
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" value="<?php echo $row['email']; ?>">
        <br>
        <label for="telefono">Teléfono:</label>
        <input type="text" name="telefono" id="telefono" value="<?php echo $row['telefono']; ?>">
        <br>
        <input type="submit" value="Actualizar">
    </form>

</body>
</html>