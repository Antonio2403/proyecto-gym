<?php

declare(strict_types=1);

/**
 * Horario de apertura del centro (fuente única para footer, validaciones PHP/JS y citas).
 *
 * Lun–Vie 9:00–21:00 · Sáb 10:00–14:00 · Dom cerrado.
 */

/** @return array<string, array{open: string, close: string}|null> */
function gp_horario_centro_mapa(): array
{
    return [
        'L' => ['open' => '09:00', 'close' => '21:00'],
        'M' => ['open' => '09:00', 'close' => '21:00'],
        'X' => ['open' => '09:00', 'close' => '21:00'],
        'J' => ['open' => '09:00', 'close' => '21:00'],
        'V' => ['open' => '09:00', 'close' => '21:00'],
        'S' => ['open' => '10:00', 'close' => '14:00'],
        'D' => null,
    ];
}

/** @return string[] */
function gp_horario_centro_lineas(): array
{
    return [
        'Lun–Vie 9:00–21:00',
        'Sáb 10:00–14:00',
    ];
}

function gp_horario_centro_dia_letra(DateTimeInterface $dt): string
{
    $n = (int) $dt->format('N');

    return [1 => 'L', 2 => 'M', 3 => 'X', 4 => 'J', 5 => 'V', 6 => 'S', 7 => 'D'][$n] ?? '';
}

function gp_horario_centro_dia_abierto(string $diaLetra): bool
{
    $mapa = gp_horario_centro_mapa();

    return isset($mapa[$diaLetra]) && $mapa[$diaLetra] !== null;
}

/** @return array{open: string, close: string}|null */
function gp_horario_centro_rango_dia(string $diaLetra): ?array
{
    $mapa = gp_horario_centro_mapa();

    return $mapa[$diaLetra] ?? null;
}

function gp_horario_centro_nombre_dia(string $diaLetra): string
{
    $nombres = [
        'L' => 'lunes',
        'M' => 'martes',
        'X' => 'miércoles',
        'J' => 'jueves',
        'V' => 'viernes',
        'S' => 'sábado',
        'D' => 'domingo',
    ];

    return $nombres[$diaLetra] ?? $diaLetra;
}

/**
 * Comprueba que hora inicio + duración caen dentro del rango del día.
 */
function gp_horario_centro_cabe_en_dia(string $diaLetra, string $horaInicio, int $duracionMin): bool
{
    $rango = gp_horario_centro_rango_dia($diaLetra);
    if ($rango === null) {
        return false;
    }

    $inicio = strtotime('1970-01-01 ' . $horaInicio . ':00');
    $fin = strtotime('1970-01-01 ' . $horaInicio . ':00 +' . $duracionMin . ' minutes');
    $open = strtotime('1970-01-01 ' . $rango['open'] . ':00');
    $close = strtotime('1970-01-01 ' . $rango['close'] . ':00');

    return $inicio !== false && $fin !== false && $open !== false && $close !== false
        && $inicio >= $open && $fin <= $close;
}

/**
 * Valida programación de actividad (recurrente o puntual).
 *
 * @param string[] $diasLetras
 */
function gp_horario_validar_programacion_actividad(
    array $diasLetras,
    string $horaInicio,
    int $duracionMin,
    int $recurrente,
    ?string $fechaPuntualYmd = null
): ?string {
    if (!preg_match('/^\d{2}:\d{2}$/', $horaInicio)) {
        return 'La hora de inicio no es válida.';
    }
    if ($duracionMin < 1 || $duracionMin > 600) {
        return 'La duración debe estar entre 1 y 600 minutos.';
    }

    if ($recurrente === 0) {
        if ($fechaPuntualYmd === null || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaPuntualYmd)) {
            return 'Indica la fecha del evento puntual.';
        }
        $dia = gp_horario_centro_dia_letra(new DateTimeImmutable($fechaPuntualYmd));
        if (!gp_horario_centro_dia_abierto($dia)) {
            return 'El centro está cerrado ese día (' . gp_horario_centro_nombre_dia($dia) . ').';
        }
        if (!gp_horario_centro_cabe_en_dia($dia, $horaInicio, $duracionMin)) {
            $r = gp_horario_centro_rango_dia($dia);

            return 'La actividad debe terminar dentro del horario del centro (' . ($r['open'] ?? '') . '–' . ($r['close'] ?? '') . ').';
        }

        return null;
    }

    if ($diasLetras === []) {
        return 'Marca al menos un día de la semana.';
    }

    foreach ($diasLetras as $dia) {
        if (!gp_horario_centro_dia_abierto($dia)) {
            return 'El centro está cerrado los ' . gp_horario_centro_nombre_dia($dia) . '.';
        }
        if (!gp_horario_centro_cabe_en_dia($dia, $horaInicio, $duracionMin)) {
            $r = gp_horario_centro_rango_dia($dia);

            return 'El horario no cabe el ' . gp_horario_centro_nombre_dia($dia)
                . ' (apertura ' . ($r['open'] ?? '') . '–' . ($r['close'] ?? '') . ').';
        }
    }

    return null;
}

