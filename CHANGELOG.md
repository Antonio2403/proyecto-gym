# Changelog — Spartum (gym)

Registro detallado de cambios **pendientes de commit** respecto al último commit en GitHub.

| Campo | Valor |
|--------|--------|
| **Último commit en remoto** | `f5aee39` — *hosteando* |
| **Fecha de este inventario** | 13 de mayo de 2026 |
| **Alcance** | Release completa: seguridad, tickets, UI Spartum unificada, transiciones GSAP, formularios panel, bento admin/monitor |

> **Nota:** Este documento resume el commit que integra todo el working tree desde `f5aee39`.

---

## Índice

1. [Base de datos](#1-base-de-datos)
2. [Dependencias y configuración](#2-dependencias-y-configuración)
3. [Seguridad y arranque](#3-seguridad-y-arranque)
4. [Rutas HTTP nuevas o modificadas](#4-rutas-http-nuevas-o-modificadas)
5. [Controladores](#5-controladores)
6. [Modelos](#6-modelos)
7. [Vistas y layouts](#7-vistas-y-layouts)
8. [Assets (CSS / JS)](#8-assets-css--js)
9. [Scripts CLI](#9-scripts-cli)
10. [Documentación auxiliar](#10-documentación-auxiliar)
11. [Eliminaciones y regresiones funcionales](#11-eliminaciones-y-regresiones-funcionales)
12. [Listado de ficheros por estado git](#12-listado-de-ficheros-por-estado-git)
13. [Unificación UI Spartum (formularios, bento, transiciones, banners)](#13-unificación-ui-spartum-formularios-bento-transiciones-banners)

---

## 1. Base de datos

Toda la evolución del esquema se aplica de forma **idempotente** al conectar (`BasedeDatos::Conectar()` → `ensureSchemaUpgrades()` en `app/modelos/database.php`). No hay **triggers**, **procedimientos almacenados**, **funciones SQL** ni **eventos** definidos en el proyecto.

### 1.1 Tablas nuevas

| Tabla | Descripción |
|--------|-------------|
| **`recuperacion_cuenta_ticket`** | Tickets de gestión de cuenta en recepción (código de 6 dígitos, token de acceso, caducidad 48 h, tipos y estados). |
| **`admin_config`** | Configuración editable desde el panel admin (clave/valor). Valores iniciales: `password_rotation_days=90`, `session_idle_timeout_seconds=2700`. |
| **`actividad_dias`** | Relación N:M actividad ↔ días de la semana (`L`…`D`). Migra datos desde `actividades.dia_semana`. |

#### `recuperacion_cuenta_ticket` (columnas principales)

- `id`, `usuario_id` (FK → `usuarios.id` ON DELETE CASCADE)
- `tipo` ENUM: `correo`, `contrasena`, `reactivacion`, `recuperacion` (default `correo`)
- `codigo` VARCHAR(16), `intentos_codigo` TINYINT UNSIGNED (default 0)
- `acceso_token` VARCHAR(64) UNIQUE
- `expira_en` DATETIME, `estado` ENUM: `pendiente`, `usado`, `cancelado`, `cerrado_por_admin`
- `creado_en` DATETIME
- Índice: `idx_rec_ticket_user_estado (usuario_id, estado)`

#### `admin_config`

- `clave` VARCHAR(64) PK, `valor` TEXT, `actualizado_en` DATETIME ON UPDATE CURRENT_TIMESTAMP

#### `actividad_dias`

- PK compuesta `(actividad_id, dia_semana)`
- FK `actividad_id` → `actividades.id` ON DELETE CASCADE

### 1.2 Columnas nuevas en tablas existentes

| Tabla | Columna(s) | Propósito |
|--------|------------|-----------|
| **`usuarios`** | `password_changed_at` | Caducidad de contraseña por antigüedad |
| | `password_reset_token`, `password_reset_expires` | Enlace de restablecimiento por correo |
| | `email_confirmado`, `token_confirmacion`, `token_confirmacion_expira` | Confirmación de email en registro |
| | `avatar_path` | *(columna en esquema; funcionalidad de subida eliminada en código)* |
| | `bloqueo_tipo` ENUM `N`/`T`/`P` | Normal / baja temporal (indefinida) / baja permanente |
| | `bloqueado_hasta`, `bloqueo_motivo` | Detalle de bloqueo |
| | `ticket_usuario_cancelado_en` | Cooldown 48 h tras cancelar ticket por el socio |
| **`subscripciones`** | `en_oferta`, `oferta_motivo`, `oferta_fin` | Promociones con fecha límite de compra |
| **`actividades`** | `recurrente` TINYINT(1) | Sesión recurrente vs puntual |
| **`inscripciones`** | `asistio` ENUM `S`/`N` | Control de asistencia |
| **`fisioterapeutas`** | `usuario_id` (+ UNIQUE, FK opcional a `usuarios`) | Vincular fisio a cuenta de usuario |

### 1.3 Índices nuevos

| Tabla | Índice |
|--------|--------|
| `usuarios` | `idx_usu_token (token_confirmacion)` |
| `usuarios` | `idx_usu_pwreset (password_reset_token)` |
| `recuperacion_cuenta_ticket` | `uq_rec_ticket_token`, `idx_rec_ticket_user_estado` |
| `fisioterapeutas` | `uq_fisio_usuario (usuario_id)` |

### 1.4 Restricciones CHECK (integridad en motor)

Aplicadas si no existen (`ensureIntegrityCheckConstraints`):

| Constraint | Tabla | Regla |
|------------|--------|--------|
| `chk_gp_usuarios_dni` | `usuarios` | DNI 8 dígitos + letra o NIE X/Y/Z + 7 dígitos + letra |
| `chk_gp_usuarios_email` | `usuarios` | Longitud 3–255 y patrón email mínimo |
| `chk_gp_usuarios_email_confirmado` | `usuarios` | `email_confirmado IN (0,1)` |
| `chk_gp_rec_intentos` | `recuperacion_cuenta_ticket` | `intentos_codigo` entre 0 y 50 |
| `chk_gp_rec_codigo_len` | `recuperacion_cuenta_ticket` | Longitud de `codigo` 1–32 |
| `chk_gp_sub_precio` | `subscripciones` | `precio >= 0` si no NULL |
| `chk_gp_sub_duracion` | `subscripciones` | `duracion >= 0` si no NULL |
| `chk_gp_sub_num_clases` | `subscripciones` | `numero_clases >= 0` si no NULL |
| `chk_gp_fb_email` | `feedback` | Mismo patrón email que usuarios |

La **letra de control DNI/NIE (módulo 23)** y la **política de contraseñas** siguen validándose en **PHP**, no en CHECK sobre `clave` (hash).

### 1.5 Otros cambios de esquema

- Conversión de tablas a **InnoDB** si el motor era distinto (`ensureInnoDbEngine`).
- Admin inicial: email por defecto `INITIAL_ADMIN_EMAIL` (default `alfonsojaime02@gmail.com`); contraseña desde `INITIAL_ADMIN_PASSWORD` o aleatoria con aviso en log + script `reset_admin_password.php`.
- `PDO::ATTR_EMULATE_PREPARES = false` en la conexión.

### 1.6 Modelo PHP de tickets (`app/modelos/recuperacion_cuenta_ticket.php`) — métodos nuevos

`etiquetaTipo`, `tieneTicketPendienteVigente`, `puedeUsuarioCrearTicket`, `intentarCrear`, `obtenerPendientePorId`, `obtenerPendienteVigentePorUsuario`, `obtenerPendientePorToken`, `marcarUsado`, `registrarIntentoCodigoFallido`, `cancelarPorUsuario`, `cerrarPorAdministrador`, `regenerarAcceso`, `listarPendientes`, `listarHistorial`, `firmaDesdeListas`, `firmaVistaAdminTickets`.

### 1.7 Modelo PHP de configuración (`app/modelos/admin_config.php`) — clase nueva

`AdminConfig::get`, `getInt`, `set`.

---

## 2. Dependencias y configuración

### `composer.json`

- PHP **>= 8.1**
- Extensiones requeridas: `curl`, `mbstring`, `openssl`, `pdo`, `pdo_mysql`
- Paquetes: `bramus/router`, `stripe/stripe-php`, `vlucas/phpdotenv`, `phpmailer/phpmailer`

### `.gitignore`

- Añadidos: `env`, `.claves`, `*.local` (además de `.env` y `/vendor`).

### `.htaccess`

- Bloqueo de acceso web a `env`, `.env`, `.claves`, `composer.json/lock`, `CHANGELOG.txt`, carpetas `app`, `core`, `script(s)`, `vendor`, `.git` (redirigen a `index.php` → 404 controlado).

### Variables de entorno relevantes (nuevas o usadas)

| Variable | Uso |
|----------|-----|
| `SESSION_IDLE_TIMEOUT_SECONDS` | Prioridad sobre `admin_config.session_idle_timeout_seconds` |
| `INITIAL_ADMIN_EMAIL` / `INITIAL_ADMIN_PASSWORD` | Alta del admin en primer arranque |
| `APP_FORCE_SECURE_COOKIES` / `APP_TRUST_PROXY` | Cookies Secure y detección HTTPS detrás de proxy |
| `MAIL_*` | SMTP vía PHPMailer (`core/helpers/mail_smtp.php`) |

---

## 3. Seguridad y arranque

### Archivo nuevo: `core/bootstrap_security.php`

- `gp_env_bool`, `gp_request_is_https`, `gp_session_cookie_secure`
- `gp_configure_session_ini()` — strict mode, HttpOnly, SameSite=Lax, Secure condicional
- `gp_send_security_headers()` — X-Frame-Options, nosniff, Referrer-Policy, Permissions-Policy, HSTS si HTTPS

### `index.php`

- Carga `.env` / `env` con `safeLoad`
- Bootstrap de seguridad **antes** de `session_start()`
- **Timeout de sesión por inactividad en servidor**: `last_activity_at`, TTL desde env o `AdminConfig`, redirección a login o JSON 440
- Comprobación de **bloqueo de cuenta** en cada petición autenticada; cierre de sesión y JSON para polling `/sesion/estado`
- Router 404 delegado a `ErrorControlador@notFound`

### CSRF global en rutas POST (`routers/web.php`)

- Todas las rutas POST pasan por `csrf_validate_request()`; respuesta **419** (JSON o redirect `/404`).

### `core/helpers/form_validacion.php` — funciones nuevas/ampliadas

- `csrf_token`, `csrf_validate`, `csrf_validate_request`
- `fv_email_valido`, `fv_documento_identidad_es`, `fv_clave_registro_valida`, `fv_clave_fuerte`
- `fv_login_identificador`, `fv_telefono_es_opcional`, `fv_telefono_es_obligatorio`, `fv_telefono_es_a_digitos9`

### `core/Controller.php`

- `requireRole()` para áreas admin/monitor
- `redirigirFisioFueraPortal()`
- Redirección obligatoria a cambio de clave si `password_must_change` en sesión

### Sesión e inactividad (cliente + servidor)

| Capa | Comportamiento |
|------|----------------|
| **Servidor** (`index.php`) | Expira sesión PHP si supera TTL sin peticiones |
| **Cliente** (`public/assets/js/site-preferences.js`) | Tras TTL sin interacción, POST `/sesion/cerrar-inactividad` + modal Spartum |
| **Meta** (`app/vistas/layouts/partials/session_idle_meta.php`) | `gp-session-idle-seconds` para JS |
| **Polling** | GET `/sesion/estado` cada 5 s — cierre forzado si baja admin (modal) |
| **Admin** | Panel `/admin/config-seguridad` — días rotación contraseña e inactividad (solo enteros, `ctype_digit`) |

### Borrados críticos: GET → POST

Eliminación de actividades, monitores, fisios, salas, materiales, feedback y suscripciones pasa a **POST** (+ CSRF en formularios generados por `admin-datagrid.js`).

---

## 4. Rutas HTTP nuevas o modificadas

### Nuevas (resumen)

| Método | Ruta | Controlador |
|--------|------|-------------|
| POST | `/cookies/aceptar` | `InicioControlador@aceptarCookies` |
| GET | `/sesion/estado` | `LoginControlador@estadoSesion` |
| POST | `/sesion/cerrar-inactividad` | `LoginControlador@cerrarInactividad` |
| GET/POST | `/cuenta/cambiar-clave` | `CambioClaveControlador` |
| GET/POST | `/cuenta/perfil` | `UsuarioControlador` (admin/monitor) |
| GET | `/ticket`, `/recuperar-contrasena`, `/no-recuerdo-mi-cuenta`, `/no-recuerdo-quien-soy` | `RecuperarContrasenaControlador@formulario` |
| POST | `/ticket/crear`, `/ticket/codigo`, `/ticket/codigo-por-dni`, `/ticket/cancelar` | Flujo ticket DNI/teléfono/código |
| POST | `/recuperar-contrasena/email`, rutas legacy `/recuperar-contrasena/dni/*` | Recuperación por email y DNI |
| GET | `/recuperar-contrasena/ticket/{token64}` | Continuar flujo por token |
| GET/POST | `/restablecer-contrasena` | Nueva contraseña con token |
| GET/POST | `/reactivar-cuenta` (+ solicitar/código) | `ReactivarCuentaControlador` |
| GET | `/404` | `ErrorControlador@notFound` |
| GET | `/admin/recuperacion-cuenta` | Tickets en panel |
| POST | `/admin/recuperacion-cuenta/enviar-correo`, `cerrar-ticket` | Gestión ticket admin |
| GET/POST | `/admin/config-seguridad` | Políticas de seguridad |
| GET | `/admin/ajax/recuperacion-tickets-firma` | Polling firma lista tickets |
| POST | `/admin/clientes/cancelar-plan`, `bloquear`, `desbloquear` | Gestión clientes |
| GET/POST | `/admin/feedback/responder`, `/admin/feedback/responder` | Respuesta por email al contacto |
| POST | `/admin/eliminarSubscripcion` | Retirar plan del catálogo |

### Modificadas (método)

| Antes | Ahora |
|--------|--------|
| GET eliminar fisio/monitor/sala/material/actividad/feedback | **POST** (+ CSRF) |

---

## 5. Controladores

### Nuevos

| Archivo | Responsabilidad |
|---------|-----------------|
| `CambioClaveControlador.php` | Cambio obligatorio o voluntario de contraseña (política fuerte + rotación) |
| `RecuperarContrasenaControlador.php` | Flujo `/ticket`, email, DNI, código, restablecer clave, cancelar ticket |
| `ReactivarCuentaControlador.php` | Reactivación de cuenta con baja normal (código) |

### Cambios relevantes por controlador

| Controlador | Cambios principales |
|-------------|---------------------|
| **LoginControlador** | Login por identificador (email/DNI), bloqueo T/P, regeneración sesión, `estadoSesion`, `cerrarInactividad` (JSON+CSRF), logout ampliado |
| **UsuarioControlador** | Registro con confirmación email opcional, `formCuentaPerfil` / `guardarCuentaPerfil`, validaciones fuertes, bloqueo/baja, **eliminada lógica de avatar** |
| **AdminControlador** | `cancelarPlanCliente`, `bloquearCliente`, `desbloquearCliente`, `configSeguridad`, `guardarConfigSeguridad` (solo enteros), `recuperacionCuentaTickets`, envío correo y cierre ticket |
| **AdminAjaxControlador** | `recuperacionTicketsFirma` para auto-refresh panel tickets |
| **FeedbackControlador** | `formResponder`, `enviarRespuesta` (email SMTP al remitente, CSRF en POST) |
| **ActividadControlador** | Múltiples días por actividad, borrado en cascada (comentarios, inscripciones, días), validación fechas sesión |
| **SubscripcionControlador** | Campos oferta, `eliminarSubscripcion` (retirada catálogo) |
| **PagoControlador** | Stripe endurecido (metadata, importe, moneda, usuario, plan), vista planes con ofertas y plan activo |
| **InscripcionControlador** | Cupo semanal por suscripción, validación fecha sesión |
| **InicioControlador** | `aceptarCookies`, redirecciones por rol en `inicioAdmin` |
| **ErrorControlador** | Página 404 con contexto de sesión |

---

## 6. Modelos

| Modelo | Cambios principales |
|--------|---------------------|
| **usuario.php** | Confirmación email, login por identificador, tokens recuperación, `establecerClaveFuerte`, bloqueo `N/T/P`, `estadoBloqueo`, búsqueda DNI+teléfono; **sin `avatarPath` / `actualizarAvatar`** |
| **recuperacion_cuenta_ticket.php** | *(archivo nuevo — ver §1.6)* |
| **admin_config.php** | *(archivo nuevo — ver §1.7)* |
| **actividades.php** | Tabla `actividad_dias`, `sincronizarDias`, `fechaEsSesionValida`, borrado relacionado, paginación admin |
| **susbscripcion.php** | Ofertas, `obtenerActivasCatalogo`, `sincronizarCatalogo`, `purgarRetiradasSinUso` |
| **cliente_subscripcion.php** | Plan activo por usuario, cupo reservas semanal, nombre plan en navbar |
| **cliente.php** | Consultas sin `avatar_path` en SELECT |
| **inscripcion.php** | Asistencia, reservas alineadas a días de actividad |
| **feedback.php** | `buscarPaginado`, `obtenerPorId` |
| **database.php** | Esquema completo, migraciones, CHECK, tablas nuevas (+352 líneas netas aprox.) |

---

## 7. Vistas y layouts

### Nuevas

| Vista | Uso |
|--------|-----|
| `admin/configSeguridad.php` | Formulario rotación contraseña + minutos inactividad |
| `admin/recuperacionCuentaTickets.php` | Panel tickets (abiertos/historial, copiar código, enviar enlace) |
| `admin/feedback/responder.php` | Formulario respuesta a mensaje de contacto |
| `frontend/cambiarClaveObligatoria.php` | Cambio de clave forzado |
| `frontend/error404.php` | 404 tematizado |
| `frontend/formEditarPerfilCuenta.php` | Perfil admin/monitor |
| `frontend/recuperarContrasena.php` | Flujo `/ticket` unificado |
| `frontend/restablecerContrasena.php` | Nueva contraseña con token |
| `frontend/reactivarCuenta.php` | Reactivación cuenta |
| `layouts/partials/session_idle_meta.php` | Meta TTL inactividad |

### Modificadas (destacadas)

| Área | Cambios |
|------|---------|
| **admin/inicioAdmin.php** | Hub 3×3 con **9 botones** Spartum (clientes, monitores, fisios, suscripciones, solicitudes, tickets, feedback, actividades, seguridad) |
| **monitor/inicioMonitor.php** | Mismos botones hub para monitor (4 accesos) |
| **monitor/verMonitorSolicitudes.php**, **verMisSolicitudes.php** | Toolbar `gp-sp-toolbar`, tablas `gp-table-light` |
| **admin/feedback/ver.php** | Columna Responder + datagrid `data-url-responder-prefix` |
| **frontend/login.php** | Toggle login/registro, enlaces ticket, modales, validación |
| **frontend/pago/pagar.php** | Plan activo vs catálogo, ofertas, contador |
| **frontend/actividades.php** | Reservas por día válido de actividad |
| **cliente/formEditarCliente.php**, **formEditarPerfilCuenta.php** | **Sin subida de foto de perfil** |
| **layouts/frontend/header.php** | Avatar solo ui-avatars por nombre (sin upload) |
| **layouts/admin/sidebar.php** | Tickets cuenta, seguridad, feedback, rutas monitor |
| **layouts/*/footer.php** | Cookies, `site-preferences.js`, URL cerrar inactividad |
| **layouts/admin/header.php** | CSRF meta + partial inactividad |

---

## 8. Assets (CSS / JS)

### CSS

| Archivo | Cambios |
|---------|---------|
| **app-theme.css** | Tema Spartum ampliado (~+584 líneas): horario, ticket, scrollbars WebKit, modales, mapa footer, **`.gp-modal-idle-spartum`**; eliminado `.gp-profile-avatar*` |
| **admin-panel-light.css** | Panel admin/monitor unificado: **`.gp-sp-hub-btn`**, **`.gp-sp-toolbar`**, tickets, seguridad, sidebar acento, topnav |

### JavaScript

| Archivo | Cambios |
|---------|---------|
| **site-preferences.js** *(nuevo)* | Cookies, polling sesión, **cierre estricto por inactividad**, modal Spartum inactividad |
| **form-validacion.js** | Handlers DNI, teléfono, clave fuerte, contacto, actividades, etc. |
| **admin-datagrid.js** | POST+CSRF en eliminaciones, columna Responder feedback, acciones clientes (baja/cancelar) |
| **gp-confirm-modal.js** | Modal global confirmaciones peligrosas |

### Uploads

- `public/assets/uploads/.htaccess` — bloqueo ejecución PHP en subidas
- Carpeta `uploads/avatars` **eliminada** del árbol (la columna `avatar_path` puede seguir en BD)

---

## 9. Scripts CLI

| Script | Descripción |
|--------|-------------|
| `scripts/import_demo_database.php` | Solo CLI (`--force`): importa `spartum_full_demo.sql` (esquema + demo) |
| `scripts/spartum_full_demo.sql` | Volcado SQL con tablas y datos de prueba |
| `scripts/reset_admin_password.php` | Solo CLI: restablecer clave admin con política fuerte |

Ambos bloqueados desde web vía `.htaccess` (carpeta `scripts`).

---

## 10. Documentación auxiliar

| Archivo | Contenido |
|---------|-----------|
| `CHANGELOG.txt` | Resumen narrativo (añadido al índice git) |
| `VALIDACIONES_CAPAS.txt` | Capas BD / PHP / JS, CHECK, CSRF, bootstrap seguridad (añadido al índice) |
| **`CHANGELOG.md`** | Este documento (añadido al índice) |

---

## 11. Eliminaciones y regresiones funcionales

| Elemento | Estado |
|----------|--------|
| **Fotos de perfil (upload)** | Eliminados formularios, controlador, modelo y CSS; navbar usa **ui-avatars** generado |
| **Columna `usuarios.avatar_path`** | Permanece en esquema/migraciones por compatibilidad; **sin uso en aplicación** |
| **Bentos `.gp-admin-bento*` / hub tipo carta** | Sustituidos por **`.gp-panel-menu`**: rejilla de botones Bootstrap tematizados (sin tarjetas/bento) |
| **Aviso previo ~85 % inactividad** | Sustituido por cierre estricto al 100 % del TTL |

---

## 12. Listado de ficheros por estado git

### Añadidos al índice (`A`) — 24 archivos nuevos

Ejecutado `git add` el 13-may-2026. Todos pasan de `??` a **staged** (`A`):

| Fichero |
|---------|
| `CHANGELOG.txt` |
| `VALIDACIONES_CAPAS.txt` |
| `CHANGELOG.md` |
| `app/controladores/CambioClaveControlador.php` |
| `app/controladores/ReactivarCuentaControlador.php` |
| `app/controladores/RecuperarContrasenaControlador.php` |
| `app/modelos/admin_config.php` |
| `app/modelos/recuperacion_cuenta_ticket.php` |
| `app/vistas/admin/configSeguridad.php` |
| `app/vistas/admin/feedback/responder.php` |
| `app/vistas/admin/recuperacionCuentaTickets.php` |
| `app/vistas/frontend/cambiarClaveObligatoria.php` |
| `app/vistas/frontend/error404.php` |
| `app/vistas/frontend/formEditarPerfilCuenta.php` |
| `app/vistas/frontend/reactivarCuenta.php` |
| `app/vistas/frontend/recuperarContrasena.php` |
| `app/vistas/frontend/restablecerContrasena.php` |
| `app/vistas/layouts/partials/session_idle_meta.php` |
| `core/bootstrap_security.php` |
| `public/assets/js/site-preferences.js` |
| `public/assets/js/gp-view-transitions.js` |
| `public/assets/uploads/.htaccess` |
| `scripts/reset_admin_password.php` |
| `scripts/import_demo_database.php` |
| `scripts/spartum_full_demo.sql` |

### Modificados (`M`) — 67 archivos

`.gitignore`, `.htaccess`, `composer.json`, `index.php`, `routers/web.php`, `core/Controller.php`, `core/helpers/form_validacion.php`, `core/helpers/mail_smtp.php`, `core/helpers/url.php`, controladores y modelos en `app/`, vistas admin/cliente/frontend/monitor/materiales/salas, layouts admin/frontend, `public/assets/css/*`, `public/assets/js/admin-datagrid.js`, `form-validacion.js`, `gp-confirm-modal.js`.

### Sin trackear (`??`)

Tras el último `git add`, los 24 ficheros nuevos listados arriba (incl. `gp-view-transitions.js`) están en staging. El resto del working tree son modificaciones (`M`) aún no commiteadas.

---

## 13. Unificación UI Spartum (formularios, bento, transiciones, banners)

### Botones y tema global

**Archivo:** `public/assets/css/app-theme.css`

- Sistema unificado de botones Bootstrap en web pública y panel admin/monitor (paleta Spartum: naranja, navy, verdes/rojos suaves).
- Modales de confirmación: `.gp-modal-confirm` (tema claro) en `admin/footer.php` y `frontend/footer.php`.
- Overlay inactividad: `.gp-idle-overlay` con cabecera degradada navy + acento naranja (`site-preferences.js`).
- CTA navbar invitado: `.gp-navbar-cta` para **Planes** (tamaño y color coherentes, sin `btn-sm` amarillo Bootstrap).

### Inicios de panel (bento adaptativo)

| Vista | Cambio |
|--------|--------|
| `app/vistas/admin/inicioAdmin.php` | Cuadrícula **5×5** en XL (`gp-bento--admin-grid`): 10 módulos con icono, título y descripción corta (Clientes, Monitores, Fisios, Suscripciones, Solicitudes, Tickets, Feedback, Actividades, Salas, Seguridad). |
| `app/vistas/monitor/inicioMonitor.php` | Cuadrícula **2×2** adaptativa (`gp-bento--monitor-grid`): Solicitudes centro, Salas, Nueva solicitud, Mis solicitudes. |

**CSS:** `.gp-bento-tile__desc`, tamaños con `clamp()` en rejillas admin y monitor.

### Sidebar simplificado

**Archivo:** `app/vistas/layouts/admin/sidebar.php`

- **Admin:** solo Inicio + Tu cuenta + Operaciones (accesos de módulos en bento del inicio).
- **Monitor:** mismo esquema; eliminado bloque lateral Monitor (accesos en bento 2×2).

### Formularios unificados (`gp-form-panel`)

**Partials:** `app/vistas/layouts/partials/gp_form_panel_start.php`, `gp_form_panel_end.php`

- Cabecera con toolbar: badge a la izquierda, **Volver** a la derecha, título y subtítulo debajo.
- Grid de campos: `.gp-form-grid`, `.gp-form-grid--2`, `.gp-form-fieldset` (ofertas en suscripciones).

**Vistas migradas:** crear/editar monitor, fisio, suscripción, actividad, sala, material, feedback responder, solicitud monitor, perfil cuenta (`formEditarPerfilCuenta.php`).

**JS:** `public/assets/js/gp-form-unsaved.js` — aviso al salir del perfil sin guardar (`data-gp-unsaved-guard`).

### Banners flash (sin alerts duplicados)

**Partials / JS:** `gp_flash_banner.php`, `gp-flash-banner.js`

- Mensajes `?success`, `?error`, `?warning`, `?info`, `?created`, `?updated`, `?deleted` bajo el navbar.
- Eliminados `alert` duplicados en listados y formularios admin.
- Botón cerrar reposicionado (absoluto a la derecha del banner).

### Transición de página (GSAP + cortinas)

**Archivos:** `gp-page-transition.js`, `gp_page_transition_boot.php`, `gp-gsap-motion.js`, `vendor/gsap.min.js`

- Cortinas superior/inferior + logo Spartum en navegación interna.
- Prefetch de página destino; tiempos reducidos (~350 ms extra, ~0,4 s animación).
- Scroll bloqueado durante transición; handoff boot → cortina JS sin flash azul.
- **Eliminado:** `gp-view-transitions.js` (View Transitions API).

### Validación contraseña en cliente

**Archivo:** `public/assets/js/form-validacion.js`

- Reglas `.gp-pass-rule--idle` hasta que el usuario escribe (login, registro, cambiar/restablecer clave).

### Tablas AJAX y listados

- `admin-datagrid.js`: acciones pill `.gp-btn-action`, confirmaciones vía `gpConfirm` (rechazar solicitud, retirar suscripción, eliminar actividad/feedback, etc.).
- Cabeceras `.gp-page-header` + `.gp-view-toolbar` en listados admin/monitor.

---

## Resumen ejecutivo

Desde `f5aee39` el proyecto incorpora **tickets de cuenta en recepción**, **políticas de contraseña e inactividad configurables**, **bloqueos de usuario**, **ofertas de suscripción**, **actividades multi-día**, **CSRF global**, **arranque seguro HTTP**, **feedback por email**, **UI Spartum unificada** (botones, bento admin 5×5 y monitor 2×2, formularios `gp-form-panel`, banners flash, modales confirmación, transiciones GSAP pantalla completa, validación contraseña progresiva, guardado perfil con aviso), y **eliminación funcional de avatares subidos**. La base de datos gana **3 tablas nuevas**, columnas, índices y **9 CHECK**; sin triggers ni procedimientos almacenados.

*Changelog actualizado — commit `main` → GitHub.*
