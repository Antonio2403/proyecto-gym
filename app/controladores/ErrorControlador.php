<?php

require_once 'core/Controller.php';

class ErrorControlador extends Controller
{
    public function notFound(): void
    {
        http_response_code(404);
        $motivo = trim((string) ($_GET['motivo'] ?? ''));
        $this->renderFrontend('frontend/error404', ['motivo' => $motivo]);
    }
}
