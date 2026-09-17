<?php
$nombre = "Aaron";
$edad = 32;
// echo "<h1>".$nombre."</h1>";
// echo "<br>";
// echo "<h2>". $edad ."</h2>";


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php if($edad >= 18){  ?>
    <h1>Eres mayor puedes pasar :)</h1>

    <?php }else{ ?>

    <h1 style="color:red;">No puedes pasar </h1>


    <?php }?>
</body>
</html>