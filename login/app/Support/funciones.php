<?php
declare(strict_types=1);

/** FUNCIONES COMPARTIDAS: evitan repetir presentación, redirecciones y lectura de campos.
 * Las vistas pueden llamarlas porque public/index.php incluye este archivo antes de renderizar.
 */
/** Recibe un valor y devuelve texto escapado para HTML, evitando interpretar etiquetas del usuario. */
function escapar(mixed $valor): string
{
    // ENT_QUOTES protege ambas comillas; ENT_SUBSTITUTE reemplaza secuencias UTF-8 inválidas.
    // Se usa al imprimir en HTML, no para guardar datos ni para preparar SQL.
    return htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Recibe una ruta conocida y devuelve una URL relativa compatible con /Login/public/ y php -S. */
function url(string $ruta): string
{
    // rawurlencode convierte símbolos especiales en una representación válida para la URL.
    return 'index.php?r=' . rawurlencode($ruta);
}

/** Recibe el nombre de la ruta, redirige y termina la solicitud para no ejecutar más código. */
function redireccionar(string $ruta): never
{
    // 303 indica que el navegador debe abrir el destino mediante GET después de un POST.
    header('Location: ' . url($ruta), true, 303);
    exit;
}

/** Recibe vista y datos del controlador. Incluye las plantillas; no devuelve contenido. */
function vista(string $nombreVista, array $datos = [], int $estado = 200): void
{
    // 200 muestra una página normal; el controlador puede indicar 403, 404, 422 o 500.
    http_response_code($estado);
    // EXTR_SKIP evita que los datos sobrescriban variables internas de esta función.
    extract($datos, EXTR_SKIP);
    // Ejemplo: ['correo' => 'a@b.com'] queda disponible como $correo dentro de la vista.
    $nombreApp = $GLOBALS['config']['app_name'];
    $mensaje = $_SESSION['mensaje'] ?? null;
    // Es un mensaje de una sola lectura: desaparece después de ser mostrado.
    unset($_SESSION['mensaje']);
    // El nombre de la vista lo fija el controlador, nunca se toma directamente de $_GET.
    // Se ensamblan tres partes: cabecera compartida, contenido y pie compartido.
    require dirname(__DIR__) . '/Views/partials/header.php';
    require dirname(__DIR__) . '/Views/' . $nombreVista . '.php';
    require dirname(__DIR__) . '/Views/partials/footer.php';
}

/** Recibe un campo POST y devuelve una cadena; arreglos u objetos se consideran vacíos. */
function campo(string $nombre, bool $recortar = true): string
{
    // ?? devuelve '' si el formulario no envió ese campo.
    $valor = $_POST[$nombre] ?? '';
    if (!is_string($valor)) {
        return '';
    }
    // trim() limpia nombre y correo. Para contraseñas se llama campo('password', false).
    return $recortar ? trim($valor) : $valor;
}
