<div class="content-wrapper">

        <div class="mb-4">
            <a href="../inicioAdmin" class="btn btn-secondary">Volver</a>
            <a href="formSubscripcion" class="btn btn-primary">Crear Subscripción</a>
        </div>

        <?php if (!empty($subscripciones)): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Precio</th>
                            <th>Duración</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($subscripciones as $subscripcion): ?>
                            <tr>
                                <td><?php echo $subscripcion['id']; ?></td>
                                <td><?php echo $subscripcion['nombre']; ?></td>
                                <td><?php echo $subscripcion['precio']. " €"; ?></td>
                                <td><?php echo $subscripcion['duracion']." meses"; ?></td>
                                <td>
                                    <a href="formEditarSubscripcion?id=<?php echo $subscripcion['id']; ?>" class="btn btn-sm btn-warning">Editar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info">No hay subscripciones disponibles</div>
        <?php endif; ?>
    </div>