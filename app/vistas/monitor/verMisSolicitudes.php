<?php
$lista = $solicitudes ?? [];

$badgeEstado = static function (string $est): string {
    switch (strtoupper($est)) {
        case 'P':
            return 'bg-yellow-400 text-slate-800';
        case 'A':
            return 'bg-green-600 text-white';
        case 'R':
            return 'bg-red-600 text-white';
        default:
            return 'bg-slate-500 text-white';
    }
};
?>
<div class="content-wrapper">
    <div class="relative w-full px-3 gp-monitor-requests">
        <div class="gp-admin-card-panel mb-6 border-0 shadow-sm">
            <div class="flex flex-col lg:flex-row justify-between items-start gap-4">
                <div>
                    <h1 class="text-[1.75rem] mb-2">Mis solicitudes</h1>
                    <p class="text-slate-500 mb-0">
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
            <div class="flex flex-wrap -mx-2 -mt-4">
                <?php foreach ($lista as $s):
                    $est = strtoupper((string) ($s['estado'] ?? ''));
                    $meta = Solicitud::metaEstado($est);
                    ?>
                    <div class="w-full xl:w-1/2 max-w-full shrink-0 px-2 mt-4">
                        <div class="gp-admin-card-panel h-full border-0 shadow-sm gp-monitor-request-card">
                            <div class="flex flex-wrap items-center gap-2 mb-2">
                                <span class="badge <?= htmlspecialchars($badgeEstado($est)) ?>"><?= htmlspecialchars($meta['label']) ?></span>
                                <span class="text-sm text-slate-500 ms-auto text-nowrap">
                                    <i class="far fa-clock me-1 opacity-75" aria-hidden="true"></i><?= htmlspecialchars((string) ($s['fecha_creacion'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </div>
                            <h2 class="text-base text-slate-800 mb-2">
                                <i class="fas fa-tag me-1 text-spartum-orange opacity-75" aria-hidden="true"></i><?= htmlspecialchars((string) ($s['tipo'] ?? '—'), ENT_QUOTES, 'UTF-8') ?>
                            </h2>
                            <?php if (trim((string) ($s['descripcion'] ?? '')) !== ''): ?>
                                <p class="text-sm text-slate-500 mb-0" style="white-space: pre-wrap;"><?= htmlspecialchars((string) ($s['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
                            <?php else: ?>
                                <p class="text-sm text-slate-500 mb-0">Sin descripción.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="gp-admin-card-panel text-center py-12 border-0 shadow-sm">
                <i class="fas fa-inbox text-2xl text-slate-500 mb-4 block opacity-50" aria-hidden="true"></i>
                <p class="text-slate-500 mb-4">No hay solicitudes hechas por ti.</p>
                <a href="<?= htmlspecialchars(url('/monitor/formSolicitud')) ?>" class="btn btn-primary btn-sm">Crear la primera</a>
            </div>
        <?php endif; ?>
    </div>
</div>
