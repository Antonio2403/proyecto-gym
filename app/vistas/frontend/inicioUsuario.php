<div class="container mt-5">
    <h1 class="mb-4">Esta es la vista usuario</h1>
    <p class="lead">Bienvenido, <?php echo $_SESSION['nombre']; ?>!</p>
    <p class="text-muted">Rol: <?php echo $_SESSION['rol']; ?></p>
    
    <div class="mt-3">
        <a href="pago" class="btn btn-primary">Pago</a>
        <?php if (isset($_SESSION['nombre'])): ?>
            <a href="logout" class="btn btn-danger">Logout</a>
        <?php endif; ?>
    </div>
</div>
