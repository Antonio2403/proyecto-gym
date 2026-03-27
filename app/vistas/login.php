<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Proyecto Gym</title>
    <style>
        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .tab-btn {
            padding: 10px 20px;
            cursor: pointer;
            background: #ddd;
            border: none;
        }

        .tab-btn.active {
            background: #007bff;
            color: white;
        }

        .form-container {
            display: none;
        }

        .form-container.active {
            display: block;
        }
    </style>
</head>

<body>
    <a href="inicio">Inicio</a>

    <div class="tabs">
        <button class="tab-btn active" onclick="mostrarTab('login')">Login</button>
        <button class="tab-btn" onclick="mostrarTab('registro')">Registrarse</button>
    </div>

    <!-- Formulario Login -->
    <div id="login" class="form-container active">
        <form action="/proyecto-gym/login" method="post">
            <label for="email_login">Email:</label>
            <input type="email" id="email_login" name="email" required><br>

            <label for="clave_login">Contraseña:</label>
            <input type="password" id="clave_login" name="clave" required><br>

            <button type="submit">Iniciar Sesión</button>
        </form>
    </div>

    <!-- Formulario Registro -->
    <div id="registro" class="form-container">
        <form action="/proyecto-gym/usuario/registrar" method="POST">
            <label for="DNI">DNI:</label>
            <input type="text" id="DNI" name="DNI" required><br>

            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required><br>

            <label for="apellido1">Primer Apellido:</label>
            <input type="text" id="apellido1" name="apellido1" required><br>

            <label for="apellido2">Segundo Apellido:</label>
            <input type="text" id="apellido2" name="apellido2"><br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required><br>

            <label for="clave">Contraseña:</label>
            <input type="password" id="clave" name="clave" required><br>

            <label for="telefono">Teléfono:</label>
            <input type="text" id="telefono" name="telefono"><br>

            <button type="submit">Registrarse</button>
        </form>
    </div>

    <script>
        function mostrarTab(tab) {
            document.querySelectorAll('.form-container').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
            document.getElementById(tab).classList.add('active');
            event.target.classList.add('active');
        }
    </script>
</body>

</html>