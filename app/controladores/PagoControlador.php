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
            Subscripcion::sincronizarCatalogo($conexion);



            Subscripcion::sincronizarCatalogo($conexion);
            $stmt = $conexion->prepare('SELECT * FROM subscripciones WHERE id = ? AND estado = \'A\' AND (en_oferta = 0 OR (oferta_fin IS NOT NULL AND oferta_fin > NOW()))');

            $stmt->execute([$subscripcion_id]);



            $subscripcion = $stmt->fetch(PDO::FETCH_ASSOC);



            if (!$subscripcion) {

                echo json_encode(['error' => 'Suscripción no válida o no disponible']);



                return;

            }



            $precio = (int) round(((float) $subscripcion['precio']) * 100);
            if ($precio < 50) {
                echo json_encode(['error' => 'Importe no válido para Stripe']);
                return;
            }



            $intent = \Stripe\PaymentIntent::create([

                'amount' => $precio,

                'currency' => 'eur',

                'metadata' => [

                    'usuario_id' => (string) (int) $_SESSION['usuario_id'],

                    'subscripcion_id' => (string) (int) $subscripcion_id,

                ],

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

        if (!$payment_intent_id) {
            header('Location: ' . url('/pago') . '?error=' . rawurlencode('Datos de pago incompletos'));
            exit;
        }



        if (!isset($_SESSION['usuario_id']) || ($_SESSION['rol'] ?? '') !== 'cliente') {

            header('Location: ' . url('/login') . '?error=' . rawurlencode('Inicia sesión para completar tu suscripción'));

            exit;

        }

        try {
            $intent = \Stripe\PaymentIntent::retrieve($payment_intent_id);

            if ($intent->status !== 'succeeded') {
                header('Location: ' . url('/pago') . '?error=' . rawurlencode('El pago no aparece como completado.'));
                exit;
            }

            $metadata = $intent->metadata ? $intent->metadata->toArray() : [];
            $intentUsuarioId = (int) ($metadata['usuario_id'] ?? 0);
            $subscripcion_id = (int) ($metadata['subscripcion_id'] ?? 0);
            if ($intentUsuarioId !== (int) $_SESSION['usuario_id'] || $subscripcion_id <= 0) {
                header('Location: ' . url('/pago') . '?error=' . rawurlencode('El pago no corresponde a esta sesión.'));
                exit;
            }

            $conexion = BasedeDatos::Conectar();

            $stmt = $conexion->prepare('SELECT id FROM clientes WHERE usuario_id = ?');
            $stmt->execute([$_SESSION['usuario_id']]);
            $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$cliente) {
                header('Location: ' . url('/pago') . '?error=' . rawurlencode('El usuario no es cliente'));
                exit;
            }

            $cliente_id = (int) $cliente['id'];

            if (ClienteSubscripcion::obtenerActivaPorUsuarioId((int) $_SESSION['usuario_id']) !== null) {
                header('Location: ' . url('/pago') . '?info=' . rawurlencode('Ya tenías una suscripción activa; no se ha duplicado el alta.'));
                exit;
            }

            $planStmt = $conexion->prepare('SELECT precio, duracion, estado, en_oferta, oferta_fin FROM subscripciones WHERE id = ? LIMIT 1');
            $planStmt->execute([$subscripcion_id]);
            $plan = $planStmt->fetch(PDO::FETCH_ASSOC);
            if (!$plan) {
                header('Location: ' . url('/pago') . '?error=' . rawurlencode('Plan no válido'));
                exit;
            }

            $esOferta = (int) ($plan['en_oferta'] ?? 0) === 1;
            $ofertaFinRaw = (string) ($plan['oferta_fin'] ?? '');
            $ofertaFinTs = $ofertaFinRaw !== '' ? strtotime($ofertaFinRaw) : false;
            $intentCreatedTs = isset($intent->created) ? (int) $intent->created : time();
            $compraDentroDelPlazoOferta = $esOferta && $ofertaFinTs !== false && $intentCreatedTs <= $ofertaFinTs;
            if ($esOferta && $ofertaFinTs === false) {
                header('Location: ' . url('/pago') . '?error=' . rawurlencode('La oferta no tiene una fecha límite válida.'));
                exit;
            }
            if ((string) ($plan['estado'] ?? '') !== 'A' && !$compraDentroDelPlazoOferta) {
                header('Location: ' . url('/pago') . '?error=' . rawurlencode('La oferta o plan ya no está disponible para comprar.'));
                exit;
            }
            if ($esOferta && $ofertaFinTs !== false && !$compraDentroDelPlazoOferta) {
                header('Location: ' . url('/pago') . '?error=' . rawurlencode('El plazo para comprar esta oferta ha terminado.'));
                exit;
            }

            $expectedAmount = (int) round(((float) $plan['precio']) * 100);
            if ((int) $intent->amount !== $expectedAmount || strtolower((string) $intent->currency) !== 'eur') {
                header('Location: ' . url('/pago') . '?error=' . rawurlencode('El importe pagado no coincide con el plan seleccionado.'));
                exit;
            }

            $duracion = max(1, (int) $plan['duracion']);
            $fechaFin = (new DateTimeImmutable('now'))->modify('+' . $duracion . ' months')->format('Y-m-d H:i:s');

            $stmt = $conexion->prepare('
                INSERT INTO cliente_subscripcion (cliente_id, subscripcion_id, fecha_inicio, fecha_fin, estado)
                VALUES (?, ?, NOW(), ?, \'A\')
            ');
            $stmt->execute([$cliente_id, $subscripcion_id, $fechaFin]);

        } catch (Exception $e) {
            error_log('[Pago] exito: ' . $e->getMessage());
            header('Location: ' . url('/pago') . '?error=' . rawurlencode('No se pudo confirmar el pago. Contacta con el centro si se ha cobrado.'));
            exit;
        }



        $this->renderFrontend('frontend/pago/exito');

    }



    public function cancelado()

    {

        $this->redirigirFisioFueraPortal();

        $this->renderFrontend('frontend/pago/cancelado');

    }

}

