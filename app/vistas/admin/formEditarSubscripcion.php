<?php
$gpFormTitle = 'Editar suscripción';
$gpFormBackUrl = url('/admin/gestionSubscripciones');
$gpFormSubtitle = 'Modifica los datos del plan seleccionado.';
$gpFormBadge = 'Suscripciones';
require dirname(__DIR__) . '/layouts/partials/gp_form_panel_start.php';
$enOferta = (int) ($subscripcion['en_oferta'] ?? 0) === 1;
$ofertaFin = '';
if (!empty($subscripcion['oferta_fin'])) {
    $ofertaFin = str_replace(' ', 'T', substr((string) $subscripcion['oferta_fin'], 0, 16));
}
$fisioVal = (string) ($subscripcion['fisio'] ?? 'N');
?>
                <p class="gp-form-required-legend text-muted small mb-3">Todos los datos son obligatorios (<span class="text-danger fw-bold" aria-hidden="true">*</span>).</p>
                <form action="<?= htmlspecialchars(url('/admin/editarSubscripcion')) ?>" method="POST" class="needs-validation gp-form-stack" novalidate data-gp-validate="subscriptionEdit"
                      data-gp-confirm data-gp-confirm-title="Actualizar suscripción" data-gp-confirm-body="¿Guardar los cambios del plan?" data-gp-confirm-ok="Guardar">
                    <input type="hidden" name="id" value="<?= htmlspecialchars((string) $subscripcion['id']) ?>">

                    <div class="gp-form-grid gp-form-grid--2">
                        <div class="gp-form-span-2">
                            <label for="nombre" class="form-label gp-label-required">Nombre</label>
                            <input type="text" id="nombre" name="nombre" class="form-control" value="<?= htmlspecialchars((string) $subscripcion['nombre']) ?>" required>
                        </div>
                        <div>
                            <label for="precio" class="form-label gp-label-required">Precio (€)</label>
                            <input type="number" id="precio" name="precio" step="0.01" class="form-control" value="<?= htmlspecialchars((string) $subscripcion['precio']) ?>" required>
                        </div>
                        <div>
                            <label for="duracion" class="form-label gp-label-required">Duración (meses)</label>
                            <input type="number" id="duracion" name="duracion" class="form-control" value="<?= htmlspecialchars((string) $subscripcion['duracion']) ?>" required min="1" max="120" step="1">
                        </div>
                        <div>
                            <label for="numero_clases" class="form-label gp-label-required">Clases por semana</label>
                            <input type="number" id="numero_clases" name="numero_clases" class="form-control" value="<?= htmlspecialchars((string) ($subscripcion['numero_clases'] ?? 0)) ?>" required min="0" max="99" step="1">
                            <p class="form-text small mb-0">0 = sin límite semanal explícito.</p>
                        </div>
                        <div>
                            <label for="fisio" class="form-label gp-label-required">Fisioterapia incluida</label>
                            <select id="fisio" name="fisio" class="form-select" required>
                                <option value="N"<?= $fisioVal === 'N' ? ' selected' : '' ?>>No</option>
                                <option value="S"<?= $fisioVal === 'S' ? ' selected' : '' ?>>Sí</option>
                            </select>
                        </div>
                    </div>

                    <fieldset class="gp-form-fieldset mb-0">
                        <legend class="gp-form-fieldset__legend">Oferta promocional</legend>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" role="switch" id="en_oferta" name="en_oferta" value="1"<?= $enOferta ? ' checked' : '' ?>>
                            <label class="form-check-label fw-semibold" for="en_oferta">Marcar como oferta por tiempo limitado</label>
                        </div>
                        <div class="mb-3">
                            <label for="oferta_motivo" class="form-label">Motivo de la promoción</label>
                            <input type="text" id="oferta_motivo" name="oferta_motivo" class="form-control" maxlength="120" value="<?= htmlspecialchars((string) ($subscripcion['oferta_motivo'] ?? '')) ?>" placeholder="Black Friday, Navidad, Halloween...">
                        </div>
                        <div class="mb-0">
                            <label for="oferta_fin" class="form-label">Fecha límite para comprar la oferta</label>
                            <input type="datetime-local" id="oferta_fin" name="oferta_fin" class="form-control" value="<?= htmlspecialchars($ofertaFin) ?>">
                            <p class="form-text small mb-0">Al pasar esta fecha se retira del catálogo; los clientes que ya la compraron conservan su vigencia hasta su fecha_fin.</p>
                        </div>
                    </fieldset>

                    <div class="gp-form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1" aria-hidden="true"></i> Actualizar suscripción
                        </button>
                        <a href="<?= htmlspecialchars(url('/admin/gestionSubscripciones')) ?>" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
<?php require dirname(__DIR__) . '/layouts/partials/gp_form_panel_end.php'; ?>
