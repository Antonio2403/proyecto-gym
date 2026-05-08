<div class="content-wrapper">

        <h1 class="mb-4">Salas disponibles</h1>
        <a href="<?= htmlspecialchars(url('/monitor/salas/crear')) ?>" class="btn btn-primary mb-3">Crear nueva sala</a>

        <?php if (!empty($salas)): ?>
            <div class="row">
                <?php foreach ($salas as $sala): ?>
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($sala['nombre']) ?></h5>
                                <p class="card-text">
                                    <strong>Capacidad:</strong> <?= htmlspecialchars($sala['capacidad']) ?><br>
                                    <strong>Disponibilidad:</strong> <?= htmlspecialchars($sala['disponibilidad']) ?><br>
                                    <strong>Materiales:</strong> <?= $sala['nmateriales'] ?>
                                </p>
                                <a href="<?= htmlspecialchars(url('/monitor/salas/' . $sala['id'] . '/materiales/')) ?>" class="btn btn-info btn-sm">Editar materiales</a>
                                <a href="<?= htmlspecialchars(url('/monitor/salas/editar/' . $sala['id'])) ?>" class="btn btn-warning btn-sm">Editar sala</a>
                                <a href="<?= htmlspecialchars(url('/monitor/salas/eliminar/' . $sala['id'])) ?>" class="btn btn-danger btn-sm"
                                   data-gp-confirm
                                   data-gp-danger="true"
                                   data-gp-confirm-title="Eliminar sala"
                                   data-gp-confirm-body="¿Eliminar esta sala? Esta acción no se puede deshacer desde la web."
                                   data-gp-confirm-ok="Sí, eliminar">Eliminar</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">No hay salas disponibles</div>
        <?php endif; ?>
    </div>
