<div class="content-wrapper">
    <div class="container-fluid mt-4">
        <header class="gp-page-header">
            <div class="gp-page-header__title">
                <h1 class="h4 mb-1">Materiales de la sala</h1>
                <p class="text-muted small mb-0">Inventario y estado del material en esta sala.</p>
            </div>
            <div class="gp-view-toolbar">
                <a href="<?= htmlspecialchars(url('/monitor/salas/' . (int) $sala_id . '/materiales/crear')) ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1" aria-hidden="true"></i> Nuevo material
                </a>
                <a href="<?= htmlspecialchars(url('/monitor/verSalas')) ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1" aria-hidden="true"></i> Volver a salas
                </a>
            </div>
        </header>

        <?php if (empty($materiales)): ?>
            <div class="alert alert-info border-0 shadow-sm">Aún no hay ningún material en esta sala.</div>
        <?php else: ?>
            <div class="gp-admin-card-panel p-0 overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0 gp-data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Estado</th>
                                <th class="gp-actions-col">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($materiales as $material): ?>
                                <tr>
                                    <td><?= (int) $material['id'] ?></td>
                                    <td><?= htmlspecialchars((string) $material['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars((string) $material['estado'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td class="gp-actions-cell">
                                        <div class="gp-actions-stack">
                                            <a href="<?= htmlspecialchars(url('/monitor/salas/' . $material['sala_id'] . '/materiales/editar/' . $material['id'])) ?>" class="btn btn-sm gp-btn-action gp-btn-action--edit">
                                                <i class="fas fa-pen me-1" aria-hidden="true"></i> Editar
                                            </a>
                                            <form method="post" action="<?= htmlspecialchars(url('/monitor/salas/' . (int) $material['sala_id'] . '/materiales/eliminar/' . (int) $material['id'])) ?>"
                                                  data-gp-confirm
                                                  data-gp-danger="true"
                                                  data-gp-confirm-title="Eliminar material"
                                                  data-gp-confirm-body="¿Eliminar este material de la sala?"
                                                  data-gp-confirm-ok="Sí, eliminar">
                                                <button type="submit" class="btn btn-sm gp-btn-action gp-btn-action--danger">
                                                    <i class="fas fa-trash me-1" aria-hidden="true"></i> Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
