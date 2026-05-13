<?php

declare(strict_types=1);

/**
 * Pares clave/valor de configuración del panel de administración.
 */
class AdminConfig
{
    public static function get(string $clave, string $default = ''): string
    {
        $db = BasedeDatos::Conectar();
        $st = $db->prepare('SELECT valor FROM admin_config WHERE clave = ? LIMIT 1');
        $st->execute([$clave]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        if (!$row || !isset($row['valor'])) {
            return $default;
        }
        $v = trim((string) $row['valor']);

        return $v !== '' ? $v : $default;
    }

    public static function getInt(string $clave, int $default): int
    {
        $raw = self::get($clave, (string) $default);
        if (!ctype_digit($raw) && !(is_numeric($raw) && (int) $raw >= 0)) {
            return $default;
        }

        return max(0, (int) $raw);
    }

    public static function set(string $clave, string $valor): bool
    {
        $db = BasedeDatos::Conectar();
        $st = $db->prepare(
            'INSERT INTO admin_config (clave, valor) VALUES (?, ?)
             ON DUPLICATE KEY UPDATE valor = VALUES(valor)'
        );

        return $st->execute([$clave, $valor]);
    }
}
