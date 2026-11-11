# Descripción técnica y guía para alumnos

## 1. Problema que resuelve

El sistema permite crear una cuenta, autenticarla y consultar un directorio. El objetivo educativo es entender cómo se conectan formularios HTML, PHP, PDO y MariaDB/MySQL sin utilizar un framework.

Las pantallas públicas contienen solo los formularios con estilos. En el dashboard hay únicamente listado y cierre de sesión. El registro, tanto GET como POST, rechaza una sesión ya autenticada mediante redirección al listado.

## 2. Arquitectura MVC

```text
Formulario HTML
      ↓ solicitud GET o POST
public/index.php: configuración, sesión y selección de ruta
      ↓
Controlador: valida y decide qué hacer
      ↓
Modelo Usuario → Conexion → PDO → MariaDB/MySQL
      ↓
Controlador → función vista() → cabecera + contenido + pie
      ↓ respuesta HTML con CSS
Navegador
```

| Capa | Archivos | Responsabilidad |
|---|---|---|
| Modelo | app/Models/Usuario.php | Consultar, insertar y mantener hashes |
| Vista | app/Views/ | Presentar HTML y datos escapados |
| Controlador | app/Controllers/ | Validar, coordinar y seleccionar la respuesta |
| Entrada | public/index.php | Cargar servicios y ejecutar rutas conocidas |
| Infraestructura | config/config.php, app/Core/ | Entorno, PDO, sesión y protección de formularios |
| Funciones comunes | app/Support/funciones.php | Evitar duplicar escape, URLs y renderizado |
| Presentación | public/assets/css/estilos.css | Colores, tamaños, distribución y móvil |

Las vistas no hacen SQL. El modelo no lee formularios. El controlador recibe las entradas y entrega datos preparados para mostrar.

## 3. Arranque de una solicitud

1. `index.php` de la raíz redirige a `public/`.
2. `public/index.php` registra el autoload que transforma los namespaces `App` en rutas dentro de `app/`.
3. Carga las funciones comunes y envía cabeceras HTTP.
4. Obtiene configuración e inicia/recupera la sesión.
5. Construye conexión, modelo y controladores. La conexión a MySQL aún no se abre.
6. Lee método HTTP y parámetro `r`, y busca una acción en la tabla de rutas.
7. En POST verifica el token CSRF antes de ejecutar el controlador.
8. Ejecuta la acción o presenta un error amigable.

El nombre de la vista lo fija el código del controlador. El visitante no puede elegir un archivo PHP para incluir pasando una ruta arbitraria.

## 4. Variables y conexión

`config/config.php` lee `.env` directamente con `parse_ini_file()`, en formato INI simple y modo RAW. Comprueba que exista el archivo y que las claves requeridas sean cadenas. Valida el puerto y el nombre de la base.

La función anónima `$leer` recibe el nombre de una variable y devuelve primero su valor del entorno real; si no existe, usa el valor del archivo. No convierte el archivo en variables globales del sistema.

| Clave | Uso |
|---|---|
| APP_NAME | Nombre mostrado en título y pie |
| DB_HOST | Dirección del servidor MariaDB/MySQL |
| DB_PORT | Puerto de conexión |
| DB_DATABASE | Nombre de la base de trabajo |
| DB_USERNAME | Usuario SQL |
| DB_PASSWORD | Contraseña de ese usuario SQL |

`Conexion::__construct()` guarda el arreglo de configuración. `obtener()` abre PDO al necesitarlo y reutiliza el objeto durante esa solicitud. El DSN identifica servidor, puerto y base; `utf8mb4` permite Unicode y la zona de conexión se fija en UTC.

PDO se configura para lanzar excepciones, devolver arreglos asociativos y preparar las consultas de forma nativa. Una consulta preparada mantiene separados la estructura SQL y los valores del visitante.

## 5. Modelo de datos

La instalación se encuentra completa en `database/instalar.sql`. Se importa desde phpMyAdmin.

| Columna | Tipo | Explicación |
|---|---|---|
| id | INT UNSIGNED AUTO_INCREMENT | Identificador positivo autogenerado |
| nombre | VARCHAR(100) NOT NULL | Nombre visible |
| correo | VARCHAR(190) NOT NULL, UNIQUE | Correo de acceso |
| password_hash | VARCHAR(255) NOT NULL | Hash, algoritmo, sal y parámetros |
| creado_en | TIMESTAMP | Fecha de alta generada por MySQL |

`IF NOT EXISTS` evita recrear una base o tabla existente; no modifica ni migra estructuras anteriores. `UNIQUE` impide correos repetidos incluso ante dos solicitudes simultáneas.

## 6. Mapa de clases y métodos

### Modelo Usuario

