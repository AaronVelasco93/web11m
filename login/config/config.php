<?php
declare(strict_types=1);
$archivo = dirname(__DIR__) . '/.env';
if(!is_file($archivo) || !is_readable($archivo)) {
    throw new RuntimeException('No se puede leer o falta  el archivo de configuración');
}
$variables = parse_ini_file($archivo, false, INI_SCANNER_RAW);

if($variables === false) {
    throw new RuntimeException('No se puede leer el archivo de configuración');
}
$leer = static function(string $clave) use ($variables): string{
    $valor = getenv($clave);
    if($valor === false) {
        $valor = $variables[$clave] ?? null;
    }
    if(!is_string($valor)) {
        throw new RuntimeException('Falta la variable de entorno');
    }
    return $valor;
};
$base = $leer('DB_DATABASE');
$puerto = filter_var($leer('DB_PORT'), FILTER_VALIDATE_INT,['options' => ['min_range' => 1, 'max_range' => 65535]]);
if(!preg_match('/^[a-zA-Z0-9_]{1,64}+$/', $base) ||$puerto === false) {
    throw new RuntimeException('Puerto de base de datos inválido o revisar la base de datos'); 
}
return [
   'app_name' => $leer('APP_NAME'),
   'database' => [
       'host' => $leer('DB_HOST'),
       'port' => $puerto,
       'name' => $base,
       'user' => $leer('DB_USERNAME'),
       'password' => $leer('DB_PASSWORD'),
   ],
];