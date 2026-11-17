<?php //recibe errores desde el controlador y escapa con un mensaje?> 

<?php if($errores):?>
    <div class="aviso aviso-error " role="alert" >
        <p class="aviso-titulo" >Revisa informacion</p>
        <ul<?php foreach($errores as $error): ?>>
            <li><?= escapar($error) ?></li> <?php endforeach; ?>

        </ul>
    </div>

<?php endif;?>
