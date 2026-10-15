<?php 
include ('db.php');
$id = $_GET['id'];

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];
    
    $sql="UPDATE usuarios SET nombre='$nombre', email='$correo', telefono='$telefono' WHERE id=$id";

    if($conn->query($sql) === TRUE){
        header('Location: index.php');
        exit();
    }else{
        echo "Error en la actualizacion";
    }
      
}
?>