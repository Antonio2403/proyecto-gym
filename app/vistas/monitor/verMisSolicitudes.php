<?php
$lista = $solicitudes ?? [];

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
?>
<div class="content-wrapper">
    <div class="container-fluid gp-monitor-requests">
        <div class="gp-admin-card-panel mb-4 border-0 shadow-sm">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start gap-3">
                <div>
                    <h1 class="h3 mb-2">Mis solicitudes</h1>
                    <p class="text-muted mb-0">
                        Historial de peticiones que has enviado a administración.
                    </p>
                </div>
                <div class="gp-view-toolbar">
                    <a href="<?= htmlspecialchars(url('/monitor/formSolicitud')) ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus-circle me-1" aria-hidden="true"></i> Nueva solicitud
                    </a>
                    <a href="<?= htmlspecialchars(url('/monitor/verMonitorSolicitudes')) ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-envelope-open me-1" aria-hidden="true"></i> Solicitudes del centro
                    </a>
                    <a href="<?= htmlspecialchars(url('/inicioMonitor')) ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-home me-1" aria-hidden="true"></i> Inicio monitor
                    </a>
                </div>
            </div>
        </div>

        <?php if (!empty($lista)): ?>
            <div class="row g-3">
                <?php foreach ($lista as $s):
                    $est = strtoupper((string) ($s['estado'] ?? ''));
                    $meta = Solicitud::metaEstado($est);
                    ?>
                    <div class="col-12 col-xl-6">
                        <div class="gp-admin-card-panel h-100 border-0 shadow-sm gp-monitor-request-card">
                            <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                <span class="badge <?= htmlspecialchars($badgeEstado($est)) ?>"><?= htmlspecialchars($meta['label']) ?></span>
                                <span class="small text-muted ms-auto text-nowrap">
                                    <i class="far fa-clock me-1 opacity-75" aria-hidden="true"></i><?= htmlspecialchars((string) ($s['fecha_creacion'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </div>
                            <h2 class="h6 text-dark mb-2">
                                <i class="fas fa-tag me-1 text-primary opacity-75" aria-hidden="true"></i><?= htmlspecialchars((string) ($s['tipo'] ?? '—'), ENT_QUOTES, 'UTF-8') ?>
                            </h2>
                            <?php if (trim((string) ($s['descripcion'] ?? '')) !== ''): ?>
                                <p class="small text-muted mb-0" style="white-space: pre-wrap;"><?= htmlspecialchars((string) ($s['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
                            <?php else: ?>
                                <p class="small text-muted mb-0">Sin descripción.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="gp-admin-card-panel text-center py-5 border-0 shadow-sm">
                <i class="fas fa-inbox fs-2 text-muted mb-3 d-block opacity-50" aria-hidden="true"></i>
                <p class="text-muted mb-3">No hay solicitudes hechas por ti.</p>
                <a href="<?= htmlspecialchars(url('/monitor/formSolicitud')) ?>" class="btn btn-primary btn-sm">Crear la primera</a>
            </div>
        <?php endif; ?>
    </div>
</div>
