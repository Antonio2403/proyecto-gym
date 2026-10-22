<div class="mx-auto w-full max-w-[1320px] lg:max-w-[1024px] xl:max-w-7xl px-3 py-12">
    <div class="items-center flex flex-wrap -mx-6 -mt-12">
        <div class="w-full lg:w-1/2 max-w-full shrink-0 px-6 mt-12">
            <span class="gp-badge mb-4">Sobre nosotros</span>
            <h1 class="gp-hero-title mb-6">Más que un gimnasio,<br><span>un equipo</span></h1>
            <p class="text-xl font-light text-slate-500">Somos un espacio pensado para quien quiere constancia, técnica y buen rollo sin perder el foco.</p>
            <p class="text-slate-500">Entrenadores titulados, salas equipadas y una comunidad que empuja en cada repetición. Aquí el objetivo no es lucir: es rendir mejor que ayer.</p>
            <div class="gp-photo-frame gp-photo-frame--wide">
                <img class="gp-photo" src="<?= htmlspecialchars(asset('img/pixabay/equipo.jpg')) ?>"
                     width="1280" height="853" alt="Equipo de entrenamiento en la sala del gimnasio">
            </div>
        </div>
        <div class="w-full lg:w-1/2 max-w-full shrink-0 px-6 mt-12">
            <div class="flex flex-wrap -mx-2 -mt-4">
                <div class="w-full sm:w-1/2 max-w-full shrink-0 px-2 mt-4">
                    <div class="gp-stat-card h-full">
                        <div class="gp-icon">📍</div>
                        <h3 class="text-base mb-2">Instalaciones</h3>
                        <p class="text-slate-500 text-sm mb-0">Zonas de fuerza, cardio y salas para clase colectiva.</p>
                    </div>
                </div>
                <div class="w-full sm:w-1/2 max-w-full shrink-0 px-2 mt-4">
                    <div class="gp-stat-card h-full">
                        <div class="gp-icon">🎯</div>
                        <h3 class="text-base mb-2">Enfoque</h3>
                        <p class="text-slate-500 text-sm mb-0">Planes claros y seguimiento para cada nivel.</p>
                    </div>
                </div>
                <div class="w-full max-w-full shrink-0 px-2 mt-4">
                    <div class="gp-stat-card">
                        <div class="gp-icon">🔥</div>
                        <h3 class="text-base mb-2">Energía</h3>
                        <p class="text-slate-500 text-sm mb-0">Sesiones exigentes pero seguras, con monitores que corrigen y motivan.</p>
                    </div>
                </div>
            </div>
            <div class="mt-6">
                <a href="<?= htmlspecialchars(url('/usuario/actividades')) ?>" class="btn btn-primary me-2">Ver actividades</a>
                <a href="<?= htmlspecialchars(url('/contacto')) ?>" class="btn btn-outline-light">Contactar</a>
            </div>
        </div>
    </div>
</div>
