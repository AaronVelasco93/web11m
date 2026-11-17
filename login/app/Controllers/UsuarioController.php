<?php 
declare(strict_types =1);
namespace App\Controllers;

use App\Models\Usuario;
//controlador de la seccion privada que lista las cuentas registradas
final class UsuarioController{
    //funcion que recibe el modelo de usuarios para consultar datos sin incluir SQL en la vista
    public function __construct(private Usuario $usuarios)
    {
    }
    //comprobar la session y muestra unicamente el ID, nombre, correo, fecha de cracion
    public function index():void{
        if(!isset($_SESSION['usuario'])){
                $_SESSION['mensaje']=['tipo'=>'info','texto'=>'Inicia session para consulta de usuario'];
                redireccionar('login');
        }
        vistas('usuarios/index',['titulo'=>'Usuarios','usuarios'=>$this->usuarios->listar()]); 
    }


}
?>