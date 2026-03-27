<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitudes</title>
</head>

<body>
    <a href="verSolicitudesAprobadas">Ver Solicitudes Aprobadas</a> <br>
    <a href="verSolicitudesRechazadas">Ver Solicitudes Rechazas</a> <br>
    <a href="/proyecto-gym/inicioAdmin">Volver</a><br>

    <?php
    if (!empty($data)) {
        foreach ($data as $s):
    ?>

            <p>
                Solicitante: <?= $s['nombre'] ?> |
                Tipo: <?= $s['tipo'] ?> |
                Fecha: <?= $s['fecha'] ?>
            </p>

            <form method="POST" action="/proyecto-gym/admin/aprobar">
                <input type="hidden" name="id" value="<?= $s['id'] ?>">
                <button name="estado" value="A">Aprobar</button>
                <button name="estado" value="R">Rechazar</button>
            </form>

    <?php endforeach;
    } else {
        echo "<p>No hay solicitudes pendientes.</p>";
    } ?>
</body>

</html>