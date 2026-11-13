<div class="content-wrapper gp-dash">
    <div class="relative w-full px-3">
        <header class="gp-dash-hero">
            <span class="gp-badge inline-block mb-2">Panel</span>
            <h1 class="text-[1.75rem] mb-0">Administración</h1>
            <p class="mb-0 mt-2">Accesos rápidos al centro. Elige un módulo para gestionar.</p>
        </header>

        <nav class="gp-bento gp-bento--admin gp-bento--admin-grid" aria-label="Menú de administración">
            <a href="<?= htmlspecialchars(url('/admin/verClientes')) ?>" class="gp-bento-tile gp-motion-item">
                <span class="gp-bento-tile__icon" aria-hidden="true"><i class="fas fa-users"></i></span>
                <span class="gp-bento-tile__label">Clientes</span>
                <span class="gp-bento-tile__desc">Altas, planes y accesos</span>
            </a>
            <a href="<?= htmlspecialchars(url('/admin/verMonitores')) ?>" class="gp-bento-tile gp-motion-item">
                <span class="gp-bento-tile__icon" aria-hidden="true"><i class="fas fa-user-tie"></i></span>
                <span class="gp-bento-tile__label">Monitores</span>
                <span class="gp-bento-tile__desc">Equipo y especialidades</span>
            </a>
            <a href="<?= htmlspecialchars(url('/admin/fisioterapeutas')) ?>" class="gp-bento-tile gp-motion-item">
                <span class="gp-bento-tile__icon" aria-hidden="true"><i class="fas fa-heart-pulse"></i></span>
                <span class="gp-bento-tile__label">Fisioterapeutas</span>
                <span class="gp-bento-tile__desc">Profesionales y citas</span>
            </a>
            <a href="<?= htmlspecialchars(url('/admin/gestionSubscripciones')) ?>" class="gp-bento-tile gp-motion-item">
                <span class="gp-bento-tile__icon" aria-hidden="true"><i class="fas fa-credit-card"></i></span>
                <span class="gp-bento-tile__label">Suscripciones</span>
                <span class="gp-bento-tile__desc">Planes y ofertas</span>
            </a>
            <a href="<?= htmlspecialchars(url('/admin/verSolicitudes')) ?>" class="gp-bento-tile gp-motion-item">
                <span class="gp-bento-tile__icon" aria-hidden="true"><i class="fas fa-envelope"></i></span>
                <span class="gp-bento-tile__label">Reportes</span>
                <span class="gp-bento-tile__desc">Revisar y consultar por año o mes</span>
            </a>
            <a href="<?= htmlspecialchars(url('/admin/recuperacion-cuenta')) ?>" class="gp-bento-tile gp-motion-item">
                <span class="gp-bento-tile__icon" aria-hidden="true"><i class="fas fa-ticket"></i></span>
                <span class="gp-bento-tile__label">Tickets de cuenta</span>
                <span class="gp-bento-tile__desc">Reactivación de accesos</span>
            </a>
            <a href="<?= htmlspecialchars(url('/admin/feedback')) ?>" class="gp-bento-tile gp-motion-item">
                <span class="gp-bento-tile__icon" aria-hidden="true"><i class="fas fa-comment-dots"></i></span>
                <span class="gp-bento-tile__label">Feedback contacto</span>
                <span class="gp-bento-tile__desc">Mensajes del formulario</span>
            </a>
            <a href="<?= htmlspecialchars(url('/admin/gestionarActividades')) ?>" class="gp-bento-tile gp-motion-item">
                <span class="gp-bento-tile__icon" aria-hidden="true"><i class="fas fa-calendar-alt"></i></span>
                <span class="gp-bento-tile__label">Actividades</span>
                <span class="gp-bento-tile__desc">Horarios y clases</span>
            </a>
            <a href="<?= htmlspecialchars(url('/monitor/verSalas')) ?>" class="gp-bento-tile gp-motion-item">
                <span class="gp-bento-tile__icon" aria-hidden="true"><i class="fas fa-dumbbell"></i></span>
                <span class="gp-bento-tile__label">Salas</span>
                <span class="gp-bento-tile__desc">Espacios y capacidad</span>
            </a>
            <a href="<?= htmlspecialchars(url('/admin/config-seguridad')) ?>" class="gp-bento-tile gp-motion-item">
                <span class="gp-bento-tile__icon" aria-hidden="true"><i class="fas fa-shield-halved"></i></span>
                <span class="gp-bento-tile__label">Seguridad</span>
                <span class="gp-bento-tile__desc">Claves e inactividad</span>
            </a>
        </nav>

        <?php
        $stats = is_array($estadisticas ?? null) ? $estadisticas : [];
        $textoCuenta = static function (int $n, string $uno, string $varios): string {
            return ($n === 1 ? '1 ' . $uno : $n . ' ' . $varios);
        };
        $altasMes = (int) ($stats['altas_mes'] ?? 0);
        $bajasMes = (int) ($stats['bajas_mes'] ?? 0);
        $ocupacion = $stats['ocupacion_pct'] ?? null;
        $porClase = is_array($stats['por_clase'] ?? null) ? $stats['por_clase'] : [];
        $porDia = is_array($stats['por_dia'] ?? null) ? $stats['por_dia'] : [];
        $planes = is_array($stats['planes'] ?? null) ? $stats['planes'] : [];
        $planesTotal = (int) ($stats['planes_total'] ?? 0);
        $leyendaPlanes = [];
        foreach ($planes as $plan) {
            $leyendaPlanes[] = (int) ($plan['n'] ?? 0) . ' ' . (string) ($plan['nombre'] ?? '');
        }
        ?>
        <section class="gp-admin-stats" aria-label="Estadísticas del centro">
            <header class="gp-admin-stats__head">
                <h2>El centro</h2>
                <p>Semana del <?= htmlspecialchars((string) ($stats['semana_etiqueta'] ?? '')) ?></p>
            </header>

            <div class="gp-admin-kpis">
                <article class="gp-admin-kpi">
                    <p class="gp-admin-kpi__value"><?= (int) ($stats['clientes_activos'] ?? 0) ?></p>
                    <h3>Con plan activo</h3>
                    <p><?= htmlspecialchars($textoCuenta($altasMes, 'alta', 'altas')) ?> · <?= htmlspecialchars($textoCuenta($bajasMes, 'baja', 'bajas')) ?> este mes</p>
                </article>
                <article class="gp-admin-kpi">
                    <p class="gp-admin-kpi__value"><?= $ocupacion === null ? '—' : (int) $ocupacion . '%' ?></p>
                    <h3>Ocupación</h3>
                    <p><?= (int) ($stats['sesiones'] ?? 0) === 0 ? 'Sin clases esta semana' : 'Inscritos entre las plazas de la semana' ?></p>
                </article>
                <article class="gp-admin-kpi">
                    <p class="gp-admin-kpi__value"><?= (int) ($stats['canceladas'] ?? 0) ?></p>
                    <h3>Clases canceladas</h3>
                    <p>No llegaron a 5 personas</p>
                </article>
                <article class="gp-admin-kpi">
                    <div class="gp-admin-kpi__split">
                        <p><strong><?= (int) ($stats['citas_pendientes'] ?? 0) ?></strong><span>pendientes</span></p>
                        <p><strong><?= (int) ($stats['citas_confirmadas'] ?? 0) ?></strong><span>confirmadas</span></p>
                    </div>
                    <h3>Fisioterapia</h3>
                    <p>Solicitadas y confirmadas desde hoy</p>
                </article>
            </div>

            <div class="gp-admin-charts">
                <article class="gp-admin-chart">
                    <header>
                        <h3>Ocupación por clase</h3>
                        <p>Plazas cubiertas en cada actividad de la semana.</p>
                    </header>
                    <?php if ($porClase === []): ?>
                        <p class="gp-admin-chart__empty">No hay clases programadas esta semana.</p>
                    <?php else: ?>
                        <ul class="gp-occ">
                            <?php foreach ($porClase as $clase): ?>
                                <?php
                                $pct = (int) ($clase['pct'] ?? 0);
                                $ancho = min(100, max(0, $pct));
                                ?>
                                <li class="gp-occ__row">
                                    <span class="gp-occ__name"><?= htmlspecialchars((string) $clase['nombre']) ?></span>
                                    <span class="gp-occ__track" role="img" aria-label="<?= htmlspecialchars((string) $clase['nombre']) ?>: <?= (int) $clase['inscritos'] ?> de <?= (int) $clase['plazas'] ?> plazas, <?= $pct ?>%">
                                        <span class="gp-occ__fill" style="width: <?= $ancho ?>%; background: <?= htmlspecialchars((string) $clase['color']) ?>"></span>
                                    </span>
                                    <span class="gp-occ__meta"><?= (int) $clase['inscritos'] ?>/<?= (int) $clase['plazas'] ?> · <?= $pct ?>%</span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </article>

                <article class="gp-admin-chart">
                    <header>
                        <h3>Reservas por día</h3>
                        <p>Inscripciones de lunes a sábado.</p>
                    </header>
                    <div class="gp-weekbars">
                        <?php foreach ($porDia as $dia): ?>
                            <div class="gp-weekbar">
                                <span class="gp-weekbar__n"><?= (int) ($dia['reservas'] ?? 0) ?></span>
                                <span class="gp-weekbar__track">
                                    <span class="gp-weekbar__fill" style="height: <?= (int) ($dia['alto'] ?? 0) ?>%"></span>
                                </span>
                                <span class="gp-weekbar__d"><?= htmlspecialchars((string) ($dia['etiqueta'] ?? '')) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </article>

                <article class="gp-admin-chart">
                    <header>
                        <h3>Planes activos</h3>
                        <p>Reparto de las suscripciones en vigor.</p>
                    </header>
                    <div class="gp-plans">
                        <div class="gp-donut">
                            <svg viewBox="0 0 42 42" role="img" aria-label="<?= $planesTotal === 0 ? 'Nadie tiene un plan activo' : 'Planes activos: ' . htmlspecialchars(implode(', ', $leyendaPlanes)) ?>">
                                <circle class="gp-donut__track" cx="21" cy="21" r="15.915" fill="none" stroke-width="5"></circle>
                                <?php foreach ($planes as $plan): ?>
                                    <circle cx="21" cy="21" r="15.915" fill="none"
                                            stroke="<?= htmlspecialchars((string) $plan['color']) ?>"
                                            stroke-width="5"
                                            stroke-dasharray="<?= htmlspecialchars((string) $plan['dash']) ?>"
                                            stroke-dashoffset="<?= htmlspecialchars((string) $plan['offset']) ?>"
                                            transform="rotate(-90 21 21)"></circle>
                                <?php endforeach; ?>
                            </svg>
                            <div class="gp-donut__center">
                                <strong><?= $planesTotal ?></strong>
                                <span><?= $planesTotal === 1 ? 'activo' : 'activos' ?></span>
                            </div>
                        </div>
                        <?php if ($planes === []): ?>
                            <p class="gp-admin-chart__empty">Nadie tiene un plan activo.</p>
                        <?php else: ?>
                            <ul class="gp-plan-legend">
                                <?php foreach ($planes as $plan): ?>
                                    <li>
                                        <span class="gp-plan-legend__swatch" style="background: <?= htmlspecialchars((string) $plan['color']) ?>"></span>
                                        <span><?= htmlspecialchars((string) $plan['nombre']) ?></span>
                                        <strong><?= (int) $plan['n'] ?></strong>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </article>
            </div>
        </section>
    </div>
</div>
