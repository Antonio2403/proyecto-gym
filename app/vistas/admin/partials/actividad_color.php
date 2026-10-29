<?php
$gpActividadColor = Actividad::normalizarColor((string) ($gpActividadColor ?? '')) ?? '#C45C26';
$gpActividadPaleta = Actividad::paletaColores();
?>
<div class="mb-4">
    <label class="form-label gp-label-required" for="actividad_color">Color de la clase</label>
    <div class="gp-color-field">
        <input type="color" class="gp-color-field__picker" id="actividad_color" name="color" value="<?= htmlspecialchars($gpActividadColor) ?>" required>
        <span class="gp-color-field__hex" data-gp-color-hex><?= htmlspecialchars($gpActividadColor) ?></span>
    </div>
    <div class="gp-color-presets" role="group" aria-label="Colores sugeridos">
        <?php foreach ($gpActividadPaleta as $hex): ?>
            <button type="button" class="gp-color-presets__btn<?= $hex === $gpActividadColor ? ' is-selected' : '' ?>"
                    data-gp-color-preset="<?= htmlspecialchars($hex) ?>"
                    style="background: <?= htmlspecialchars($hex) ?>"
                    title="<?= htmlspecialchars($hex) ?>"
                    aria-label="Usar el color <?= htmlspecialchars($hex) ?>"></button>
        <?php endforeach; ?>
    </div>
    <p class="form-text text-sm text-slate-500 mb-0">Este color identifica la clase en el horario.</p>
</div>
