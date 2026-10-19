<div class="mx-auto w-full max-w-[1320px] lg:max-w-[1024px] xl:max-w-7xl px-3 py-12">
    <div class="justify-center flex flex-wrap -mx-3">
        <div class="text-center w-full md:w-2/3 lg:w-1/2 max-w-full shrink-0 px-3">
            <div class="gp-result-icon gp-result-icon--success mb-6" aria-hidden="true">✓</div>
            <span class="gp-badge mb-4">Pago completado</span>
            <h1 class="text-[2rem] mb-4">¡Todo listo!</h1>
            <p class="text-slate-500 mb-6">
                Tu suscripción se ha activado correctamente. Ya puedes reservar actividades y disfrutar del gimnasio.
            </p>
            <div class="flex flex-col sm:flex-row gap-2 justify-center">
                <a href="<?= htmlspecialchars(url('/usuario/actividades')) ?>" class="btn btn-primary">Ver actividades</a>
                <a href="<?= htmlspecialchars(url('/inicio')) ?>" class="btn btn-outline-light">Volver al inicio</a>
            </div>
        </div>
    </div>
</div>
