<div class="content-wrapper">
    <div class="container-fluid mt-4">
        <header class="gp-page-header">
            <div class="gp-page-header__title">
                <h1 class="h4 mb-1">Salas del centro</h1>
                <p class="text-muted small mb-0">Gestiona salas, capacidad y materiales asociados.</p>
            </div>
            <div class="gp-view-toolbar">
                <a href="<?= htmlspecialchars(url('/monitor/salas/crear')) ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1" aria-hidden="true"></i> Nueva sala
                </a>
            </div>
        </header>

        <?php if (!empty($salas)): ?>
            <div class="row g-3">
                <?php foreach ($salas as $sala): ?>
                    <div class="col-md-6 col-xl-4">
                        <article class="card gp-sala-card h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <h2 class="h5 card-title mb-2"><?= htmlspecialchars($sala['nombre']) ?></h2>
                                <ul class="list-unstyled small text-muted mb-0">
                                    <li class="mb-1"><strong class="text-dark">Capacidad:</strong> <?= htmlspecialchars((string) $sala['capacidad']) ?></li>
                                    <li class="mb-1"><strong class="text-dark">Disponibilidad:</strong> <?= htmlspecialchars((string) $sala['disponibilidad']) ?></li>
                                    <li><strong class="text-dark">Materiales:</strong> <?= (int) $sala['nmateriales'] ?></li>
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
