<?php 
require_once __DIR__ . "/../config/db.php";
//variables para mostrar errores
$error= "";
$nombre= "";
$email= "";
//Si el formulario es por el metodo POST
if($_SERVER['REQUEST_METHOD'] === "POST"){
    //Obtener los datos del Form
    $nombre = trim($_POST['nombre'] ?? "");
    $email  = trim($_POST['email'] ?? "");

    //Validaciones basicas
    if($nombre === "" || $email === ""){
        $error = "Todos los campos son obligatorios";
    }elseif(!filter_var($email,FILTER_VALIDATE_EMAIL)){
        $error="El email no es valido";
    }else{
        try{
            //insertar la consulta preparada
            $sql="INSERT INTO alumnos (nombre, email) VALUES (:nombre, :email)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ":nombre" =>$nombre,
                ":email" => $email,
            ]);
            // regresar a el listado -> index.php
            header("Location: index.php");
            exit;

        }catch(PDOException $e){
            $error = "Error al guardar". $e->getMessage();
        }
    }

}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./css/estilos.css">
</head>
<body>
    <h1>Crear Alumno</h1>
    <p><a href="index.php"><- Regresar</a></p>
    <?php  if($error): ?>
        <p><?= htmlspecialchars($error)?></p>
    <?php endif; ?>
        <form method="POST">
            <label for="">Nombre: </label>
            <input type="text" name="nombre" required value="<?= htmlspecialchars($nombre)?>"> <br><br>
            <label for="">Correo:</label>
            <input type="text" name="email" required value="<?= htmlspecialchars($email)?>"> <br><br>
            <button type="submit" >Guardar</button>
        </form>
</body>
</html>