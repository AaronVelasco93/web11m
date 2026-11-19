# Login PHP MVC · Guía del proyecto

Aplicación educativa para registrar una cuenta, iniciar sesión y consultar usuarios. Utiliza PHP, PDO, MariaDB/MySQL, HTML y CSS local. El código contiene comentarios sobre clases, métodos, validaciones, consultas y vistas.

El dashboard permite únicamente **listar usuarios y cerrar sesión**. Para registrar una cuenta hay que estar sin sesión. Las contraseñas se almacenan como hashes, no como texto plano.

## 1. Preparar el proyecto en XAMPP

1. Iniciar Apache y MySQL desde XAMPP.
2. Abrir phpMyAdmin: http://localhost/phpmyadmin/.
3. Importar **`database/instalar.sql`**. Crea la base `login_mvc` y la tabla `usuarios` si no existen; no elimina registros.
4. Revisar el archivo local `.env`: host, puerto, nombre de base, usuario y contraseña deben coincidir con el servidor.
5. Abrir **http://localhost/Login/**. Se redirige a `public/`.
6. Crear una cuenta desde el enlace del login e iniciar sesión.

Se necesita PHP 8.1 o posterior, con las extensiones `pdo_mysql` y `mbstring`. No es obligatorio instalar Composer, Node.js, frameworks ni librerías CSS.

Si el servidor ya proporciona una base con otro nombre, adaptar las referencias a `login_mvc` del SQL o ejecutar únicamente su bloque `CREATE TABLE` dentro de la base elegida. Usar ese mismo nombre en `DB_DATABASE`.

El proyecto actual se instala con phpMyAdmin: no incluye instalador PHP, carpeta de pruebas ni un segundo archivo de esquema.

## 2. Variables del .env

```ini
APP_NAME="Aula Acceso"
DB_HOST="127.0.0.1"
DB_PORT="3306"
DB_DATABASE="login_mvc"
DB_USERNAME="tu_usuario"
DB_PASSWORD="tu_contrasena"
```

Estos son ejemplos; no son credenciales verificadas. `.env.example` es la plantilla compartible y `.env` contiene la configuración local, excluida de Git.

`config/config.php` lee directamente el archivo con `parse_ini_file(..., INI_SCANNER_RAW)`. PHP no lo carga por sí solo. Si una variable existe en el entorno real del proceso, tiene prioridad, incluso si vale una cadena vacía. Los valores se entregan a PDO en un arreglo; no se exportan con `putenv()`.

El archivo usa sintaxis INI simple: comentarios con `;`, valores entre comillas y sin interpolación de otras variables. La contraseña de la base no es la contraseña que una persona registra en el formulario.

## 3. Estructura actual

```text
Login/
├── .env                          Configuración local, no versionada
├── .env.example                  Plantilla comentada
├── .gitignore                    Exclusiones de Git
├── .htaccess                     Protección de archivos privados
├── index.php                     Redirección a public/
├── README.md                     Instalación y orientación
├── DESCRIPCION_TECNICA.md          Recorridos, clases y funciones
├── MANUAL_USUARIO.md              Instrucciones de las pantallas
├── config/
│   ├── .htaccess
│   └── config.php                Lectura y validación del .env
├── app/
│   ├── .htaccess
│   ├── Core/
│   │   ├── Conexion.php          Conexión PDO
│   │   └── Seguridad.php         Sesión, cookies y CSRF
│   ├── Controllers/
│   │   ├── AutenticacionController.php
│   │   └── UsuarioController.php
│   ├── Models/
│   │   └── Usuario.php           Acceso a la tabla usuarios
│   ├── Support/
│   │   └── funciones.php         Escape, URLs, vistas y campos
│   └── Views/
│       ├── auth/login.php
│       ├── auth/registro.php
│       ├── usuarios/index.php
│       ├── error.php
│       └── partials/
│           ├── header.php
│           ├── footer.php
│           └── errores.php
├── public/
│   ├── index.php                 Entrada principal y rutas
│   └── assets/css/estilos.css     Estilos comentados
└── database/
    ├── .htaccess
    └── instalar.sql              Creación de base y tabla
```

## 4. Rutas del sistema

Las rutas utilizan el parámetro `r`; no requieren reescritura de direcciones.

| Método | Dirección relativa a public/ | Propósito |
|---|---|---|
| GET | index.php?r=login | Mostrar login |
| POST | index.php?r=login | Autenticar |
| GET | index.php?r=registro | Mostrar registro sin sesión |
| POST | index.php?r=registro | Guardar cuenta sin sesión |
| GET | index.php?r=usuarios | Listado privado |
| POST | index.php?r=logout | Cerrar sesión con token CSRF |

## 5. Cómo estudiar el código

Leer primero `public/index.php`, después un controlador, luego `Usuario.php` y finalmente la vista de esa operación. Para la conexión, seguir `config/config.php → Conexion.php → PDO`.

Los comentarios distinguen pasos del servidor y del navegador. En particular, explican que ocultar un enlace no sustituye validar permisos, que el hash no se descifra y que el escape HTML no sustituye los parámetros SQL.

La [descripción técnica](DESCRIPCION_TECNICA.md) contiene el mapa de métodos y un glosario. El [manual de usuario](MANUAL_USUARIO.md) describe el uso sin entrar en el código.

## 6. Verificación manual

- Crear una cuenta nueva y comprobar el mensaje de confirmación.
- Intentar el mismo correo otra vez: debe rechazarse.
- Iniciar sesión con contraseña incorrecta y después con la correcta.
- Comprobar que el listado solo muestre ID, nombre, correo y fecha.
- Estando autenticado, abrir la ruta de registro: debe regresar al listado.
- Cerrar sesión e intentar abrir usuarios: debe regresar al login.

La revisión de sintaxis puede hacerse con `php -l ruta/al/archivo.php`. Ese comando no prueba la conexión ni las reglas de negocio.

## 7. Servidor PHP opcional

```powershell
php -S 127.0.0.1:8080 -t public
```

Abrir http://127.0.0.1:8080. Conservar `-t public`: este servidor no interpreta `.htaccess`.

## Alcance y protección

Se usan sesiones, tokens CSRF, escape HTML y consultas preparadas. Los `.htaccess` requieren Apache con la configuración que permita aplicarlos. Para publicar, preferir `public/` como DocumentRoot y usar HTTPS y permisos SQL mínimos.

No incluye edición, eliminación, roles, recuperación de contraseña, verificación de correo ni bloqueo de intentos repetidos. Todas las personas autenticadas ven el mismo listado. Los recursos visuales se sirven localmente.
