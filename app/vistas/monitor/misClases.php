<?php
$filtros = is_array($filtros ?? null) ? $filtros : [];
$sesiones = is_array($sesiones ?? null) ? $sesiones : [];
$page = max(1, (int) ($page ?? 1));
$perPage = max(5, (int) ($per_page ?? 10));
$total = max(0, (int) ($total ?? 0));
$totalPages = max(1, (int) ($total_pages ?? 1));
$diaOpts = ['' => 'Todos', 'L' => 'Lunes', 'M' => 'Martes', 'X' => 'Miércoles', 'J' => 'Jueves', 'V' => 'Viernes', 'S' => 'Sábado', 'D' => 'Domingo'];
$salasFiltro = is_array($salas_filtro ?? null) ? $salas_filtro : [];

$fechaDesdeVal = $filtros['fecha_desde'] ?? ($fecha_def_desde ?? date('Y-m-d'));
$fechaHastaVal = $filtros['fecha_hasta'] ?? ($fecha_def_hasta ?? date('Y-m-d', strtotime('+13 days')));

$queryBase = static function (array $extra = []) use ($filtros, $perPage): array {
    $q = array_merge([
        'q' => $filtros['q'] ?? '',
        'actividad' => $filtros['actividad'] ?? '',
        'sala' => $filtros['sala'] ?? '',
        'cliente' => $filtros['cliente'] ?? '',
        'email' => $filtros['email'] ?? '',
        'fecha_desde' => $filtros['fecha_desde'] ?? '',
        'fecha_hasta' => $filtros['fecha_hasta'] ?? '',
        'dia_semana' => $filtros['dia_semana'] ?? '',
        'per_page' => $perPage,
    ], $extra);

    return array_filter($q, static fn ($v): bool => $v !== null && $v !== '');
};
?>
<div class="content-wrapper gp-dash">
    <div class="container-fluid">
        <header class="gp-dash-hero mb-4">
            <span class="gp-badge d-inline-block mb-2">Monitor</span>
            <h1 class="h3 mb-0">Mis clases</h1>
            <p class="mb-0 mt-2 text-muted">Sesiones con socios inscritos · búsqueda avanzada y paginación.</p>
        </header>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h2 class="h6 mb-3">Búsqueda avanzada</h2>
                <form method="get" action="<?= htmlspecialchars(url('/monitor/mis-clases')) ?>" class="row g-2 align-items-end">
                    <div class="col-lg-4">
                        <label class="form-label small text-muted mb-0" for="f_q">Buscar global</label>
                        <input type="search" class="form-control form-control-sm" id="f_q" name="q"
                               value="<?= htmlspecialchars((string) ($filtros['q'] ?? '')) ?>"
                               placeholder="Actividad, sala, socio…" autocomplete="off">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small text-muted mb-0" for="f_actividad">Actividad</label>
                        <input type="text" class="form-control form-control-sm" id="f_actividad" name="actividad"
                               value="<?= htmlspecialchars((string) ($filtros['actividad'] ?? '')) ?>" autocomplete="off">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small text-muted mb-0" for="f_sala">Sala</label>
                        <select class="form-select form-select-sm" id="f_sala" name="sala">
                            <option value="">Todas</option>
                            <?php foreach ($salasFiltro as $sn): ?>
                                <option value="<?= htmlspecialchars($sn) ?>"<?= (($filtros['sala'] ?? '') === $sn) ? ' selected' : '' ?>>
                                    <?= htmlspecialchars($sn) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted mb-0" for="f_cliente">Socio (nombre)</label>
                        <input type="text" class="form-control form-control-sm" id="f_cliente" name="cliente"
                               value="<?= htmlspecialchars((string) ($filtros['cliente'] ?? '')) ?>" autocomplete="off">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted mb-0" for="f_email">Email socio</label>
                        <input type="text" class="form-control form-control-sm" id="f_email" name="email"
                               value="<?= htmlspecialchars((string) ($filtros['email'] ?? '')) ?>" autocomplete="off">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted mb-0" for="f_desde">Desde</label>
                        <input type="date" class="form-control form-control-sm" id="f_desde" name="fecha_desde"
                               value="<?= htmlspecialchars((string) $fechaDesdeVal) ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted mb-0" for="f_hasta">Hasta</label>
                        <input type="date" class="form-control form-control-sm" id="f_hasta" name="fecha_hasta"
                               value="<?= htmlspecialchars((string) $fechaHastaVal) ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted mb-0" for="f_dia">Día semana</label>
                        <select class="form-select form-select-sm" id="f_dia" name="dia_semana">
                            <?php foreach ($diaOpts as $code => $lbl): ?>
                                <option value="<?= htmlspecialchars($code) ?>"<?= (($filtros['dia_semana'] ?? '') === $code) ? ' selected' : '' ?>>
                                    <?= htmlspecialchars($lbl) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted mb-0" for="f_pp">Por página</label>
                        <select class="form-select form-select-sm" id="f_pp" name="per_page">
                            <?php foreach ([5, 10, 15, 25] as $n): ?>
                                <option value="<?= $n ?>"<?= $perPage === $n ? ' selected' : '' ?>><?= $n ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12 d-flex flex-wrap gap-2 mt-2">
                        <button type="submit" class="btn btn-primary btn-sm">Buscar</button>
                        <a href="<?= htmlspecialchars(url('/monitor/mis-clases')) ?>" class="btn btn-outline-secondary btn-sm">Limpiar</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <p class="small text-muted mb-0">
                <?php if ($total === 0): ?>
                    0 sesiones encontradas
                <?php else: ?>
                    Mostrando página <?= $page ?> de <?= $totalPages ?>
                    · <?= $total ?> sesión(es) con inscritos
                <?php endif; ?>
            </p>
            <?php if ($totalPages > 1): ?>
                <nav aria-label="Paginación de clases">
                    <ul class="pagination pagination-sm mb-0">
                        <?php
                        $prev = max(1, $page - 1);
                        $next = min($totalPages, $page + 1);
                        ?>
                        <li class="page-item<?= $page <= 1 ? ' disabled' : '' ?>">
                            <a class="page-link" href="<?= htmlspecialchars(url('/monitor/mis-clases') . '?' . http_build_query($queryBase(['page' => $prev]))) ?>">Anterior</a>
                        </li>
                        <?php
                        $start = max(1, $page - 2);
                        $end = min($totalPages, $page + 2);
                        for ($p = $start; $p <= $end; ++$p):
                        ?>
                            <li class="page-item<?= $p === $page ? ' active' : '' ?>">
                                <a class="page-link" href="<?= htmlspecialchars(url('/monitor/mis-clases') . '?' . http_build_query($queryBase(['page' => $p]))) ?>"><?= $p ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item<?= $page >= $totalPages ? ' disabled' : '' ?>">
                            <a class="page-link" href="<?= htmlspecialchars(url('/monitor/mis-clases') . '?' . http_build_query($queryBase(['page' => $next]))) ?>">Siguiente</a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>

        <?php if ($sesiones === []): ?>
            <div class="alert alert-secondary">No hay sesiones con inscritos que coincidan con los filtros.</div>
        <?php else: ?>
            <div class="d-flex flex-column gap-4">
                <?php foreach ($sesiones as $bloque): ?>
                    <?php
                    $lista = $bloque['inscritos'] ?? [];
                    $rec = (int) ($bloque['recurrente'] ?? 1) === 1;
                    $fec = (string) ($bloque['fecha_ocurrencia'] ?? '');
                    ?>
                    <section class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
                                <div>
                                    <h2 class="h5 mb-1"><?= htmlspecialchars((string) ($bloque['actividad_nombre'] ?? '')) ?></h2>
                                    <p class="text-muted small mb-0">
                                        Sesión <?= $fec !== '' ? htmlspecialchars(date('d/m/Y', strtotime($fec))) : '—' ?>
                                        · <?= $rec ? 'Semanal' : 'Puntual' ?>
                                        · <?= htmlspecialchars((string) ($bloque['hora'] ?? '')) ?>
                                        <?php if (!empty($bloque['sala_nombre'])): ?>
                                            · Sala <?= htmlspecialchars((string) $bloque['sala_nombre']) ?>
                                        <?php endif; ?>
                                    </p>
                                </div>
                                <span class="badge bg-primary bg-opacity-10 text-primary-emphasis">
                                    <?= (int) ($bloque['total_inscritos'] ?? count($lista)) ?> inscrito(s)
                                </span>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Nombre</th>
                                            <th>Email</th>
                                            <th>Teléfono</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($lista as $ins): ?>
                                            <tr>
                                                <td><?= htmlspecialchars(trim((string) ($ins['nombre'] ?? '') . ' ' . (string) ($ins['apellido1'] ?? ''))) ?></td>
                                                <td><?= htmlspecialchars((string) ($ins['email'] ?? '')) ?></td>
                                                <td><?= htmlspecialchars((string) ($ins['telefono'] ?? '—')) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </section>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <a href="<?= htmlspecialchars(url('/inicioMonitor')) ?>" class="btn btn-outline-secondary mt-4">Volver al panel</a>
    </div>
</div>
