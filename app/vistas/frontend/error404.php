<?php
$m = (string) ($motivo ?? '');
$titulo = 'Página no encontrada';
$texto = 'La dirección que has abierto no existe o ya no está disponible.';
if ($m === 'enlace') {
    $titulo = 'Enlace no válido';
    $texto = 'Este enlace ha caducado o ya no es válido. Solicita de nuevo la recuperación de contraseña o vuelve al inicio.';
}
?>
<div class="gp-error404-shell py-12">
    <div class="mx-auto w-full max-w-[1320px] lg:max-w-[1024px] xl:max-w-7xl px-3 text-center lg:py-12">
        <p class="gp-error404-code mb-2" aria-hidden="true">404</p>
        <h1 class="text-[1.75rem] mb-4"><?= htmlspecialchars($titulo) ?></h1>
        <p class="text-slate-500 mx-auto mb-6" style="max-width: 32rem;"><?= htmlspecialchars($texto) ?></p>
        <div class="flex flex-wrap gap-2 justify-center">
            <a href="<?= htmlspecialchars(url('/inicio')) ?>" class="btn btn-primary px-6">Ir al inicio</a>
            <?php if (empty($_SESSION['usuario_id'])): ?>
                <a href="<?= htmlspecialchars(url('/login')) ?>" class="btn btn-outline-secondary px-6">Iniciar sesión</a>
            <?php endif; ?>
        </div>
    </div>
</div>
