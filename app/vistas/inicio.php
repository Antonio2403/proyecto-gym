
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Proyecto Gym</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
        
        header {
            background-color: #333;
            color: white;
            padding: 1rem;
            text-align: center;
        }
        
        nav {
            background-color: #444;
            padding: 0.5rem;
            text-align: center;
        }
        
        nav a {
            color: white;
            text-decoration: none;
            margin: 0 1rem;
            padding: 0.5rem;
        }
        
        nav a:hover {
            background-color: #555;
        }
        
        main {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 1rem;
            margin-top: 2rem;
        }
    </style>
</head>
<body>
    <header>
        <h1>Bienvenido a Proyecto Gym</h1>
    </header>
    
    <nav>
        <a href="inicio">Inicio</a>
        <a href="login">Login</a>
        <a href="clases.php">Clases</a>
        <a href="contacto.php">Contacto</a>
        <a href="pago/crear-sesion">Pagar</a>
        <?php if (isset($_SESSION['usuario'])): ?>
            <a href="logout">Logout</a>
        <?php endif; ?>
    </nav>
    
    <main>
        <h2>Página de Inicio</h2>
        <p>Bienvenido a nuestro gimnasio. Aquí puedes gestionar miembros, clases y más.</p>
    </main>
    
    <footer>
        <p>&copy; 2024 Proyecto Gym. Todos los derechos reservados.</p>
    </footer>
</body>
</html>