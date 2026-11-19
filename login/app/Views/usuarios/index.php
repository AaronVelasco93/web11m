<!-- Se muestran campos públicos del directorio. El modelo no envía hashes a esta vista. -->
<div class="encabezado-listado">
    <div>
        <h1>Usuarios registrados</h1>
        <p class="introduccion">Hola, <?= escapar($_SESSION['usuario']['nombre']) ?>. Aquí están las cuentas
            registradas.</p>
    </div>
</div>
<section class="tarjeta-listado" aria-labelledby="titulo-listado">
    <div class="titulo-listado">
        <div>
            <h2 id="titulo-listado">Usuarios</h2>
            <p>La comunidad, de un vistazo.</p>
        </div><span class="contador"><?= count($usuarios) ?> <?= count($usuarios) === 1 ? 'persona' : 'personas' ?></span></div>
    <div class="tabla-contenedor"><table>
        <caption>Usuarios ordenados del más reciente al más antiguo. Fechas en UTC.</caption>
        <thead><tr><th scope="col">ID</th><th scope="col">Nombre</th><th scope="col">Correo electrónico</th><th scope="col">Registro (UTC)</th></tr></thead>
        <tbody>
        <?php if (!$usuarios): ?><tr><td colspan="4" class="sin-datos">Todavía no hay usuarios registrados.</td></tr><?php endif; ?>
        <?php foreach ($usuarios as $usuario): ?>
                <tr><td class="folio">#<?= escapar(str_pad((string) $usuario['id'], 3, '0', STR_PAD_LEFT)) ?></td><td><span class="nombre-usuario"><span class="avatar" aria-hidden="true"><?= escapar(mb_strtoupper(mb_substr($usuario['nombre'], 0, 1))) ?></span><?= escapar($usuario['nombre']) ?></span></td><td><?= escapar($usuario['correo']) ?></td><td class="fecha"><?= escapar($usuario['creado_en']) ?></td></tr>
        <?php endforeach; ?>
        </tbody>
    </table></div>
</section>