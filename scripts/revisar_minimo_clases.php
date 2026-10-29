<?php

/**
 * Cancela las clases que empiezan en menos de 30 minutos si hay menos de 5 personas.
 * En Plesk, programa esta orden cada 5 minutos:
 *   php /ruta/al/proyecto/scripts/revisar_minimo_clases.php
 */

require_once dirname(__DIR__) . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__), ['.env', 'env']);
$dotenv->safeLoad();

require_once dirname(__DIR__) . '/core/helpers/actividad_minimo.php';

$canceladas = gp_actividad_revisar_minimo_asistencia();
if ($canceladas === []) {
    echo "Ninguna clase por cancelar.\n";
    exit(0);
}

foreach ($canceladas as $fila) {
    echo sprintf(
        "Cancelada: %s el %s a las %s (%d de %d)\n",
        $fila['nombre'],
        $fila['fecha'],
        $fila['hora'],
        $fila['inscritos'],
        GP_CLASE_MINIMO_PERSONAS
    );
}
