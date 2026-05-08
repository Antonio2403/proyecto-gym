<div class="content-wrapper">
<a href="crear" class="btn btn-primary mb-3">Registrar Nuevo Material</a>
<a href="<?= htmlspecialchars(url('/monitor/verSalas')) ?>" class="btn btn-secondary mb-3">Volver a Salas</a>
<?php
if (empty($materiales)): ?>
    <div class="alert alert-info">
        <p>Aún no hay ningún material en esta sala.</p>
    </div>
<?php else: ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($materiales as $material): ?>
                <tr>
                    <td><?php echo $material['id']; ?></td>
                    <td><?php echo $material['nombre']; ?></td>
                    <td><?php echo $material['estado']; ?></td>
                    <td>
                        <a href="<?= htmlspecialchars(url('/monitor/salas/' . $material['sala_id'] . '/materiales/editar/' . $material['id'])) ?>" class="btn btn-sm btn-primary">Editar</a>
                        <a href="<?= htmlspecialchars(url('/monitor/salas/' . $material['sala_id'] . '/materiales/eliminar/' . $material['id'])) ?>" class="btn btn-sm btn-danger"
                           data-gp-confirm
                           data-gp-danger="true"
                           data-gp-confirm-title="Eliminar material"
                           data-gp-confirm-body="¿Eliminar este material de la sala?"
                           data-gp-confirm-ok="Sí, eliminar">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

</div>