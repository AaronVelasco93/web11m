<?php
//cargar la configuracion
$config = require __DIR__ . "/config.php";

// Extraer las variables de entorno
$host    = $config["DB_HOST"];
$dbname  = $config["DB_NAME"];
$user    = $config["DB_USER"];
$pass    = $config["DB_PASS"];
$charset = $config["DB_CHARSERT"];

// Opciones recomendadas para usar PDO
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,//lanzar excepciones de Mysql
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
    ];
try{
    //DSN:indica el motor + host + nombreDB+chrset || configuracion
    $dsn ="mysql:host=$host;dbname=$dbname;charset=$charset";
    $pdo = new PDO($dsn, $user, $pass, $options);

}catch(PDOException $e){
    die("Error en la DB". $e->getMessage());
}

?>