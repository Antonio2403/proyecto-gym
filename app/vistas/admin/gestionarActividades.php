<div class="content-wrapper">
    <div>
        <a href="/proyecto-gym/admin/actividades/crear" class="btn btn-primary">Agregar Nueva Actividad</a>
        <h2>Gestión de Actividades</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Sala</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($actividades as $actividad): ?>
                    <tr>
                        <td><?= $actividad['nombre'] ?></td>
                        <td><?= $actividad['descripcion'] ?></td>
                        <td><?= $actividad['sala_id'] ?></td>
                        <td>
                            <a href="/proyecto-gym/admin/actividades/editar/<?= $actividad['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                            <a href="/proyecto-gym/admin/actividades/eliminar/<?= $actividad['id'] ?>" class="btn btn-danger btn-sm">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php if (empty($actividades)): ?>
        <tr>
            <td colspan="4">No hay actividades registradas aún.</td>
        </tr>
    <?php endif; ?>
    </div>
    <div>