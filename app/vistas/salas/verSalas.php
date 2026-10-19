<div class="content-wrapper">
    <div class="relative w-full px-3 mt-6">
        <header class="gp-page-header">
            <div class="gp-page-header__title">
                <h1 class="text-2xl mb-1">Salas del centro</h1>
                <p class="text-slate-500 text-sm mb-0">Gestiona salas, capacidad y materiales asociados.</p>
            </div>
            <div class="gp-view-toolbar">
                <a href="<?= htmlspecialchars(url('/monitor/salas/crear')) ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1" aria-hidden="true"></i> Nueva sala
                </a>
            </div>
        </header>

        <?php if (!empty($salas)): ?>
            <div class="flex flex-wrap -mx-2 -mt-4">
                <?php foreach ($salas as $sala): ?>
                    <div class="w-full md:w-1/2 xl:w-1/3 max-w-full shrink-0 px-2 mt-4">
                        <article class="card gp-sala-card h-full border-0 shadow-sm">
                            <div class="card-body">
                                <h2 class="text-xl card-title mb-2"><?= htmlspecialchars($sala['nombre']) ?></h2>
                                <ul class="list-none ps-0 text-sm text-slate-500 mb-0">
                                    <li class="mb-1"><strong class="text-slate-800">Capacidad:</strong> <?= htmlspecialchars((string) $sala['capacidad']) ?></li>
                                    <li class="mb-1"><strong class="text-slate-800">Disponibilidad:</strong> <?= htmlspecialchars((string) $sala['disponibilidad']) ?></li>
                                    <li><strong class="text-slate-800">Materiales:</strong> <?= (int) $sala['nmateriales'] ?></li>
                                </ul>
                                <div class="gp-actions-stack">
                                    <a href="<?= htmlspecialchars(url('/monitor/salas/' . $sala['id'] . '/materiales/')) ?>" class="btn btn-sm gp-btn-action gp-btn-action--reply">
                                        <i class="fas fa-boxes-stacked me-1" aria-hidden="true"></i> Materiales
                                    </a>
                                    <a href="<?= htmlspecialchars(url('/monitor/salas/editar/' . $sala['id'])) ?>" class="btn btn-sm gp-btn-action gp-btn-action--edit">
                                        <i class="fas fa-pen me-1" aria-hidden="true"></i> Editar
                                    </a>
                                    <form method="post" action="<?= htmlspecialchars(url('/monitor/salas/eliminar/' . (int) $sala['id'])) ?>"
                                          data-gp-confirm
                                          data-gp-danger="true"
                                          data-gp-confirm-title="Eliminar sala"
                                          data-gp-confirm-body="¿Eliminar esta sala? Esta acción no se puede deshacer desde la web."
                                          data-gp-confirm-ok="Sí, eliminar">
                                        <button type="submit" class="btn btn-sm gp-btn-action gp-btn-action--danger">
                                            <i class="fas fa-trash me-1" aria-hidden="true"></i> Eliminar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info border-0 shadow-sm">No hay salas disponibles.</div>
        <?php endif; ?>
    </div>
</div>
