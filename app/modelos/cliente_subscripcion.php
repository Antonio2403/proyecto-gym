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
        self::cerrarCaducadas($db);
        $st = $db->prepare(
            'SELECT s.nombre AS plan_nombre, cs.fecha_inicio, cs.fecha_fin
             FROM cliente_subscripcion cs
             INNER JOIN subscripciones s ON s.id = cs.subscripcion_id
             WHERE cs.cliente_id = ?
               AND cs.estado = \'A\'
               AND s.fisio = \'S\'
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
        self::cerrarCaducadas($db);
        $st = $db->prepare(
            'SELECT cs.id AS cs_id, cs.cliente_id, cs.subscripcion_id, cs.fecha_inicio, cs.fecha_fin,
                    s.id AS plan_id, s.nombre AS plan_nombre, s.precio, s.duracion, s.numero_clases, s.fisio,
                    s.estado AS plan_catalogo_estado, s.en_oferta, s.oferta_motivo, s.oferta_fin
             FROM clientes c
             INNER JOIN cliente_subscripcion cs ON cs.cliente_id = c.id AND cs.estado = \'A\'
             INNER JOIN subscripciones s ON s.id = cs.subscripcion_id
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

    /** Tiene suscripción activa pagada (alta en cliente_subscripcion en vigor). */
    public static function tieneSuscripcionActivaPorUsuarioId(int $usuarioId): bool
    {
        return self::obtenerActivaPorUsuarioId($usuarioId) !== null;
    }

    /**
     * Reservas que cuentan para el cupo semanal (lunes–domingo de la semana de $fechaReferenciaYmd).
     *
     * @return array{usado: int, max_semana: int} max_semana 0 = sin límite explícito
     */
    public static function cupoReservasSemana(int $clienteId, string $fechaReferenciaYmd): array
    {
        $db = BasedeDatos::Conectar();
        self::cerrarCaducadas($db);
        $st = $db->prepare(
            'SELECT s.numero_clases
             FROM cliente_subscripcion cs
             INNER JOIN subscripciones s ON s.id = cs.subscripcion_id
             WHERE cs.cliente_id = ?
               AND cs.estado = \'A\'
               AND (cs.fecha_inicio IS NULL OR cs.fecha_inicio <= NOW())
               AND (cs.fecha_fin IS NULL OR cs.fecha_fin >= NOW())
             ORDER BY cs.fecha_inicio DESC
             LIMIT 1'
        );
        $st->execute([$clienteId]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        $max = $row ? (int) ($row['numero_clases'] ?? 0) : 0;

        $tz = new \DateTimeZone(date_default_timezone_get());
        $ref = \DateTimeImmutable::createFromFormat('Y-m-d', $fechaReferenciaYmd, $tz);
        if ($ref === false) {
            return ['usado' => 0, 'max_semana' => $max];
        }
        $dow = (int) $ref->format('N');
        $monday = $ref->modify('-' . ($dow - 1) . ' days');
        $monYmd = $monday->format('Y-m-d');
        $sunYmd = $monday->modify('+6 days')->format('Y-m-d');

        $st2 = $db->prepare(
            'SELECT COUNT(*) FROM inscripciones
             WHERE cliente_id = ?
               AND fecha_ocurrencia IS NOT NULL
               AND fecha_ocurrencia >= ?
               AND fecha_ocurrencia <= ?'
        );
        $st2->execute([$clienteId, $monYmd, $sunYmd]);
        $usado = (int) $st2->fetchColumn();

        return ['usado' => $usado, 'max_semana' => $max];
    }

    /**
     * ¿Puede hacer una reserva más esa semana? Si max_semana es 0 se interpreta como sin tope (solo requiere suscripción).
     */
    public static function puedeNuevaReservaEsaSemana(int $clienteId, string $fechaSesionYmd): bool
    {
        $c = self::cupoReservasSemana($clienteId, $fechaSesionYmd);
        if ($c['max_semana'] <= 0) {
            return true;
        }

        return $c['usado'] < $c['max_semana'];
    }

    private static function cerrarCaducadas(PDO $db): void
    {
        try {
            $db->exec("UPDATE cliente_subscripcion SET estado = 'C' WHERE estado = 'A' AND fecha_fin IS NOT NULL AND fecha_fin < NOW()");
        } catch (Throwable $e) {
            error_log('[ClienteSubscripcion] cerrarCaducadas: ' . $e->getMessage());
        }
    }
}
