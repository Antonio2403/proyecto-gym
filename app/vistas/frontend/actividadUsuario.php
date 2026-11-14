<?php
$historial = is_array($historial ?? null) ? $historial : [];
$eventos = is_array($historial['eventos'] ?? null) ? $historial['eventos'] : [];
$resumen = is_array($historial['resumen'] ?? null) ? $historial['resumen'] : [];
$filtros = is_array($filtros ?? null) ? $filtros : [];
$pagina = max(1, (int) ($historial['pagina'] ?? 1));
$paginas = max(1, (int) ($historial['paginas'] ?? 1));
$tipos = [
    'clases' => ['label' => 'Clases', 'icon' => 'fa-calendar-check'],
    'planes' => ['label' => 'Planes', 'icon' => 'fa-credit-card'],
    'fisio' => ['label' => 'Fisioterapia', 'icon' => 'fa-heart-pulse'],
];
$subtipos = [
    'reserva' => ['Reserva realizada', 'fa-calendar-plus'],
    'comentario' => ['Opinión publicada', 'fa-comment-dots'],
    'plan_alta' => ['Plan contratado', 'fa-circle-check'],
    'plan_baja' => ['Plan finalizado', 'fa-circle-minus'],
    'cita' => ['Cita de fisioterapia', 'fa-stethoscope'],
];
$estadoCita = ['S' => 'Solicitada', 'C' => 'Confirmada', 'A' => 'Atendida', 'CA' => 'Cancelada'];

$queryPagina = static function (int $destino) use ($filtros): string {
    $query = array_filter([
        'tipo' => $filtros['tipo'] ?? '',
        'desde' => $filtros['desde'] ?? '',
        'hasta' => $filtros['hasta'] ?? '',
        'pagina' => $destino,
    ], static fn($v): bool => $v !== '' && $v !== null);

    return '?' . http_build_query($query);
};
?>

<div class="gp-activity-page mx-auto w-full px-4 py-10 lg:py-12">
    <header class="gp-activity-head">
        <div>
            <span class="gp-badge">Tu recorrido</span>
            <h1 class="text-[2rem] mt-4 mb-2">Mi actividad</h1>
            <p class="text-slate-500 mb-0">Reservas, planes, opiniones y fisioterapia reunidos en una sola cronología.</p>
        </div>
        <a href="<?= htmlspecialchars(url('/usuario/actividades')) ?>" class="btn btn-primary">Ver horario</a>
    </header>

    <section class="gp-activity-summary" aria-label="Resumen de actividad">
        <?php foreach ($tipos as $tipo => $config): ?>
            <article>
                <span class="gp-activity-summary__icon" aria-hidden="true"><i class="fa-solid <?= htmlspecialchars($config['icon']) ?>"></i></span>
                <div>
                    <strong><?= (int) ($resumen[$tipo] ?? 0) ?></strong>
                    <span><?= htmlspecialchars($config['label']) ?></span>
                </div>
            </article>
        <?php endforeach; ?>
    </section>

    <form class="gp-activity-filters" method="get" action="<?= htmlspecialchars(url('/usuario/actividad')) ?>">
        <label>
            <span>Tipo</span>
            <select class="form-select" name="tipo">
                <option value="">Toda la actividad</option>
                <?php foreach ($tipos as $tipo => $config): ?>
                    <option value="<?= htmlspecialchars($tipo) ?>" <?= ($filtros['tipo'] ?? '') === $tipo ? 'selected' : '' ?>>
                        <?= htmlspecialchars($config['label']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>
            <span>Desde</span>
            <input class="form-control" type="date" name="desde" value="<?= htmlspecialchars((string) ($filtros['desde'] ?? '')) ?>">
        </label>
        <label>
            <span>Hasta</span>
            <input class="form-control" type="date" name="hasta" value="<?= htmlspecialchars((string) ($filtros['hasta'] ?? '')) ?>">
        </label>
        <button class="btn btn-primary" type="submit">Aplicar filtros</button>
        <?php if (array_filter($filtros)): ?>
            <a class="btn btn-outline-secondary" href="<?= htmlspecialchars(url('/usuario/actividad')) ?>">Limpiar</a>
        <?php endif; ?>
    </form>

    <?php if ($eventos === []): ?>
        <div class="gp-activity-empty">
            <span aria-hidden="true"><i class="fa-regular fa-calendar"></i></span>
            <h2>Aún no hay actividad</h2>
            <p>No encontramos movimientos con estos filtros.</p>
            <a class="btn btn-primary" href="<?= htmlspecialchars(url('/usuario/actividades')) ?>">Explorar clases</a>
        </div>
    <?php else: ?>
        <ol class="gp-activity-timeline">
            <?php foreach ($eventos as $evento): ?>
                <?php
                $subtipo = (string) ($evento['subtipo'] ?? '');
                [$etiqueta, $icono] = $subtipos[$subtipo] ?? ['Actividad', 'fa-circle'];
                $estado = (string) ($evento['estado'] ?? '');
                $fecha = strtotime((string) ($evento['occurred_at'] ?? ''));
                ?>
                <li>
                    <span class="gp-activity-timeline__marker" aria-hidden="true"><i class="fa-solid <?= htmlspecialchars($icono) ?>"></i></span>
                    <article>
                        <div class="gp-activity-timeline__meta">
                            <span><?= htmlspecialchars($etiqueta) ?></span>
                            <time datetime="<?= htmlspecialchars((string) ($evento['occurred_at'] ?? '')) ?>">
                                <?= $fecha ? htmlspecialchars(date('d/m/Y · H:i', $fecha)) : 'Fecha no disponible' ?>
                            </time>
                        </div>
                        <h2><?= htmlspecialchars((string) ($evento['titulo'] ?? 'Actividad')) ?></h2>
                        <?php if (!empty($evento['detalle'])): ?>
                            <p><?= htmlspecialchars((string) $evento['detalle']) ?></p>
                        <?php endif; ?>
                        <?php if ($subtipo === 'cita' && isset($estadoCita[$estado])): ?>
                            <span class="gp-activity-state gp-activity-state--<?= strtolower(htmlspecialchars($estado)) ?>">
                                <?= htmlspecialchars($estadoCita[$estado]) ?>
                            </span>
                        <?php endif; ?>
                    </article>
                </li>
            <?php endforeach; ?>
        </ol>

        <?php if ($paginas > 1): ?>
            <nav class="gp-activity-pagination" aria-label="Páginas del historial">
                <a class="btn btn-outline-secondary<?= $pagina <= 1 ? ' disabled' : '' ?>"
                   href="<?= $pagina > 1 ? htmlspecialchars($queryPagina($pagina - 1)) : '#' ?>">Anterior</a>
                <span>Página <?= $pagina ?> de <?= $paginas ?></span>
                <a class="btn btn-outline-secondary<?= $pagina >= $paginas ? ' disabled' : '' ?>"
                   href="<?= $pagina < $paginas ? htmlspecialchars($queryPagina($pagina + 1)) : '#' ?>">Siguiente</a>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>
