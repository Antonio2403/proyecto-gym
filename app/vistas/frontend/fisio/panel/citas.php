<?php
$estadoEtiqueta = static function (?string $e): string {
    return match ($e) {
        'S' => 'Solicitada',
        'C' => 'Confirmada',
        'A' => 'Atendida',
        'CA' => 'Cancelada',
        default => (string) $e,
    };
};

$r = $citas_resultado ?? ['rows' => [], 'total' => 0, 'page' => 1, 'per_page' => 10, 'total_pages' => 1];
$rows = $r['rows'];
$total = (int) $r['total'];
$page = (int) $r['page'];
$perPage = (int) $r['per_page'];
$totalPages = (int) $r['total_pages'];

$filtros = $filtros ?? [];
$solo_confirmadas = !empty($solo_confirmadas);

$baseUrl = $solo_confirmadas ? url('/fisio/citas/confirmadas') : url('/fisio/citas');
$queryKeep = [];
foreach (['fecha_desde', 'fecha_hasta', 'motivo', 'cliente', 'solo_futuras'] as $k) {
    if (isset($filtros[$k]) && $filtros[$k] !== '' && $filtros[$k] !== null) {
        $queryKeep[$k] = $filtros[$k];
    }
}
if (!$solo_confirmadas && !empty($filtros['estado'])) {
    $queryKeep['estado'] = $filtros['estado'];
}
if ($perPage !== 10) {
    $queryKeep['per_page'] = $perPage;
}

$buildPageHref = static function (int $p) use ($baseUrl, $queryKeep): string {
    $q = array_merge($queryKeep, ['page' => $p]);

    return $baseUrl . '?' . http_build_query($q);
};

