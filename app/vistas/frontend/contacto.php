<div class="mx-auto w-full max-w-[1320px] lg:max-w-[1024px] xl:max-w-7xl px-3 py-12">
    <div class="justify-center flex flex-wrap -mx-3">
        <div class="w-full lg:w-7/12 max-w-full shrink-0 px-3">
            <header class="gp-page-header text-center lg:text-left">
                <span class="gp-badge">Escríbenos</span>
                <h1 class="text-[2rem] mt-4 mb-2">Contacto</h1>
                <p class="text-slate-500 mb-0">Dudas, sugerencias o empresas. Respondemos lo antes posible.</p>
            </header>

            <?php if (!empty($_GET['success'])): ?>
                <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
            <?php endif; ?>
            <?php if (!empty($_GET['error'])): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
            <?php endif; ?>

            <div class="card border-0">
                <div class="card-body p-6 lg:p-12">
                    <form method="post" action="<?= htmlspecialchars(url('/contacto/enviar')) ?>" class="needs-validation" novalidate data-gp-validate="contact"
                          data-gp-confirm data-gp-confirm-title="Enviar mensaje" data-gp-confirm-body="¿Enviar este mensaje al equipo de Spartum?" data-gp-confirm-ok="Enviar">
                        <p class="gp-form-required-legend mb-4">Campos marcados con <span class="text-red-600 font-bold" aria-hidden="true">*</span> son obligatorios.</p>
                        <div class="mb-4">
                            <label class="form-label gp-label-required" for="nombre">Nombre</label>
                            <input class="form-control form-control-lg" type="text" id="nombre" name="nombre" required maxlength="150">
                        </div>
                        <div class="mb-4">
                            <label class="form-label gp-label-required" for="email">Email</label>
                            <input class="form-control form-control-lg" type="email" id="email" name="email" required maxlength="255">
                        </div>
                        <div class="mb-4">
                            <label class="form-label gp-label-required" for="asunto">Asunto</label>
                            <input class="form-control form-control-lg" type="text" id="asunto" name="asunto" required maxlength="255">
                        </div>
                        <div class="mb-6">
                            <label class="form-label gp-label-required" for="mensaje">Mensaje</label>
                            <textarea class="form-control" id="mensaje" name="mensaje" rows="6" required maxlength="8000"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-full">Enviar mensaje</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
