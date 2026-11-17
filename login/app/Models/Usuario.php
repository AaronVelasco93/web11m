<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Conexion;

/** Modelo MVC: concentra SQL y persistencia, sin generar HTML ni leer formularios. */
final class Usuario
{
    /** Recibe el administrador de conexión para acceder a PDO cuando haga falta. */
    public function __construct(private Conexion $conexion)
    {
    }

    /** Recibe correo normalizado. Devuelve el usuario o false; el hash solo se usa al autenticar. */
    public function buscarPorCorreo(string $correo): array|false
    {
        // :correo es un marcador de parámetro, no una concatenación del texto recibido.
        $consulta = $this->conexion->obtener()->prepare('SELECT id, nombre, correo, password_hash FROM usuarios WHERE correo = :correo LIMIT 1');
        $consulta->execute(['correo' => $correo]);
        // fetch() devuelve una fila; si no hay coincidencias devuelve false.
        return $consulta->fetch();
    }

    /** Recibe nombre, correo y HASH (no contraseña original). Devuelve el ID insertado. */
    public function registrar(string $nombre, string $correo, string $hash): int
    {
        // El controlador ya validó los campos y produjo el hash antes de llegar aquí.
        $pdo = $this->conexion->obtener();
        $consulta = $pdo->prepare('INSERT INTO usuarios (nombre, correo, password_hash) VALUES (:nombre, :correo, :hash)');
        $consulta->execute(['nombre' => $nombre, 'correo' => $correo, 'hash' => $hash]);
        // lastInsertId() obtiene el ID autoincremental de esta inserción, no el máximo de toda la tabla.
        return (int) $pdo->lastInsertId();
    }

    /** Devuelve SOLO campos visibles. El hash nunca se selecciona para la tabla de usuarios. */
    public function listar(): array
    {
        // Esta consulta no recibe datos del visitante; por eso puede ejecutarse con query().
        // ORDER BY id DESC coloca primero los registros insertados más recientemente.
        return $this->conexion->obtener()->query('SELECT id, nombre, correo, creado_en FROM usuarios ORDER BY id DESC')->fetchAll();
    }

    /** Recibe ID y hash actualizado cuando cambia el algoritmo o costo recomendado por PHP. */
    public function actualizarHash(int $id, string $hash): void
    {
        // Es mantenimiento interno del login, no una opción para editar usuarios desde el panel.
        // WHERE limita el UPDATE a la cuenta cuya contraseña acaba de verificarse.
        $consulta = $this->conexion->obtener()->prepare('UPDATE usuarios SET password_hash = :hash WHERE id = :id');
        $consulta->execute(['hash' => $hash, 'id' => $id]);
    }
}
