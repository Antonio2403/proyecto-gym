<div class="content-wrapper">

        <h1 class="mb-4">Salas disponibles</h1>
        <a href="salas/crear" class="btn btn-primary mb-3">Crear nueva sala</a>
        
        <?php if (!empty($salas)): ?>
            <div class="row">
                <?php foreach ($salas as $sala): ?>
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($sala['nombre']) ?></h5>
                                <p class="card-text">
                                    <strong>Capacidad:</strong> <?= htmlspecialchars($sala['capacidad']) ?><br>
                                    <strong>Disponibilidad:</strong> <?= htmlspecialchars($sala['disponibilidad']) ?>
                                </p>
                                <a href="salas/editar/<?= $sala['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                                <a href="/salas/eliminar/<?= $sala['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que deseas eliminar esta sala?')">Eliminar</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">No hay salas disponibles</div>
        <?php endif; ?>
    </div>