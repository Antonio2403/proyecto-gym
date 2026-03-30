<?php
require_once "core/Controller.php";
require_once "vendor/autoload.php";
require_once "app/modelos/susbscripcion.php";

class PagoControlador extends Controller
{
    public function index()
    {
        $subscripciones = Subscripcion::obtenerTodas();
        $this->view("pago/pagar", ['subscripciones' => $subscripciones]);
    }

    public function crearIntentoPago()
    {
        header('Content-Type: application/json');

        \Stripe\Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        $data = json_decode(file_get_contents("php://input"), true);
        $subscripcion_id = $data['subscripcion_id'] ?? null;

        if (!$subscripcion_id) {
            echo json_encode(['error' => 'Falta subscripcion_id']);
            return;
        }

        try {

            $conexion = BasedeDatos::Conectar();

            $stmt = $conexion->prepare("SELECT * FROM subscripciones WHERE id = ?");
            $stmt->execute([$subscripcion_id]);

            $subscripcion = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$subscripcion) {
                echo json_encode(['error' => 'Suscripción no válida']);
                return;
            }

            $precio = $subscripcion['precio'] * 100;

            $intent = \Stripe\PaymentIntent::create([
                'amount' => $precio,
                'currency' => 'eur',
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
            ]);

            echo json_encode([
                'clientSecret' => $intent->client_secret
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'error' => $e->getMessage()
            ]);
        }
    }

    public function exito()
    {
        \Stripe\Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        $payment_intent_id = $_GET['payment_intent'] ?? null;
        $subscripcion_id = $_GET['subscripcion_id'] ?? null;

        if (!$payment_intent_id || !$subscripcion_id) {
            echo "Error: datos incompletos";
            return;
        }

        try {

            $intent = \Stripe\PaymentIntent::retrieve($payment_intent_id);

            if ($intent->status === 'succeeded') {

                $conexion = BasedeDatos::Conectar();

                $stmt = $conexion->prepare("
                    INSERT INTO cliente_subscripcion (cliente_id, subscripcion_id, fecha_inicio)
                    VALUES (?, ?, NOW())
                ");

                $cliente_id = $_SESSION['usuario_id'] ?? null;

                if (!$cliente_id) {
                    throw new Exception("Usuario no logueado");
                }

                $stmt->execute([$cliente_id, $subscripcion_id]);
            }

        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }

        $this->view("pago/exito");
    }

    public function cancelado()
    {
        $this->view("pago/cancelado");
    }
}