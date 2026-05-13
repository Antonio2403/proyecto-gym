<?php
?>

<div class="gp-auth-shell">
    <div class="container gp-auth-page-wrap gp-auth-page-wrap--xl px-3">
        <div class="gp-auth-card gp-auth-card--wide gp-auth-card--landscape">
            <div class="text-center mb-3 mb-lg-4">
                <span class="gp-badge">Acceso miembros</span>
                <h1 class="h3 mt-3 mb-2">Entra a Spartum</h1>
                <p class="text-muted small mb-0">Sesión segura · Registro en un solo lugar</p>
            </div>

            <div class="gp-auth-switch-wrap px-2 px-sm-3 mb-0" id="authTabs" role="tablist">
                <div class="gp-auth-switch" id="authModeSwitch">
                    <span class="gp-auth-switch-thumb" aria-hidden="true"></span>
                    <button class="gp-auth-switch-btn active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login" type="button" role="tab" aria-controls="login" aria-selected="true">
                        <span class="gp-auth-switch-icon" aria-hidden="true"><i class="fas fa-right-to-bracket"></i></span>
                        <span>Iniciar sesión</span>
                    </button>
                    <button class="gp-auth-switch-btn" id="registro-tab" data-bs-toggle="tab" data-bs-target="#registro" type="button" role="tab" aria-controls="registro" aria-selected="false">
                        <span class="gp-auth-switch-icon" aria-hidden="true"><i class="fas fa-user-plus"></i></span>
                        <span>Crear cuenta</span>
                    </button>
                </div>
            </div>

            <div class="card border-0 shadow-sm gp-auth-inner-card mt-3">
                <div class="card-body p-4 p-xl-5">
                    <p class="gp-form-required-legend text-muted text-center mb-3">Campos obligatorios: <span class="text-danger fw-bold" aria-hidden="true">*</span></p>
                    <div class="tab-content" id="authTabsContent">

                        <div class="tab-pane fade show active" id="login" role="tabpanel">
                            <form action="<?= htmlspecialchars(url('/login')) ?>" method="post" class="needs-validation gp-auth-form-narrow mx-auto" novalidate data-gp-validate="login">
                                <div class="mb-3">
                                    <label for="identificador_login" class="form-label gp-label-required">Email o DNI / NIE</label>
                                    <input type="text" class="form-control form-control-lg" id="identificador_login" name="identificador" required autocomplete="username"
                                           placeholder="correo@ejemplo.com o 12345678A">
                                    <p class="form-text small text-muted mb-0">Puedes entrar con el email de la cuenta o con tu documento si no recuerdas el correo.</p>
                                </div>
                                <div class="mb-3">
                                    <label for="clave_login" class="form-label gp-label-required">Contraseña</label>
                                    <div class="input-group input-group-lg">
                                        <input type="password" class="form-control" id="clave_login" name="clave" required autocomplete="current-password">
                                        <button class="btn btn-outline-secondary" type="button" data-gp-pass-reveal="clave_login" title="Mantén pulsado para ver" aria-label="Mostrar contraseña mientras mantienes pulsado">Ver</button>
                                    </div>
                                    <p class="form-text small text-muted mb-0">Mantén pulsado «Ver» para mostrar la contraseña un instante.</p>
                                </div>
                                <button type="submit" class="btn btn-primary btn-lg w-100">Entrar</button>
                                <p class="text-center mt-3 mb-0 small">
                                    <a href="<?= htmlspecialchars(url('/ticket')) ?>">Crear ticket de gestión de cuenta</a>
                                </p>
                            </form>
                        </div>

                        <div class="tab-pane fade" id="registro" role="tabpanel">
                            <form action="<?= htmlspecialchars(url('/usuario/registrar')) ?>" method="POST" class="needs-validation" novalidate data-gp-validate="register">
                                <div class="row g-3 gx-lg-4">
                                    <div class="col-md-6">
                                        <label for="DNI" class="form-label gp-label-required">DNI / NIE</label>
                                        <input type="text" class="form-control form-control-lg font-monospace gp-doc-identidad-input" id="DNI" name="DNI" required maxlength="9" autocomplete="off" inputmode="text" placeholder="12345678A" data-gp-doc-identidad-es>
                                        <p class="form-text small mb-0">8 números y letra (DNI) o NIE (X/Y/Z + 7 números + letra).</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="nombre" class="form-label gp-label-required">Nombre</label>
                                        <input type="text" class="form-control form-control-lg" id="nombre" name="nombre" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="apellido1" class="form-label gp-label-required">Primer apellido</label>
                                        <input type="text" class="form-control form-control-lg" id="apellido1" name="apellido1" required>
                                    </div>
                                    <div class="col-6">
                                        <label for="apellido2" class="form-label">Segundo apellido</label>
                                        <input type="text" class="form-control form-control-lg" id="apellido2" name="apellido2">
                                    </div>
                                    <div class="col-md-12">
                                        <label for="email" class="form-label gp-label-required">Email</label>
                                        <input type="email" class="form-control form-control-lg" id="email" name="email" required autocomplete="email">
                                        <p class="form-text small text-muted mb-0">Correo real: allí recibirás el enlace para activar la cuenta.</p>
                                    </div>
                                    <div class="col-6">
                                        <label for="telefono" class="form-label">Teléfono</label>
                                        <input type="text" class="form-control form-control-lg" id="telefono" name="telefono" inputmode="numeric" autocomplete="tel" placeholder="612 34 56 78" maxlength="12" pattern="[0-9]{3} [0-9]{2} [0-9]{2} [0-9]{2}" data-gp-phone-input>
                                        <p class="form-text small text-muted mb-0">Opcional. 9 dígitos; se formatea como 612 34 56 78.</p>
                                    </div>
                                    <div class="col-12">
                                        <fieldset class="border-0 p-0 m-0">
                                            <legend class="form-label gp-label-required mb-2">Contraseña</legend>
                                            <div class="row g-2 align-items-end gp-reg-pass-row">
                                                <div class="col-lg-5">
                                                    <label class="visually-hidden" for="clave">Contraseña</label>
                                                    <input type="password" class="form-control form-control-lg" id="clave" name="clave" required minlength="16" autocomplete="new-password" placeholder="Nueva contraseña">
                                                </div>
                                                <div class="col-lg-5">
                                                    <label class="visually-hidden" for="clave_confirmar">Confirmar contraseña</label>
                                                    <input type="password" class="form-control form-control-lg" id="clave_confirmar" name="clave_confirmar" required minlength="16" autocomplete="new-password" placeholder="Repite la contraseña">
                                                </div>
                                                <div class="col-lg-2">
                                                    <button class="btn btn-outline-secondary btn-lg w-100 text-nowrap" type="button" data-gp-pass-reveal-group="clave,clave_confirmar" title="Mantén pulsado para ver ambas" aria-label="Mostrar contraseñas mientras mantienes pulsado">Ver</button>
                                                </div>
                                            </div>
                                            <ul class="gp-pass-rules list-unstyled small mt-3 mb-0" data-gp-pass-rules-for="clave" aria-live="polite">
                                                <li class="gp-pass-rule gp-pass-rule--idle mb-1" data-rule="len"><span class="gp-pass-rule-mark" aria-hidden="true"></span> Al menos 16 caracteres</li>
                                                <li class="gp-pass-rule gp-pass-rule--idle mb-1" data-rule="upper"><span class="gp-pass-rule-mark" aria-hidden="true"></span> Una letra mayúscula</li>
                                                <li class="gp-pass-rule gp-pass-rule--idle mb-1" data-rule="lower"><span class="gp-pass-rule-mark" aria-hidden="true"></span> Una letra minúscula</li>
                                                <li class="gp-pass-rule gp-pass-rule--idle mb-1" data-rule="num"><span class="gp-pass-rule-mark" aria-hidden="true"></span> Un número</li>
                                                <li class="gp-pass-rule gp-pass-rule--idle mb-0" data-rule="sym"><span class="gp-pass-rule-mark" aria-hidden="true"></span> Un símbolo (carácter no alfanumérico)</li>
                                            </ul>
                                        </fieldset>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-lg w-100 mt-4">Crear cuenta</button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var modeSwitch = document.getElementById('authModeSwitch');
    var switchBar = document.getElementById('authTabs');
    if (!modeSwitch || !switchBar) return;

    function syncSwitchFromTrigger(trigger) {
        if (!trigger || trigger.getAttribute('role') !== 'tab') return;
        var target = trigger.getAttribute('data-bs-target') || '';
        var isRegistro = target === '#registro';
        modeSwitch.classList.toggle('gp-auth-switch--slid', isRegistro);
        switchBar.querySelectorAll('.gp-auth-switch-btn').forEach(function (btn) {
            var on = btn === trigger;
            btn.classList.toggle('active', on);
            btn.setAttribute('aria-selected', on ? 'true' : 'false');
        });
    }

    switchBar.querySelectorAll('.gp-auth-switch-btn').forEach(function (btn) {
        btn.addEventListener('shown.bs.tab', function (e) {
            syncSwitchFromTrigger(e.target);
        });
    });
});
</script>
