# Spartum

Aplicación PHP para la gestión de un gimnasio.

## Interfaz

La interfaz utiliza Tailwind CSS 4.3.0. El archivo fuente está en
`resources/css/tailwind.css` y el resultado compilado se publica en
`public/assets/css/tailwind.css`.

```bash
npm install
npm run build:css
```

Para desarrollar estilos con recompilación automática:

```bash
npm run watch:css
```

El CSS compilado se mantiene en el repositorio para que despliegues como
HelioHost no necesiten Node.js ni npm.

## Ejecución local

```bash
composer install
php -S localhost:8000 router.php
```

La aplicación carga la configuración desde `.env` o `env`. Ambos archivos
están excluidos de Git y no deben contenerse en el repositorio.
