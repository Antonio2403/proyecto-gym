<script src="https://js.stripe.com/v3/"></script>

<h2>Elige tu plan</h2>

<div style="display:flex; gap:20px;">

    <?php

    foreach ($subscripciones as $sub) {
        echo '<div style="border:1px solid #ccc; padding:20px;">';
        echo '<h3>' . $sub['nombre'] . '</h3>';
        echo '<p>' . $sub['precio'] . '</p>';
        echo '<button onclick="iniciarPago(' . $sub['id'] . ')">Elegir</button>';
        echo '</div>';
    }
    ?>


</div>

<hr>

<form id="payment-form" style="display:none;">
    <div id="payment-element"></div>
    <button id="submit">Pagar</button>
</form>

<div id="error-message"></div>

<script>
    const stripe = Stripe("<?= $_ENV['STRIPE_PUBLISHABLE_KEY'] ?>");

    let elements;

    function iniciarPago(subscripcion_id) {

        fetch("/proyecto-gym/pago/crear-intento", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    subscripcion_id
                })
            })
            .then(res => res.json())
            .then(data => {

                if (!data.clientSecret) {
                    throw new Error(data.error || "Error al crear el pago");
                }

                const clientSecret = data.clientSecret;

                elements = stripe.elements({
                    clientSecret
                });

                const paymentElement = elements.create("payment");
                paymentElement.mount("#payment-element");

                document.getElementById("payment-form").style.display = "block";

                window.subscripcion_id = subscripcion_id;
            })
            .catch(err => {
                document.getElementById("error-message").textContent = err.message;
            });
    }

    document.getElementById("payment-form").addEventListener("submit", async (e) => {
        e.preventDefault();

        const {
            error
        } = await stripe.confirmPayment({
            elements,
            confirmParams: {
                return_url: "http://localhost/proyecto-gym/pago/exito?subscripcion_id=" + window.subscripcion_id
            },
        });

        if (error) {
            document.getElementById("error-message").textContent = error.message;
        }
    });
</script>