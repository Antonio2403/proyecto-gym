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

/**
 * Aviso a quien tenía plaza: la sesión no llega al mínimo.
 *
 * @param array<int, array{email: string, nombre: string}> $inscritos
 */
function gp_actividad_notificar_minimo_no_alcanzado(
    string $nombreActividad,
    string $fechaYmd,
    string $hora,
    int $inscritos,
    int $minimo,
    array $personas
): void {
    $cuando = date('d/m/Y', strtotime($fechaYmd)) . ' a las ' . $hora;
    $link = app_url_absolute('/usuario/actividades');
    $asunto = 'Clase cancelada por falta de gente: ' . $nombreActividad;

    foreach ($personas as $row) {
        $email = trim((string) ($row['email'] ?? ''));
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            continue;
        }
        $nombre = trim((string) ($row['nombre'] ?? 'socio'));
        $html = '<p>Hola ' . htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') . ',</p>'
            . '<p>La clase <strong>' . htmlspecialchars($nombreActividad, ENT_QUOTES, 'UTF-8') . '</strong>'
            . ' del ' . htmlspecialchars($cuando, ENT_QUOTES, 'UTF-8') . ' no llega al mínimo de '
            . (int) $minimo . ' personas. Había <strong>' . (int) $inscritos . '</strong> apuntadas.</p>'
            . '<p>Esa sesión queda cancelada. Tu reserva se anula y no cuenta como clase usada.</p>'
            . '<p><a href="' . htmlspecialchars($link, ENT_QUOTES, 'UTF-8') . '">Ver el horario</a></p>'
            . '<p>— Spartum</p>';
        $text = "Hola {$nombre},\n\n"
            . "La clase «{$nombreActividad}» del {$cuando} se cancela: había {$inscritos} personas y el mínimo es {$minimo}.\n"
            . "Tu reserva queda anulada. Horario: {$link}\n";
        $err = null;
        if (!gp_mail_send($email, $asunto, $html, $text, $err)) {
            error_log('[Spartum actividad] correo mínimo no enviado a ' . $email . ': ' . (string) $err);
        }
    }
}

/**
 * Reporte al monitor y a administración.
 *
 * @param array<int, array{email: string, nombre: string}> $destinatarios
 * @param array<int, string> $nombresApuntados
 */
function gp_actividad_reportar_minimo_no_alcanzado(
    string $nombreActividad,
    string $fechaYmd,
    string $hora,
    int $inscritos,
    int $minimo,
    array $destinatarios,
    array $nombresApuntados
): void {
    $cuando = date('d/m/Y', strtotime($fechaYmd)) . ' a las ' . $hora;
    $lista = $nombresApuntados === [] ? 'Nadie tenía plaza.' : implode(', ', $nombresApuntados);
    $asunto = 'Reporte: clase cancelada por poca gente — ' . $nombreActividad;

    foreach ($destinatarios as $row) {
        $email = trim((string) ($row['email'] ?? ''));
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            continue;
        }
        $nombre = trim((string) ($row['nombre'] ?? ''));
        $html = '<p>Hola' . ($nombre !== '' ? ' ' . htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') : '') . ',</p>'
            . '<p>Media hora antes de empezar, la clase <strong>' . htmlspecialchars($nombreActividad, ENT_QUOTES, 'UTF-8') . '</strong>'
            . ' del ' . htmlspecialchars($cuando, ENT_QUOTES, 'UTF-8') . ' no llegaba al mínimo.</p>'
            . '<ul>'
            . '<li>Mínimo: ' . (int) $minimo . ' personas</li>'
            . '<li>Apuntadas: ' . (int) $inscritos . '</li>'
            . '<li>Quiénes: ' . htmlspecialchars($lista, ENT_QUOTES, 'UTF-8') . '</li>'
            . '</ul>'
            . '<p>La sesión se ha cancelado y las reservas de esa hora han quedado anuladas.</p>'
            . '<p>— Spartum</p>';
        $text = "Reporte de cancelación\n"
            . "Clase: {$nombreActividad}\n"
            . "Cuándo: {$cuando}\n"
            . "Mínimo: {$minimo}\n"
            . "Apuntadas: {$inscritos}\n"
            . "Quiénes: {$lista}\n"
            . "La sesión se ha cancelado por poca gente.\n";
        $err = null;
        if (!gp_mail_send($email, $asunto, $html, $text, $err)) {
            error_log('[Spartum actividad] reporte mínimo no enviado a ' . $email . ': ' . (string) $err);
        }
    }
}
