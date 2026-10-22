<?php
$orden = $comentarios_orden ?? 'asc';
$cp = (int) ($comentarios_page ?? 1);
$pp = (int) ($comentarios_per_page ?? 10);
$tp = (int) ($comentarios_total_pages ?? 1);
$aid = (int) ($actividad['id'] ?? 0);
$fechaKey = (string) ($fecha_ocurrencia ?? '');
$qBase = [
    'actividad_id' => $aid,
    'fecha' => $fechaKey,
    'por_pagina' => $pp,
];
?>
<div class="mx-auto w-full max-w-[1320px] lg:max-w-[1024px] xl:max-w-7xl px-3 py-12">
    <header class="gp-page-header">
        <span class="gp-badge mb-2">Sesión</span>
        <h1 class="text-[1.75rem] mb-2">Comentarios</h1>
        <p class="text-slate-500 mb-0">
            <?= htmlspecialchars($actividad['nombre'] ?? '') ?> —
            <strong><?= htmlspecialchars($fechaKey) ?></strong>
        </p>
    </header>

    <?php if (!empty($_GET['info'])): ?>
        <div class="alert alert-info border-0"><?= htmlspecialchars((string) $_GET['info']) ?></div>
    <?php endif; ?>
    <?php if (!empty($_GET['success'])): ?>
        <div class="alert alert-success border-0">Comentario publicado.</div>
    <?php endif; ?>
    <?php if (!empty($_GET['error'])): ?>
        <div class="alert alert-danger border-0"><?= htmlspecialchars((string) $_GET['error'], ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <?php if (empty($tiene_reserva_sesion)): ?>
        <div class="alert alert-warning border-0">No estás inscrito en esta sesión.</div>
    <?php elseif (empty($asistio_registrado)): ?>
        <div class="alert alert-warning border-0">
            No tienes marcada la asistencia para esta sesión, así que no puedes comentar. Si hubo un error,
            habla con recepción.
        </div>
    <?php elseif (empty($sesionPasada)): ?>
        <div class="alert alert-info border-0">
            Tu reserva está confirmada. Cuando termine esta sesión podrás valorarla y escribir un comentario.
        </div>
    <?php endif; ?>

    <?php if (!empty($puedeComentar)): ?>
        <div class="card border-0 mb-6">
            <div class="card-body p-6">
                <h2 class="text-base mb-4">Añadir comentario</h2>
                <p class="gp-form-required-legend text-slate-500 text-sm mb-4">El comentario es obligatorio (<span class="text-red-600 font-bold" aria-hidden="true">*</span>).</p>
                <form method="post" action="<?= htmlspecialchars(url('/usuario/actividades/sesion/comentarios')) ?>" class="needs-validation" novalidate data-gp-validate="comments"
                      data-gp-confirm
                      data-gp-confirm-title="Publicar comentario"
                      data-gp-confirm-body="¿Publicar este comentario en la sesión? Podrás consultarlo después en la lista."
                      data-gp-confirm-ok="Publicar">
                    <input type="hidden" name="actividad_id" value="<?= $aid ?>">
                    <input type="hidden" name="fecha_ocurrencia" value="<?= htmlspecialchars($fechaKey) ?>">
                    <input type="hidden" name="orden" value="<?= htmlspecialchars($orden) ?>">
                    <input type="hidden" name="por_pagina" value="<?= $pp ?>">
                    <div class="mb-4">
                        <label class="form-label gp-label-required" for="texto">Tu comentario</label>
                        <textarea class="form-control" id="texto" name="texto" rows="4" maxlength="2000" minlength="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Publicar</button>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <div class="flex flex-wrap justify-between items-center gap-4 mb-4">
        <span class="text-sm text-slate-500"><?= (int) ($comentarios_total ?? 0) ?> comentario(s)</span>
        <div class="flex flex-wrap items-center gap-2">
            <span class="text-sm text-slate-500 me-1">Orden por fecha:</span>
            <a class="btn btn-sm <?= $orden === 'asc' ? 'btn-primary' : 'btn-outline-secondary' ?>"
               href="?<?= htmlspecialchars(http_build_query(array_merge($qBase, ['orden' => 'asc', 'p' => 1])), ENT_QUOTES, 'UTF-8') ?>">
                Más antiguos primero
            </a>
            <a class="btn btn-sm <?= $orden === 'desc' ? 'btn-primary' : 'btn-outline-secondary' ?>"
               href="?<?= htmlspecialchars(http_build_query(array_merge($qBase, ['orden' => 'desc', 'p' => 1])), ENT_QUOTES, 'UTF-8') ?>">
                Más recientes primero
            </a>
        </div>
    </div>

    <ul class="list-group list-group-flush rounded-lg overflow-hidden border border-slate-500/25">
        <?php foreach ($comentarios ?? [] as $c): ?>
            <li class="list-group-item px-6 py-4">
                <div class="text-sm text-slate-500 mb-2">
                    <?= htmlspecialchars(trim(($c['autor_nombre'] ?? '') . ' ' . ($c['autor_apellido1'] ?? ''))) ?>
                    · <?= htmlspecialchars((string) ($c['fecha'] ?? '')) ?>
                </div>
                <div class="wrap-anywhere" style="white-space: pre-wrap;"><?= htmlspecialchars((string) ($c['texto'] ?? '')) ?></div>
            </li>
        <?php endforeach; ?>
        <?php if (empty($comentarios)): ?>
            <li class="list-group-item text-slate-500 px-6 py-12 text-center">Aún no hay comentarios.</li>
        <?php endif; ?>
    </ul>

    <?php if ($tp > 1): ?>
        <nav class="mt-6 flex flex-wrap justify-between items-center gap-4" aria-label="Paginación de comentarios">
            <div>
                <?php if ($cp > 1): ?>
                    <a class="btn btn-sm btn-outline-light"
                       href="?<?= htmlspecialchars(http_build_query(array_merge($qBase, ['orden' => $orden, 'p' => $cp - 1])), ENT_QUOTES, 'UTF-8') ?>">&laquo; Anterior</a>
                <?php endif; ?>
                <?php if ($cp < $tp): ?>
                    <a class="btn btn-sm btn-outline-light <?= $cp > 1 ? 'ms-2' : '' ?>"
                       href="?<?= htmlspecialchars(http_build_query(array_merge($qBase, ['orden' => $orden, 'p' => $cp + 1])), ENT_QUOTES, 'UTF-8') ?>">
                        Siguiente &raquo;
                    </a>
                <?php endif; ?>
            </div>
            <ul class="pagination mb-0 flex-wrap justify-center">
                <?php for ($p = 1; $p <= $tp; $p++): ?>
                    <li class="page-item <?= $p === $cp ? 'active' : '' ?>">
                        <a class="page-link" href="?<?= htmlspecialchars(http_build_query(array_merge($qBase, ['orden' => $orden, 'p' => $p])), ENT_QUOTES, 'UTF-8') ?>"><?= $p ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>

    <p class="mt-12 mb-0">
        <a href="<?= htmlspecialchars(url('/usuario/actividades')) ?>" class="btn btn-outline-light btn-sm">&larr; Volver al horario</a>
    </p>
</div>
