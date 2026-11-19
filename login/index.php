<?php
// Entrada breve para XAMPP: la aplicación pública vive en public/.
// Se ejecuta al visitar http://localhost/Login/ y evita duplicar el enrutador.
header('Location: public/');
// Tras enviar Location se detiene este archivo; la siguiente solicitud abre public/index.php.
exit;
