<?php



require_once 'core/Controller.php';

require_once 'vendor/autoload.php';

require_once 'app/modelos/susbscripcion.php';

require_once 'app/modelos/cliente_subscripcion.php';



class PagoControlador extends Controller

{

    public function index()

    {

        $this->redirigirFisioFueraPortal();

        $planActivo = null;

        if (isset($_SESSION['usuario_id']) && ($_SESSION['rol'] ?? '') === 'cliente') {

            $planActivo = ClienteSubscripcion::obtenerActivaPorUsuarioId((int) $_SESSION['usuario_id']);

        }



        $subscripciones = Subscripcion::obtenerActivasCatalogo();

        $this->renderFrontend('frontend/pago/pagar', [

            'subscripciones' => $subscripciones,

            'plan_activo' => $planActivo,

        ]);

    }



    public function crearIntentoPago()

    {

        header('Content-Type: application/json');



        \Stripe\Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);



        if (!isset($_SESSION['usuario_id']) || ($_SESSION['rol'] ?? '') !== 'cliente') {

            echo json_encode(['error' => 'Debes iniciar sesión como cliente para contratar un plan']);



            return;

        }



        $data = json_decode(file_get_contents('php://input'), true);

        $subscripcion_id = isset($data['subscripcion_id']) ? (int) $data['subscripcion_id'] : 0;



        if ($subscripcion_id <= 0) {

            echo json_encode(['error' => 'Falta subscripcion_id o no es válido']);



            return;

        }



        $ya = ClienteSubscripcion::obtenerActivaPorUsuarioId((int) $_SESSION['usuario_id']);

        if ($ya !== null) {

            echo json_encode([

                'error' => 'Ya tienes un plan activo. No puedes contratar otro hasta que venza tu suscripción actual o solicites cambios desde el centro.',

            ]);



            return;

        }



        try {



            $conexion = BasedeDatos::Conectar();



            $stmt = $conexion->prepare('SELECT * FROM subscripciones WHERE id = ? AND estado = \'A\'');

            $stmt->execute([$subscripcion_id]);



            $subscripcion = $stmt->fetch(PDO::FETCH_ASSOC);



            if (!$subscripcion) {

                echo json_encode(['error' => 'Suscripción no válida o no disponible']);



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

                'clientSecret' => $intent->client_secret,

            ]);

        } catch (Exception $e) {

            echo json_encode([

                'error' => $e->getMessage(),

            ]);

        }

    }



    public function exito()

    {

        \Stripe\Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);



        $payment_intent_id = $_GET['payment_intent'] ?? null;

        $subscripcion_id = $_GET['subscripcion_id'] ?? null;



        if (!$payment_intent_id || !$subscripcion_id) {

            echo 'Error: datos incompletos';



            return;

        }



        if (!isset($_SESSION['usuario_id']) || ($_SESSION['rol'] ?? '') !== 'cliente') {

            header('Location: ' . url('/login') . '?error=' . rawurlencode('Inicia sesión para completar tu suscripción'));

            exit;

        }



        $subscripcion_id = (int) $subscripcion_id;

        try {



            $intent = \Stripe\PaymentIntent::retrieve($payment_intent_id);



            if ($intent->status === 'succeeded') {



                $conexion = BasedeDatos::Conectar();



                $stmt = $conexion->prepare('SELECT id FROM clientes WHERE usuario_id = ?');

                $stmt->execute([$_SESSION['usuario_id']]);

                $cliente = $stmt->fetch(PDO::FETCH_ASSOC);



                if (!$cliente) {

                    die('El usuario no es cliente');

                }



                $cliente_id = $cliente['id'];



                if (ClienteSubscripcion::obtenerActivaPorUsuarioId((int) $_SESSION['usuario_id']) !== null) {

                    header('Location: ' . url('/pago') . '?info=' . rawurlencode('Ya tenías una suscripción activa; no se ha duplicado el alta.'));



                    exit;

                }



                $chk = $conexion->prepare('SELECT id FROM subscripciones WHERE id = ? AND estado = \'A\'');

                $chk->execute([$subscripcion_id]);

                if (!$chk->fetch()) {

                    header('Location: ' . url('/pago') . '?error=' . rawurlencode('Plan no válido'));



                    exit;

                }



                $stmt = $conexion->prepare('

                    INSERT INTO cliente_subscripcion (cliente_id, subscripcion_id, fecha_inicio)

                    VALUES (?, ?, NOW())

                ');



                $stmt->execute([$cliente_id, $subscripcion_id]);

            }

        } catch (Exception $e) {

            echo 'Error: ' . $e->getMessage();



            return;

        }



        $this->renderFrontend('frontend/pago/exito');

    }



    public function cancelado()

    {

        $this->redirigirFisioFueraPortal();

        $this->renderFrontend('frontend/pago/cancelado');

    }

}

