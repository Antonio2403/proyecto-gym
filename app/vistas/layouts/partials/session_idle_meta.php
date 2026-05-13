<?php
declare(strict_types=1);
/** Meta para el aviso de inactividad en cliente (site-preferences.js). */
if (empty($_SESSION['usuario_id'])) {
    return;
}
require_once __DIR__ . '/../../../modelos/database.php';
require_once __DIR__ . '/../../../modelos/admin_config.php';
$envTtl = (int) ($_ENV['SESSION_IDLE_TIMEOUT_SECONDS'] ?? getenv('SESSION_IDLE_TIMEOUT_SECONDS') ?: 0);
$sec = $envTtl > 0 ? $envTtl : AdminConfig::getInt('session_idle_timeout_seconds', 2700);
$sec = min(max($sec, 60), 86400 * 7);
echo '<meta name="gp-session-idle-seconds" content="' . (int) $sec . '">' . "\n";
