<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Solicitud</title>
</head>

<body>
    <a href="verMonitorSolicitudes">Volver</a>
    <br>
    <form action="/proyecto-gym/monitor/crearSolicitud" method="POST">

        <label>Tipo de solicitud:</label>
        <input type="text" name="tipo" required>

        <button type="submit">Enviar solicitud</button>

    </form>
</body>

</html>