<?php

declare(strict_types=1);

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/actividades.php';

/**
 * Cifras del panel de administración: semana en curso y mes en curso.
 */
class PanelEstadisticas
{
    /** @var list<string> */
    private const COLORES_PLAN = ['#2C3544', '#C45C28', '#3D6B8C', '#6B7C5A', '#8A5A44', '#5C6B7A'];

    /**
     * @return array{
     *   semana_etiqueta: string,
     *   clientes_activos: int,
     *   altas_mes: int,
     *   bajas_mes: int,
     *   ocupacion_pct: int|null,
     *   sesiones: int,
     *   canceladas: int,
     *   citas_pendientes: int,
     *   citas_confirmadas: int,
     *   por_clase: list<array{nombre: string, color: string, inscritos: int, plazas: int, pct: int}>,
     *   por_dia: list<array{etiqueta: string, reservas: int, alto: int}>,
     *   planes: list<array{nombre: string, n: int, color: string, dash: string, offset: float}>,
     *   planes_total: int
     * }
     */
    public static function resumenAdmin(): array
    {
        $db = BasedeDatos::Conectar();
        try {
            $db->exec("UPDATE cliente_subscripcion SET estado = 'C' WHERE estado = 'A' AND fecha_fin IS NOT NULL AND fecha_fin < NOW()");
        } catch (Throwable $e) {
            error_log('[PanelEstadisticas] cerrar caducadas: ' . $e->getMessage());
        }

        $tz = new DateTimeZone(date_default_timezone_get());
        $ahora = new DateTimeImmutable('now', $tz);
        $lunes = $ahora->setTime(0, 0)->modify('-' . ((int) $ahora->format('N') - 1) . ' days');
        $domingo = $lunes->modify('+6 days');
        $lunesSiguiente = $lunes->modify('+7 days');
        $mesInicio = $ahora->modify('first day of this month')->setTime(0, 0);
        $mesFin = $mesInicio->modify('+1 month');

        return self::completar(
            $db,
            $lunes,
            $domingo,
            $lunesSiguiente,
            $lunes->format('Y-m-d'),
            $domingo->format('Y-m-d'),
            $mesInicio,
            $mesFin
        );
    }

    /**
     * @return array<string, mixed>
     */
    private static function completar(
        PDO $db,
        DateTimeImmutable $lunes,
        DateTimeImmutable $domingo,
        DateTimeImmutable $lunesSiguiente,
        string $desde,
        string $hasta,
        DateTimeImmutable $mesInicio,
        DateTimeImmutable $mesFin
    ): array {
        $inscripciones = self::mapaInscripciones($db, $desde, $hasta);
        $canceladas = Actividad::mapaSesionesCanceladas($desde, $hasta);
        $codigos = [1 => 'L', 2 => 'M', 3 => 'X', 4 => 'J', 5 => 'V', 6 => 'S', 7 => 'D'];
        $etiquetas = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];

        $porDia = [];
        for ($i = 0; $i < 7; $i++) {
            $porDia[] = ['etiqueta' => $etiquetas[$i], 'reservas' => 0, 'alto' => 0];
        }

        $porClase = [];
        $inscritosTotal = 0;
        $plazasTotal = 0;
        $sesiones = 0;

