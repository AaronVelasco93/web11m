<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Usuario;

/** Controlador de la sección privada que lista las cuentas registradas. */
final class UsuarioController
{
    /** Recibe el modelo de usuarios para consultar datos sin incluir SQL en la vista. */
    public function __construct(private Usuario $usuarios)
    {
    }

    /** Comprueba sesión y muestra únicamente ID, nombre, correo y fecha de creación. */
    public function index(): void
    {
        // Se comprueba la sesión ANTES de consultar el modelo o generar la tabla.
        if (!isset($_SESSION['usuario'])) {
            $_SESSION['mensaje'] = ['tipo' => 'info', 'texto' => 'Inicia sesión para consultar los usuarios.'];
            // redireccionar() termina la ejecución; un visitante no continúa hasta listar().
            redireccionar('login');
        }
        // El arreglo usuarios queda disponible como $usuarios en app/Views/usuarios/index.php.
        vista('usuarios/index', ['titulo' => 'Usuarios', 'usuarios' => $this->usuarios->listar()]);
    }
}
