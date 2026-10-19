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
                <dl class="text-sm mb-6 text-slate-500 gp-form-meta flex flex-wrap -mx-3">
                    <dt class="w-full sm:w-1/4 max-w-full shrink-0 px-3">De</dt>
                    <dd class="text-slate-800 w-full sm:w-3/4 max-w-full shrink-0 px-3"><?= htmlspecialchars((string) ($fb['nombre'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                        &lt;<?= htmlspecialchars((string) ($fb['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>&gt;</dd>
                    <dt class="w-full sm:w-1/4 max-w-full shrink-0 px-3">Asunto</dt>
                    <dd class="text-slate-800 w-full sm:w-3/4 max-w-full shrink-0 px-3"><?= htmlspecialchars((string) ($fb['asunto'] ?? ''), ENT_QUOTES, 'UTF-8') ?></dd>
                    <dt class="w-full sm:w-1/4 max-w-full shrink-0 px-3">Fecha</dt>
                    <dd class="w-full sm:w-3/4 max-w-full shrink-0 px-3"><?= htmlspecialchars((string) ($fb['fecha_creacion'] ?? ''), ENT_QUOTES, 'UTF-8') ?></dd>
                </dl>
                <div class="mb-6 p-4 rounded-lg border border-slate-300 bg-slate-50/50 text-sm gp-form-quote" style="white-space: pre-wrap;"><?= htmlspecialchars((string) ($fb['mensaje'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
                <form method="post" action="<?= htmlspecialchars(url('/admin/feedback/responder')) ?>" class="needs-validation gp-form-stack" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="feedback_id" value="<?= $id ?>">
                    <div class="mb-0">
                        <label for="respuesta_fb" class="form-label font-semibold">Tu respuesta (llegará por correo)</label>
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
