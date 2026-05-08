<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <header class="gp-page-header">
                <span class="gp-badge mb-2">Cuenta</span>
                <h1 class="h2 mb-2">Darse de baja</h1>
                <p class="text-muted mb-0">Si quieres cancelar tu membresía o borrar tus datos, contacta con recepción o escríbenos.</p>
            </header>
            <div class="card border-0">
                <div class="card-body p-4">
                    <p class="text-muted mb-4">
                        Podemos gestionar tu baja de forma segura. Indica en el mensaje tu email de registro y lo que necesitas (pausa, baja definitiva, etc.).
                    </p>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="<?= htmlspecialchars(url('/contacto')) ?>"
                           class="btn btn-primary"
                           data-gp-confirm
                           data-gp-confirm-title="Solicitud de baja"
                           data-gp-confirm-body="¿Ir al formulario de contacto para comunicar tu baja o consulta sobre la membresía? Indica tu email registrado."
                           data-gp-confirm-ok="Ir a contacto">Ir a contacto</a>
                        <a href="<?= htmlspecialchars(url('/inicioUsuario')) ?>" class="btn btn-outline-light">Volver a mi área</a>
                        <a href="<?= htmlspecialchars(url('/logout')) ?>"
                           class="btn btn-outline-danger"
                           data-gp-danger="true"
                           data-gp-confirm
                           data-gp-confirm-title="Cerrar sesión"
                           data-gp-confirm-body="Tu sesión se cerrará antes de poder seguir con otros trámites. ¿Salir igualmente?"
                           data-gp-confirm-ok="Sí, salir">Cerrar sesión</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
