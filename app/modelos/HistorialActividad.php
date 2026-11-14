<?php

declare(strict_types=1);

namespace Spartum\Modelos;

final class HistorialActividad
{
    /**
     * @param array{tipo?: string, desde?: string, hasta?: string} $filtros
     * @return array{
     *   eventos: array<int, array<string, mixed>>,
     *   total: int,
     *   pagina: int,
     *   paginas: int,
     *   resumen: array<string, int>
     * }
     */
    public static function porUsuario(int $usuarioId, int $pagina, int $porPagina, array $filtros): array
    {
        $pagina = max(1, $pagina);
        $porPagina = min(25, max(5, $porPagina));
        $conexion = \BasedeDatos::Conectar();
        $union = self::unionSql();
        $bind = self::usuarioBind($usuarioId);
        $condiciones = [];

        $tipo = (string) ($filtros['tipo'] ?? '');
        if (in_array($tipo, ['clases', 'planes', 'fisio'], true)) {
            $condiciones[] = 't.tipo = :tipo';
            $bind[':tipo'] = $tipo;
        }
        foreach (['desde' => '>=', 'hasta' => '<='] as $campo => $operador) {
            $fecha = trim((string) ($filtros[$campo] ?? ''));
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
                $condiciones[] = 'DATE(t.occurred_at) ' . $operador . ' :' . $campo;
                $bind[':' . $campo] = $fecha;
            }
        }
        $where = $condiciones === [] ? '' : ' WHERE ' . implode(' AND ', $condiciones);

        $count = $conexion->prepare('SELECT COUNT(*) FROM (' . $union . ') t' . $where);
        self::bind($count, $bind);
        $count->execute();
        $total = (int) $count->fetchColumn();
        $paginas = max(1, (int) ceil($total / $porPagina));
        $pagina = min($pagina, $paginas);

        $sql = 'SELECT t.* FROM (' . $union . ') t' . $where
            . ' ORDER BY t.occurred_at DESC, t.ref_id DESC LIMIT :lim OFFSET :off';
        $stmt = $conexion->prepare($sql);
        self::bind($stmt, $bind);
        $stmt->bindValue(':lim', $porPagina, \PDO::PARAM_INT);
        $stmt->bindValue(':off', ($pagina - 1) * $porPagina, \PDO::PARAM_INT);
        $stmt->execute();

        return [
            'eventos' => $stmt->fetchAll(\PDO::FETCH_ASSOC),
            'total' => $total,
            'pagina' => $pagina,
            'paginas' => $paginas,
            'resumen' => self::resumen($conexion, $union, $usuarioId),
        ];
    }

    private static function unionSql(): string
    {
        return "
            SELECT 'clases' AS tipo, 'reserva' AS subtipo, i.fecha_inscripcion AS occurred_at,
                   i.id AS ref_id, a.nombre AS titulo,
                   CONCAT(
                       DATE_FORMAT(i.fecha_ocurrencia, '%d/%m/%Y'),
                       IF(a.fecha_inicio IS NULL, '', CONCAT(' · ', DATE_FORMAT(a.fecha_inicio, '%H:%i'))),
                       IF(s.nombre IS NULL OR s.nombre = '', '', CONCAT(' · ', s.nombre))
                   ) AS detalle,
                   i.asistio AS estado
            FROM inscripciones i
            INNER JOIN clientes cli_r ON cli_r.id = i.cliente_id
            INNER JOIN actividades a ON a.id = i.actividad_id
            LEFT JOIN salas s ON s.id = a.sala_id
            WHERE cli_r.usuario_id = :uid_reserva

            UNION ALL

            SELECT 'clases', 'comentario', co.fecha, co.id, aco.nombre,
                   LEFT(co.texto, 180), NULL
            FROM comentarios co
            INNER JOIN clientes cli_c ON cli_c.id = co.cliente_id
            INNER JOIN actividades aco ON aco.id = co.actividad_id
            WHERE cli_c.usuario_id = :uid_comentario

            UNION ALL

            SELECT 'planes', 'plan_alta', cs.fecha_inicio, cs.id, sub.nombre,
                   CONCAT(
                       FORMAT(IFNULL(sub.precio, 0), 2), ' €',
                       IF(sub.duracion IS NULL, '', CONCAT(' · ', sub.duracion, ' mes(es)'))
                   ),
                   cs.estado
            FROM cliente_subscripcion cs
            INNER JOIN clientes cli_p ON cli_p.id = cs.cliente_id
            INNER JOIN subscripciones sub ON sub.id = cs.subscripcion_id
            WHERE cli_p.usuario_id = :uid_plan_alta

            UNION ALL

            SELECT 'planes', 'plan_baja', csb.fecha_fin, csb.id, subb.nombre,
                   'Suscripción finalizada', csb.estado
            FROM cliente_subscripcion csb
            INNER JOIN clientes cli_b ON cli_b.id = csb.cliente_id
            INNER JOIN subscripciones subb ON subb.id = csb.subscripcion_id
            WHERE cli_b.usuario_id = :uid_plan_baja
              AND csb.estado = 'C'
              AND csb.fecha_fin IS NOT NULL

            UNION ALL

            SELECT 'fisio', 'cita', ci.fecha, ci.id, IFNULL(f.nombre, 'Fisioterapia'),
                   LEFT(IFNULL(ci.motivo, 'Cita de fisioterapia'), 180), ci.estado
            FROM citas ci
            INNER JOIN clientes cli_f ON cli_f.id = ci.cliente_id
            LEFT JOIN fisioterapeutas f ON f.id = ci.fisio_id
            WHERE cli_f.usuario_id = :uid_fisio
        ";
    }

    /** @return array<string, int> */
    private static function resumen(\PDO $conexion, string $union, int $usuarioId): array
    {
        $stmt = $conexion->prepare('SELECT tipo, COUNT(*) AS n FROM (' . $union . ') t GROUP BY tipo');
        self::bind($stmt, self::usuarioBind($usuarioId));
        $stmt->execute();
        $resumen = ['clases' => 0, 'planes' => 0, 'fisio' => 0];
        foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            $tipo = (string) ($row['tipo'] ?? '');
            if (array_key_exists($tipo, $resumen)) {
                $resumen[$tipo] = (int) $row['n'];
            }
        }

        return $resumen;
    }

    /** @return array<string, int> */
    private static function usuarioBind(int $usuarioId): array
    {
        return [
            ':uid_reserva' => $usuarioId,
            ':uid_comentario' => $usuarioId,
            ':uid_plan_alta' => $usuarioId,
            ':uid_plan_baja' => $usuarioId,
            ':uid_fisio' => $usuarioId,
        ];
    }

    /** @param array<string, int|string> $bind */
    private static function bind(\PDOStatement $stmt, array $bind): void
    {
        foreach ($bind as $key => $value) {
            $stmt->bindValue($key, $value, is_int($value) ? \PDO::PARAM_INT : \PDO::PARAM_STR);
        }
    }
}
