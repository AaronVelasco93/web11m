<?php // Parcial de login y registro: recibe $errores desde el controlador y escapa cada mensaje. ?>
<!-- Un arreglo vacío no muestra el bloque. El mismo parcial sirve para ambos formularios. -->
<?php if ($errores): ?>
    <div class="aviso aviso-error" role="alert">
        <p class="aviso-titulo">Revisa la información</p>
        <!-- foreach recorre cada mensaje; role=alert anuncia el resumen a lectores de pantalla. -->
        <ul><?php foreach ($errores as $error): ?>
                <li><?= escapar($error) ?></li><?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
