<?php
require_once dirname(__DIR__, 4) . '/core/helpers/horario_centro.php';
$gpHorarioLineas = gp_horario_centro_lineas();
$minFecha = (string) ($min_fecha_cita ?? date('Y-m-d'));
$horasUrl = (string) ($horas_disponibles_url ?? url('/usuario/fisio/horas-disponibles'));
$slotMin = gp_fisio_duracion_consulta_minutos();
?>
<div class="gp-schedule-page py-4 py-lg-5">
    <div class="container">
        <div class="gp-schedule-panel shadow-sm">
            <header class="gp-schedule-head text-center py-4 px-3">
                <h1 class="gp-schedule-title mb-0">Solicitar cita</h1>
                <p class="gp-schedule-subtitle mb-0 mt-2">Consultas de <?= (int) $slotMin ?> min · elige profesional, día y hora libre.</p>
            </header>

            <?php if (!empty($_GET['error'])): ?>
                <div class="px-4 pb-3">
                    <div class="alert alert-warning mb-0"><?= htmlspecialchars((string) $_GET['error']) ?></div>
                </div>
            <?php endif; ?>

            <div class="px-3 px-md-4 pb-4">
                <?php if (empty($fisioterapeutas)): ?>
                    <div class="gp-light-panel-inner p-4 mb-3">
                        <p class="text-muted mb-3 mb-0">Aún no hay fisioterapeutas dados de alta. Contacta con recepción.</p>
                    </div>
                    <a href="<?= htmlspecialchars(url('/usuario/fisio')) ?>" class="btn btn-outline-secondary">Volver</a>
                <?php else: ?>
                    <div class="row justify-content-center">
                        <div class="col-lg-7">
                            <form method="post"
                                  action="<?= htmlspecialchars(url('/usuario/fisio/solicitar')) ?>"
                                  class="gp-light-panel-inner p-4 needs-validation"
                                  novalidate
                                  data-gp-validate="fisioRequest"
                                  data-gp-fisio-slots="1"
                                  data-horas-url="<?= htmlspecialchars($horasUrl) ?>"
                                  data-gp-confirm
                                  data-gp-confirm-title="Solicitar cita"
                                  data-gp-confirm-body="¿Enviar esta solicitud de cita?"
                                  data-gp-confirm-ok="Enviar solicitud">
                                <p class="text-muted small mb-4">Horario del centro: <?= htmlspecialchars(implode(' · ', $gpHorarioLineas)) ?> (domingo cerrado). Solo se muestran huecos libres cada <?= (int) $slotMin ?> min.</p>
                                <p class="gp-form-required-legend text-muted small mb-4">Todos los datos son obligatorios (<span class="text-danger fw-bold" aria-hidden="true">*</span>).</p>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold text-dark gp-label-required" for="fisio_id">Fisioterapeuta</label>
                                    <select name="fisio_id" id="fisio_id" class="form-select" required>
                                        <option value="">— Selecciona —</option>
                                        <?php foreach ($fisioterapeutas as $f): ?>
                                            <option value="<?= (int) $f['id'] ?>">
                                                <?= htmlspecialchars((string) $f['nombre']) ?>
                                                <?= $f['especialidad'] ? ' · ' . htmlspecialchars((string) $f['especialidad']) : '' ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="gp-form-grid gp-form-grid--2 mb-3">
                                    <div>
                                        <label class="form-label fw-semibold text-dark gp-label-required" for="fecha_cita">Fecha</label>
                                        <input type="date" name="fecha_cita" id="fecha_cita" class="form-control" required
                                               min="<?= htmlspecialchars($minFecha) ?>">
                                    </div>
                                    <div>
                                        <label class="form-label fw-semibold text-dark gp-label-required" for="hora_cita">Hora</label>
                                        <select name="hora_cita" id="hora_cita" class="form-select" required disabled>
                                            <option value="">— Primero elige fisio y fecha —</option>
                                        </select>
                                    </div>
                                </div>
                                <p class="small mb-3" data-gp-fisio-slots-hint></p>
                                <div class="mb-4">
                                    <label class="form-label fw-semibold text-dark gp-label-required" for="motivo">Motivo / síntomas</label>
                                    <textarea name="motivo" id="motivo" class="form-control" rows="4" required maxlength="2000"
                                              placeholder="Ej. molestia en rodilla tras entreno"></textarea>
                                </div>
                                <div class="d-flex flex-wrap gap-2">
                                    <button type="submit" class="btn gp-btn-orange px-4">Enviar solicitud</button>
                                    <a href="<?= htmlspecialchars(url('/usuario/fisio')) ?>" class="btn btn-outline-secondary">Cancelar</a>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<script defer src="<?= htmlspecialchars(asset('js/fisio-citas-slots.js')) ?>"></script>
