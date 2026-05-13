<?php

/**
 * Tickets de gestión de cuenta: recuperar correo, cambiar contraseña o reactivar tras baja normal.
 * Un usuario solo puede tener un ticket pendiente a la vez; caducan a las 48 h.
 * Si el usuario cancela el ticket, debe esperar 48 h antes de abrir otro.
 */
class RecuperacionCuentaTicket
{
    public const HORAS_VALIDEZ = 48;

    public const HORAS_ESPERA_TRAS_CANCEL_USUARIO = 48;

    private static function normalizarTipo(string $tipo): string
    {
        $tipo = strtolower(trim($tipo));
        if (in_array($tipo, ['correo', 'contrasena', 'reactivacion'], true)) {
            return $tipo;
        }

        return 'correo';
    }

    public static function etiquetaTipo(string $tipo): string
    {
        $tipo = self::normalizarTipo($tipo);
        if ($tipo === 'contrasena') {
            return 'Cambiar contraseña';
        }
        if ($tipo === 'reactivacion') {
            return 'Reactivar cuenta';
        }

        return 'Recuperar correo';
    }

    /** Hay ticket pendiente y aún no caducado (48 h desde creación). */
    public static function tieneTicketPendienteVigente(int $usuarioId): bool
    {
        if ($usuarioId <= 0) {
            return false;
        }
        $db = BasedeDatos::Conectar();
        $st = $db->prepare(
            "SELECT 1 FROM recuperacion_cuenta_ticket
             WHERE usuario_id = :uid AND estado = 'pendiente' AND expira_en > NOW()
             LIMIT 1"
        );
        $st->execute([':uid' => $usuarioId]);

        return (bool) $st->fetchColumn();
    }

