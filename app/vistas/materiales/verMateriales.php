<div class="content-wrapper">
<a href="crear" class="btn btn-primary mb-3">Registrar Nuevo Material</a>
<a href="/proyecto-gym/monitor/verSalas" class="btn btn-secondary mb-3">Volver a Salas</a>
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
                        <a href="/proyecto-gym/monitor/salas/<?php echo $material['sala_id']; ?>/materiales/editar/<?php echo $material['id']; ?>" class="btn btn-sm btn-primary">Editar</a>
                        <a href="/proyecto-gym/monitor/salas/<?php echo $material['sala_id']; ?>/materiales/eliminar/<?php echo $material['id']; ?>" class="btn btn-sm btn-danger">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

</div>