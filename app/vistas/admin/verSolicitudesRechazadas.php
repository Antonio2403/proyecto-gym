
        <a href="verSolicitudes" class="btn btn-secondary mb-3">Volver</a>
        
        <?php
        if (!empty($data)) {
        ?>
            <div class="list-group">
                <?php foreach ($data as $s): ?>
                    <div class="list-group-item">
                        <p class="mb-0">
                            <strong>Solicitante:</strong> <?= $s['nombre'] ?> | 
                            <strong>Tipo:</strong> <?= $s['tipo'] ?> | 
                            <strong>Fecha:</strong> <?= $s['fecha_creacion'] ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php
        } else {
            echo "<div class='alert alert-info'>No hay solicitudes Rechazadas.</div>";
        }
        ?>
