# Guía de archivos y funcionamiento del proyecto Login

Este documento explica qué hace cada archivo, cuál es su función principal y cómo se relaciona con el resto de la aplicación. Está pensado para estudiar el proyecto por partes.

El sistema permite registrar una cuenta, iniciar sesión, consultar el listado de usuarios y cerrar sesión. Está desarrollado con PHP, PDO, MariaDB/MySQL, HTML y CSS, siguiendo una organización MVC.

## 1. Cómo se organiza la aplicación

MVC significa **Modelo, Vista y Controlador**:

- **Modelo:** ejecuta las consultas a la base de datos. En este proyecto es `app/Models/Usuario.php`.
- **Vista:** presenta formularios, tablas y mensajes. Son los archivos de `app/Views/`.
- **Controlador:** recibe la acción, valida los datos y decide qué modelo y vista utilizar. Son las clases de `app/Controllers/`.

La carpeta `Core` reúne servicios comunes, como la conexión y la seguridad. `public/index.php` prepara estos servicios y dirige cada solicitud a un controlador.

```text
Login/
├── .env
├── .env.example
├── .gitignore
├── .htaccess
├── index.php
├── README.md
├── DESCRIPCION_TECNICA.md
├── MANUAL_USUARIO.md
├── GUIA_DE_ARCHIVOS.md
├── GLOSARIO_LOGIN_MVC.csv
├── GLOSARIO_LOGIN_MVC.xlsx
├── config/
│   ├── .htaccess
│   └── config.php
├── app/
│   ├── .htaccess
│   ├── Core/
│   │   ├── Conexion.php
│   │   └── Seguridad.php
│   ├── Controllers/
│   │   ├── AutenticacionController.php
│   │   └── UsuarioController.php
│   ├── Models/
│   │   └── Usuario.php
│   ├── Support/
│   │   └── funciones.php
│   └── Views/
│       ├── auth/
│       │   ├── login.php
│       │   └── registro.php
│       ├── usuarios/
│       │   └── index.php
│       ├── error.php
│       └── partials/
│           ├── header.php
│           ├── footer.php
│           └── errores.php
├── public/
│   ├── index.php
│   └── assets/
│       └── css/
│           └── estilos.css
└── database/
    ├── .htaccess
    └── instalar.sql
```

La carpeta oculta `.git/` contiene el historial de Git; sus archivos internos no forman parte del funcionamiento del login.

## 2. Archivos de la raíz

### `index.php`

- **Qué hace:** recibe la visita a `http://localhost/Login/`.
- **Función principal:** enviar una redirección a `public/` y terminar su ejecución con `exit`.
- **Cómo se conecta:** el navegador realiza una nueva solicitud a `public/`, donde se ejecuta `public/index.php` como índice del directorio en la configuración habitual de Apache.

No contiene la validación del login ni consultas SQL. Hay tres archivos llamados `index.php` en el proyecto: este redirige, el de `public/` dirige solicitudes y el de `Views/usuarios/` presenta la tabla.

### `.env`

