<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario Susbcripción</title>
</head>
<body>
    <a href="gestionSubscripciones">Volver</a>
    <h2>Crear Nueva Subscripción</h2>
    <form action="crearSubscripcion" method="POST">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" required><br><br>

        <label for="precio">Precio:</label>
        <input type="number" id="precio" name="precio" step="0.01" required><br><br>

        <label for="duracion">Duración (en meses):</label>
        <input type="number" id="duracion" name="duracion" required><br><br>

        <input type="submit" value="Crear Subscripción">
    </form>
</body>
</html>