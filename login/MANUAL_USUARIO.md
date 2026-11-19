# Manual de usuario · Aula Acceso

## Preparación inicial

La persona que configura el equipo debe iniciar Apache y MySQL en XAMPP, importar `database/instalar.sql` desde phpMyAdmin y completar `.env` con los datos de esa base. Las instrucciones técnicas están en `README.md`.

Abrir **http://localhost/Login/**. Aparecerá el formulario de acceso. No hay cuentas predeterminadas.

## Crear una cuenta

1. Pulsar **Crear una cuenta** debajo del formulario de acceso.
2. Escribir un nombre de entre 2 y 100 caracteres.
3. Escribir un correo válido que no esté registrado.
4. Elegir una contraseña de al menos 8 caracteres y repetirla exactamente.
5. Pulsar **Crear cuenta**.

Si todo es correcto, se muestra **Cuenta creada. Ya puedes iniciar sesión.** El registro no envía correo ni requiere activación. Si se señalan errores, corregirlos y volver a escribir las contraseñas; esos campos no se conservan por seguridad.

Las contraseñas muy largas, especialmente con muchos emojis, pueden superar el máximo admitido de 72 bytes. En ese caso, elegir una contraseña que cumpla el límite indicado en el aviso.

## Iniciar sesión

1. Escribir el correo con el que se creó la cuenta.
2. Escribir su contraseña.
3. Pulsar **Iniciar sesión**.

Con datos válidos, se abre el listado de usuarios. Si aparece **Correo o contraseña incorrectos**, verificar ambos datos. Los espacios forman parte de la contraseña; no agregarlos al copiarla.

## Consultar usuarios

La pantalla muestra ID, nombre, correo y fecha de registro UTC, de la cuenta más reciente a la más antigua. No se muestran contraseñas ni hashes.

Todas las cuentas que inicien sesión pueden ver el mismo directorio. En celular, deslizar la tabla horizontalmente para consultar todas las columnas.

## Registrar otra cuenta

El dashboard solo permite consultar el listado y cerrar sesión. Para crear otra cuenta, primero pulsar **Cerrar sesión** y después **Crear una cuenta** en el formulario de acceso. No se puede abrir ni enviar el registro mientras haya una sesión iniciada, aunque se escriba su dirección manualmente.

## Cerrar sesión

Pulsar **Cerrar sesión** en la barra superior. El sistema vuelve al formulario de acceso. Al intentar abrir el listado, se pedirá iniciar sesión de nuevo.

La sesión también vence tras 30 minutos sin actividad. Esto no elimina la cuenta ni modifica su contraseña.

## Mensajes frecuentes

| Mensaje o situación | Qué hacer |
|---|---|
| Ese correo ya está registrado | Usar la cuenta existente o registrar un correo diferente |
| Las contraseñas no coinciden | Volver a escribir ambos campos con el mismo valor |
| Formulario vencido | Abrir de nuevo el formulario; puede ocurrir después de cambiar la sesión en otra pestaña |
| No fue posible completar la operación | Avisar a quien configura el equipo para revisar `.env`, la tabla y el servicio MySQL |
| No abre localhost | Comprobar que Apache esté iniciado y que se utilice su puerto correcto |
| No recuerda su contraseña | Este ejemplo no incluye recuperación; solicitar apoyo a quien administra el ejercicio |

No compartir contraseñas. La lista sirve únicamente para consultar usuarios; el proyecto no incluye edición ni eliminación.