$formAction = $baseUrl;
$nombreSocio = static function (array $c): string {
    $n = trim(
        implode(' ', array_filter([
            (string) ($c['cli_nombre'] ?? ''),
            (string) ($c['cli_apellido1'] ?? ''),
            (string) ($c['cli_apellido2'] ?? ''),
        ]))
    );

    return $n !== '' ? $n : '—';
};
$fromIx = $total === 0 ? 0 : ($page - 1) * $perPage + 1;
$toIx = min($total, $page * $perPage);
?>
<div class="gp-schedule-page py-4 py-lg-5">
    <div class="container">
        <div class="gp-schedule-panel shadow-sm">
            <header class="gp-schedule-head text-center py-4 px-3">
                <h1 class="gp-schedule-title mb-0">
                    <?= $solo_confirmadas ? 'Citas confirmadas' : 'Citas asignadas' ?>
                </h1>
                <p class="gp-schedule-subtitle mb-0 mt-2">
                    <?= htmlspecialchars((string) ($fisio['nombre'] ?? '')) ?>
                    <?php if (!$solo_confirmadas): ?>
                        — búsqueda avanzada y paginación
                    <?php else: ?>
                        — filtrado y paginación
                    <?php endif; ?>
                </p>
            </header>

            <div class="px-3 px-md-4 pb-3 d-flex flex-wrap gap-2 justify-content-center">
                <a href="<?= htmlspecialchars(url('/fisio')) ?>" class="btn btn-outline-secondary btn-sm">Inicio</a>
                <?php if ($solo_confirmadas): ?>
                    <a href="<?= htmlspecialchars(url('/fisio/citas')) ?>" class="btn gp-btn-orange btn-sm">Todas las citas</a>
                <?php else: ?>
                    <a href="<?= htmlspecialchars(url('/fisio/citas/confirmadas')) ?>" class="btn gp-btn-orange btn-sm">Solo confirmadas</a>
                <?php endif; ?>
            </div>

            <div class="px-3 px-md-4 pb-4">
                <form method="get" action="<?= htmlspecialchars($formAction) ?>" class="gp-light-panel-inner p-3 mb-4">
                    <p class="small text-muted mb-3 mb-md-2 fw-semibold">Búsqueda avanzada</p>
                    <div class="row g-3">
                        <?php if (!$solo_confirmadas): ?>
                            <div class="col-md-6 col-lg-3">
                                <label class="form-label small mb-1" for="f_estado">Estado</label>
                                <select name="estado" id="f_estado" class="form-select form-select-sm">
                                    <?php $ev = $filtros['estado'] ?? ''; ?>
                                    <option value=""<?= $ev === '' ? ' selected' : '' ?>>Todas</option>
                                    <option value="S"<?= $ev === 'S' ? ' selected' : '' ?>>Solicitada</option>
                                    <option value="C"<?= $ev === 'C' ? ' selected' : '' ?>>Confirmada</option>
                                    <option value="A"<?= $ev === 'A' ? ' selected' : '' ?>>Atendida</option>
                                    <option value="CA"<?= $ev === 'CA' ? ' selected' : '' ?>>Cancelada</option>
                                </select>
                            </div>
                        <?php endif; ?>
                        <div class="col-md-6 col-lg-3">
                            <label class="form-label small mb-1" for="f_desde">Fecha desde</label>
                            <input type="date" name="fecha_desde" id="f_desde" class="form-control form-control-sm"
                                   value="<?= htmlspecialchars((string) ($filtros['fecha_desde'] ?? '')) ?>">
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <label class="form-label small mb-1" for="f_hasta">Fecha hasta</label>
                            <input type="date" name="fecha_hasta" id="f_hasta" class="form-control form-control-sm"
                                   value="<?= htmlspecialchars((string) ($filtros['fecha_hasta'] ?? '')) ?>">
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <label class="form-label small mb-1" for="f_pp">Por página</label>
                            <select name="per_page" id="f_pp" class="form-select form-select-sm">
                                <?php foreach ([10, 20, 50] as $n): ?>
                                    <option value="<?= $n ?>"<?= $perPage === $n ? ' selected' : '' ?>><?= $n ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label small mb-1" for="f_cli">Socio (nombre, DNI o email)</label>
                            <input type="text" name="cliente" id="f_cli" class="form-control form-control-sm"
                                   placeholder="Ej. Carlos, 12345678Z…"
                                   value="<?= htmlspecialchars((string) ($filtros['cliente'] ?? '')) ?>">
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label small mb-1" for="f_mot">Motivo / notas</label>
                            <input type="text" name="motivo" id="f_mot" class="form-control form-control-sm"
                                   placeholder="Texto contenido en el motivo…"
                                   value="<?= htmlspecialchars((string) ($filtros['motivo'] ?? '')) ?>">
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <?php $sf = ($filtros['solo_futuras'] ?? '') === '1'; ?>
                                <input class="form-check-input" type="checkbox" value="1" name="solo_futuras"
                                       id="f_fut"<?= $sf ? ' checked' : '' ?>>
                                <label class="form-check-label small" for="f_fut">Solo citas desde ahora</label>
                            </div>
                        </div>
                        <div class="col-12 d-flex flex-wrap gap-2 pt-2">
                            <button type="submit" class="btn btn-primary btn-sm px-4">Buscar</button>
                            <a href="<?= htmlspecialchars($baseUrl) ?>" class="btn btn-outline-secondary btn-sm">Limpiar</a>
                        </div>
                    </div>
                </form>

                <p class="small text-muted mb-3">
                    <?php if ($total > 0): ?>
                        Resultados <?= (int) $fromIx ?>–<?= (int) $toIx ?> de <?= $total ?>.
                    <?php else: ?>
                        Sin resultados para los filtros actuales.
                    <?php endif; ?>
                </p>

                <?php if (empty($rows)): ?>
                    <div class="gp-light-panel-inner p-4 text-muted">No hay citas que coincidan.</div>
                <?php else: ?>
                    <div class="table-responsive rounded-2 overflow-hidden border border-light">
                        <table class="table gp-table-light mb-0 text-center align-middle">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Socio</th>
                                    <th class="text-start">Motivo</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rows as $c): ?>
                                    <tr>
                                        <td class="text-nowrap small"><?= htmlspecialchars(date('d/m/Y H:i', strtotime((string) $c['fecha']))) ?></td>
                                        <td class="small text-start">
                                            <span class="fw-medium"><?= htmlspecialchars($nombreSocio($c)) ?></span>
                                            <?php if (!empty($c['cli_email'])): ?>
                                                <div class="text-muted"><?= htmlspecialchars((string) $c['cli_email']) ?></div>
                                            <?php endif; ?>
                                            <?php if (!empty($c['cli_dni']) || !empty($c['cli_telefono'])): ?>
                                                <div class="text-muted">
                                                    <?php if (!empty($c['cli_dni'])): ?>
                                                        <?= htmlspecialchars((string) $c['cli_dni']) ?>
                                                    <?php endif; ?>
                                                    <?php if (!empty($c['cli_dni']) && !empty($c['cli_telefono'])): ?> · <?php endif; ?>
                                                    <?php if (!empty($c['cli_telefono'])): ?>
                                                        <?= htmlspecialchars((string) $c['cli_telefono']) ?>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-start small"><?= nl2br(htmlspecialchars((string) ($c['motivo'] ?? ''))) ?></td>
                                        <td>
                                            <span class="badge rounded-pill bg-secondary">
                                                <?= htmlspecialchars($estadoEtiqueta($c['estado'] ?? null)) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if ($totalPages > 1): ?>
                        <nav class="mt-3 d-flex justify-content-center" aria-label="Paginación">
                            <ul class="pagination pagination-sm mb-0 flex-wrap">
                                <li class="page-item<?= $page <= 1 ? ' disabled' : '' ?>">
                                    <a class="page-link" href="<?= $page <= 1 ? '#' : htmlspecialchars($buildPageHref($page - 1)) ?>">Anterior</a>
                                </li>
                                <?php
                                $win = [max(1, $page - 2), min($totalPages, $page + 2)];
                                if ($win[0] > 1): ?>
                                    <li class="page-item"><a class="page-link" href="<?= htmlspecialchars($buildPageHref(1)) ?>">1</a></li>
                                    <?php if ($win[0] > 2): ?><li class="page-item disabled"><span class="page-link">…</span></li><?php endif; ?>
                                <?php endif;
                                for ($pi = $win[0]; $pi <= $win[1]; $pi++): ?>
                                    <li class="page-item<?= $pi === $page ? ' active' : '' ?>">
                                        <a class="page-link" href="<?= htmlspecialchars($buildPageHref($pi)) ?>"><?= $pi ?></a>
                                    </li>
                                <?php endfor;
                                if ($win[1] < $totalPages): ?>
                                    <?php if ($win[1] < $totalPages - 1): ?><li class="page-item disabled"><span class="page-link">…</span></li><?php endif; ?>
                                    <li class="page-item"><a class="page-link" href="<?= htmlspecialchars($buildPageHref($totalPages)) ?>"><?= $totalPages ?></a></li>
                                <?php endif; ?>
                                <li class="page-item<?= $page >= $totalPages ? ' disabled' : '' ?>">
                                    <a class="page-link" href="<?= $page >= $totalPages ? '#' : htmlspecialchars($buildPageHref($page + 1)) ?>">Siguiente</a>
                                </li>
                            </ul>
                        </nav>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
