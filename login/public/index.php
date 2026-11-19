<?php
declare(strict_types=1);

/**
 * ARCHIVO DE ENTRADA (Front Controller).
 * Recibe las solicitudes del navegador, prepara los servicios comunes y elige
 * una acción del controlador. No contiene las consultas SQL ni el HTML de las vistas.
 * strict_types exige respetar los tipos declarados en las llamadas hechas desde este archivo.
 */

// use permite escribir nombres cortos para las clases organizadas con namespaces.
use App\Controllers\AutenticacionController;
use App\Controllers\UsuarioController;
use App\Core\Conexion;
use App\Core\Seguridad;
use App\Models\Usuario;

// Front Controller: todas las solicitudes del sistema pasan por este archivo.
// El autoload sencillo convierte App\Models\Usuario en app/Models/Usuario.php.
spl_autoload_register(static function (string $clase): void {
    // PHP llama a esta función cuando se utiliza una clase que todavía no está cargada.
    $prefijo = 'App\\';
    if (str_starts_with($clase, $prefijo)) {
        // dirname(__DIR__) obtiene la raíz; __DIR__ es la carpeta public de este archivo.
        $archivo = dirname(__DIR__) . '/app/' . str_replace('\\', '/', substr($clase, strlen($prefijo))) . '.php';
        if (is_file($archivo)) {
            // Solo se carga un archivo existente del namespace interno App.
            require $archivo;
        }
    }
});
// Las funciones globales no son clases, por lo que se incluyen explícitamente.
require dirname(__DIR__) . '/app/Support/funciones.php';

// Evita almacenar pantallas privadas en caché y añade cabeceras básicas de protección.
ini_set('display_errors', '0');
// UTF-8 permite mostrar acentos; no-store pide no conservar estas páginas en caché.
header('Content-Type: text/html; charset=UTF-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');
// Impide incrustar la aplicación en un iframe de otra página.
header('X-Frame-Options: DENY');
// CSP limita los recursos al mismo sitio y deshabilita JavaScript en este ejemplo.
header("Content-Security-Policy: default-src 'self'; style-src 'self'; img-src 'self' data:; script-src 'none'; form-action 'self'; frame-ancestors 'none'; base-uri 'self'");
// No envía la dirección completa de esta página como referencia a sitios externos.
header('Referrer-Policy: same-origin');

try {
    // 1. Leer configuración. require devuelve el arreglo retornado por config.php.
    $config = require dirname(__DIR__) . '/config/config.php';
    // 2. Recuperar o crear la sesión antes de enviar cualquier HTML al navegador.
    Seguridad::iniciarSesion();
    // 3. Entregar al modelo su conexión y compartir ese modelo con los controladores.
    // Crear Conexion no abre MySQL todavía; obtener() lo hace cuando se necesitan datos.
    $usuarios = new Usuario(new Conexion($config['database']));
    $auth = new AutenticacionController($usuarios);
    $controladorUsuarios = new UsuarioController($usuarios);
    // 4. Leer ?r=login, ?r=registro o ?r=usuarios. Sin r se muestra el login.
    $ruta = $_GET['r'] ?? 'login';
    // GET suele mostrar páginas; POST envía datos de formularios para procesarlos.
    $metodo = $_SERVER['REQUEST_METHOD'];
    // Lista cerrada de rutas: el visitante nunca elige el archivo PHP que se incluye.
    $rutas = [
        // Cada par [objeto, método] representa una acción que PHP puede ejecutar.
        'GET' => ['login' => [$auth, 'formularioLogin'], 'registro' => [$auth, 'formularioRegistro'], 'usuarios' => [$controladorUsuarios, 'index']],
        'POST' => ['login' => [$auth, 'login'], 'registro' => [$auth, 'registrar'], 'logout' => [$auth, 'logout']],
    ];
    // 5. Rechazar rutas desconocidas antes de verificar o ejecutar una acción.
    if (!is_string($ruta) || !isset($rutas[$metodo][$ruta])) {
        vista('error', ['titulo' => 'Página no encontrada', 'detalle' => 'La página solicitada no está disponible.'], 404);
    } elseif ($metodo === 'POST' && !Seguridad::csrfValido($_POST['csrf'] ?? null)) {
        // Un token inválido no llega al controlador ni puede escribir en la base.
        vista('error', ['titulo' => 'Formulario vencido', 'detalle' => 'Vuelve a abrir el formulario e intenta de nuevo.'], 403);
    } else {
        // 6. Ejecutar únicamente el método previamente autorizado por la tabla de rutas.
        call_user_func($rutas[$metodo][$ruta]);
    }
} catch (Throwable $error) {
    // Captura fallos de conexión, configuración y otras excepciones de esta solicitud.
    // Los detalles técnicos se registran en el servidor, nunca se imprimen al visitante.
    error_log('[Login MVC] ' . $error->getMessage());
    // Este nombre de respaldo permite mostrar el error aunque no se pudiera leer .env.
    $config ??= ['app_name' => 'Aula Acceso'];
    vista('error', ['titulo' => 'No fue posible completar la operación', 'detalle' => 'Intenta nuevamente. Si el problema continúa, revisa la conexión y la instalación de la base de datos.'], 500);
}
