<?php
declare(strict_types=1);

namespace App\Core;

/** Reúne la configuración de sesión y la protección de formularios CSRF. */
final class Seguridad
{
    /** No recibe datos. Inicia una sesión con cookies y vence la sesión tras 30 min de inactividad. */
    public static function iniciarSesion(): void
    {
        // Rechaza IDs de sesión no inicializados por el servidor y evita recibirlos por la URL.
        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        session_name('LOGINMVCSESSID');
        // La cookie contiene un identificador; el usuario autenticado se guarda en $_SESSION.
        session_set_cookie_params([
            // 0 crea una cookie de sesión. El navegador puede restaurarla al recuperar pestañas.
            'lifetime' => 0,
            // Se envía para las rutas del sitio; el nombre propio evita confundirla con otras apps.
            'path' => '/',
            // En HTTPS se limita su envío a conexiones seguras. En XAMPP HTTP será false.
            'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            // Impide leer la cookie con JavaScript; no sustituye las demás protecciones.
            'httponly' => true,
            // Restringe ciertos envíos desde otros sitios; además se verifican tokens CSRF.
            'samesite' => 'Lax',
        ]);
        // Recupera los datos de sesión del lado del servidor o crea una sesión nueva.
        session_start();
        // 1800 segundos equivalen a 30 minutos. La expiración se revisa en cada solicitud.
        if (isset($_SESSION['ultima_actividad']) && time() - $_SESSION['ultima_actividad'] > 1800) {
            $_SESSION = [];
            // Descarta el identificador anterior y conserva solamente el mensaje informativo.
            session_regenerate_id(true);
            $_SESSION['mensaje'] = ['tipo' => 'info', 'texto' => 'Tu sesión venció. Inicia sesión nuevamente.'];
        }
        // Al haber actividad, se actualiza el tiempo de referencia para la siguiente petición.
        $_SESSION['ultima_actividad'] = time();
    }

    /** Devuelve un token aleatorio de la sesión, creándolo si aún no existe. */
    public static function token(): string
    {
        // ??= solo asigna si el token no existe. 32 bytes aleatorios se convierten en 64 caracteres hexadecimales.
        return $_SESSION['csrf'] ??= bin2hex(random_bytes(32));
    }

    /** Recibe el token enviado por POST y devuelve si coincide con el de la sesión. */
    public static function csrfValido(mixed $token): bool
    {
        // Se rechazan arreglos y entradas ausentes. hash_equals compara sin depender de prefijos coincidentes.
        return is_string($token) && isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $token);
    }

    /** Destruye datos y cookie de la sesión actual; no elimina al usuario de la base. */
    public static function cerrarSesion(): void
    {
        // Limpiar la sesión no ejecuta DELETE en la tabla usuarios.
        $_SESSION = [];
        $parametros = session_get_cookie_params();
        // El mismo nombre y ruta permiten vencer la cookie correcta en el navegador.
        setcookie(session_name(), '', [
            'expires' => time() - 3600,
            'path' => $parametros['path'],
            'secure' => $parametros['secure'],
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        // Elimina el almacenamiento de esta sesión en el servidor.
        session_destroy();
    }
}
