<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Monitor</title>
    <style>
        form {
            width: 400px;
            margin: auto;
            font-family: Arial;
        }

        input {
            width: 100%;
            padding: 8px;
            margin: 5px 0;
        }

        button {
            width: 100%;
            padding: 10px;
            background: black;
            color: white;
        }
    </style>
</head>

<body>
    <a href="/proyecto-gym/inicioAdmin">Volver</a><br>
    <form action="/proyecto-gym/admin/crearMonitor" method="POST">

        <h2>Crear Monitor</h2>

        <label>DNI:</label>
        <input type="text" name="DNI" required>

        <label>Nombre:</label>
        <input type="text" name="nombre" required>

        <label>Apellido 1:</label>
        <input type="text" name="apellido1" required>

        <label>Apellido 2:</label>
        <input type="text" name="apellido2">

        <label>Email:</label>
        <input type="email" name="email" required>

        <label>Contraseña:</label>
        <input type="password" name="clave" required>

        <label>Teléfono:</label>
        <input type="text" name="telefono" required>

        <hr>

        <label>Especialidad:</label>
        <input type="text" name="especialidad" required>

        <label>Disponibilidad:</label>
        <input type="text" name="disponibilidad" placeholder="Ej: mañanas, tardes..." required>

        <br><br>

        <button type="submit">Crear Monitor</button>

    </form>
</body>

</html>