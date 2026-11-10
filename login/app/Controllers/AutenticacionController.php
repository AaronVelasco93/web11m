<?php
declare(strict_types=1);

namespace App\Controllers;

// El controlador coordina servicios: Seguridad maneja la sesión y Usuario ejecuta SQL.
use App\Core\Seguridad;
use App\Models\Usuario;
use PDOException;

/** Controlador MVC: valida la entrada, usa el modelo y decide qué pantalla mostrar. */
final class AutenticacionController
{
    /** Recibe el modelo. No necesita conocer cómo se construye la conexión PDO. */
    public function __construct(private Usuario $usuarios)
    {
    }

    /** Presenta el formulario de acceso o redirige al listado si ya existe una sesión autenticada. */
    public function formularioLogin(): void
    {
        // Esta acción responde a GET ?r=login; no comprueba contraseñas ni modifica la base.
        if (isset($_SESSION['usuario'])) {
            redireccionar('usuarios');
        }
        // Los datos iniciales permiten a la vista mostrar los campos vacíos sin variables indefinidas.
        vista('auth/login', ['titulo' => 'Iniciar sesión', 'errores' => [], 'correo' => '']);
    }

    /** Presenta el registro solo a visitantes sin sesión; el dashboard es únicamente de consulta. */
    public function formularioRegistro(): void
    {
        // La regla del proyecto exige cerrar sesión antes de registrar una nueva cuenta.
        if (isset($_SESSION['usuario'])) {
            redireccionar('usuarios');
        }
        vista('auth/registro', ['titulo' => 'Crear cuenta', 'errores' => [], 'nombre' => '', 'correo' => '']);
    }

    /** Recibe POST del registro. Valida, genera el hash y guarda una cuenta sin iniciar sesión automáticamente. */
    public function registrar(): void
    {
        // La restricción se aplica también al POST: ocultar un botón no impide una petición manual.
        if (isset($_SESSION['usuario'])) {
            redireccionar('usuarios');
        }
        // PASO 1. Leer el POST. campo() descarta arreglos y limpia espacios cuando corresponde.
        $nombre = campo('nombre');
        // Normalizar el correo permite buscarlo de la misma forma al iniciar sesión.
        $correo = strtolower(campo('correo'));
        // Las contraseñas conservan sus espacios originales; nunca se les aplica trim().
        $password = campo('password', false);
        $confirmacion = campo('confirmacion', false);
        // PASO 2. Acumular todos los errores para mostrarlos juntos, sin guardar parcialmente.
        $errores = [];
        // mb_strlen cuenta caracteres Unicode; strlen cuenta bytes.
        if (mb_strlen($nombre) < 2 || mb_strlen($nombre) > 100) {
            $errores[] = 'Escribe un nombre de entre 2 y 100 caracteres.';
        }
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL) || strlen($correo) > 190) {
            // Validar el formato no envía correos ni comprueba la propiedad de la dirección.
            $errores[] = 'Escribe un correo electrónico válido de hasta 190 caracteres.';
        }
        // PASSWORD_DEFAULT usa bcrypt en PHP 8.2: el máximo de 72 bytes evita truncamientos.
        if (mb_strlen($password) < 8 || strlen($password) > 72 || str_contains($password, "\0")) {
            $errores[] = 'La contraseña requiere al menos 8 caracteres y un máximo de 72 bytes, sin caracteres nulos.';
        }
        if ($password !== $confirmacion) {
            $errores[] = 'Las contraseñas no coinciden.';
        }
        // PASO 3. Solo consultar/guardar cuando TODAS las validaciones anteriores fueron correctas.
        if (!$errores) {
            try {
                if ($this->usuarios->buscarPorCorreo($correo)) {
                    $errores[] = 'Ese correo ya está registrado.';
                } else {
                    // Un hash es de una sola vía: no se descifra. PHP agrega una sal aleatoria.
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    // La contraseña original y su confirmación no se pasan al modelo ni se guardan en sesión.
                    $this->usuarios->registrar($nombre, $correo, $hash);
                    $_SESSION['mensaje'] = ['tipo' => 'exito', 'texto' => 'Cuenta creada. Ya puedes iniciar sesión.'];
                    // PASO 4. Redirigir con 303 evita repetir el alta al actualizar la página final.
                    redireccionar('login');
                }
            } catch (PDOException $error) {
                // La restricción UNIQUE resuelve también registros simultáneos del mismo correo.
                if ($error->getCode() !== '23000') {
                    // Los errores distintos de integridad se dejan al manejador general de errores.
                    throw $error;
                }
                $errores[] = 'Ese correo ya está registrado.';
            }
        }
        // Nunca se vuelven a enviar contraseñas a las vistas al fallar una validación.
        // compact() arma el arreglo de datos a partir de variables locales; 422 indica datos no válidos.
        vista('auth/registro', compact('nombre', 'correo', 'errores') + ['titulo' => 'Crear cuenta'], 422);
    }

    /** Recibe correo y contraseña por POST; verifica el hash y crea una sesión autenticada. */
    public function login(): void
    {
        // PASO 1. Leer credenciales. Este método responde al POST, no al GET del formulario.
        $correo = strtolower(campo('correo'));
        $password = campo('password', false);
        $usuario = false;
        // Antes de buscar se descartan formatos inválidos y contraseñas que bcrypt no debe aceptar.
        if (filter_var($correo, FILTER_VALIDATE_EMAIL) && strlen($correo) <= 190 && strlen($password) <= 72 && !str_contains($password, "\0")) {
            $usuario = $this->usuarios->buscarPorCorreo($correo);
        }
        // Mensaje único: no revela si el correo existe o cuál de los dos datos falló.
        // || hace cortocircuito: si no hay usuario, no intenta leer password_hash de false.
        // password_verify() aplica la verificación apropiada usando los datos del hash almacenado.
        if (!$usuario || !password_verify($password, $usuario['password_hash'])) {
            vista('auth/login', ['titulo' => 'Iniciar sesión', 'correo' => $correo, 'errores' => ['Correo o contraseña incorrectos.']], 422);
            return;
        }
        // PASO 2. Mantener actualizado el hash solo DESPUÉS de verificar una contraseña correcta.
        if (password_needs_rehash($usuario['password_hash'], PASSWORD_DEFAULT)) {
            $this->usuarios->actualizarHash((int) $usuario['id'], password_hash($password, PASSWORD_DEFAULT));
        }
        // Regenerar el ID evita reutilizar una sesión fijada antes de iniciar sesión.
        session_regenerate_id(true);
        // PASO 3. Crear el estado autenticado que revisa UsuarioController antes de listar.
        $_SESSION['usuario'] = ['id' => (int) $usuario['id'], 'nombre' => $usuario['nombre']];
        unset($_SESSION['csrf']);
        // El próximo formulario pedirá un token nuevo con Seguridad::token().
        $_SESSION['mensaje'] = ['tipo' => 'exito', 'texto' => 'Sesión iniciada correctamente.'];
        redireccionar('usuarios');
    }

    /** Destruye la sesión actual y devuelve al formulario de inicio. Solo se llama mediante POST. */
    public function logout(): void
    {
        // El Front Controller ya comprobó el verbo POST y el token CSRF antes de llegar aquí.
        Seguridad::cerrarSesion();
        redireccionar('login');
    }
}
