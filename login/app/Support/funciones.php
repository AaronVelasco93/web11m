<?php 
declare(strict_types =1);
//Fuciones compartidas: evita repetir presentacio, redireccion y lectura de campos
//las vistas pueden llamarlas porque public/index.php incluye este archivo antes de redenrizar
function escapar(mixed $valor):string{

    return htmlspecialchars((string)$valor,ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

}
//recibe una ruta conocida y devuelve una URL relativa comapatible con el /Login/public/ y php -s
function url(string $ruta):string{
    return 'index.php?r='.rawurldecode($ruta);
}
//recibe el nombre de la ruta, redirige y termina la solicitud para no ejecutar mas codigo
function redireccionar(string $ruta):never{
    header('Location:'.url($ruta),true,303);
    exit;
}
//recibe la vista y datos del controlador. Incluye las plantillas: no devuelve contenido
function vistas(string $nombreVista, array $datos=[],int $estado =200):void{
    http_response_code($estado);
    extract($datos,EXTR_SKIP);
    $nombreApp =$GLOBALS['config']['app_name'];
    $mensaje = $_SESSION['mensaje']??null;
    unset($_SESSION['mensaje']);
    require dirname(__DIR__) . '/Views/partials/header.php';
    require dirname(__DIR__) . '/Views'.$nommbreVista .'.php';
    require dirname(__DIR__) .'/Views/partials/footer.php';

}

//recibe un capo POST y devuelve una cadena; arreglos u objetos se consideran vacios
function campo(string $nombre, bool $recotar = true):string{
    //?? devuelve'' si el formulario no envio ese campo.
    $valor  = $_POST[$nombre];
    if(!is_string($valor)){
        return '';
    }
    //trim limpia el nombre y correo, 
    return $recotar ? trim($valor):$valor;
}



?>