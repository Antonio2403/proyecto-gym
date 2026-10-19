<div class="content-wrapper">
    <div class="mx-auto w-full max-w-[1320px] lg:max-w-[1024px] xl:max-w-7xl px-3 mt-6" style="max-width: 720px;">
        <div class="gp-admin-card-panel gp-admin-security-card border-0 shadow-sm">
            <div class="flex items-start gap-4 mb-4 pb-4 border-b border-slate-500/10">
                <div class="rounded-lg flex items-center justify-center shrink-0 gp-admin-security-icon-wrap">
                    <i class="fas fa-shield-halved text-white" aria-hidden="true"></i>
                </div>
                <div>
                    <h1 class="text-2xl text-slate-800 mb-1">Seguridad y contraseñas</h1>
                    <p class="text-slate-500 text-sm mb-0">
                        Políticas de acceso alineadas con el resto de Spartum.
                    </p>
                </div>
            </div>
            <div class="card-body px-0 pt-0">
                <p class="text-slate-500 text-sm">
                    Los usuarios deben usar contraseñas de al menos 16 caracteres con mayúsculas, minúsculas, números y símbolos.
                    Se les pedirá renovar la contraseña tras el número de días indicado (0 = sin caducidad por antigüedad).
                    El tiempo de <strong>inactividad</strong> cierra la sesión en el navegador y muestra un aviso para volver a entrar.
                    La variable de entorno <code>SESSION_IDLE_TIMEOUT_SECONDS</code>, si existe, tiene prioridad sobre el valor del panel.
                </p>

                <form method="post" action="<?= htmlspecialchars(url('/admin/config-seguridad')) ?>" class="gp-admin-security-form" novalidate>
                    <div class="mb-4">
                        <label class="form-label font-semibold" for="password_rotation_days">Días hasta renovar contraseña</label>
                        <input type="text" class="form-control font-mono" id="password_rotation_days" name="password_rotation_days"
                               inputmode="numeric" pattern="[0-9]+" maxlength="4" autocomplete="off"
                               value="<?= (int) ($dias_rotacion ?? 90) ?>" required
                               title="Solo dígitos, sin decimales">
                        <p class="form-text text-sm mb-0">Ejemplo: 90. Usa 0 para no exigir renovación por tiempo.</p>
                    </div>
                    <div class="mb-4">
                        <label class="form-label font-semibold" for="session_idle_minutos">Cerrar sesión por inactividad (minutos)</label>
                        <input type="text" class="form-control font-mono" id="session_idle_minutos" name="session_idle_minutos"
                               inputmode="numeric" pattern="[0-9]+" maxlength="5" autocomplete="off"
                               value="<?= (int) ($idle_minutos ?? 45) ?>" required
                               title="Solo dígitos enteros">
                        <p class="form-text text-sm mb-0">Entre 1 min y 7 días (10080). Por defecto 45 min.</p>
                    </div>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <a href="<?= htmlspecialchars(url('/admin')) ?>" class="btn btn-outline-secondary ms-2">Volver</a>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
(function () {
    var form = document.querySelector('.gp-admin-security-form');
    if (!form) return;
    form.addEventListener('submit', function (e) {
        var d = document.getElementById('password_rotation_days');
        var m = document.getElementById('session_idle_minutos');
        if (!d || !m) return;
        var rd = String(d.value || '').trim();
        var rm = String(m.value || '').trim();
        if (!/^\d+$/.test(rd) || !/^\d+$/.test(rm)) {
            e.preventDefault();
            alert('Introduce solo números enteros (sin decimales ni letras).');
        }
    });
})();
</script>
