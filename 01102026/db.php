<?php
// datos del servidor de la base de datos, MYSQL
$host="127.0.0.1:3306";
$user="root";
$pass="Aaron123";
$dbName="crud_app";
$conn = new mysqli($host,$user,$pass,$dbName);


if($conn->connect_error){
    die('Error de conexion'.$conn->connect_error);
}else{
    echo "Conexion exitosa";
}


?>