<?php
require_once __DIR__ . "/../config/db.php";
$id = (int)($_GET['id'] ?? 0);
if($id <= 0){
    die("ID Invalido");
}

$consulta = "DELETE FROM alumnos WHERE id = :id";
$stmt=$pdo->prepare($consulta);
$stmt->execute([":id" => $id]);
header("Location: index.php");
exit;
?>