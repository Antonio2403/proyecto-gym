<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver salas</title>
</head>
<body>
    <h1>Salas disponibles</h1>
    <a href="salas/crear">Crear nueva sala</a>
    <ul>
        <?php if (!empty($salas)): ?>
            <?php foreach ($salas as $sala): ?>
                <li>
                    <?= htmlspecialchars($sala['nombre']) ?> - Capacidad: <?= htmlspecialchars($sala['capacidad']) ?> - Disponibilidad: <?= htmlspecialchars($sala['disponibilidad']) ?>
                    <a href="/salas/editar/<?= $sala['id'] ?>">Editar</a>
                    <a href="/salas/eliminar/<?= $sala['id'] ?>" onclick="return confirm('¿Estás seguro de que deseas eliminar esta sala?')">Eliminar</a>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <li>No hay salas disponibles</li>
        <?php endif; ?>
    </ul>
</body>
</html>