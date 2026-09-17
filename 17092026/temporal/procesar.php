<?php
session_start();
$nombre = $_GET['nombre_dato'];
$edad = $_GET['edad_dato'];

if($edad>=18){
    header('Location: mayor.php');
    $_SESSION['nombre'] = $nombre; 
}else{
    header('Location: menor.html');

}

?>

