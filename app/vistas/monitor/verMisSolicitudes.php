
        <a href="verMonitorSolicitudes" class="btn btn-secondary mb-4">Volver</a>
        
        <?php
        if (!empty($data)) {
            foreach ($data as $s):
        ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <p class="mb-2"><strong>Tipo:</strong> <?= $s['tipo'] ?></p>
                                <p class="mb-2"><strong>Fecha:</strong> <?= $s['fecha_creacion'] ?></p>
                                <p class="mb-2"><strong>Estado:</strong> <span class="badge bg-info"><?= $s['estado'] ?></span></p>
                            </div>
                            <div class="col-md-4 text-end">
                                <?php if ($s['estado']=='P') { ?>
                                    <button class="btn btn-danger btn-sm">Borrar</button>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
        <?php 
            endforeach;
        } else {
            echo "<p class='alert alert-info'>No hay solicitudes hechas por ti.</p>";
        } 
        ?>