        foreach (Actividad::obtenerTodas() as $act) {
            $id = (int) ($act['id'] ?? 0);
            if ($id <= 0) {
                continue;
            }
            $inscritos = 0;
            $plazasSum = 0;
            $sesionesClase = 0;
            for ($i = 0; $i < 7; $i++) {
                $dia = $lunes->modify('+' . $i . ' days');
                $ymd = $dia->format('Y-m-d');
                $cod = $codigos[(int) $dia->format('N')] ?? '';
                if (!self::haySesion($act, $ymd, $cod)) {
                    continue;
                }
                if (isset($canceladas[$id . '_' . $ymd])) {
                    continue;
                }
                $n = $inscripciones[$id . '_' . $ymd] ?? 0;
                $plazas = max(0, (int) ($act['plazas'] ?? 0));
                $inscritos += $n;
                $plazasSum += $plazas;
                $sesionesClase++;
                $porDia[$i]['reservas'] += $n;
            }
            if ($sesionesClase === 0) {
                continue;
            }
            $sesiones += $sesionesClase;
            $inscritosTotal += $inscritos;
            $plazasTotal += $plazasSum;
            $color = Actividad::normalizarColor((string) ($act['color'] ?? '')) ?? '#C45C26';
            $porClase[] = [
                'nombre' => trim((string) ($act['nombre'] ?? '')) !== '' ? trim((string) $act['nombre']) : 'Clase',
                'color' => $color,
                'inscritos' => $inscritos,
                'plazas' => $plazasSum,
                'pct' => $plazasSum > 0 ? (int) round(100 * $inscritos / $plazasSum) : 0,
            ];
        }

        usort($porClase, static function (array $a, array $b): int {
            return [$b['pct'], $a['nombre']] <=> [$a['pct'], $b['nombre']];
        });

        $maxDia = 0;
        foreach ($porDia as $dia) {
            $maxDia = max($maxDia, $dia['reservas']);
        }
        foreach ($porDia as &$dia) {
            $dia['alto'] = $maxDia > 0 ? (int) round(100 * $dia['reservas'] / $maxDia) : 0;
        }
        unset($dia);

        $citas = self::citasSemana($db, $lunes, $lunesSiguiente);
        $planes = self::planesActivos($db);

