<!-- Cierra exactamente los contenedores que abrió header.php para cada tipo de pantalla. -->
<?php if ($autenticado): ?>
    </main>
    <!-- Este pie es solo informativo: no agrega acciones al dashboard. -->
    <footer class="pie-privado"><span><?= escapar($nombreApp) ?> · Directorio de usuarios</span><span>Proyecto
            educativo</span></footer>
<?php else: ?>
    </div>
    </main>
<?php endif; ?>
</body>

</html>
