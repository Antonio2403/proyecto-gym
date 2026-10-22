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
            return 'bg-yellow-400 text-slate-800';
        case 'A':
            return 'bg-green-600 text-white';
        case 'R':
            return 'bg-red-600 text-white';
        default:
            return 'bg-slate-500 text-white';
    }
};

$labelEstado = static function (string $est): string {
    $m = Solicitud::metaEstado(strtoupper($est));

    return $m['label'];
};
?>
<div class="content-wrapper">
    <div class="relative w-full px-3 gp-monitor-requests">
        <div class="gp-admin-card-panel mb-6 border-0 shadow-sm">
            <div class="flex flex-col lg:flex-row justify-between items-start gap-4">
                <div>
                    <h1 class="text-[1.75rem] mb-2">Solicitudes del centro</h1>
                    <p class="text-slate-500 mb-0">
                        Peticiones del equipo de monitores. Las pendientes las gestiona administración; aquí solo consultas el estado del centro.
                    </p>
                </div>
                <div class="gp-view-toolbar">
                    <a href="<?= htmlspecialchars(url('/monitor/formSolicitud')) ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus-circle me-1" aria-hidden="true"></i> Nueva solicitud
                    </a>
                    <a href="<?= htmlspecialchars(url('/monitor/verMisSolicitudes')) ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-list me-1" aria-hidden="true"></i> Mis solicitudes
                    </a>
                    <a href="<?= htmlspecialchars(url('/inicioMonitor')) ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-home me-1" aria-hidden="true"></i> Inicio monitor
                    </a>
                </div>
            </div>
        </div>

        <div class="gp-admin-card-panel p-0 overflow-hidden mb-6 border-0 shadow-sm">
            <div class="px-4 md:px-6 py-4 border-b border-slate-300 bg-slate-50/50">
                <h2 class="text-xl mb-0 text-slate-800 font-semibold">
                    <i class="fas fa-hourglass-half me-2 text-yellow-700"></i>
                    Pendientes
                    <span class="badge rounded-full ms-2 bg-slate-500"><?= count($pendientes) ?></span>
                </h2>
            </div>
            <div class="table-responsive">
                <?php if (!empty($pendientes)): ?>
                    <table class="table table-bordered align-middle mb-0 gp-table-light">
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
                                    <td class="text-nowrap text-sm"><?= htmlspecialchars((string) ($s['fecha_creacion'] ?? '')) ?></td>
                                    <td class="text-sm" style="max-width: 280px; white-space: pre-wrap;"><?= htmlspecialchars((string) ($s['descripcion'] ?? '—')) ?></td>
                                    <td>
                                        <span class="badge <?= htmlspecialchars($badgeEstado('P')) ?>"><?= htmlspecialchars($labelEstado('P')) ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="p-6 text-center text-slate-500">
                        <i class="fas fa-check-circle text-xl block mb-2 text-green-700 opacity-75"></i>
                        No hay solicitudes pendientes en el centro.
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="flex flex-wrap -mx-3 -mt-6">
            <div class="w-full lg:w-1/2 max-w-full shrink-0 px-3 mt-6">
                <div class="gp-admin-card-panel p-0 overflow-hidden h-full border-0 shadow-sm">
                    <div class="px-4 py-4 border-b border-slate-300 bg-slate-50/50">
                        <h2 class="text-base mb-0 text-slate-800 font-semibold">
                            <i class="fas fa-check me-2 text-green-700"></i> Últimas aprobadas
                        </h2>
                        <p class="text-sm text-slate-500 mb-0 mt-1">Hasta 12 registros recientes.</p>
                    </div>
                    <div class="table-responsive">
                        <?php if (!empty($aprobadas)): ?>
                            <table class="table table-sm table-bordered align-middle mb-0 gp-table-light">
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
                                            <td class="text-sm"><?= htmlspecialchars($nomMon($s)) ?></td>
                                            <td class="text-sm"><?= htmlspecialchars((string) ($s['tipo'] ?? '—')) ?></td>
                                            <td class="text-sm text-nowrap"><?= htmlspecialchars((string) ($s['fecha_revision'] ?? '—')) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="p-4 text-slate-500 text-sm text-center">Sin aprobadas recientes.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="w-full lg:w-1/2 max-w-full shrink-0 px-3 mt-6">
                <div class="gp-admin-card-panel p-0 overflow-hidden h-full border-0 shadow-sm">
                    <div class="px-4 py-4 border-b border-slate-300 bg-slate-50/50">
                        <h2 class="text-base mb-0 text-slate-800 font-semibold">
                            <i class="fas fa-times me-2 text-red-600"></i> Últimas rechazadas
                        </h2>
                        <p class="text-sm text-slate-500 mb-0 mt-1">Hasta 12 registros recientes.</p>
                    </div>
                    <div class="table-responsive">
                        <?php if (!empty($rechazadas)): ?>
                            <table class="table table-sm table-bordered align-middle mb-0 gp-table-light">
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
                                            <td class="text-sm"><?= htmlspecialchars($nomMon($s)) ?></td>
                                            <td class="text-sm"><?= htmlspecialchars((string) ($s['tipo'] ?? '—')) ?></td>
                                            <td class="text-sm text-nowrap"><?= htmlspecialchars((string) ($s['fecha_revision'] ?? '—')) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="p-4 text-slate-500 text-sm text-center">Sin rechazadas recientes.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