- **Qué hace:** guarda la configuración local de la aplicación y de la base de datos.
- **Función principal:** proporcionar `APP_NAME`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` y `DB_PASSWORD` sin escribir credenciales dentro de las clases.
- **Cómo se conecta:** `config/config.php` lo lee directamente con `parse_ini_file()`. Después, `public/index.php` entrega la configuración de base de datos a `Conexion`.

PHP no carga este archivo automáticamente. En este proyecto sus valores se leen como un arreglo; no se exportan al entorno del sistema. La contraseña de la base de datos es distinta de las contraseñas de los usuarios del formulario. Aquí no se reproducen los valores locales.

### `.env.example`

- **Qué hace:** muestra las claves y el formato necesarios para crear el `.env`.
- **Función principal:** servir como plantilla de instalación, con comentarios explicativos.
- **Cómo se conecta:** se copia como `.env` y se ajustan sus valores. La aplicación no lo utiliza como configuración de respaldo.

### `.gitignore`

- **Qué hace:** indica a Git qué archivos no debe añadir normalmente al repositorio.
- **Función principal:** excluir `.env` y reservar la exclusión de `vendor/` si se incorporan dependencias en el futuro.
- **Cómo se conecta:** lo interpreta Git, no PHP. No impide el acceso por navegador ni deja de rastrear por sí solo archivos ya versionados.

### `.htaccess`

- **Qué hace:** configura restricciones de acceso para Apache.
- **Función principal:** desactivar el listado de directorios, responder con 404 ante accesos a `.git` y denegar archivos como `.env`, `.env.example` y documentos Markdown.
- **Cómo se conecta:** Apache aplica sus reglas antes de servir los recursos cuando permite estas directivas mediante su configuración de `AllowOverride`.

No sustituye la autenticación de PHP. El servidor integrado `php -S` no interpreta `.htaccess`; por eso, al usarlo, la raíz pública debe ser `public/`.

### `README.md`

- **Qué hace:** presenta el proyecto y sus instrucciones de preparación.
- **Función principal:** explicar la instalación, las variables, la estructura, las rutas y la verificación manual.
- **Cómo se conecta:** orienta al lector hacia `.env.example`, `database/instalar.sql` y la entrada pública. La aplicación no ejecuta este documento.

### `DESCRIPCION_TECNICA.md`

- **Qué hace:** desarrolla la arquitectura y las decisiones técnicas del sistema.
- **Función principal:** explicar MVC, la configuración, el modelo de datos, los métodos y las protecciones implementadas.
- **Cómo se conecta:** complementa la lectura de controladores, modelo, servicios y vistas. Es material de consulta, no una dependencia de PHP.

### `MANUAL_USUARIO.md`

- **Qué hace:** describe cómo utilizar las pantallas.
- **Función principal:** guiar el registro, el inicio de sesión, la consulta de usuarios y la salida.
- **Cómo se conecta:** sus instrucciones corresponden a los formularios y al listado que presentan las vistas.

### `GUIA_DE_ARCHIVOS.md`

- **Qué hace:** es el documento que estás leyendo.
- **Función principal:** relacionar cada archivo con su responsabilidad y explicar el recorrido de las acciones.
- **Cómo se conecta:** sirve de mapa de lectura del código y de los documentos existentes.

### `GLOSARIO_LOGIN_MVC.csv`

- **Qué hace:** reúne términos y explicaciones en formato de texto tabular CSV.
- **Función principal:** facilitar la consulta de conceptos, palabras reservadas, funciones y métodos del proyecto.
- **Cómo se conecta:** sus referencias permiten ubicar conceptos en el código. PHP no lo lee para ejecutar el login.

### `GLOSARIO_LOGIN_MVC.xlsx`

- **Qué hace:** ofrece el glosario como archivo de hoja de cálculo.
- **Función principal:** permitir su consulta con una aplicación compatible con Excel.
- **Cómo se conecta:** es material educativo relacionado con el CSV; no interviene en las solicitudes ni en la base de datos.

## 3. Configuración: carpeta `config/`

### `config/config.php`

- **Qué hace:** verifica que exista un `.env` legible, lo interpreta y valida datos de configuración como el puerto y el nombre de la base.
- **Función principal:** devolver un arreglo con `app_name` y `database`.
- **Cómo se conecta:** `public/index.php` recibe ese arreglo mediante `require`. La sección `database` se pasa al constructor de `Conexion`; `app_name` se utiliza al presentar las páginas.

Su función anónima `$leer` busca primero la clave con `getenv()`. Si no existe en el entorno real del proceso, utiliza el valor leído del `.env`. Aunque haya variables en el entorno, el código actual requiere que exista un `.env` legible y válido.

Este archivo prepara datos de configuración; no abre la conexión PDO.

### `config/.htaccess`

- **Qué hace:** contiene `Require all denied`.
- **Función principal:** impedir solicitudes HTTP directas a la carpeta de configuración en Apache.
- **Cómo se conecta:** el bloqueo del navegador no impide que `public/index.php` incluya `config.php` desde el disco mediante PHP.

## 4. Servicios comunes: carpeta `app/Core/`

### `app/Core/Conexion.php`

- **Qué hace:** define la clase `Conexion`, que conserva la configuración y administra un objeto PDO.
- **Función principal:** proporcionar la conexión a MariaDB/MySQL mediante `obtener()`.
- **Cómo se conecta:** `public/index.php` construye esta clase y la entrega al modelo `Usuario`. El modelo llama a `obtener()` cuando necesita ejecutar SQL.

| Método | Responsabilidad |
| --- | --- |
| `__construct()` | Guardar el arreglo de configuración recibido, sin conectarse todavía. |
| `obtener()` | Crear PDO la primera vez y reutilizarlo en ese mismo objeto durante la solicitud. |

`obtener()` forma el DSN, establece `utf8mb4`, activa excepciones, devuelve filas asociativas, desactiva consultas preparadas emuladas y fija UTC para la conexión.

La conexión se abre cuando se necesitan datos. Mostrar el formulario inicial no exige ejecutar una consulta. La reutilización ocurre dentro del objeto de la solicitud, no entre todos los visitantes de la aplicación.

### `app/Core/Seguridad.php`

- **Qué hace:** concentra las operaciones de sesión y los tokens CSRF de los formularios.
- **Función principal:** mantener el estado de sesión y comprobar que los POST incluyan el token esperado.
- **Cómo se conecta:** `public/index.php` inicia la sesión y valida los POST; las vistas solicitan el token; `AutenticacionController` utiliza el cierre de sesión.

| Método | Responsabilidad |
| --- | --- |
| `iniciarSesion()` | Configurar la cookie, iniciar o recuperar la sesión y revisar 30 minutos de inactividad. |
| `token()` | Crear o recuperar el token CSRF de la sesión. |
| `csrfValido()` | Comparar el token recibido con el guardado en sesión. |
| `cerrarSesion()` | Vaciar los datos de sesión, vencer su cookie y destruir la sesión. |

La sesión guarda datos de autenticación en el servidor; la cookie contiene su identificador. Cerrar sesión no elimina la cuenta de la tabla `usuarios`.

### `app/.htaccess`

- **Qué hace:** deniega el acceso HTTP directo a `app/` y sus subcarpetas.
- **Función principal:** mantener controladores, servicios, modelos y vistas como archivos internos.
- **Cómo se conecta:** Apache bloquea su visita directa, pero PHP puede cargarlos mediante el autoload o `require` desde la entrada pública.

## 5. Modelo: carpeta `app/Models/`

### `app/Models/Usuario.php`

- **Qué hace:** define la clase `Usuario` y concentra las consultas SQL de las cuentas.
- **Función principal:** buscar, insertar y listar usuarios, y actualizar internamente un hash cuando el login lo requiere.
- **Cómo se conecta:** recibe un objeto `Conexion` y es compartido por ambos controladores. Trabaja con la tabla creada por `database/instalar.sql`.

| Método | Responsabilidad y resultado |
| --- | --- |
| `__construct()` | Recibir y conservar `Conexion`. |
| `buscarPorCorreo()` | Consultar una cuenta y devolver un arreglo con su hash, o `false` si no existe. |
| `registrar()` | Insertar nombre, correo y hash; devolver el ID generado. |
| `listar()` | Devolver ID, nombre, correo y fecha, ordenados por ID descendente. No selecciona hashes. |
| `actualizarHash()` | Actualizar el hash de una cuenta después de un login válido cuando corresponde renovarlo. |

Las consultas que reciben valores variables utilizan parámetros preparados. El modelo no genera HTML ni lee directamente `$_POST`.

## 6. Controladores: carpeta `app/Controllers/`

### `app/Controllers/AutenticacionController.php`

- **Qué hace:** coordina las acciones de acceso, registro y salida.
- **Función principal:** validar entradas, verificar o generar hashes y decidir qué vista mostrar o a qué ruta redirigir.
- **Cómo se conecta:** `public/index.php` le entrega el modelo `Usuario` y ejecuta sus métodos según la ruta. Utiliza `Seguridad` y las funciones de `Support/funciones.php`.

| Método | Responsabilidad |
| --- | --- |
| `__construct()` | Recibir el modelo `Usuario`. |
| `formularioLogin()` | Mostrar el login; si hay usuario autenticado, redirigir al listado. |
| `formularioRegistro()` | Mostrar el registro solo a visitantes sin sesión autenticada. |
| `registrar()` | Validar campos, comprobar duplicados, generar el hash, guardar y redirigir al login. |
| `login()` | Buscar por correo, verificar la contraseña, renovar el hash si corresponde y guardar la identidad en sesión. |
| `logout()` | Solicitar el cierre de sesión y redirigir al login. |

El registro no inicia sesión automáticamente. Las restricciones de registro se comprueban tanto al mostrar el formulario como al procesar el POST. El panel privado conserva únicamente las acciones de listar y salir.

Las contraseñas se procesan con `password_hash()` y `password_verify()`. El hash no se descifra para compararlo. La contraseña original y la confirmación no se guardan en la base de datos ni se devuelven a las vistas.

### `app/Controllers/UsuarioController.php`

- **Qué hace:** controla el acceso al listado de cuentas.
- **Función principal:** comprobar que exista `$_SESSION['usuario']` antes de consultar o mostrar datos.
- **Cómo se conecta:** recibe el modelo `Usuario`; su método `index()` llama a `listar()` y entrega el resultado a `app/Views/usuarios/index.php` mediante `vista()`.

Si no hay usuario autenticado, guarda un mensaje y redirige al login. No implementa creación, edición ni eliminación desde el panel.

## 7. Funciones compartidas: carpeta `app/Support/`

### `app/Support/funciones.php`

- **Qué hace:** define funciones globales para tareas que se repiten.
- **Función principal:** construir URLs, redirigir, presentar vistas, leer campos y escapar texto.
- **Cómo se conecta:** `public/index.php` lo incluye explícitamente antes de ejecutar los controladores; sus funciones quedan disponibles también en las vistas.

| Función | Para qué sirve | Quién la utiliza |
| --- | --- | --- |
| `escapar()` | Convertir caracteres especiales para imprimir texto en HTML. | Vistas y parciales. |
| `url()` | Construir URLs como `index.php?r=login`. | Formularios, enlaces y redirecciones. |
| `redireccionar()` | Enviar una redirección HTTP 303 y terminar la ejecución. | Controladores. |
| `vista()` | Establecer el estado HTTP y ensamblar cabecera, contenido y pie. | Controladores y manejador de errores de la entrada pública. |
| `campo()` | Leer una cadena de `$_POST` y, opcionalmente, quitar espacios de los extremos. | Controlador de autenticación. |

`vista()` convierte el arreglo recibido en variables disponibles para las plantillas mediante `extract(..., EXTR_SKIP)`, obtiene el nombre de la aplicación de `$GLOBALS['config']` y consume el mensaje de sesión de una sola lectura. La vista concreta siempre la decide el código interno.

`escapar()` protege la salida HTML. Los parámetros preparados de PDO protegen la separación entre SQL y datos: son tareas distintas.

## 8. Vistas: carpeta `app/Views/`

### `app/Views/auth/login.php`

- **Qué hace:** muestra correo, contraseña, botón de acceso y enlace al registro.
- **Función principal:** enviar las credenciales mediante POST a `index.php?r=login`.
- **Cómo se conecta:** `formularioLogin()` o `login()` la muestran con `vista()`. Incluye `partials/errores.php` y obtiene el token de `Seguridad::token()`.

Recibe título, correo y errores. Cuando hay un fallo, conserva el correo escapado, pero no rellena la contraseña.

### `app/Views/auth/registro.php`

- **Qué hace:** muestra nombre, correo, contraseña y confirmación.
- **Función principal:** enviar los datos mediante POST a `index.php?r=registro`.
- **Cómo se conecta:** la presentan `formularioRegistro()` y `registrar()`. Comparte el parcial de errores y solicita el token CSRF.

Las validaciones HTML ayudan al usuario; el controlador vuelve a validar en el servidor antes de guardar. Solo nombre y correo pueden devolverse al formulario tras un error.

### `app/Views/usuarios/index.php`

- **Qué hace:** presenta el saludo, el contador y la tabla de usuarios.
- **Función principal:** recorrer `$usuarios` e imprimir ID, nombre, correo y fecha de registro en UTC.
- **Cómo se conecta:** `UsuarioController::index()` obtiene el listado del modelo y se lo entrega mediante `vista()`. Utiliza `escapar()` al mostrar datos de los usuarios.

No ejecuta SQL, no comprueba contraseñas y no ofrece acciones para crear usuarios dentro del panel.

### `app/Views/error.php`

- **Qué hace:** presenta un título y un detalle de error comprensibles.
- **Función principal:** mostrar una respuesta de error con un enlace de regreso.
- **Cómo se conecta:** `public/index.php` la solicita para rutas desconocidas, tokens inválidos y fallos capturados. `vista()` establece el código HTTP correspondiente.

No muestra la excepción técnica ni las credenciales. El enlace vuelve al listado o al login según la sesión.

### `app/Views/partials/header.php`

- **Qué hace:** abre el documento HTML, carga CSS y prepara la estructura inicial de la página.
- **Función principal:** compartir título, metadatos, avisos y navegación entre las vistas.
- **Cómo se conecta:** `vista()` lo incluye antes del contenido. Enlaza `public/assets/css/estilos.css` mediante una ruta relativa a la página pública.

Consulta si existe un usuario autenticado. En ese caso muestra el enlace a usuarios y el formulario POST de cierre de sesión con token CSRF. Para visitantes abre el contenedor del formulario. También define `$autenticado`, que reutilizan el contenido de error y el pie.

### `app/Views/partials/footer.php`

- **Qué hace:** cierra los contenedores y el documento HTML abiertos por la cabecera.
- **Función principal:** completar correctamente la estructura de la página y mostrar el pie informativo del panel privado.
- **Cómo se conecta:** `vista()` lo incluye después del contenido. Utiliza `$autenticado` y `$nombreApp`, disponibles en el mismo ámbito de renderizado.

### `app/Views/partials/errores.php`

- **Qué hace:** muestra una lista de errores cuando `$errores` contiene mensajes.
- **Función principal:** reutilizar el mismo bloque de validación en login y registro.
- **Cómo se conecta:** lo incluyen directamente `auth/login.php` y `auth/registro.php`; los mensajes proceden del controlador de autenticación y se imprimen escapados.

## 9. Entrada y estilos: carpeta `public/`

### `public/index.php`

- **Qué hace:** recibe las solicitudes de la aplicación y coordina su preparación y ejecución.
- **Función principal:** actuar como punto de entrada común, también llamado controlador frontal.
- **Cómo se conecta:** registra el autoload de clases `App`, incluye funciones, lee configuración, inicia sesión, construye objetos y selecciona una acción de controlador.

El autoload convierte, por ejemplo, `App\Models\Usuario` en `app/Models/Usuario.php`. Las instrucciones `use` permiten usar nombres cortos de clases; la carga de los archivos la resuelve el autoload.

La secuencia principal es:

1. Registrar el autoload e incluir las funciones compartidas.
2. Establecer cabeceras de respuesta y ocultar errores técnicos al visitante.
3. Leer `config/config.php` e iniciar la sesión mediante `Seguridad`.
4. Construir `Conexion`, el modelo `Usuario` y los dos controladores.
5. Leer el método HTTP y la ruta `r`; si no se indica ruta, utilizar `login`.
6. Rechazar rutas desconocidas y comprobar CSRF en los POST válidamente enrutados.
7. Ejecutar la acción seleccionada; ante una excepción capturada, registrar el detalle en el servidor y mostrar un error general.

| Método HTTP | Valor de `r` | Acción |
| --- | --- | --- |
| GET | `login` | `AutenticacionController::formularioLogin()` |
| GET | `registro` | `AutenticacionController::formularioRegistro()` |
| GET | `usuarios` | `UsuarioController::index()` |
| POST | `login` | `AutenticacionController::login()` |
| POST | `registro` | `AutenticacionController::registrar()` |
| POST | `logout` | `AutenticacionController::logout()` |

Una combinación de método y ruta no registrada devuelve 404. Un POST con token CSRF inválido devuelve 403 antes de llegar al controlador. Los formularios con errores de validación se presentan con 422; los fallos capturados por el manejador general generan 500.

### `public/assets/css/estilos.css`

- **Qué hace:** define colores, tipografía, tarjetas, campos, botones, avisos, tabla y adaptación a pantallas pequeñas.
- **Función principal:** dar presentación visual al HTML generado por las vistas.
- **Cómo se conecta:** el navegador lo solicita por el enlace de `header.php`. Sus selectores corresponden a clases e identificadores de las vistas.

Las variables de `:root` permiten cambiar colores y bordes comunes. CSS no procesa formularios, valida credenciales ni administra permisos. En este proyecto las acciones usan solicitudes normales de formularios y enlaces; no hay JavaScript ni AJAX.

## 10. Base de datos: carpeta `database/`

### `database/instalar.sql`

- **Qué hace:** crea la base `login_mvc`, la selecciona y crea la tabla `usuarios` si no existen.
- **Función principal:** preparar la estructura persistente necesaria antes de usar la aplicación.
- **Cómo se conecta:** se importa manualmente, por ejemplo desde phpMyAdmin. Después, `Conexion` accede a la base indicada en `.env` y `Usuario` utiliza la tabla.

| Columna | Para qué sirve |
| --- | --- |
| `id` | Identificador autoincremental y clave primaria. |
| `nombre` | Nombre presentado en el listado y en el saludo de sesión. |
| `correo` | Dato de acceso; una clave única impide duplicados. |
| `password_hash` | Hash de la contraseña utilizado al autenticar. |
| `creado_en` | Fecha de alta generada automáticamente por la base. |

El archivo no se ejecuta en cada visita y no crea cuentas iniciales. Si se usa otro nombre de base, deben ajustarse las referencias `CREATE DATABASE` y `USE` del SQL, además de `DB_DATABASE` en `.env`, o importarse solo el bloque de la tabla dentro de una base ya seleccionada.

### `database/.htaccess`

- **Qué hace:** deniega el acceso HTTP directo a la carpeta.
- **Función principal:** evitar que Apache ofrezca el SQL como descarga pública.
- **Cómo se conecta:** protege la carpeta en el servidor web, sin impedir la importación del archivo desde disco.

## 11. Cómo se conectan configuración, Core y modelo

Este es el recorrido de los datos de conexión:

```text
.env ──────────────────────────┐
                              v
