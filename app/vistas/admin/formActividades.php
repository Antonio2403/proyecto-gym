<?php
require_once dirname(__DIR__, 3) . '/core/helpers/horario_centro.php';
$gpHorarioLineas = gp_horario_centro_lineas();
$gpFormTitle = 'Crear actividad';
$gpFormBackUrl = url('/admin/gestionarActividades');
$gpFormSubtitle = 'Programa una actividad en el horario del centro.';
$gpFormBadge = 'Actividades';
require dirname(__DIR__) . '/layouts/partials/gp_form_panel_start.php';
$diaOpts = ['L' => 'Lun', 'M' => 'Mar', 'X' => 'Mié', 'J' => 'Jue', 'V' => 'Vie', 'S' => 'Sáb', 'D' => 'Dom'];
?>
                <p class="text-muted small mb-3">Horario del centro: <?= htmlspecialchars(implode(' · ', $gpHorarioLineas)) ?> (domingo cerrado).</p>
                <p class="gp-form-required-legend text-muted small mb-3">Los campos con <span class="text-danger fw-bold" aria-hidden="true">*</span> son obligatorios.</p>
                <form action="<?= htmlspecialchars(url('/admin/actividades/crear')) ?>" method="post" class="needs-validation gp-form-stack" novalidate data-gp-validate="activityCreate"
                      data-gp-confirm data-gp-confirm-title="Crear actividad" data-gp-confirm-body="¿Crear esta actividad en el horario?" data-gp-confirm-ok="Crear">

                    <div class="mb-3">
                        <label class="form-label gp-label-required">Nombre</label>
                        <input type="text" class="form-control" name="nombre" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" name="descripcion" rows="3"></textarea>
                    </div>

                    <div class="gp-form-grid gp-form-grid--2">
                        <div>
                            <label class="form-label gp-label-required">Duración (minutos)</label>
                            <input type="number" class="form-control" name="duracion" required min="1" max="600" step="1">
                        </div>
                        <div>
                            <label class="form-label gp-label-required">Hora de inicio</label>
                            <input type="time" class="form-control" name="hora_inicio" required>
                        </div>
                    </div>

                    <div class="mb-3 form-check">
                        <input class="form-check-input" type="checkbox" name="recurrente" id="recurrente" value="1" checked>
                        <label class="form-check-label" for="recurrente">Actividad semanal recurrente</label>
                        <p class="form-text small text-muted mb-0">Desmárcalo solo para un evento puntual (una sola fecha en el calendario).</p>
                    </div>

                    <div class="mb-3" data-gp-fecha-puntual hidden>
                        <label class="form-label gp-label-required" for="fecha_actividad">Fecha del evento</label>
                        <input type="date" class="form-control" name="fecha_actividad" id="fecha_actividad" min="<?= htmlspecialchars(date('Y-m-d')) ?>">
                    </div>

                    <div class="mb-3" data-gp-dias-semana>
                        <span class="form-label gp-label-required d-block">Días (puedes marcar varios con la misma hora)</span>
                        <div class="row row-cols-2 row-cols-md-4 g-2">
                            <?php foreach ($diaOpts as $code => $lbl): ?>
                                <div class="col">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="dias_semana[]" value="<?= $code ?>" id="dia_<?= $code ?>"<?= $code === 'D' ? ' disabled title="Domingo: centro cerrado"' : '' ?>>
                                        <label class="form-check-label" for="dia_<?= $code ?>"><?= htmlspecialchars($lbl) ?></label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="gp-form-grid gp-form-grid--2">
                        <div>
                            <label class="form-label">Sala</label>
                            <select class="form-select" name="sala_id" required>
                                <option value="">Seleccione una sala</option>
                                <?php foreach ($salas as $sala): ?>
                                    <option value="<?= (int) $sala['id'] ?>">
                                        <?= htmlspecialchars((string) $sala['nombre'], ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="form-label gp-label-required">Monitor</label>
                            <select class="form-select" name="monitor_id" required>
                                <option value="">Seleccione un monitor</option>
                                <?php foreach ($monitores as $monitor): ?>
                                    <?php
                                    $mid = (int) ($monitor['monitor_id'] ?? $monitor['id'] ?? 0);
                                    $mnom = trim(($monitor['nombre'] ?? '') . ' ' . ($monitor['apellido1'] ?? ''));
                                    if ($mnom === '') {
                                        $mnom = (string) ($monitor['email'] ?? 'Monitor');
                                    }
                                    ?>
                                    <option value="<?= $mid ?>">
                                        <?= htmlspecialchars($mnom) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="gp-form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1" aria-hidden="true"></i> Guardar actividad
                        </button>
                        <a href="<?= htmlspecialchars(url('/admin/gestionarActividades')) ?>" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
<?php require dirname(__DIR__) . '/layouts/partials/gp_form_panel_end.php'; ?>
