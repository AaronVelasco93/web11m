<?php
declare(strict_types=1);
//espcio de trabajo para la conexion a la base de datos
namespace App\Core;
/*
PDO es la clase incluida en PHP para la conexión a bases
de datos, es una interfaz que permite acceder a diferentes 
sistemas de gestión de bases de datos (DBMS) de manera 
uniforme. Proporciona una forma segura y eficiente de 
interactuar con bases de datos, permitiendo ejecutar  
*/

use PDO;
final class Conexion{
    private ?PDO $pdo = null; 
    public function __construct(private array $configuracion)
    {
    }
    public function obtener():PDO
    {
        if($this->pdo ===null){
            $c = $this->configuracion;
            //dsn = data source name
            //motor, servidor, base de datos, utg8mb4 adminite unicode y emojis
            $dsn= sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $c['host'], $c['port'], $c['dbname']);
            $this->pdo= new PDO($dsn, $c['username'], $c['password'],[
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
            // las fechas generadas en MYSQL se guardan en UTC, para que se guarden en la zona horaria de la aplicación, se debe establecer la zona horaria en la conexión
            $this->pdo->exec("SET time_zone = '+00:00'");
        }
        return $this->pdo;
    }
}