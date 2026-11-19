<!-- VISTA DE ERROR: recibe título y detalle amigables, nunca la excepción SQL ni sus credenciales. -->
<section class="panel-error">
    <p class="sobretitulo texto-suave">VOLVAMOS A INTENTARLO</p>
    <h2 class="titulo-formulario"><?= escapar($titulo) ?></h2>
    <!-- El destino depende de la sesión: una cuenta activa vuelve al listado; un visitante, al login. -->
    <p class="introduccion"><?= escapar($detalle) ?></p><a class="boton boton-principal"
        href="<?= escapar(url($autenticado ? 'usuarios' : 'login')) ?>">Volver al inicio</a>
</section>
