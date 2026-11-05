<?php 
declare(strict_types=1);
namespace App\Models;
use App\Core\Conexion;

// Modelo de MVC: para conectar con SQL y persistencia, sin generar HTML no leer formularios, solo para persistencia de datos y consultas a la base de datos
final class Usuario{
    //Recibe el administrador de la base de datos y la configuración de la conexión
    public function __construct(private Conexion $conexion){

    }
    public function buscarPorCorreo(string $correo): array|false{
        $consulta = $this->conexion->obtener()->prepare('SELECT id, nombre, correo, password_hash FROM usuarios WHERE correo = :correo');
        $consulta->execute(['correo' => $correo]);
        //fetch() devuelve un array asociativo con los datos del usuario si se encuentra, o false si no se encuentra
        return $consulta->fetch();
    }
    // funcion para generar un nuevo usuario en la base de datos
    public function registrar(string $nombre, string $correo, string $hash):int{
        
    }

}