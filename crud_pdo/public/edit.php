<?php
require_once __DIR__ . "/../config/db.php";

// Tomar el ID desde la URL
$id = (int)($_GET['id'] ?? 0);
if($id <= 0){
    die("ID invalido");
}

// Buscar el registro por medio del ID

$sql= "SELECT id, nombre, email FROM alumnos WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([":id" => $id]);
$alumno=$stmt->fetch();
if($_SERVER['REQUEST_METHOD']== "POST"){
    $nombre = trim($_POST['nombre'] ?? "");
    $email = trim($_POST['email'] ?? "");

    if($nombre === "" || $email === ""){
        $error="Tonoto";
    }elseif( !filter_var($email,FILTER_VALIDATE_EMAIL)){
        $error="ERROR CON EL CORREO, NO ES VALIDO :V";

    }else{
        try{
            $sql=" UPDATE alumnos SET nombre= :nombre, email= :email WHERE id= :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ":nombre" => $nombre,
                ":email" => $email,
                ":id" => $id,
            ]);
            header("Location: index.php");
            exit;
        }catch(PDOException $e){
            $error = "Error en actualizar". $e->getMessage();
        }
    }

}else{
    $nombre = $alumno['nombre'];
    $correo = $alumno['email'];
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
    <h1>Editar</h1>
    <p><a href="index.php"> <- Regresar</a></p>
    <form method="post">
        <label for="">Nombre</label>
        <input type="text" name="nombre" required value="<?= htmlspecialchars($nombre)?>"><br><br>
        <label for="">Correo</label>
        <input type="email" name="email" required value="<?= htmlspecialchars($correo)?>"> <br><br>
        <button type="submit" >Actualizar</button>


    </form>
</body>
</html>