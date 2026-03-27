<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago</title>
</head>

<body>
    <h2>Suscripción mensual</h2>

    <form action="/proyecto-gym/pago/crear-sesion" method="POST">
        <input type="hidden" name="precio" value="20">
        <input type="hidden" name="nombre" value="Suscripción mensual">

        <button type="submit">Pagar 20€</button>
    </form>
</body>

</html>