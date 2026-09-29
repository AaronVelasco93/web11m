<?php
    include('db.php');
    $id = $_GET['id'];
    echo "<br>"."<h1>".$id."</h1>";
    $sql = "DELETE FROM usuarios WHERE id =$id";
    if($conn->query($sql)===TRUE){
        header('Location: index.php');

    }else{
        echo "Error al eliminar";
    }


?>