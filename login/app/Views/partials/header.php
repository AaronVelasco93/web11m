<?php
// Las vistas solo presentan datos: no ejecutan SQL ni verifican contraseñas.
// vista() incluye esta cabecera antes del contenido, con $titulo, $nombreApp y $mensaje disponibles.
// isset() distingue si hay una cuenta autenticada, no solamente si existe una cookie de sesión.
$autenticado = isset($_SESSION['usuario']);
?>
<!doctype html>
<html lang="es">

<head>
    <!-- charset admite acentos; viewport permite que la misma página se adapte a celulares. -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- La etiqueta corta de salida de PHP imprime expresiones; escapar() protege sus datos en HTML. -->
    <title><?= escapar($titulo) ?> · <?= escapar($nombreApp) ?></title>
    <!-- Este recurso es local: no depende de Bootstrap ni de una conexión a internet. -->
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>

<body class="<?= $autenticado ? 'pagina-privada' : 'pagina-acceso' ?>">
    <!-- El destino #contenido permite saltar la navegación usando el teclado. -->
    <a class="saltar" href="#contenido">Saltar al contenido</a>
    <?php if ($autenticado): ?>
        <!-- Solo en sesión: la navegación ofrece listado y salida, sin enlace para crear cuentas. -->
        <header class="barra-superior">
            <nav aria-label="Navegación principal" class="navegacion">
                <a href="<?= escapar(url('usuarios')) ?>">Usuarios</a>
                <!-- Cerrar sesión es una escritura: requiere POST y un token CSRF válido. -->
                <!-- El campo oculto viaja en $_POST['csrf']; no contiene la contraseña. -->
                <form action="<?= escapar(url('logout')) ?>" method="post"><input type="hidden" name="csrf"
                        value="<?= escapar(\App\Core\Seguridad::token()) ?>"><button class="boton boton-borde boton-pequeno"
                        type="submit">Cerrar sesión</button></form>
            </nav>
        </header>
        <main class="contenido-privado" id="contenido">
        <?php else: ?>
            <!-- Las pantallas públicas muestran únicamente la tarjeta del formulario. -->
            <main class="acceso-contenido" id="contenido">
                <div class="panel-formulario">
                <?php endif; ?>
                <?php if ($mensaje): ?>
                    <!-- Un aviso de sesión se muestra una vez; role=status lo anuncia sin interrumpir. -->
                    <div class="aviso aviso-<?= escapar($mensaje['tipo']) ?>" role="status">
                        <?= escapar($mensaje['texto']) ?>
                    </div>
                <?php endif; ?>