/**
 * Valida cita de fisioterapia (mismo horario de centro; domingo cerrado).
 */
function gp_horario_validar_cita_fisio(DateTimeInterface $fechaHora, ?int $duracionMin = null): ?string
{
    if ($duracionMin === null) {
        $duracionMin = gp_fisio_duracion_consulta_minutos();
    }
    if ($fechaHora->getTimestamp() < time()) {
        return 'Elige una fecha y hora futuras.';
    }

    $dia = gp_horario_centro_dia_letra($fechaHora);
    if (!gp_horario_centro_dia_abierto($dia)) {
        return 'El centro está cerrado ese día. Horario: Lun–Vie 9:00–21:00, Sáb 10:00–14:00.';
    }

    $hora = $fechaHora->format('H:i');
    if (!gp_horario_centro_cabe_en_dia($dia, $hora, $duracionMin)) {
        $r = gp_horario_centro_rango_dia($dia);

        return 'La cita debe estar dentro del horario del centro (' . ($r['open'] ?? '') . '–' . ($r['close'] ?? '') . ').';
    }

    return null;
}

function gp_fisio_duracion_consulta_minutos(): int
{
    return 30;
}

/**
 * Franjas de inicio cada 30 min dentro del horario del centro para una fecha.
 *
 * @return string[] Horas HH:MM futuras (respecto a ahora si es hoy).
 */
function gp_horario_slots_centro_dia(string $fechaYmd, ?int $intervaloMin = null): array
{
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaYmd)) {
        return [];
    }

    $intervaloMin = $intervaloMin ?? gp_fisio_duracion_consulta_minutos();
    $dt = new DateTimeImmutable($fechaYmd);
    $dia = gp_horario_centro_dia_letra($dt);
    $rango = gp_horario_centro_rango_dia($dia);
    if ($rango === null) {
        return [];
    }

    $dur = gp_fisio_duracion_consulta_minutos();
    $cursor = strtotime($fechaYmd . ' ' . $rango['open'] . ':00');
    $close = strtotime($fechaYmd . ' ' . $rango['close'] . ':00');
    if ($cursor === false || $close === false) {
        return [];
    }

    $now = time();
    $slots = [];
    while ($cursor + ($dur * 60) <= $close) {
        if ($cursor > $now) {
            $slots[] = date('H:i', $cursor);
        }
        $cursor += $intervaloMin * 60;
    }

    return $slots;
}

function gp_horario_es_slot_valido_fisio(string $fechaYmd, string $horaHi): bool
{
    if (!preg_match('/^\d{2}:\d{2}$/', $horaHi)) {
        return false;
    }

    return in_array($horaHi, gp_horario_slots_centro_dia($fechaYmd), true);
}

/** Config JSON para validación en el navegador. */
function gp_horario_centro_json(): array
{
    return [
        'dias' => gp_horario_centro_mapa(),
        'lineas' => gp_horario_centro_lineas(),
        'fisio_slot_minutos' => gp_fisio_duracion_consulta_minutos(),
    ];
}

function gp_horario_centro_direccion(): string
{
    return 'Calle Camelia Nº 16, Lepe (Huelva)';
}

function gp_horario_centro_maps_query(): string
{
    return 'Calle Camelia Nº 16, Lepe, Huelva';
}

function gp_horario_centro_maps_embed_url(): string
{
    $q = rawurlencode(gp_horario_centro_maps_query());

    return 'https://maps.google.com/maps?q=' . $q . '&output=embed';
}

function gp_horario_centro_maps_link_url(): string
{
    $q = rawurlencode(gp_horario_centro_maps_query());

    return 'https://www.google.com/maps/search/?api=1&query=' . $q;
}
