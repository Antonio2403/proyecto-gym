<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis solicitudes</title>
</head>

<body>
    <a href="verMonitorSolicitudes">Volver</a>
    <?php
    if (!empty($data)) {
        foreach ($data as $s):
    ?>

            <p>
                Tipo: <?= $s['tipo'] ?> |
                Fecha: <?= $s['fecha_creacion'] ?>
                Estado: <?= $s['estado'] ?> |
                <?php if ($s['estado']=='P') {
                    echo "Boton borrar";
                }?>
            </p>


    <?php endforeach;
    } else {
        echo "<p>No hay solicitudes hechas por ti.</p>";
    } ?>
</body>

</html>