| Método | Recibe | Devuelve o realiza |
|---|---|---|
| __construct | Objeto Conexion | Conserva el acceso a PDO |
| buscarPorCorreo | Correo normalizado | Fila con hash o false |
| registrar | Nombre, correo y hash | ID insertado |
| listar | Sin parámetros | Filas sin contraseñas ni hashes |
| actualizarHash | ID y nuevo hash | Actualiza solo esa cuenta |

`prepare()` define los marcadores, `execute()` aporta sus valores y `fetch()` recupera una fila. `fetchAll()` obtiene el arreglo de filas. `lastInsertId()` devuelve el ID generado por la inserción de la conexión.

`listar()` utiliza `query()` porque su SQL es fijo y no contiene entradas del visitante. Selecciona explícitamente campos visibles; el hash no llega a la tabla HTML.

### AutenticacionController

| Método | Entrada de la solicitud | Respuesta |
|---|---|---|
| formularioLogin | GET login | Formulario o redirección a usuarios |
| formularioRegistro | GET registro | Formulario solo sin sesión |
| registrar | POST nombre, correo, contraseña y confirmación | Errores 422 o redirección al login |
| login | POST correo y contraseña | Errores 422 o sesión y redirección al listado |
| logout | POST previamente validado con CSRF | Destruye sesión y redirige al login |

El constructor recibe el modelo ya creado. Esta entrega de dependencias permite que el controlador use datos sin conocer el DSN ni las credenciales de PDO.

### UsuarioController

`index()` comprueba la existencia de `$_SESSION['usuario']`. Si falta, guarda un mensaje y redirige antes de consultar la base. Si existe, solicita `Usuario::listar()` y entrega el arreglo a `usuarios/index.php`.

### Seguridad

| Método | Entrada | Salida o efecto |
|---|---|---|
| iniciarSesion | Contexto HTTP y sesión previa | Inicializa cookies, verifica inactividad y actualiza el tiempo |
| token | Sesión actual | Token aleatorio persistente en esa sesión |
| csrfValido | Token del POST | true o false |
| cerrarSesion | Sesión actual | Limpia datos, vence cookie y destruye sesión |

Estos métodos son `static`: se invocan con el nombre de la clase, sin crear una instancia. La sesión almacena ID y nombre del usuario, nunca la contraseña ni el hash.

## 7. Funciones compartidas

| Función | Recibe | Propósito y resultado |
|---|---|---|
| escapar | Valor a imprimir | Texto protegido con htmlspecialchars para contexto HTML |
| url | Nombre de ruta conocida | Dirección relativa index.php?r=... |
| redireccionar | Nombre de ruta | Envía Location con 303 y termina con exit |
| vista | Nombre interno, arreglo de datos y código HTTP | Incluye cabecera, vista y pie; no retorna contenido |
| campo | Nombre de campo y opción de recortar | Cadena del POST o cadena vacía |

`extract(..., EXTR_SKIP)` hace disponibles las claves del arreglo como variables de la vista sin sobrescribir variables locales existentes. `compact()` realiza el recorrido inverso: construye un arreglo a partir de nombres de variables.

`escapar()` no es una protección SQL: se utiliza al imprimir HTML. Las consultas SQL se protegen separando sus parámetros con PDO.

## 8. Recorrido del registro

1. Un visitante abre `?r=registro` y recibe campos vacíos con token CSRF.
2. Al guardar, PHP verifica el token y ejecuta `registrar()`.
3. El controlador comprueba que no haya sesión autenticada.
4. Lee nombre y correo sin espacios extremos. Conserva los espacios de las contraseñas.
5. Valida nombre de 2–100 caracteres, correo válido de hasta 190 bytes, contraseña de al menos 8 caracteres y hasta 72 bytes, ausencia de caracteres nulos y confirmación idéntica.
6. Consulta el correo. Si existe, muestra un error.
7. Genera `password_hash($password, PASSWORD_DEFAULT)` y entrega únicamente el hash al modelo.
8. Tras insertar, guarda un mensaje de una sola lectura y redirige al login con 303.

El hash no es cifrado reversible. PHP genera la sal y guarda dentro del resultado los datos necesarios para verificarlo. En PHP 8.2, PASSWORD_DEFAULT utiliza bcrypt; el límite de 72 bytes evita su truncamiento. Un carácter Unicode puede ocupar más de un byte.

El registro no inicia sesión automáticamente. Ante validaciones fallidas solo se conservan nombre y correo, no los campos de contraseña.

## 9. Recorrido del login y del logout

El login normaliza el correo y busca su fila. `password_verify()` comprueba la contraseña contra el hash. Un usuario inexistente o una contraseña incorrecta producen el mismo mensaje.

