<?php
declare(strict_types=1);

namespace App\Core;

// PDO es la clase incluida en PHP para conectarse a distintos motores de base de datos.
use PDO;

/** Encapsula PDO. La conexión se abre solo cuando una operación necesita datos. */
final class Conexion
{
    // ?PDO permite guardar null antes de conectarse, y un objeto PDO después.
    private ?PDO $pdo = null;

    /**
     * Constructor: recibe host, puerto, base, usuario y contraseña ya leídos de .env.
     * La propiedad promovida private array conserva el arreglo sin asignaciones adicionales.
     * No retorna valores ni abre una conexión en este momento.
     */
    public function __construct(private array $configuracion)
    {
    }

    /** Devuelve una conexión PDO reutilizable o lanza PDOException si falla el servidor. */
    public function obtener(): PDO
    {
        // Se crea una única conexión por objeto Conexion durante esta solicitud HTTP.
        if ($this->pdo === null) {
            $c = $this->configuracion;
            // DSN identifica el motor, servidor y base. utf8mb4 admite Unicode, incluidos emojis.
            $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $c['host'], $c['port'], $c['name']);
            $this->pdo = new PDO($dsn, $c['username'], $c['password'], [
                // Un error SQL lanza una excepción que puede capturar public/index.php.
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                // fetch() entrega arreglos con claves como nombre, correo e id.
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                // MySQL prepara las consultas en el servidor; los datos no se concatenan al SQL.
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
            // Las fechas generadas por MySQL se interpretan en UTC para toda esta conexión.
            $this->pdo->exec("SET time_zone = '+00:00'");
        }
        // Si ya existía una conexión, se devuelve sin volver a autenticar con MySQL.
        return $this->pdo;
    }
}