    /**
     * @return array{ok:bool,error:?string,disponible_desde:?string}
     */
    public static function puedeUsuarioCrearTicket(int $usuarioId): array
    {
        if ($usuarioId <= 0) {
            return ['ok' => false, 'error' => 'Usuario no válido.', 'disponible_desde' => null];
        }

        if (self::tieneTicketPendienteVigente($usuarioId)) {
            return [
                'ok' => false,
                'error' => 'Ya tienes un ticket abierto. Solo puede haber uno por cuenta: espera a que caduque (48 horas desde su creación), complétalo con el código o cancélalo desde la pantalla del ticket. Hasta entonces no puedes crear otro.',
                'disponible_desde' => null,
            ];
        }

        $db = BasedeDatos::Conectar();
        $st = $db->prepare('SELECT ticket_usuario_cancelado_en FROM usuarios WHERE id = ? LIMIT 1');
        $st->execute([$usuarioId]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        $cancelTs = $row['ticket_usuario_cancelado_en'] ?? null;
        if ($cancelTs !== null && $cancelTs !== '') {
            $liberar = strtotime((string) $cancelTs) + (self::HORAS_ESPERA_TRAS_CANCEL_USUARIO * 3600);
            if (time() < $liberar) {
                $fecha = date('d/m/Y \a \l\a\s H:i', $liberar);

                return [
                    'ok' => false,
                    'error' => 'Cancelaste un ticket hace menos de 48 horas. Podrás crear otro el ' . $fecha . '.',
                    'disponible_desde' => $fecha,
                ];
            }
        }

        return ['ok' => true, 'error' => null, 'disponible_desde' => null];
    }

    /**
     * @return array{ok:true,id:int,codigo:string,acceso_token:string,expira_en:string}|array{ok:false,error:string}
     */
    public static function intentarCrear(int $usuarioId, string $tipo = 'correo'): array
    {
        $tipo = self::normalizarTipo($tipo);
        $perm = self::puedeUsuarioCrearTicket($usuarioId);
        if (!$perm['ok']) {
            return ['ok' => false, 'error' => (string) $perm['error']];
        }

        $codigo = self::generarCodigo();
        $token = bin2hex(random_bytes(32));
        $expira = (new DateTimeImmutable('+' . self::HORAS_VALIDEZ . ' hours'))->format('Y-m-d H:i:s');

        $db = BasedeDatos::Conectar();
        $st = $db->prepare(
            'INSERT INTO recuperacion_cuenta_ticket (usuario_id, tipo, codigo, acceso_token, expira_en, estado)
             VALUES (:uid, :tipo, :codigo, :token, :expira, \'pendiente\')'
        );
        $st->execute([
            ':uid' => $usuarioId,
            ':tipo' => $tipo,
            ':codigo' => $codigo,
            ':token' => $token,
            ':expira' => $expira,
        ]);

        return [
            'ok' => true,
            'id' => (int) $db->lastInsertId(),
            'codigo' => $codigo,
            'acceso_token' => $token,
            'expira_en' => $expira,
        ];
    }

    private static function generarCodigo(): string
    {
        $chars = '0123456789';
        $pick = static function () use ($chars): string {
            return $chars[random_int(0, strlen($chars) - 1)];
        };
        $p1 = $pick() . $pick() . $pick();
        $p2 = $pick() . $pick() . $pick();

        return $p1 . '-' . $p2;
    }

    /** @return ?array<string,mixed> */
    public static function obtenerPendientePorId(int $id): ?array
    {
        $db = BasedeDatos::Conectar();
        $st = $db->prepare(
            "SELECT * FROM recuperacion_cuenta_ticket
             WHERE id = :id AND estado = 'pendiente' AND expira_en > NOW()"
        );
        $st->execute([':id' => $id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);

        return $row !== false ? $row : null;
    }

    /** Ticket pendiente vigente del usuario (como máximo uno activo por política de negocio). */
    public static function obtenerPendienteVigentePorUsuario(int $usuarioId): ?array
    {
        if ($usuarioId <= 0) {
            return null;
        }
        $db = BasedeDatos::Conectar();
        $st = $db->prepare(
            "SELECT * FROM recuperacion_cuenta_ticket
             WHERE usuario_id = :uid AND estado = 'pendiente' AND expira_en > NOW()
             ORDER BY id DESC
             LIMIT 1"
        );
        $st->execute([':uid' => $usuarioId]);
        $row = $st->fetch(PDO::FETCH_ASSOC);

        return $row !== false ? $row : null;
    }

    /** @return ?array<string,mixed> */
    public static function obtenerPendientePorToken(string $token): ?array
    {
        $t = strtolower(preg_replace('/[^a-f0-9]/', '', $token) ?? '');
        if (strlen($t) !== 64) {
            return null;
        }
        $db = BasedeDatos::Conectar();
        $st = $db->prepare(
            "SELECT * FROM recuperacion_cuenta_ticket
             WHERE acceso_token = :tok AND estado = 'pendiente' AND expira_en > NOW()"
        );
        $st->execute([':tok' => $t]);
        $row = $st->fetch(PDO::FETCH_ASSOC);

        return $row !== false ? $row : null;
    }

    public static function marcarUsado(int $id): void
    {
        $db = BasedeDatos::Conectar();
        $st = $db->prepare(
            "UPDATE recuperacion_cuenta_ticket SET estado = 'usado' WHERE id = :id AND estado = 'pendiente'"
        );
        $st->execute([':id' => $id]);
    }

    public static function registrarIntentoCodigoFallido(int $id, int $maxIntentos = 5): int
    {
        $db = BasedeDatos::Conectar();
        $st = $db->prepare(
            "UPDATE recuperacion_cuenta_ticket
             SET intentos_codigo = intentos_codigo + 1,
                 estado = IF(intentos_codigo + 1 >= :max, 'cancelado', estado)
             WHERE id = :id AND estado = 'pendiente'"
        );
        $st->bindValue(':max', $maxIntentos, PDO::PARAM_INT);
        $st->bindValue(':id', $id, PDO::PARAM_INT);
        $st->execute();

        $st2 = $db->prepare('SELECT intentos_codigo FROM recuperacion_cuenta_ticket WHERE id = ? LIMIT 1');
        $st2->execute([$id]);

        return (int) $st2->fetchColumn();
    }

    /**
     * El socio cancela su ticket pendiente desde la web. Marca espera de 48 h antes de otro ticket.
     */
    public static function cancelarPorUsuario(int $ticketId, int $usuarioId): bool
    {
        if ($ticketId <= 0 || $usuarioId <= 0) {
            return false;
        }
        $db = BasedeDatos::Conectar();
        try {
            $db->beginTransaction();
            $st = $db->prepare(
                "UPDATE recuperacion_cuenta_ticket
                 SET estado = 'cancelado'
                 WHERE id = :id AND usuario_id = :uid AND estado = 'pendiente' AND expira_en > NOW()"
            );
            $st->execute([':id' => $ticketId, ':uid' => $usuarioId]);
            if ($st->rowCount() < 1) {
                $db->rollBack();

                return false;
            }
            $u = $db->prepare('UPDATE usuarios SET ticket_usuario_cancelado_en = NOW() WHERE id = :uid');
            $u->execute([':uid' => $usuarioId]);
            $db->commit();

            return true;
        } catch (\Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            error_log('[Spartum] cancelarPorUsuario ticket: ' . $e->getMessage());

            return false;
        }
    }

    /** Cierra un ticket pendiente desde el panel (sin completar la recuperación en la web). */
    public static function cerrarPorAdministrador(int $id): bool
    {
        if ($id <= 0) {
            return false;
        }
        $db = BasedeDatos::Conectar();
        $st = $db->prepare(
            "UPDATE recuperacion_cuenta_ticket SET estado = 'cerrado_por_admin' WHERE id = :id AND estado = 'pendiente'"
        );
        $st->execute([':id' => $id]);

        return $st->rowCount() > 0;
    }

    /**
     * Nuevo enlace y ampliación de caducidad al reenviar correo desde administración.
     */
    public static function regenerarAcceso(int $id): ?string
    {
        $db = BasedeDatos::Conectar();
        $token = bin2hex(random_bytes(32));
        $expira = (new DateTimeImmutable('+' . self::HORAS_VALIDEZ . ' hours'))->format('Y-m-d H:i:s');
        $st = $db->prepare(
            "UPDATE recuperacion_cuenta_ticket
             SET acceso_token = :tok, expira_en = :exp
             WHERE id = :id AND estado = 'pendiente'"
        );
        $st->execute([':tok' => $token, ':exp' => $expira, ':id' => $id]);

        return $st->rowCount() > 0 ? $token : null;
    }

    /** @return list<array<string,mixed>> */
    public static function listarPendientes(): array
    {
        $db = BasedeDatos::Conectar();
        $st = $db->query(
            "SELECT t.id, t.tipo, t.codigo, t.acceso_token, t.expira_en, t.creado_en, t.usuario_id,
                    u.nombre, u.apellido1, u.apellido2, u.email, u.telefono, u.DNI
             FROM recuperacion_cuenta_ticket t
             INNER JOIN usuarios u ON u.id = t.usuario_id
             WHERE t.estado = 'pendiente' AND t.expira_en > NOW()
             ORDER BY t.creado_en DESC"
        );

        return $st ? $st->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    /** @return list<array<string,mixed>> */
    public static function listarHistorial(int $limite = 40): array
    {
        $limite = max(1, min(200, $limite));
        $db = BasedeDatos::Conectar();
        $st = $db->prepare(
            'SELECT t.id, t.tipo, t.estado, t.creado_en, t.expira_en, t.usuario_id,
                    u.nombre, u.apellido1, u.apellido2, u.DNI
             FROM recuperacion_cuenta_ticket t
             INNER JOIN usuarios u ON u.id = t.usuario_id
             WHERE t.estado IN (\'usado\',\'cancelado\',\'cerrado_por_admin\')
             ORDER BY t.creado_en DESC
             LIMIT ?'
        );
        $st->bindValue(1, $limite, PDO::PARAM_INT);
        $st->execute();

        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Firma coherente con {@see listarPendientes()} + {@see listarHistorial()} ya cargados.
     *
     * @param list<array<string,mixed>> $pend
     * @param list<array<string,mixed>> $hist
     */
    public static function firmaDesdeListas(array $pend, array $hist): string
    {
        $pendSort = $pend;
        usort($pendSort, static function ($a, $b): int {
            return ((int) ($a['id'] ?? 0)) <=> ((int) ($b['id'] ?? 0));
        });
        $partes = [];
        foreach ($pendSort as $r) {
            $partes[] = 'P:' . (int) ($r['id'] ?? 0)
                . ':' . (string) ($r['estado'] ?? '')
                . ':' . (string) ($r['expira_en'] ?? '')
                . ':' . (string) ($r['codigo'] ?? '')
                . ':' . (string) ($r['acceso_token'] ?? '');
        }
        foreach ($hist as $r) {
            $partes[] = 'H:' . (int) ($r['id'] ?? 0)
                . ':' . (string) ($r['estado'] ?? '')
                . ':' . (string) ($r['creado_en'] ?? '');
        }

        return hash('sha256', implode("\n", $partes));
    }

    /**
     * Firma del estado mostrado en el panel admin de tickets (pendientes + historial reciente).
     * Cambia si el socio o recepción alteran tickets, caducan o se regenera el enlace.
     */
    public static function firmaVistaAdminTickets(int $historialLimite = 50): string
    {
        return self::firmaDesdeListas(self::listarPendientes(), self::listarHistorial(max(1, min(200, $historialLimite))));
    }
}
