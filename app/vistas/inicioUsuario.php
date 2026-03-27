<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuario</title>
</head>
<body>
    <h1>Esta es la vista usuario</h1>
    <p>Bienvenido, <?php echo $_SESSION['nombre']; ?>!</p>
    <p>Rol: <?php echo $_SESSION['rol']; ?></p>
    <a href="pago">Pago</a>
    <?php if (isset($_SESSION['nombre'])): ?>
            <a href="logout">Logout</a>
        <?php endif; ?>
</body>
</html>