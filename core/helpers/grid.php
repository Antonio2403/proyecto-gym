<?php

declare(strict_types=1);

/**
 * Escape metacaracteres para LIKE SQL (compat MySQL/MariaDB con \ escape por defecto).
 */
function gp_grid_escape_like(string $value): string
{
    return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);
}

/**
 * LIKE %valor% si el trim no está vacío; si no null.
 */
function gp_grid_like_contains(?string $value): ?string
{
    $v = trim((string) $value);

    return $v === '' ? null : '%' . gp_grid_escape_like($v) . '%';
}

/**
 * Paginación típica admin (GET).
 *
 * @return array{page:int,per_page:int}
 */
function gp_grid_pagination(?array $src = null): array
{
    $src = $src ?? $_GET;
    $page = max(1, (int) ($src['page'] ?? 1));
    $perPage = (int) ($src['per_page'] ?? 10);
    $perPage = min(50, max(5, $perPage));

    return ['page' => $page, 'per_page' => $perPage];
}

/**
 * Trim de parámetro de filtro; cadena vacía → null para no aplicar condición.
 */
function gp_grid_str(?string $v): ?string
{
    $t = trim((string) $v);

    return $t === '' ? null : $t;
}

/**
 * Opción select «todas»: '', 'any', '*' → vacío tratado como no filtrar por entero opcional.
 */
function gp_grid_int_opt(?string $v): ?int
{
    $t = trim((string) $v);
    if ($t === '' || strcasecmp($t, 'any') === 0) {
        return null;
    }
    $n = (int) $t;

    return $n > 0 ? $n : null;
}

function gp_grid_float_opt(?string $v): ?float
{
    $t = str_replace(',', '.', trim((string) $v));
    if ($t === '' || !is_numeric($t)) {
        return null;
    }

    return (float) $t;
}

/** Fecha YYYY-MM-DD para filtros SQL seguros. */
function gp_grid_date_opt(?string $v): ?string
{
    $t = trim((string) $v);

    return preg_match('/^\d{4}-\d{2}-\d{2}$/', $t) === 1 ? $t : null;
}

/** recurrente: vacío = sin filtro, 0 / 1 solo esos valores. */
function gp_grid_recurrent_opt(?string $v): ?int
{
    if ($v === null) {
        return null;
    }
    $t = trim((string) $v);
    if ($t === '') {
        return null;
    }

    return $t === '0' || $t === '1' ? (int) $t : null;
}
