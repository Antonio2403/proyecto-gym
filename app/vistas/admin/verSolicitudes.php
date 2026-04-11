<div class="content-wrapper">

        <div class="mb-4">
            <a href="verSolicitudesAprobadas" class="btn btn-success">Ver Solicitudes Aprobadas</a>
            <a href="verSolicitudesRechazadas" class="btn btn-danger">Ver Solicitudes Rechazadas</a>
            <a href="/proyecto-gym/inicioAdmin" class="btn btn-secondary">Volver</a>
        </div>

        <?php
        if (!empty($solicitudes)) {
            foreach ($solicitudes as $s):
        ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <p class="card-text">
                            <strong>Solicitante:</strong> <?= $s['nombre'] ?> <br>
                            <strong>Tipo:</strong> <?= $s['tipo'] ?> <br>
                            <strong>Fecha:</strong> <?= $s['fecha_creacion'] ?>
                        </p>
                        <form method="POST" action="/proyecto-gym/admin/aprobar">
                            <input type="hidden" name="id" value="<?= $s['id'] ?>">
                            <button type="submit" name="estado" value="A" class="btn btn-sm btn-success">Aprobar</button>
                            <button type="submit" name="estado" value="R" class="btn btn-sm btn-danger">Rechazar</button>
                        </form>
                    </div>
                </div>
        <?php endforeach;
        } else {
            echo "<p class='alert alert-info'>No hay solicitudes pendientes.</p>";
        } ?>
    </div>