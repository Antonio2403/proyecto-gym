<?php
$fisio = $fisio ?? null;
$editando = is_array($fisio);
?>
<div class="content-wrapper">
    <div class="row mb-3">
        <div class="col-12">
            <a href="<?= htmlspecialchars(url('/admin/fisioterapeutas')) ?>" class="btn btn-secondary btn-sm fw-semibold">Volver al listado</a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
            <div class="gp-admin-card-panel">
                <h2 class="mb-1"><?= $editando ? 'Editar fisioterapeuta' : 'Nuevo fisioterapeuta' ?></h2>
                <p class="gp-form-required-legend text-muted mb-4">Los campos con <span class="text-danger fw-bold" aria-hidden="true">*</span> son obligatorios. Máximo 100 caracteres por campo.</p>

                <?php if (!empty($_GET['error'])): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars((string) $_GET['error']) ?></div>
                <?php endif; ?>

                <form
                    <?php if ($editando): ?>
                        action="<?= htmlspecialchars(url('/admin/fisioterapeutas/editar')) ?>"
                        method="post"
                    <?php else: ?>
                        action="<?= htmlspecialchars(url('/admin/fisioterapeutas/nuevo')) ?>"
                        method="post"
                    <?php endif; ?>
                    class="needs-validation" novalidate data-gp-validate="adminFisio"
                    data-gp-confirm
                    data-gp-confirm-title="<?= htmlspecialchars($editando ? 'Guardar fisioterapeuta' : 'Crear fisioterapeuta') ?>"
                    data-gp-confirm-body="<?= htmlspecialchars($editando ? '¿Guardar los cambios?' : '¿Crear este fisioterapeuta?') ?>"
                    data-gp-confirm-ok="<?= htmlspecialchars($editando ? 'Guardar' : 'Crear') ?>">

                    <?php if ($editando): ?>
                        <input type="hidden" name="id" value="<?= (int) ($fisio['id'] ?? 0) ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="nombre" class="form-label gp-label-required">Nombre</label>
                        <input id="nombre" type="text" name="nombre" class="form-control" maxlength="100" required
                               value="<?= $editando ? htmlspecialchars((string) ($fisio['nombre'] ?? '')) : '' ?>">
                        <div class="invalid-feedback">Indica un nombre (máx. 100 caracteres).</div>
                    </div>

                    <div class="mb-4">
                        <label for="especialidad" class="form-label">Especialidad</label>
                        <input id="especialidad" type="text" name="especialidad" class="form-control" maxlength="100"
                               value="<?= $editando ? htmlspecialchars((string) ($fisio['especialidad'] ?? '')) : '' ?>">
                        <div class="form-text">Ej. rehabilitación deportiva, fisiología, etc.</div>
                    </div>

                    <div class="mb-4">
                        <label for="usuario_email" class="form-label">Email de acceso al panel (opcional)</label>
                        <input id="usuario_email" type="email" name="usuario_email" class="form-control"
                               maxlength="255" autocomplete="off"
                               placeholder="ej. fisio@gym.com"
                               value="<?= htmlspecialchars((string) ($usuario_email ?? '')) ?>">
                        <div class="form-text">
                            Debe ser un usuario ya registrado (misma tabla de usuarios que el login principal), no socio, monitor ni admin.
                            Vacío = sin acceso al panel de fisioterapeuta o desvincular al editar.
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        <button type="submit" class="btn btn-primary fw-semibold px-4">
                            <?= $editando ? 'Guardar cambios' : 'Crear fisioterapeuta' ?>
                        </button>
                        <a href="<?= htmlspecialchars(url('/admin/fisioterapeutas')) ?>" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