Después de verificar, `password_needs_rehash()` permite actualizar el hash si cambiaron los parámetros recomendados. Se regenera el ID de sesión, se guardan ID/nombre, se descarta el token CSRF anterior y se redirige al listado.

La cookie contiene un identificador, no las credenciales. Se configura HttpOnly y SameSite=Lax; se añade Secure cuando PHP recibe HTTPS directamente. La expiración por inactividad se comprueba en la siguiente solicitud después de 30 minutos.

El logout solo se ejecuta mediante POST válido con CSRF. Limpia la sesión, vence su cookie y destruye sus datos del lado del servidor. No elimina ninguna fila de usuarios.

El flujo POST → 303 → GET evita repetir escrituras al actualizar la página de destino.

## 10. Vistas y CSS

| Vista | Datos principales y función |
|---|---|
| auth/login.php | Correo, errores y formulario de acceso |
| auth/registro.php | Nombre, correo, errores y confirmación |
| usuarios/index.php | Usuario autenticado y arreglo del listado |
| error.php | Título y detalle amigable |
| partials/header.php | Metadatos, estilos, navegación y aviso de sesión |
| partials/footer.php | Cierre de contenedores y pie informativo |
| partials/errores.php | Lista compartida de errores |

En los formularios, `name` define la clave enviada en POST; `id` enlaza el campo con su etiqueta `label`. Los atributos `required` y `minlength` ayudan en el navegador, pero no reemplazan las validaciones PHP.

`type=password` oculta caracteres en pantalla; no cifra su envío. Los campos de contraseña no tienen un `value` con el contenido previo. El token oculto CSRF no es una contraseña.

La tabla recorre `$usuarios` con `foreach`. Su avatar es solo la inicial del nombre y el ID con ceros es una presentación: no altera el valor almacenado. Los datos dinámicos se escapan antes de imprimirse.

El CSS está dividido en seis partes: variables, tarjetas, controles, mensajes, dashboard y móvil. Los comentarios explican selectores, unidades, Flexbox, márgenes, foco y desplazamiento de tablas. No hay panel decorativo, logotipo en la cabecera ni recursos externos.

## 11. Respuestas y archivos de protección

| Código HTTP | Significado en este proyecto |
|---|---|
| 200 | Página mostrada correctamente |
| 303 | Redirección posterior a una operación o comprobación |
| 403 | Token de formulario inválido |
| 404 | Ruta no definida |
| 422 | Campos o credenciales no válidos |
| 500 | Fallo inesperado de configuración, conexión o ejecución |

El error técnico se registra con `error_log()` según la configuración de PHP; no se imprime la excepción al visitante. La aplicación no incluye una carpeta propia de logs.

`.gitignore` excluye `.env` del repositorio. Los `.htaccess` bloquean la descarga de configuración, SQL y código interno mediante Apache. El de la raíz también evita listar directorios y acceder al historial Git. No sustituyen permisos del sistema de archivos y no son interpretados por `php -S`.

## 12. Glosario breve

- **MVC:** separación entre datos, presentación y coordinación.
- **PDO:** interfaz de PHP para conectar y ejecutar consultas en una base.
- **DSN:** descripción del servidor y base que necesita PDO.
- **Hash:** resultado de una función de una sola vía para verificar contraseñas.
- **Sesión:** datos de una visita conservados en el servidor mediante un identificador.
- **Cookie:** dato que el navegador conserva y envía al sitio.
- **CSRF:** intento de provocar solicitudes usando una sesión ajena; aquí se verifican tokens.
- **Namespace:** organización lógica de nombres de clases.
- **Autoload:** carga de una clase cuando PHP la necesita.
- **GET / POST:** métodos HTTP usados para consultar páginas o enviar formularios.
- **Escape HTML:** convertir caracteres especiales para mostrarlos como texto.
- **Media query:** regla CSS que se aplica, por ejemplo, a un ancho de pantalla concreto.

## 13. Verificación y actividades

La carpeta actual no incluye un ejecutor de pruebas automatizadas. Se puede revisar sintaxis con `php -l archivo.php` y seguir las comprobaciones manuales del README sin confundir esas dos tareas.

Actividades para alumnos:

1. Seguir una solicitud desde el campo HTML hasta el parámetro PDO.
2. Explicar por qué ocultar el enlace de registro no basta para impedir altas con sesión.
3. Localizar cada uso de hash, token y escape e indicar qué problema resuelve.
4. Cambiar solo las variables CSS y comprobar qué componentes reutilizan sus colores.
5. Explicar por qué una contraseña nunca aparece en el arreglo de sesión ni en la tabla HTML.

El alcance no incluye roles, verificación de correo, recuperación de contraseña ni bloqueo de intentos. Todas las cuentas autenticadas ven el mismo directorio. Para producción se requieren HTTPS, permisos mínimos y las reglas institucionales correspondientes.
