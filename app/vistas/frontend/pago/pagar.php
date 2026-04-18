<script src="https://js.stripe.com/v3/"></script>

<h2 class="text-center my-4">Elige tu plan</h2>

<style>
    .card-flip {
        perspective: 1000px;
    }

    .card-inner {
        position: relative;
        width: 100%;
        height: 250px;
        transform-style: preserve-3d;
        transition: transform 0.6s;
    }

    .card-flip:hover .card-inner {
        transform: rotateY(180deg);
    }

    .card-front,
    .card-back {
        position: absolute;
        width: 100%;
        height: 100%;
        backface-visibility: hidden;
        border-radius: 10px;
    }

    .card-front {
        background: #111;
        color: white;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    }

    .card-back {
        background: #0d6efd;
        color: white;
        transform: rotateY(180deg);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }
</style>

<div class="container">
    <div class="row">

        <?php foreach ($subscripciones as $sub): ?>
            <div class="col-md-4 mb-4">

                <div class="card-flip">
                    <div class="card-inner">

                        <!-- FRONT -->
                        <div class="card-front p-3">
                            <h3><?= $sub['nombre'] ?></h3>
                            <h4><?= $sub['precio'] ?> €</h4>
                        </div>

                        <!-- BACK -->
                        <div class="card-back p-3 text-center">
                            <p>
                                <?= $sub['duracion'] ?> mes(es) <br>
                                <?= $sub['numero_clases'] ?> clase(s) a la semana <br>
                                <?= $sub['fisio'] == 'S' ? "Incluye fisioterapia" : "Sin fisioterapia" ?>
                            </p>
                            <button class="btn btn-light mt-2"
                                onclick="iniciarPago(<?= $sub['id'] ?>)">
                                Elegir
                            </button>
                        </div>

                    </div>
                </div>

            </div>
        <?php endforeach; ?>

    </div>
</div>

<hr>

<form id="payment-form" class="container" style="display:none; max-width:500px;">
    <div id="payment-element"></div>
    <button id="submit" class="btn btn-success mt-3 w-100">Pagar</button>
</form>

<div id="error-message" class="text-danger text-center mt-3"></div>
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

                elements = stripe.elements({
                    clientSecret: data.clientSecret
                });
                document.getElementById("payment-element").innerHTML = "";
                const paymentElement = elements.create("payment");
                paymentElement.mount("#payment-element");

                document.getElementById("payment-form").style.display = "block";

                document.getElementById("payment-form").scrollIntoView({
                    behavior: "smooth"
                });

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
            }
        });

        if (error) {
            document.getElementById("error-message").textContent = error.message;
        }
    });
</script>