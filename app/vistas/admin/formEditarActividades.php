<?php
require_once dirname(__DIR__, 3) . '/core/helpers/horario_centro.php';
$gpHorarioLineas = gp_horario_centro_lineas();
$act = $actividad;
$diasSel = $dias_actividad ?? [];
$horaIni = !empty($act['fecha_inicio']) ? date('H:i', strtotime((string) $act['fecha_inicio'])) : '';
$fechaPuntual = !empty($act['fecha_inicio']) ? substr((string) $act['fecha_inicio'], 0, 10) : '';
$dur = (int) ($act['duracion'] ?? 60);
$recurrente = (int) ($act['recurrente'] ?? 1) === 1;
$diaOpts = ['L' => 'Lun', 'M' => 'Mar', 'X' => 'Mié', 'J' => 'Jue', 'V' => 'Vie', 'S' => 'Sáb', 'D' => 'Dom'];
$gpFormTitle = 'Editar actividad';
$gpFormBackUrl = url('/admin/gestionarActividades');
$gpFormSubtitle = 'Modifica la programación de la actividad seleccionada.';
$gpFormBadge = 'Actividades';
require dirname(__DIR__) . '/layouts/partials/gp_form_panel_start.php';
?>
                <p class="text-muted small mb-3">Horario del centro: <?= htmlspecialchars(implode(' · ', $gpHorarioLineas)) ?> (domingo cerrado). Si cambias día u hora, se anulan las reservas y se avisa por correo.</p>
                <p class="gp-form-required-legend text-muted small mb-3">Los campos con <span class="text-danger fw-bold" aria-hidden="true">*</span> son obligatorios.</p>
                <form action="<?= htmlspecialchars(url('/admin/actividades/editar/' . (int) $act['id'])) ?>" method="post" class="needs-validation gp-form-stack" novalidate data-gp-validate="activityEdit"
                      data-gp-confirm data-gp-confirm-title="Guardar actividad" data-gp-confirm-body="¿Guardar los cambios de esta actividad?" data-gp-confirm-ok="Guardar">

                    <input type="hidden" name="id" value="<?= (int) $act['id'] ?>">

                    <div class="mb-3">
                        <label for="nombre" class="form-label gp-label-required">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" value="<?= htmlspecialchars((string) $act['nombre']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3"><?= htmlspecialchars((string) ($act['descripcion'] ?? '')) ?></textarea>
                    </div>

                    <div class="gp-form-grid gp-form-grid--2">
                        <div>
                            <label for="duracion" class="form-label gp-label-required">Duración (minutos)</label>
                            <input type="number" class="form-control" id="duracion" name="duracion" required min="1" max="600" step="1" value="<?= (int) $dur ?>">
                        </div>
                        <div>
                            <label for="hora_inicio" class="form-label gp-label-required">Hora de inicio</label>
                            <input type="time" class="form-control" id="hora_inicio" name="hora_inicio" required value="<?= htmlspecialchars($horaIni) ?>">
                        </div>
                    </div>

                    <div class="mb-3" data-gp-dias-semana<?= $recurrente ? '' : ' hidden' ?>>
                        <span class="form-label gp-label-required d-block">Días de la semana</span>
                        <div class="row row-cols-2 row-cols-md-4 g-2">
                            <?php foreach ($diaOpts as $code => $lbl): ?>
                                <div class="col">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="dias_semana[]" value="<?= $code ?>" id="edia_<?= $code ?>"
                                            <?= in_array($code, $diasSel, true) ? ' checked' : '' ?><?= $code === 'D' ? ' disabled title="Domingo: centro cerrado"' : '' ?>>
                                        <label class="form-check-label" for="edia_<?= $code ?>"><?= htmlspecialchars($lbl) ?></label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="mb-3 form-check">
                        <input class="form-check-input" type="checkbox" name="recurrente" id="erecurrente" value="1"<?= $recurrente ? ' checked' : '' ?>>
                        <label class="form-check-label" for="erecurrente">Actividad semanal recurrente</label>
                        <p class="form-text small text-muted mb-0">Desmárcalo solo para un evento puntual (una sola fecha en el calendario).</p>
                    </div>

                    <div class="mb-3" data-gp-fecha-puntual<?= $recurrente ? ' hidden' : '' ?>>
                        <label class="form-label gp-label-required" for="fecha_actividad">Fecha del evento</label>
                        <input type="date" class="form-control" name="fecha_actividad" id="fecha_actividad"
                               min="<?= htmlspecialchars(date('Y-m-d')) ?>"
                               value="<?= htmlspecialchars($fechaPuntual) ?>"<?= $recurrente ? '' : ' required' ?>>
                    </div>

                    <div class="gp-form-grid gp-form-grid--2">
                        <div>
                            <label for="sala" class="form-label">Sala</label>
                            <select class="form-select" id="sala" name="sala_id" required>
                                <option value="">Seleccione una sala</option>
                                <?php foreach ($salas as $sala): ?>
                                    <option value="<?= (int) $sala['id'] ?>"<?= ((int) $sala['id'] === (int) ($act['sala_id'] ?? 0)) ? ' selected' : '' ?>>
                                        <?= htmlspecialchars((string) $sala['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label for="monitor" class="form-label gp-label-required">Monitor</label>
                            <select class="form-select" id="monitor" name="monitor_id" required>
                                <option value="">Seleccione un monitor</option>
                                <?php foreach ($monitores as $monitor): ?>
                                    <?php
                                    $mid = (int) ($monitor['monitor_id'] ?? $monitor['id'] ?? 0);
                                    $mnom = trim(($monitor['nombre'] ?? '') . ' ' . ($monitor['apellido1'] ?? ''));
                                    if ($mnom === '') {
                                        $mnom = (string) ($monitor['email'] ?? 'Monitor');
                                    }
                                    ?>
                                    <option value="<?= $mid ?>"<?= ($mid === (int) ($act['monitor_id'] ?? 0)) ? ' selected' : '' ?>>
                                        <?= htmlspecialchars($mnom) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="gp-form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1" aria-hidden="true"></i> Actualizar actividad
                        </button>
                        <a href="<?= htmlspecialchars(url('/admin/gestionarActividades')) ?>" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
<?php require dirname(__DIR__) . '/layouts/partials/gp_form_panel_end.php'; ?>
