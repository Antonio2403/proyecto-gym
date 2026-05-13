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
<div class="container py-5">
    <header class="gp-page-header">
        <span class="gp-badge mb-2">Sesión</span>
        <h1 class="h3 mb-2">Comentarios</h1>
        <p class="text-muted mb-0">
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
        <div class="card border-0 mb-4">
            <div class="card-body p-4">
                <h2 class="h6 mb-3">Añadir comentario</h2>
                <p class="gp-form-required-legend text-muted small mb-3">El comentario es obligatorio (<span class="text-danger fw-bold" aria-hidden="true">*</span>).</p>
                <form method="post" action="<?= htmlspecialchars(url('/usuario/actividades/sesion/comentarios')) ?>" class="needs-validation" novalidate data-gp-validate="comments"
                      data-gp-confirm
                      data-gp-confirm-title="Publicar comentario"
                      data-gp-confirm-body="¿Publicar este comentario en la sesión? Podrás consultarlo después en la lista."
                      data-gp-confirm-ok="Publicar">
                    <input type="hidden" name="actividad_id" value="<?= $aid ?>">
                    <input type="hidden" name="fecha_ocurrencia" value="<?= htmlspecialchars($fechaKey) ?>">
                    <input type="hidden" name="orden" value="<?= htmlspecialchars($orden) ?>">
                    <input type="hidden" name="por_pagina" value="<?= $pp ?>">
                    <div class="mb-3">
                        <label class="form-label gp-label-required" for="texto">Tu comentario</label>
                        <textarea class="form-control" id="texto" name="texto" rows="4" maxlength="2000" minlength="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Publicar</button>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
        <span class="small text-muted"><?= (int) ($comentarios_total ?? 0) ?> comentario(s)</span>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <span class="small text-muted me-1">Orden por fecha:</span>
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

    <ul class="list-group list-group-flush rounded-3 overflow-hidden border border-secondary border-opacity-25">
        <?php foreach ($comentarios ?? [] as $c): ?>
            <li class="list-group-item px-4 py-3">
                <div class="small text-muted mb-2">
                    <?= htmlspecialchars(trim(($c['autor_nombre'] ?? '') . ' ' . ($c['autor_apellido1'] ?? ''))) ?>
                    · <?= htmlspecialchars((string) ($c['fecha'] ?? '')) ?>
                </div>
                <div class="text-break" style="white-space: pre-wrap;"><?= htmlspecialchars((string) ($c['texto'] ?? '')) ?></div>
            </li>
        <?php endforeach; ?>
        <?php if (empty($comentarios)): ?>
            <li class="list-group-item text-muted px-4 py-5 text-center">Aún no hay comentarios.</li>
        <?php endif; ?>
    </ul>

    <?php if ($tp > 1): ?>
        <nav class="mt-4 d-flex flex-wrap justify-content-between align-items-center gap-3" aria-label="Paginación de comentarios">
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
            <ul class="pagination mb-0 flex-wrap justify-content-center">
                <?php for ($p = 1; $p <= $tp; $p++): ?>
                    <li class="page-item <?= $p === $cp ? 'active' : '' ?>">
                        <a class="page-link" href="?<?= htmlspecialchars(http_build_query(array_merge($qBase, ['orden' => $orden, 'p' => $p])), ENT_QUOTES, 'UTF-8') ?>"><?= $p ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>

    <p class="mt-5 mb-0">
        <a href="<?= htmlspecialchars(url('/usuario/actividades')) ?>" class="btn btn-outline-light btn-sm">&larr; Volver al horario</a>
    </p>
</div>
