<?php
require_once "core/Controller.php";
require_once "vendor/autoload.php";

class PagoControlador extends Controller
{
    public function index()
    {
        $this->view("pago/pagar");
    }

    public function crearSesion()
    {

        \Stripe\Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        $precio = $_POST['precio'];
        $nombre = $_POST['nombre'];

        try {

            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'mode' => 'payment',

                'line_items' => [[
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => $nombre,
                        ],
                        'unit_amount' => $precio * 100,
                    ],
                    'quantity' => 1,
                ]],

                'success_url' => 'http://localhost/proyecto-gym/pago/exito?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => 'http://localhost/proyecto-gym/pago/cancelado',
            ]);

            header("Location: " . $session->url);
            exit;

        } catch (Exception $e) {
            echo "Error Stripe: " . $e->getMessage();
        }
    }

    public function exito()
    {
        \Stripe\Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        $session_id = $_GET['session_id'];

        try {

            $session = \Stripe\Checkout\Session::retrieve($session_id);

            if ($session->payment_status == 'paid') {

                $conexion = BasedeDatos::Conectar();

                $stmt = $conexion->prepare("
                    INSERT INTO cliente_suscripcion (cliente_id, suscripcion_id, fecha_inicio)
                    VALUES (?, ?, NOW())
                ");

                $cliente_id = $_SESSION['usuario_id'];
                $suscripcion_id = 1;
                $stmt->execute([$cliente_id, $suscripcion_id]);
            }

        } catch (Exception $e) {
            echo $e->getMessage();
        }

        $this->view("pago/exito");
    }

    public function cancelado()
    {
        $this->view("pago/cancelado");
    }
}