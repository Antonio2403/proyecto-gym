Dpto. Informática - I.E.S. ………  C.F.G.S. Desarrollo de Aplicaciones Web

# PROPUESTA DE PROYECTO

| | |
|---|---|
| **Nombre del proyecto:** | Spartum (gestión web de un gimnasio) |
| **Alumno:** | Antonio Jesús Mora Sánchez |
| **Curso:** | 2º DAW |
| **Tutor:** | Déjese este campo en blanco. |

---

## OBJETIVOS

El objetivo principal es crear una aplicación web para la gestión de un gimnasio, llamada **Spartum**. Con ella se pretende que los socios puedan registrarse, contratar un plan, ver el horario de actividades, reservar plaza y, si su suscripción lo incluye, pedir cita de fisioterapia. El personal del centro (administración, monitores y fisioterapeutas) dispondrá de paneles para gestionar clientes, clases, salas, materiales y citas.

El proyecto se usará como trabajo de fin de ciclo del CFGS de Desarrollo de Aplicaciones Web. La aplicación se publicará en un hosting real (**HelioHost**) para poder demostrarla en Internet, no solo en el ordenador del alumno.

No se ha seguido ningún curso de pago: el desarrollo se basa en lo trabajado en el ciclo y en documentación libre de las herramientas utilizadas.

---

## ANÁLISIS DE LO EXISTENTE (Opcional)

Este proyecto se empezará desde cero. No hay una aplicación anterior propia que se vaya a sustituir.

En muchos gimnasios pequeños este trabajo se hace aún de forma manual o con herramientas genéricas (papel, WhatsApp, hojas de cálculo): control de socios, horarios, reservas de clase y citas de fisioterapia. Spartum pretende reunir esas tareas en una sola web.

---

## ANÁLISIS DEL SISTEMA

Las partes del sistema sobre las que se trabajará, y los requisitos que se pretenden satisfacer, son:

- **Zona pública:** inicio, quiénes somos, contacto, login y registro.
- **Usuarios y roles:** administrador, monitor, socio y fisioterapeuta.
- **Cuenta:** inicio de sesión, confirmación de correo, recuperación de cuenta/contraseña y baja.
- **Administración:** clientes, monitores, suscripciones, actividades, fisioterapeutas y mensajes de contacto.
- **Monitores:** salas, materiales, solicitudes y seguimiento de sus clases.
- **Socios:** inscripción a actividades, contratación de planes y citas de fisioterapia (si el plan lo permite).
- **Fisioterapeuta:** panel de citas pendientes y confirmadas.
- **Base de datos** relacional para guardar usuarios, planes, actividades, inscripciones y citas.
- **Publicación** en hosting con PHP y MySQL/MariaDB.

No se adapta una aplicación ya hecha. Sí se reutilizarán librerías y plantillas de código abierto (su funcionalidad ya existe) y el trabajo del alumno será el diseño de la base de datos, la lógica de Spartum, las vistas, la seguridad y el despliegue en HelioHost.

Herramientas de terceros que se reutilizan (no se programan desde cero): Bramus Router (rutas PHP), Bootstrap / AdminLTE (interfaz), Stripe (pagos en modo prueba), PHPMailer (correo) y Composer con phpdotenv (dependencias y configuración).

---

## DISEÑO DEL SISTEMA

**Lenguajes:**

- PHP 8.1 o superior (servidor)
- HTML, CSS y JavaScript (cliente)
- SQL

**Base de datos:**

- MariaDB / MySQL
- phpMyAdmin (incluido en HelioHost)

**Entorno e IDE:**

- Visual Studio Code
- Git / GitHub
- Composer
- Desarrollo en local (PHP + MySQL) y Apache en el hosting

**Hosting:**

- HelioHost (Apache, PHP, MariaDB y certificado SSL)

**Otros:**

- Stripe en modo test (sin coste)
- PHPMailer para avisos por correo

**Requisitos mínimos estimados:**

- Navegador actual (Chrome, Firefox o Edge) y conexión a Internet.
- Para desarrollar en local: Windows 10/11, unos 4 GB de RAM y PHP 8.1+ con PDO MySQL.

---

## ESTIMACIÓN DE COSTES

El único gasto económico del proyecto es el **hosting de HelioHost**, que ya está pagado. No se ha comprado ningún curso.

El resto de herramientas son gratuitas:

- Visual Studio Code, Git, Composer, PHP, MariaDB/MySQL y phpMyAdmin
- Librerías de código abierto (Bramus Router, PHPMailer, SDK de Stripe, Bootstrap, AdminLTE)
- Stripe en modo prueba (sin comisiones)
- GitHub para el repositorio

El desarrollo lo realiza el alumno como trabajo académico, sin coste laboral para ninguna empresa.

**Estimación temporal:** el trabajo se realiza a lo largo del módulo de Proyecto de 2.º DAW (análisis, desarrollo, pruebas y publicación en HelioHost).