        return [
            'semana_etiqueta' => self::etiquetaSemana($lunes, $domingo),
            'clientes_activos' => self::clientesActivos($db),
            'altas_mes' => self::contarEntre(
                $db,
                'SELECT COUNT(*) FROM cliente_subscripcion WHERE fecha_inicio >= ? AND fecha_inicio < ?',
                $mesInicio,
                $mesFin
            ),
            'bajas_mes' => self::contarEntre(
                $db,
                "SELECT COUNT(*) FROM cliente_subscripcion WHERE estado = 'C' AND fecha_fin >= ? AND fecha_fin < ?",
                $mesInicio,
                $mesFin
            ),
            'ocupacion_pct' => $sesiones > 0 && $plazasTotal > 0 ? (int) round(100 * $inscritosTotal / $plazasTotal) : ($sesiones > 0 ? 0 : null),
            'sesiones' => $sesiones,
            'canceladas' => count($canceladas),
            'citas_pendientes' => $citas['S'],
            'citas_confirmadas' => $citas['C'],
            'por_clase' => $porClase,
            'por_dia' => $porDia,
            'planes' => $planes['segmentos'],
            'planes_total' => $planes['total'],
        ];
    }

    /**
     * @param array<string, mixed> $act
     */
    private static function haySesion(array $act, string $ymd, string $cod): bool
    {
        if ((int) ($act['recurrente'] ?? 1) === 0) {
            return substr((string) ($act['fecha_inicio'] ?? ''), 0, 10) === $ymd;
        }
        $dias = $act['dias'] ?? [];
        if (!is_array($dias) || $dias === []) {
            $unico = (string) ($act['dia_semana'] ?? '');
            $dias = $unico !== '' ? [$unico] : [];
        }

        return $cod !== '' && in_array($cod, $dias, true);
    }

    /**
     * @return array<string, int>
     */
    private static function mapaInscripciones(PDO $db, string $desde, string $hasta): array
    {
        $st = $db->prepare(
            'SELECT actividad_id, fecha_ocurrencia, COUNT(*) AS n
             FROM inscripciones
             WHERE fecha_ocurrencia BETWEEN ? AND ?
             GROUP BY actividad_id, fecha_ocurrencia'
        );
        $st->execute([$desde, $hasta]);
        $map = [];
        foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $fecha = substr((string) $row['fecha_ocurrencia'], 0, 10);
            $map[(int) $row['actividad_id'] . '_' . $fecha] = (int) $row['n'];
        }

        return $map;
    }

    private static function clientesActivos(PDO $db): int
    {
        $st = $db->query(
            "SELECT COUNT(DISTINCT cliente_id) FROM cliente_subscripcion
             WHERE estado = 'A'
               AND (fecha_inicio IS NULL OR fecha_inicio <= NOW())
               AND (fecha_fin IS NULL OR fecha_fin >= NOW())"
        );

        return (int) $st->fetchColumn();
    }

    private static function contarEntre(PDO $db, string $sql, DateTimeImmutable $desde, DateTimeImmutable $hasta): int
    {
        $st = $db->prepare($sql);
        $st->execute([$desde->format('Y-m-d H:i:s'), $hasta->format('Y-m-d H:i:s')]);

        return (int) $st->fetchColumn();
    }

    /**
     * @return array{S: int, C: int}
     */
    private static function citasSemana(PDO $db, DateTimeImmutable $desde, DateTimeImmutable $hasta): array
    {
        $st = $db->prepare(
            "SELECT estado, COUNT(*) AS n FROM citas
             WHERE fecha >= ? AND fecha < ? AND estado IN ('S', 'C')
             GROUP BY estado"
        );
        $st->execute([$desde->format('Y-m-d H:i:s'), $hasta->format('Y-m-d H:i:s')]);
        $out = ['S' => 0, 'C' => 0];
        foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $estado = (string) ($row['estado'] ?? '');
            if (isset($out[$estado])) {
                $out[$estado] = (int) $row['n'];
            }
        }

        return $out;
    }

    /**
     * @return array{total: int, segmentos: list<array{nombre: string, n: int, color: string, dash: string, offset: float}>}
     */
    private static function planesActivos(PDO $db): array
    {
        $st = $db->query(
            "SELECT s.nombre, COUNT(*) AS n
             FROM cliente_subscripcion cs
             INNER JOIN subscripciones s ON s.id = cs.subscripcion_id
             WHERE cs.estado = 'A'
               AND (cs.fecha_inicio IS NULL OR cs.fecha_inicio <= NOW())
               AND (cs.fecha_fin IS NULL OR cs.fecha_fin >= NOW())
             GROUP BY s.id, s.nombre
             ORDER BY n DESC, s.nombre ASC"
        );
        $filas = $st->fetchAll(PDO::FETCH_ASSOC);
        $total = 0;
        foreach ($filas as $fila) {
            $total += (int) $fila['n'];
        }

        $segmentos = [];
        $acumulado = 0.0;
        $ultimo = count($filas) - 1;
        foreach ($filas as $i => $fila) {
            $n = (int) $fila['n'];
            if ($i === $ultimo) {
                $pct = max(0, 100 - $acumulado);
            } else {
                $pct = $total > 0 ? round(100 * $n / $total, 4) : 0.0;
            }
            $hueco = $pct >= 99.9 ? 100 : round(max(0, 100 - $pct), 4);
            $segmentos[] = [
                'nombre' => trim((string) ($fila['nombre'] ?? '')) !== '' ? trim((string) $fila['nombre']) : 'Plan',
                'n' => $n,
                'color' => self::COLORES_PLAN[$i % count(self::COLORES_PLAN)],
                'dash' => round($pct, 4) . ' ' . $hueco,
                'offset' => round(-$acumulado, 4),
            ];
            $acumulado += $pct;
        }

        return ['total' => $total, 'segmentos' => $segmentos];
    }

    private static function etiquetaSemana(DateTimeImmutable $lunes, DateTimeImmutable $domingo): string
    {
        $meses = [1 => 'ene', 2 => 'feb', 3 => 'mar', 4 => 'abr', 5 => 'may', 6 => 'jun', 7 => 'jul', 8 => 'ago', 9 => 'sep', 10 => 'oct', 11 => 'nov', 12 => 'dic'];
        $a = (int) $lunes->format('j') . ' ' . $meses[(int) $lunes->format('n')];
        $b = (int) $domingo->format('j') . ' ' . $meses[(int) $domingo->format('n')];

        return $a . ' – ' . $b;
    }
}
