<?php

/**
 * Suscripciones asignadas a clientes y reglas de negocio (p. ej. fisioterapia incluida).
 */
class ClienteSubscripcion
{
    /**
     * Suscripción activa que incluye fisioterapia (subscripciones.fisio = 'S').
     *
     * @return array{nombre: string, fecha_fin: ?string, fecha_inicio: ?string}|null
     */
    public static function obtenerActivaConFisio(int $clienteId): ?array
    {
        $db = BasedeDatos::Conectar();
        $st = $db->prepare(
            'SELECT s.nombre AS plan_nombre, cs.fecha_inicio, cs.fecha_fin
             FROM cliente_subscripcion cs
             INNER JOIN subscripciones s ON s.id = cs.subscripcion_id
             WHERE cs.cliente_id = ?
               AND cs.estado = \'A\'
               AND s.fisio = \'S\'
               AND s.estado = \'A\'
               AND (cs.fecha_inicio IS NULL OR cs.fecha_inicio <= NOW())
               AND (cs.fecha_fin IS NULL OR cs.fecha_fin >= NOW())
             ORDER BY cs.fecha_inicio DESC
             LIMIT 1'
        );
        $st->execute([$clienteId]);
        $row = $st->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public static function tieneFisioActivo(int $clienteId): bool
    {
        return self::obtenerActivaConFisio($clienteId) !== null;
    }

    /**
     * Suscripción vigente del usuario (tabla clientes).
     *
     * @return array<string, mixed>|null
     */
    public static function obtenerActivaPorUsuarioId(int $usuarioId): ?array
    {
        $db = BasedeDatos::Conectar();
        $st = $db->prepare(
            'SELECT cs.id AS cs_id, cs.cliente_id, cs.subscripcion_id, cs.fecha_inicio, cs.fecha_fin,
                    s.id AS plan_id, s.nombre AS plan_nombre, s.precio, s.duracion, s.numero_clases, s.fisio,
                    s.estado AS plan_catalogo_estado
             FROM clientes c
             INNER JOIN cliente_subscripcion cs ON cs.cliente_id = c.id AND cs.estado = \'A\'
             INNER JOIN subscripciones s ON s.id = cs.subscripcion_id AND s.estado = \'A\'
             WHERE c.usuario_id = ?
               AND (cs.fecha_inicio IS NULL OR cs.fecha_inicio <= NOW())
               AND (cs.fecha_fin IS NULL OR cs.fecha_fin >= NOW())
             ORDER BY cs.fecha_inicio DESC
             LIMIT 1'
        );
        $st->execute([$usuarioId]);
        $row = $st->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    /** Nombre del plan vigente para mostrar en cabecera; null si no tiene plan activo. */
    public static function nombrePlanVisiblePorUsuario(int $usuarioId): ?string
    {
        $a = self::obtenerActivaPorUsuarioId($usuarioId);
        if (!$a || ($a['plan_nombre'] ?? '') === '') {
            return null;
        }

        return trim((string) $a['plan_nombre']);
    }
}
