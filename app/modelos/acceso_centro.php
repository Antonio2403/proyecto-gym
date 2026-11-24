<?php

declare(strict_types=1);

require_once __DIR__ . '/database.php';

/**
 * Códigos QR de entrada y salida. El último código de cada persona decide si sigue dentro.
 */
class AccesoCentro
{
    /**
     * @return array{id: int, usuario_id: int, tipo: string, token: string, creado_en: string}|null
     */
    public static function ultimoDeUsuario(int $usuarioId): ?array
    {
        if ($usuarioId <= 0) {
            return null;
        }
        $db = BasedeDatos::Conectar();
        $st = $db->prepare(
            'SELECT id, usuario_id, tipo, token, creado_en
             FROM acceso_centro
             WHERE usuario_id = ?
             ORDER BY id DESC
             LIMIT 1'
        );
        $st->execute([$usuarioId]);
        $row = $st->fetch(PDO::FETCH_ASSOC);

        return is_array($row) ? self::fila($row) : null;
    }

    public static function siguienteTipo(int $usuarioId): string
    {
        $ultimo = self::ultimoDeUsuario($usuarioId);

        return ($ultimo !== null && $ultimo['tipo'] === 'entrada') ? 'salida' : 'entrada';
    }

    /**
     * @return array{id: int, usuario_id: int, tipo: string, token: string, creado_en: string}
     */
    public static function generar(int $usuarioId): array
    {
        if ($usuarioId <= 0) {
            throw new InvalidArgumentException('Usuario no válido.');
        }
        $tipo = self::siguienteTipo($usuarioId);
        $token = bin2hex(random_bytes(16));
        $cuando = (new DateTimeImmutable('now'))->format('Y-m-d H:i:s');
        $db = BasedeDatos::Conectar();
        $st = $db->prepare(
            'INSERT INTO acceso_centro (usuario_id, tipo, token, creado_en)
             VALUES (?, ?, ?, ?)'
        );
        $st->execute([$usuarioId, $tipo, $token, $cuando]);
        $id = (int) $db->lastInsertId();

        return [
            'id' => $id,
            'usuario_id' => $usuarioId,
            'tipo' => $tipo,
            'token' => $token,
            'creado_en' => $cuando,
        ];
    }

    /**
     * @return array{id: int, usuario_id: int, tipo: string, token: string, creado_en: string, nombre: string}|null
     */
    public static function porToken(string $token): ?array
    {
        if (!preg_match('/^[a-f0-9]{32}$/', $token)) {
            return null;
        }
        $db = BasedeDatos::Conectar();
        $st = $db->prepare(
            'SELECT a.id, a.usuario_id, a.tipo, a.token, a.creado_en,
                    TRIM(CONCAT(u.nombre, \' \', u.apellido1)) AS nombre
             FROM acceso_centro a
             INNER JOIN usuarios u ON u.id = a.usuario_id
             WHERE a.token = ?
             LIMIT 1'
        );
        $st->execute([$token]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        if (!is_array($row)) {
            return null;
        }
        $fila = self::fila($row);
        $fila['nombre'] = trim((string) ($row['nombre'] ?? ''));

        return $fila;
    }

    /**
     * @return array{
     *   dentro: int,
     *   entradas_hoy: int,
     *   salidas_hoy: int,
     *   personas: list<array{nombre: string, desde: string}>
     * }
     */
    public static function resumenAdmin(): array
    {
        $db = BasedeDatos::Conectar();
        $tz = new DateTimeZone(date_default_timezone_get());
        $hoy = new DateTimeImmutable('today', $tz);
        $manana = $hoy->modify('+1 day');

        $cuentas = $db->prepare(
            'SELECT tipo, COUNT(*) AS n
             FROM acceso_centro
             WHERE creado_en >= ? AND creado_en < ?
             GROUP BY tipo'
        );
        $cuentas->execute([$hoy->format('Y-m-d H:i:s'), $manana->format('Y-m-d H:i:s')]);
        $entradas = 0;
        $salidas = 0;
        foreach ($cuentas->fetchAll(PDO::FETCH_ASSOC) as $fila) {
            if (($fila['tipo'] ?? '') === 'entrada') {
                $entradas = (int) $fila['n'];
            } elseif (($fila['tipo'] ?? '') === 'salida') {
                $salidas = (int) $fila['n'];
            }
        }

        $st = $db->query(
            'SELECT TRIM(CONCAT(u.nombre, \' \', u.apellido1)) AS nombre, a.creado_en
             FROM acceso_centro a
             INNER JOIN (
                 SELECT usuario_id, MAX(id) AS id
                 FROM acceso_centro
                 GROUP BY usuario_id
             ) ult ON ult.id = a.id
             INNER JOIN usuarios u ON u.id = a.usuario_id
             WHERE a.tipo = \'entrada\'
             ORDER BY a.creado_en ASC, u.nombre ASC'
        );
        $personas = [];
        if ($st !== false) {
            foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $fila) {
                $personas[] = [
                    'nombre' => trim((string) ($fila['nombre'] ?? '')),
                    'desde' => (string) ($fila['creado_en'] ?? ''),
                ];
            }
        }

        return [
            'dentro' => count($personas),
            'entradas_hoy' => $entradas,
            'salidas_hoy' => $salidas,
            'personas' => $personas,
        ];
    }

    /**
     * @param array<string, mixed> $row
     * @return array{id: int, usuario_id: int, tipo: string, token: string, creado_en: string}
     */
    private static function fila(array $row): array
    {
        return [
            'id' => (int) ($row['id'] ?? 0),
            'usuario_id' => (int) ($row['usuario_id'] ?? 0),
            'tipo' => (string) ($row['tipo'] ?? ''),
            'token' => (string) ($row['token'] ?? ''),
            'creado_en' => (string) ($row['creado_en'] ?? ''),
        ];
    }
}
