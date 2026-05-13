<?php
$gpFlashMessage = null;
$gpFlashType = null;

if (!empty($_GET['success'])) {
    $gpFlashMessage = (string) $_GET['success'];
    $gpFlashType = 'success';
} elseif (!empty($_GET['error'])) {
    $gpFlashMessage = (string) $_GET['error'];
    $gpFlashType = 'error';
    $errLower = strtolower($gpFlashMessage);
    if (str_contains($errLower, 'inactividad') || str_contains($errLower, 'aún no está activa') || str_contains($errLower, 'baja.')) {
        $gpFlashType = 'warn';
    } elseif (str_contains($errLower, 'baja') && !str_contains($errLower, 'permanentemente')) {
        $gpFlashType = 'warn';
    }
} elseif (!empty($_GET['warning'])) {
    $gpFlashMessage = (string) $_GET['warning'];
    $gpFlashType = 'warn';
} elseif (!empty($_GET['info'])) {
    $gpFlashMessage = (string) $_GET['info'];
    $gpFlashType = 'warn';
} elseif (isset($_GET['deleted'])) {
    $gpFlashMessage = 'Registro eliminado correctamente.';
    $gpFlashType = 'success';
} elseif (isset($_GET['updated'])) {
    $gpFlashMessage = 'Cambios guardados correctamente.';
    $gpFlashType = 'success';
} elseif (isset($_GET['created'])) {
    $gpFlashMessage = 'Registro creado correctamente.';
    $gpFlashType = 'success';
}

if ($gpFlashMessage !== null && $gpFlashMessage !== ''):
    $gpFlashIcons = [
        'success' => 'fa-circle-check',
        'warn' => 'fa-triangle-exclamation',
        'error' => 'fa-circle-xmark',
    ];
    $gpFlashIcon = $gpFlashIcons[$gpFlashType] ?? 'fa-circle-info';
?>
<div class="gp-flash-banner gp-flash-banner--<?= htmlspecialchars($gpFlashType, ENT_QUOTES, 'UTF-8') ?>" role="alert" data-gp-flash-banner>
    <div class="gp-flash-banner__inner">
        <span class="gp-flash-banner__icon" aria-hidden="true"><i class="fas <?= htmlspecialchars($gpFlashIcon, ENT_QUOTES, 'UTF-8') ?>"></i></span>
        <p class="gp-flash-banner__text mb-0"><?= htmlspecialchars($gpFlashMessage, ENT_QUOTES, 'UTF-8') ?></p>
        <button type="button" class="gp-flash-banner__close" data-gp-flash-dismiss aria-label="Cerrar aviso">
            <i class="fas fa-times" aria-hidden="true"></i>
        </button>
    </div>
</div>
<?php endif; ?>
