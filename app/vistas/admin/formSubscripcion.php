<?php
$gpFormTitle = 'Crear suscripción';
$gpFormBackUrl = url('/admin/gestionSubscripciones');
$gpFormSubtitle = 'Define un nuevo plan o suscripción para el catálogo.';
$gpFormBadge = 'Suscripciones';
require dirname(__DIR__) . '/layouts/partials/gp_form_panel_start.php';
?>
                <p class="gp-form-required-legend text-muted small mb-3">Todos los datos son obligatorios (<span class="text-danger fw-bold" aria-hidden="true">*</span>).</p>
                <form action="<?= htmlspecialchars(url('/admin/crearSubscripcion')) ?>" method="POST" class="needs-validation gp-form-stack" novalidate data-gp-validate="subscriptionCreate"
                      data-gp-confirm data-gp-confirm-title="Crear suscripción" data-gp-confirm-body="¿Crear esta suscripción/plan?" data-gp-confirm-ok="Crear">

                    <div class="gp-form-grid gp-form-grid--2">
                        <div class="gp-form-span-2">
                            <label for="nombre" class="form-label gp-label-required">Nombre</label>
                            <input type="text" id="nombre" name="nombre" class="form-control" required>
                        </div>
                        <div>
                            <label for="precio" class="form-label gp-label-required">Precio (€)</label>
                            <input type="number" id="precio" name="precio" step="0.01" class="form-control" required>
                        </div>
                        <div>
                            <label for="duracion" class="form-label gp-label-required">Duración (meses)</label>
                            <input type="number" id="duracion" name="duracion" class="form-control" required min="1" max="120" step="1">
                        </div>
                        <div>
                            <label for="numero_clases" class="form-label gp-label-required">Clases por semana</label>
                            <input type="number" id="numero_clases" name="numero_clases" class="form-control" required min="0" max="99" step="1" value="0">
                            <p class="form-text small mb-0">0 = sin límite semanal explícito.</p>
                        </div>
                        <div>
                            <label for="fisio" class="form-label gp-label-required">Fisioterapia incluida</label>
                            <select id="fisio" name="fisio" class="form-select" required>
                                <option value="N" selected>No</option>
                                <option value="S">Sí</option>
                            </select>
                        </div>
                    </div>

                    <fieldset class="gp-form-fieldset mb-0">
                        <legend class="gp-form-fieldset__legend">Oferta promocional</legend>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" role="switch" id="en_oferta" name="en_oferta" value="1">
                            <label class="form-check-label fw-semibold" for="en_oferta">Marcar como oferta por tiempo limitado</label>
                        </div>
                        <div class="mb-3">
                            <label for="oferta_motivo" class="form-label">Motivo de la promoción</label>
                            <input type="text" id="oferta_motivo" name="oferta_motivo" class="form-control" maxlength="120" placeholder="Black Friday, Navidad, Halloween...">
                        </div>
                        <div class="mb-0">
                            <label for="oferta_fin" class="form-label">Fecha límite para comprar la oferta</label>
                            <input type="datetime-local" id="oferta_fin" name="oferta_fin" class="form-control">
                            <p class="form-text small mb-0">Hasta esta fecha se podrá comprar. Quien la compre conservará el plan durante los meses indicados en duración.</p>
                        </div>
                    </fieldset>

                    <div class="gp-form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus me-1" aria-hidden="true"></i> Crear suscripción
                        </button>
                        <a href="<?= htmlspecialchars(url('/admin/gestionSubscripciones')) ?>" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
<?php require dirname(__DIR__) . '/layouts/partials/gp_form_panel_end.php'; ?>
