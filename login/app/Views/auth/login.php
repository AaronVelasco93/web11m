<!-- VISTA DE LOGIN: GET la muestra; POST envía las credenciales a AutenticacionController::login(). -->
<h1 class="titulo-formulario">Iniciar sesión</h1>
<p class="introduccion">Ingresa tu correo y contraseña para consultar los usuarios.</p>
<!-- require incorpora el mismo resumen de errores que se utiliza en el formulario de registro. -->
<?php require dirname(__DIR__) . '/partials/errores.php'; ?>
<!-- El navegador valida lo básico; el controlador repite las validaciones en servidor. -->
<form action="<?= escapar(url('login')) ?>" method="post" class="formulario">
        <!-- El servidor compara este token con el de la sesión antes de ejecutar el controlador. -->
        <input type="hidden" name="csrf" value="<?= escapar(\App\Core\Seguridad::token()) ?>">
        <!-- name define la clave POST; id permite asociar la etiqueta label al campo. -->
        <div class="campo"><label for="correo">Correo electrónico</label><input id="correo" name="correo" type="email"
                        autocomplete="username" maxlength="190" required value="<?= escapar($correo) ?>"
                        placeholder="nombre@ejemplo.com"></div>
        <!-- type=password oculta los caracteres en pantalla; HTTPS protege su transporte al publicar. -->
        <!-- La contraseña nunca tiene un atributo value para no devolverla en el HTML tras un error. -->
        <div class="campo"><label for="password">Contraseña</label><input id="password" name="password" type="password"
                        autocomplete="current-password" required aria-describedby="ayuda-password"><small
                        id="ayuda-password">Usa la
                        contraseña que elegiste al crear tu cuenta.</small></div>
        <!-- submit envía todos los campos del formulario. La flecha es solo decorativa. -->
        <button class="boton boton-principal boton-ancho" type="submit">Iniciar sesión <span
                        aria-hidden="true">→</span></button>
</form>
<!-- Un enlace realiza GET y abre el otro formulario; no crea ninguna cuenta por sí mismo. -->
<p class="cambio-formulario">¿Aún no tienes cuenta? <a href="<?= escapar(url('registro')) ?>">Crear una cuenta</a></p>
