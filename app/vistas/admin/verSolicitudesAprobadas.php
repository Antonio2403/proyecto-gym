<div class="content-wrapper">

        <a href="verSolicitudes" class="btn btn-secondary mb-3">Volver</a>
        
        <?php
        if (!empty($solicitudes)) {
        ?>
            <div class="row">
                <?php foreach ($solicitudes as $s): ?>
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <p class="card-text">
                                    <strong>Solicitante:</strong> <?= htmlspecialchars($s['nombre']) ?> <br>
                                    <strong>Tipo:</strong> <?= htmlspecialchars($s['tipo']) ?> <br>
                                    <strong>Fecha:</strong> <?= htmlspecialchars($s['fecha_creacion']) ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php
        } else {
            echo "<div class='alert alert-info'>No hay solicitudes aprobadas.</div>";
        }
        ?>
    </div>

