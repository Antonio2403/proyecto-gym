<?php
$m = (string) ($motivo ?? '');
$titulo = 'Página no encontrada';
$texto = 'La dirección que has abierto no existe o ya no está disponible.';
if ($m === 'enlace') {
    $titulo = 'Enlace no válido';
    $texto = 'Este enlace ha caducado o ya no es válido. Solicita de nuevo la recuperación de contraseña o vuelve al inicio.';
}
?>
<div class="gp-error404-shell py-5">
    <div class="container text-center py-lg-5">
        <p class="gp-error404-code mb-2" aria-hidden="true">404</p>
        <h1 class="h3 mb-3"><?= htmlspecialchars($titulo) ?></h1>
        <p class="text-muted mx-auto mb-4" style="max-width: 32rem;"><?= htmlspecialchars($texto) ?></p>
        <div class="d-flex flex-wrap gap-2 justify-content-center">
            <a href="<?= htmlspecialchars(url('/inicio')) ?>" class="btn btn-primary px-4">Ir al inicio</a>
            <?php if (empty($_SESSION['usuario_id'])): ?>
                <a href="<?= htmlspecialchars(url('/login')) ?>" class="btn btn-outline-secondary px-4">Iniciar sesión</a>
            <?php endif; ?>
        </div>
    </div>
</div>