Entorno real del proceso → config/config.php
                              │ devuelve un arreglo
                              v
                       public/index.php
                              │ entrega $config['database']
                              v
                       Core/Conexion.php
                              │ obtener() crea/devuelve PDO
                              v
                       Models/Usuario.php
                              │ ejecuta SQL mediante PDO
                              v
                          MariaDB/MySQL
```

Si una clave existe en el entorno real, tiene prioridad sobre la del `.env`. La unión entre la configuración y el Core se realiza explícitamente en `public/index.php`:

```php
$config = require dirname(__DIR__) . '/config/config.php';
$usuarios = new Usuario(new Conexion($config['database']));
$auth = new AutenticacionController($usuarios);
$controladorUsuarios = new UsuarioController($usuarios);
```

Los constructores reciben lo que necesitan: el modelo recibe la conexión y los controladores reciben el modelo. Esto se conoce como **inyección de dependencias**. `Conexion.php` no necesita leer el `.env` de nuevo.

## 12. Recorrido completo de las acciones

### Mostrar un formulario

```text
Navegador: GET index.php?r=login o r=registro
  → public/index.php prepara configuración, sesión y objetos
  → AutenticacionController selecciona la pantalla
  → vista() incluye header.php + login.php/registro.php + footer.php
  → el navegador recibe HTML y solicita estilos.css
