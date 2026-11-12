<?php
$gpPeriodoPrefix = $gpPeriodoPrefix ?? 'gpReporte';
$gpAnioActual = (int) date('Y');
$gpAnios = Solicitud::aniosDisponibles();
$gpMeses = [
    1 => 'Enero',
    2 => 'Febrero',
    3 => 'Marzo',
    4 => 'Abril',
    5 => 'Mayo',
    6 => 'Junio',
    7 => 'Julio',
    8 => 'Agosto',
    9 => 'Septiembre',
    10 => 'Octubre',
    11 => 'Noviembre',
    12 => 'Diciembre',
];
?>
<div class="w-full md:w-1/4 lg:w-1/6 max-w-full shrink-0 px-1 mt-2">
    <label class="form-label text-sm text-slate-500 mb-0" for="<?= htmlspecialchars($gpPeriodoPrefix) ?>Anio">Año</label>
    <select class="form-select form-select-sm" id="<?= htmlspecialchars($gpPeriodoPrefix) ?>Anio" name="anio">
        <?php foreach ($gpAnios as $gpAnio): ?>
            <option value="<?= (int) $gpAnio ?>"<?= (int) $gpAnio === $gpAnioActual ? ' selected' : '' ?>><?= (int) $gpAnio ?></option>
        <?php endforeach; ?>
    </select>
</div>
<div class="w-full md:w-1/3 lg:w-1/4 max-w-full shrink-0 px-1 mt-2">
    <label class="form-label text-sm text-slate-500 mb-0" for="<?= htmlspecialchars($gpPeriodoPrefix) ?>Mes">Mes</label>
    <select class="form-select form-select-sm" id="<?= htmlspecialchars($gpPeriodoPrefix) ?>Mes" name="mes">
        <option value="">Todo el año</option>
        <?php foreach ($gpMeses as $gpMesNum => $gpMesNombre): ?>
            <option value="<?= (int) $gpMesNum ?>"><?= htmlspecialchars($gpMesNombre) ?></option>
        <?php endforeach; ?>
    </select>
</div>
