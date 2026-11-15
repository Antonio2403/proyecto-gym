<?php

declare(strict_types=1);

/**
 * Carga Spartum\* aunque el hosting no haya regenerado vendor/autoload.
 * vendor/ está fuera de git y composer dump-autoload no corre en el despliegue.
 */
spl_autoload_register(static function (string $class): void {
    $map = [
        'Spartum\\Modelos\\' => dirname(__DIR__) . '/app/modelos/',
        'Spartum\\Servicios\\' => dirname(__DIR__) . '/app/servicios/',
    ];

    foreach ($map as $prefix => $dir) {
        if (!str_starts_with($class, $prefix)) {
            continue;
        }

        $path = $dir . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
        if (is_file($path)) {
            require_once $path;
        }

        return;
    }
});