```

Si ya existe una sesión autenticada, los métodos que presentan estos formularios redirigen al listado.

### Registrar una cuenta

```text
Views/auth/registro.php envía POST
  → public/index.php revisa ruta y token CSRF
  → AutenticacionController::registrar() comprueba sesión y valida datos
  → Usuario::buscarPorCorreo() comprueba si el correo existe
  → password_hash() genera el hash si el correo está disponible
  → Usuario::registrar() obtiene PDO desde Conexion e inserta la cuenta
  → se guarda un mensaje y se redirige al login
```

Si hay errores, se vuelve a presentar el registro con los mensajes, el nombre y el correo. La restricción única de la base también impide correos duplicados si llegan registros simultáneos.

### Iniciar sesión

```text
Views/auth/login.php envía POST
  → public/index.php valida ruta y CSRF
  → AutenticacionController::login() revisa los datos
  → Usuario::buscarPorCorreo() consulta la cuenta mediante Conexion/PDO
  → password_verify() comprueba la contraseña contra el hash
  → si corresponde, Usuario::actualizarHash() renueva el hash
  → se regenera el identificador de sesión y se guarda id/nombre
  → se redirige a index.php?r=usuarios
```

Un acceso inválido vuelve a mostrar el formulario con el mensaje «Correo o contraseña incorrectos».

### Listar usuarios

```text
Navegador: GET index.php?r=usuarios
  → public/index.php ejecuta UsuarioController::index()
  → el controlador exige una sesión autenticada
  → Usuario::listar() consulta la base mediante Conexion/PDO
  → vista() combina header.php + usuarios/index.php + footer.php
  → el navegador muestra la tabla con estilos.css
