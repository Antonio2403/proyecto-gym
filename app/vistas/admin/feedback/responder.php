<?php
/** @var array<string, mixed> $fb */
$fb = is_array($fb ?? null) ? $fb : [];
$id = (int) ($fb['id'] ?? 0);
$gpFormTitle = 'Responder mensaje';
$gpFormBackUrl = url('/admin/feedback');
$gpFormSubtitle = 'La respuesta se enviará por correo al remitente.';
$gpFormBadge = 'Feedback';
require dirname(__DIR__, 2) . '/layouts/partials/gp_form_panel_start.php';
?>
                <dl class="row small mb-4 text-muted gp-form-meta">
                    <dt class="col-sm-3">De</dt>
                    <dd class="col-sm-9 text-dark"><?= htmlspecialchars((string) ($fb['nombre'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                        &lt;<?= htmlspecialchars((string) ($fb['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>&gt;</dd>
                    <dt class="col-sm-3">Asunto</dt>
                    <dd class="col-sm-9 text-dark"><?= htmlspecialchars((string) ($fb['asunto'] ?? ''), ENT_QUOTES, 'UTF-8') ?></dd>
                    <dt class="col-sm-3">Fecha</dt>
                    <dd class="col-sm-9"><?= htmlspecialchars((string) ($fb['fecha_creacion'] ?? ''), ENT_QUOTES, 'UTF-8') ?></dd>
                </dl>
                <div class="mb-4 p-3 rounded-3 border bg-light bg-opacity-50 small gp-form-quote" style="white-space: pre-wrap;"><?= htmlspecialchars((string) ($fb['mensaje'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
                <form method="post" action="<?= htmlspecialchars(url('/admin/feedback/responder')) ?>" class="needs-validation gp-form-stack" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="feedback_id" value="<?= $id ?>">
                    <div class="mb-0">
                        <label for="respuesta_fb" class="form-label fw-semibold">Tu respuesta (llegará por correo)</label>
                        <textarea class="form-control" id="respuesta_fb" name="respuesta" rows="8" required maxlength="8000" placeholder="Escribe aquí la respuesta para el remitente…"></textarea>
                    </div>
                    <div class="gp-form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-1" aria-hidden="true"></i> Enviar respuesta
                        </button>
                        <a href="<?= htmlspecialchars(url('/admin/feedback')) ?>" class="btn btn-outline-secondary">Volver al listado</a>
                    </div>
                </form>
<?php require dirname(__DIR__, 2) . '/layouts/partials/gp_form_panel_end.php'; ?>
