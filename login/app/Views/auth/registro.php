<!-- VISTA DE REGISTRO: recibe nombre, correo y errores; el controlador nunca le entrega contraseñas. -->
<section class="registro-panel">
        <h1 class="titulo-formulario">Crear cuenta</h1>
        <p class="introduccion">Completa tus datos para registrarte.</p>
        <?php require dirname(__DIR__) . '/partials/errores.php'; ?>
        <form action="<?= escapar(url('registro')) ?>" method="post" class="formulario">
                <!-- El token identifica el formulario de la sesión actual, no al usuario de la base. -->
                <input type="hidden" name="csrf" value="<?= escapar(\App\Core\Seguridad::token()) ?>">
                <!-- minlength, maxlength y required ayudan en el navegador; se repiten en el servidor. -->
                <!-- Los value escapados conservan los datos no sensibles cuando falla la validación. -->
                <div class="campo"><label for="nombre">Nombre completo</label><input id="nombre" name="nombre"
                                type="text" autocomplete="name" minlength="2" maxlength="100" required
                                value="<?= escapar($nombre) ?>" placeholder="¿Cómo te llamas?"></div>
                <div class="campo"><label for="correo">Correo electrónico</label><input id="correo" name="correo"
                                type="email" autocomplete="username" maxlength="190" required
                                value="<?= escapar($correo) ?>" placeholder="nombre@ejemplo.com"></div>
                <!-- autocomplete=new-password ayuda al navegador a proponer una contraseña para la cuenta nueva. -->
                <div class="campo"><label for="password">Contraseña</label><input id="password" name="password"
                                type="password" autocomplete="new-password" minlength="8" required
                                aria-describedby="requisitos"><small id="requisitos">Usa al menos 8 caracteres. Evita
                                reutilizar contraseñas de otros sitios.</small></div>
                <!-- Confirmar reduce errores de escritura; este segundo valor no se guarda en MariaDB. -->
                <div class="campo"><label for="confirmacion">Confirmar contraseña</label><input id="confirmacion"
                                name="confirmacion" type="password" autocomplete="new-password" minlength="8" required>
                </div>
                <button class="boton boton-principal boton-ancho" type="submit">Crear cuenta <span
                                aria-hidden="true">→</span></button>
        </form>
        <p class="cambio-formulario">¿Ya tienes cuenta? <a href="<?= escapar(url('login')) ?>">Iniciar sesión</a></p>
</section>