```

Sin autenticación se redirige al login antes de consultar la tabla. El listado no recibe hashes de contraseñas.

### Cerrar sesión

```text
El formulario de header.php envía POST con r=logout y token
  → public/index.php valida ruta y CSRF
  → AutenticacionController::logout()
  → Seguridad::cerrarSesion() elimina el estado de sesión y vence la cookie
  → se redirige al login
```

Esta acción no ejecuta una eliminación de usuarios en la base.

## 13. Dónde buscar según lo que quieras modificar

| Cambio | Archivo principal | Relación que debes revisar |
| --- | --- | --- |
| Cambiar servidor o credenciales de base | `.env` | Claves que espera `config/config.php`. |
| Cambiar cómo se interpreta la configuración | `config/config.php` | Arreglo que consume `public/index.php` y recibe `Conexion`. |
| Cambiar opciones de PDO | `app/Core/Conexion.php` | Métodos SQL del modelo. |
| Cambiar consultas | `app/Models/Usuario.php` | Datos esperados por controladores y vistas. |
| Cambiar validaciones de registro o acceso | `app/Controllers/AutenticacionController.php` | Campos de las vistas y restricciones de la tabla. |
| Cambiar reglas del listado | `app/Controllers/UsuarioController.php` | `Usuario::listar()` y la vista del listado. |
| Cambiar una pantalla | La vista correspondiente en `app/Views/` | Variables que le entrega el controlador. |
| Cambiar navegación y pie | `app/Views/partials/header.php` y `footer.php` | Rutas de `public/index.php` y estructura de contenedores. |
| Cambiar colores y tamaños | `public/assets/css/estilos.css` | Clases utilizadas por las vistas. |
| Agregar una ruta | `public/index.php` | Método del controlador y vista, si corresponde. |
| Cambiar sesión o CSRF | `app/Core/Seguridad.php` | Validación en la entrada pública y campos ocultos de formularios. |
| Cambiar estructura de datos | `database/instalar.sql` | Modelo, validaciones y vistas; modificar este archivo no migra por sí solo una tabla existente. |

Para estudiar el código, comienza por `public/index.php`, sigue hacia configuración y Core, revisa el modelo y los controladores, y termina con las vistas y CSS. Para utilizar el sistema, consulta `MANUAL_USUARIO.md`; para instalarlo, consulta `README.md`.
