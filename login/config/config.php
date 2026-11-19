<?php
declare(strict_types=1);

/** CONFIGURACIÓN: transforma valores externos en un arreglo que utiliza la aplicación.
 * No abre conexiones, no genera HTML y no escribe las variables en el entorno del sistema.
 */
// PHP no lee .env automáticamente. Aquí lo leemos directamente, sin un cargador
// adicional ni Composer. Se utiliza formato INI simple, sin interpolación ${...}.
$archivo = dirname(__DIR__) . '/.env';
// Antes de leer se comprueba que el archivo exista y que PHP tenga permiso de lectura.
if (!is_file($archivo) || !is_readable($archivo)) {
    throw new RuntimeException('Falta el archivo .env. Consulta README.md.');
}
// false evita secciones; INI_SCANNER_RAW conserva los valores como texto sin convertir tipos.
$variables = parse_ini_file($archivo, false, INI_SCANNER_RAW);
if ($variables === false) {
    throw new RuntimeException('El archivo .env no tiene un formato válido.');
}

/** Recibe una clave y devuelve el valor del entorno real o, si no existe, del .env. */
$leer = static function (string $clave) use ($variables): string {
    // use ($variables) da a esta función anónima acceso al arreglo del ámbito exterior.
    $valor = getenv($clave);
    // Comparar con false distingue una variable inexistente de una contraseña vacía válida.
    if ($valor === false) {
        $valor = $variables[$clave] ?? null;
    }
    if (!is_string($valor)) {
        // Fallar explícitamente evita iniciar con una configuración incompleta sin advertirlo.
        throw new RuntimeException('Falta una variable de configuración: ' . $clave);
    }
    return $valor;
};

// Los datos sensibles solo vienen del entorno o del .env, nunca de las clases.
$base = $leer('DB_DATABASE');
// Un puerto válido es un entero entre 1 y 65535; el .env se lee inicialmente como texto.
$puerto = filter_var($leer('DB_PORT'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 65535]]);
if (!preg_match('/^[a-zA-Z0-9_]{1,64}$/', $base) || $puerto === false) {
    throw new RuntimeException('Revisa DB_DATABASE y DB_PORT en .env.');
}
// Las claves de este arreglo son las que luego recibe el constructor de Conexion.
return [
    'app_name' => $leer('APP_NAME'),
    'database' => [
        'host' => $leer('DB_HOST'),
        'port' => $puerto,
        'name' => $base,
        'username' => $leer('DB_USERNAME'),
        'password' => $leer('DB_PASSWORD'),
    ],
];
