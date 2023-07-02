<p align="center"><a href="https://dominicantechnology.com" target="_blank"><img src="![PRESTASYSRas](https://github.com/Raxlor/api-sms-Dominican_technology/assets/69202062/f8ee45bf-6c19-4200-a2b4-ca7296064f78)
" width="400"></a></p>



## Desarrollador por Dominican Technology

# API DE ENVIO SMS 

Proyecto creado para facilitar la distribución de notificaciones en sistemas financieros, tanto en notificaciones como en solicitudes de información manual o automáticas.

## Requisitos

- PHP 8.1 o superior
- Laravel Framework 10.x

## Instalación

1. Clona el repositorio: `git clone https://github.com/Raxlor/api-sms-Dominican_technolog`
2. Instala las dependencias: `composer install`
3. Configura el archivo .env con la información de tu base de datos y otros ajustes necesarios.
4. Ejecuta las migraciones: `php artisan migrate`
5. Genera una clave de aplicación: `php artisan key:generate`

## Configuración

Antes de utilizar la API, asegúrate de configurar los siguientes parámetros en el archivo .env:

- `URI_SMS_API`: URL de la API de mensajes SMS.
- `Authorization_SMS_API`: Token de autorización para acceder a la API de mensajes SMS.
- `ApiKey_SMS_API`: Clave API para la API de mensajes SMS.
- `country_SMS_API`: País aceptado para los números telefónicos (ejemplo: DO para República Dominicana).
- `tag_SMS_API`: Etiqueta para los mensajes enviados a través de la API.

## Uso

### POST /sms

Enviar un mensaje de texto.

**Parámetros:**
- `numero` (string, requerido): Número de teléfono del destinatario (sin guiones, formato: xxxxxxxxxx).
- `sms` (string, requerido): Contenido del mensaje de texto.

**Respuestas:**
- 200 OK: Envío exitoso. Retorna un objeto JSON con la siguiente estructura:

{
"mensaje": "envío exitoso",
"caracteres": [cantidad de caracteres],
"coste_envio": [cantidad a restar del saldo]
}

- 401 Unauthorized: Faltan campos obligatorios: número y mensaje a enviar.
- 402 Payment Required: Saldo insuficiente para enviar el mensaje.
- 403 Forbidden: Acceso denegado debido a la IP no autorizada.
- 500 Internal Server Error: Error en la solicitud.

### POST /balance

Consultar saldo disponible.

**Respuesta:**
- 200 OK: Retorna un objeto JSON con el siguiente formato:
- {
"SMS_DISPONIBLES": [saldo disponible]
}

### POST /historial

Consultar historial de envíos.

**Respuesta:**
- 200 OK: Retorna un objeto JSON con los registros de envíos realizados.

## Licencia

Este software y su API son propiedad de [DOMINICAN TECHNOLOGY]. Fueron creados para ser utilizados exclusivamente en el contexto del sistema [PRESTASYS DOMINICANA]. El uso no autorizado de este software y su API está estrictamente prohibido y puede tener consecuencias legales y penales.

Consulta la licencia completa [aquí](enlace-a-la-licencia).

## Contacto

Para cualquier consulta o solicitud de permisos, ponte en contacto con nosotros en [dirección de correo electrónico].


  
## Rutas de API

| Ruta                | Método | Descripción                       | Inputs Requeridos  | Tipo de Dato       | Longitud Máxima    | Autenticación      |
|---------------------|--------|-----------------------------------|--------------------|--------------------|--------------------|--------------------|
| /sms                | POST   | Enviar un mensaje de texto         | numero, sms        | string             | 264                | Bearer token       |
| /balance            | POST   | Obtener el saldo disponible        | -                  | -                  | -                  | Bearer token       |
| /historial          | POST   | Obtener el historial de envíos     | -                  | -                  | -                  | Bearer token       |

**Nota:** Para el campo "numero" en la ruta `/sms`, se espera un número de teléfono válido en formato de República Dominicana (DO) sin guiones y con una longitud máxima de 10 dígitos.

