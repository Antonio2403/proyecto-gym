<?php
$pendientes = $pendientes ?? [];
$aprobadas = $aprobadas ?? [];
$rechazadas = $rechazadas ?? [];

$nomMon = static function (array $s): string {
    return trim(($s['monitor_nombre'] ?? '') . ' ' . ($s['monitor_apellido1'] ?? ''));
};

$badgeEstado = static function (string $est): string {
    switch (strtoupper($est)) {
        case 'P':
            return 'text-bg-warning text-dark';
        case 'A':
            return 'text-bg-success';
        case 'R':
            return 'text-bg-danger';
        default:
            return 'text-bg-secondary';
    }
};

$labelEstado = static function (string $est): string {
    $m = Solicitud::metaEstado(strtoupper($est));

    return $m['label'];
};
?>
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="gp-admin-card-panel mb-4">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start gap-3">
                <div>
                    <h1 class="h3 mb-2">Solicitudes del centro</h1>
                    <p class="text-muted mb-0">
                        Peticiones del equipo de monitores. Las pendientes las gestiona administración; aquí solo consultas el estado del centro.
                    </p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="<?= htmlspecialchars(url('/monitor/formSolicitud')) ?>" class="btn btn-primary btn-sm fw-semibold">
                        <i class="fas fa-plus-circle me-1"></i> Nueva solicitud
                    </a>
                    <a href="<?= htmlspecialchars(url('/monitor/verMisSolicitudes')) ?>" class="btn btn-outline-primary btn-sm fw-semibold">
                        <i class="fas fa-user me-1"></i> Mis solicitudes
                    </a>
                    <a href="<?= htmlspecialchars(url('/inicioMonitor')) ?>" class="btn btn-outline-secondary btn-sm fw-semibold">
                        <i class="fas fa-arrow-left me-1"></i> Inicio monitor
                    </a>
                </div>
            </div>
        </div>

        <div class="gp-admin-card-panel p-0 overflow-hidden mb-4">
            <div class="px-3 px-md-4 py-3 border-bottom bg-light bg-opacity-50">
                <h2 class="h5 mb-0 text-dark fw-semibold">
                    <i class="fas fa-hourglass-half me-2 text-warning"></i>
                    Pendientes
                    <span class="badge rounded-pill ms-2 bg-secondary"><?= count($pendientes) ?></span>
                </h2>
            </div>
            <div class="table-responsive">
                <?php if (!empty($pendientes)): ?>
                    <table class="table table-bordered align-middle mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Monitor</th>
                                <th>Email</th>
                                <th>Tipo</th>
                                <th>Fecha</th>
                                <th>Descripción</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendientes as $s): ?>
                                <tr>
                                    <td><?= (int) ($s['id'] ?? 0) ?></td>
                                    <td><?= htmlspecialchars($nomMon($s)) ?></td>
                                    <td><?= htmlspecialchars((string) ($s['monitor_email'] ?? '—')) ?></td>
                                    <td><?= htmlspecialchars((string) ($s['tipo'] ?? '—')) ?></td>
                                    <td class="text-nowrap small"><?= htmlspecialchars((string) ($s['fecha_creacion'] ?? '')) ?></td>
                                    <td class="small" style="max-width: 280px; white-space: pre-wrap;"><?= htmlspecialchars((string) ($s['descripcion'] ?? '—')) ?></td>
                                    <td>
                                        <span class="badge <?= htmlspecialchars($badgeEstado('P')) ?>"><?= htmlspecialchars($labelEstado('P')) ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="p-4 text-center text-muted">
                        <i class="fas fa-check-circle fs-3 d-block mb-2 text-success opacity-75"></i>
                        No hay solicitudes pendientes en el centro.
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-6">
                <div class="gp-admin-card-panel p-0 overflow-hidden h-100">
                    <div class="px-3 py-3 border-bottom bg-light bg-opacity-50">
                        <h2 class="h6 mb-0 text-dark fw-semibold">
                            <i class="fas fa-check me-2 text-success"></i> Últimas aprobadas
                        </h2>
                        <p class="small text-muted mb-0 mt-1">Hasta 12 registros recientes.</p>
                    </div>
                    <div class="table-responsive">
                        <?php if (!empty($aprobadas)): ?>
                            <table class="table table-sm table-bordered align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Monitor</th>
                                        <th>Tipo</th>
                                        <th>Revisión</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($aprobadas as $s): ?>
                                        <tr>
                                            <td class="small"><?= htmlspecialchars($nomMon($s)) ?></td>
                                            <td class="small"><?= htmlspecialchars((string) ($s['tipo'] ?? '—')) ?></td>
                                            <td class="small text-nowrap"><?= htmlspecialchars((string) ($s['fecha_revision'] ?? '—')) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="p-3 text-muted small text-center">Sin aprobadas recientes.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="gp-admin-card-panel p-0 overflow-hidden h-100">
                    <div class="px-3 py-3 border-bottom bg-light bg-opacity-50">
                        <h2 class="h6 mb-0 text-dark fw-semibold">
                            <i class="fas fa-times me-2 text-danger"></i> Últimas rechazadas
                        </h2>
                        <p class="small text-muted mb-0 mt-1">Hasta 12 registros recientes.</p>
                    </div>
                    <div class="table-responsive">
                        <?php if (!empty($rechazadas)): ?>
                            <table class="table table-sm table-bordered align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Monitor</th>
                                        <th>Tipo</th>
                                        <th>Revisión</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rechazadas as $s): ?>
                                        <tr>
                                            <td class="small"><?= htmlspecialchars($nomMon($s)) ?></td>
                                            <td class="small"><?= htmlspecialchars((string) ($s['tipo'] ?? '—')) ?></td>
                                            <td class="small text-nowrap"><?= htmlspecialchars((string) ($s['fecha_revision'] ?? '—')) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="p-3 text-muted small text-center">Sin rechazadas recientes.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
