<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión Subscripciones</title>
</head>
<body>
    <a href="../inicioAdmin">Volver</a>
    <a href="formSubscripcion">Crear Subscripción</a>
    <?php if (!empty($data)): ?>
        <table border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Duración</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $subscripcion): ?>
                    <tr>
                        <td><?php echo $subscripcion['id']; ?></td>
                        <td><?php echo $subscripcion['nombre']; ?></td>
                        <td><?php echo $subscripcion['precio']. " €"; ?></td>
                        <td><?php echo $subscripcion['duracion']." meses"; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay subscripciones disponibles</p>
    <?php endif; ?>
</body>
</html>