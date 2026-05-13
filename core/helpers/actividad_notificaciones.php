<?php

declare(strict_types=1);

require_once __DIR__ . '/mail_smtp.php';
require_once __DIR__ . '/url.php';

/**
 * @param array<int, array{email: string, nombre: string, fecha_ocurrencia: ?string}> $inscritos
 */
function gp_actividad_notificar_cambio_horario(
    string $nombreActividad,
    array $inscritos,
    string $motivo
): void {
    if ($inscritos === []) {
        return;
    }

    $link = url('/usuario/actividades');
    $asunto = 'Cambio en tu clase: ' . $nombreActividad;

    foreach ($inscritos as $row) {
        $email = trim((string) ($row['email'] ?? ''));
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            continue;
        }
        $nombre = trim((string) ($row['nombre'] ?? 'socio'));
        $sesion = (string) ($row['fecha_ocurrencia'] ?? '');
        $sesionTxt = $sesion !== '' ? ' del ' . date('d/m/Y', strtotime($sesion)) : '';

        $html = '<p>Hola ' . htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') . ',</p>'
            . '<p>La actividad <strong>' . htmlspecialchars($nombreActividad, ENT_QUOTES, 'UTF-8') . '</strong>'
            . ($sesionTxt !== '' ? ' en la que tenías plaza' . htmlspecialchars($sesionTxt, ENT_QUOTES, 'UTF-8') : '')
            . ' ha cambiado: ' . htmlspecialchars($motivo, ENT_QUOTES, 'UTF-8') . '.</p>'
            . '<p><strong>Tu reserva se ha anulado automáticamente.</strong> Vuelve a apuntarte con el nuevo horario desde el calendario de actividades.</p>'
            . '<p><a href="' . htmlspecialchars($link, ENT_QUOTES, 'UTF-8') . '">Ver actividades y reservar de nuevo</a></p>'
            . '<p>— Spartum</p>';

        $text = "Hola {$nombre},\n\n"
            . "La actividad «{$nombreActividad}» ha cambiado: {$motivo}.\n"
            . "Tu reserva se ha anulado. Vuelve a apuntarte: {$link}\n";

        $err = null;
        if (!gp_mail_send($email, $asunto, $html, $text, $err)) {
            error_log('[Spartum actividad] correo cambio horario no enviado a ' . $email . ': ' . (string) $err);
        }
    }
}

/**
 * @param array<int, array{email: string, nombre: string, fecha_ocurrencia: ?string}> $inscritos
 */
function gp_actividad_notificar_cancelacion(string $nombreActividad, array $inscritos): void
{
    if ($inscritos === []) {
        return;
    }

    $link = url('/usuario/actividades');
    $asunto = 'Actividad cancelada: ' . $nombreActividad;

    foreach ($inscritos as $row) {
        $email = trim((string) ($row['email'] ?? ''));
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            continue;
        }
        $nombre = trim((string) ($row['nombre'] ?? 'socio'));
        $sesion = (string) ($row['fecha_ocurrencia'] ?? '');
        $sesionTxt = $sesion !== '' ? ' del ' . date('d/m/Y', strtotime($sesion)) : '';

        $html = '<p>Hola ' . htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') . ',</p>'
            . '<p>La actividad <strong>' . htmlspecialchars($nombreActividad, ENT_QUOTES, 'UTF-8') . '</strong>'
            . ($sesionTxt !== '' ? ' en la que tenías plaza' . htmlspecialchars($sesionTxt, ENT_QUOTES, 'UTF-8') : '')
            . ' <strong>se ha cancelado</strong>.</p>'
            . '<p>Tu reserva ya no es válida. Puedes consultar otras clases disponibles y volver a apuntarte.</p>'
            . '<p><a href="' . htmlspecialchars($link, ENT_QUOTES, 'UTF-8') . '">Ver actividades</a></p>'
            . '<p>— Spartum</p>';

        $text = "Hola {$nombre},\n\n"
            . "La actividad «{$nombreActividad}» se ha cancelado. Tu reserva ya no es válida.\n"
            . "Consulta otras clases: {$link}\n";

        $err = null;
        if (!gp_mail_send($email, $asunto, $html, $text, $err)) {
            error_log('[Spartum actividad] correo cancelación no enviado a ' . $email . ': ' . (string) $err);
        }
    }
}
