<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitudes Rechazadas</title>
</head>
<body>
    <a href="verSolicitudes">Volver</a> <br>
    <?php
    if (!empty($data)) {
        foreach ($data as $s):
    ?>

            <p>
                Solicitante: <?= $s['nombre'] ?> |
                Tipo: <?= $s['tipo'] ?> |
                Fecha: <?= $s['fecha'] ?>
            </p>

    <?php endforeach;
    } else {
        echo "<p>No hay solicitudes Rechazadas.</p>";
    } ?>
</body>
</html>