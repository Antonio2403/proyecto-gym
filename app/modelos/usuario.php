<?php

class Usuario
{
    private $id;
    private $DNI;
    private $nombre;
    private $apellido1;
    private $apellido2;
    private $email;
    private $clave;
    private $telefono;
    /** 1 = puede iniciar sesión como cliente; 0 = pendiente de confirmar email */
    private $emailConfirmado = 1;

    public function __construct($DNI, $nombre, $apellido1, $apellido2, $email, $clave, $telefono)
    {
        $this->DNI = $DNI;
        $this->nombre = $nombre;
        $this->apellido1 = $apellido1;
        $this->apellido2 = $apellido2;
        $this->email = $email;
        $this->clave = $clave;
        $this->telefono = $telefono;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getDNI()
    {
        return $this->DNI;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getApellido1()
    {
        return $this->apellido1;
    }

    public function getApellido2()
    {
        return $this->apellido2;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getClave()
    {
        return $this->clave;
    }

    public function getTelefono()
    {
        return $this->telefono;
    }

    public function getEmailConfirmado(): int
    {
        return (int) $this->emailConfirmado;
    }

    /**
     * Activa la cuenta si el token coincide y no ha caducado. Devuelve el email o null.
     */
    public static function confirmarEmailConToken(string $tokenPlano): ?string
    {
        $tokenPlano = trim($tokenPlano);
        if (strlen($tokenPlano) < 32) {
            return null;
        }

        $conexion = BasedeDatos::Conectar();
        if (!$conexion) {
            return null;
        }

        $st = $conexion->prepare(
            'SELECT id, email, token_confirmacion_expira FROM usuarios
             WHERE token_confirmacion = :t AND email_confirmado = 0 LIMIT 1'
        );
        $st->execute([':t' => $tokenPlano]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }
        $exp = $row['token_confirmacion_expira'] ?? null;
        if ($exp === null || strtotime((string) $exp) < time()) {
            return null;
        }

        $upd = $conexion->prepare(
            'UPDATE usuarios SET email_confirmado = 1, token_confirmacion = NULL, token_confirmacion_expira = NULL
             WHERE id = :id AND email_confirmado = 0 AND token_confirmacion = :t2'
        );
        $upd->execute([
            ':id' => (int) $row['id'],
            ':t2' => $tokenPlano,
        ]);
        if ($upd->rowCount() < 1) {
            return null;
        }

        return (string) $row['email'];
    }

    /**
     * Inserta usuario + fila de cliente en la conexión indicada (idealmente dentro de una transacción).
     *
     * @param string|null $tokenPlano token aleatorio (se guarda igual en BD); si null, cuenta activa de inmediato
     * @param string|null $tokenExpiraYmdHis fin de validez del token
     */
    public function persistirComoNuevoCliente(\PDO $conexion, ?string $tokenPlano = null, ?string $tokenExpiraYmdHis = null): bool
    {
        try {
            $hashedPassword = password_hash($this->clave, PASSWORD_DEFAULT);
            $confirmado = ($tokenPlano === null || $tokenPlano === '') ? 1 : 0;
            $stmt = $conexion->prepare(
                'INSERT INTO usuarios (DNI, nombre, apellido1, apellido2, email, clave, telefono, password_changed_at, email_confirmado, token_confirmacion, token_confirmacion_expira)
                 VALUES (:DNI, :nombre, :apellido1, :apellido2, :email, :clave, :telefono, NOW(), :email_confirmado, :token_confirmacion, :token_confirmacion_expira)'
            );
            $stmt->bindParam(':DNI', $this->DNI);
            $stmt->bindParam(':nombre', $this->nombre);
            $stmt->bindParam(':apellido1', $this->apellido1);
            $stmt->bindParam(':apellido2', $this->apellido2);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':clave', $hashedPassword);
            $stmt->bindParam(':telefono', $this->telefono);
            $stmt->bindValue(':email_confirmado', $confirmado, PDO::PARAM_INT);
            $stmt->bindValue(':token_confirmacion', $confirmado === 0 ? $tokenPlano : null);
            $stmt->bindValue(':token_confirmacion_expira', $confirmado === 0 ? $tokenExpiraYmdHis : null);
            if (!$stmt->execute()) {
                return false;
            }
            $usuarioId = (int) $conexion->lastInsertId();
            $insCli = $conexion->prepare(
                'INSERT INTO clientes (usuario_id, metodo_pago) VALUES (?, ?)'
            );

            return $insCli->execute([$usuarioId, 'desconocido']);
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function registrar()
    {
        $conexion = BasedeDatos::Conectar();
        if (!$conexion) {
            return false;
        }

        return $this->persistirComoNuevoCliente($conexion);
    }
    public static function obtenerPorId($id)
    {
        $conexion = BasedeDatos::Conectar();
        if ($conexion) {
            try {
                $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE id = :id");
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                $usuarioData = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($usuarioData !== false) {
                    $u = new Usuario(
                        $usuarioData['DNI'],
                        $usuarioData['nombre'],
                        $usuarioData['apellido1'],
                        $usuarioData['apellido2'],
                        $usuarioData['email'],
                        $usuarioData['clave'],
                        $usuarioData['telefono']
                    );
                    $u->id = (int) $usuarioData['id'];
                    $u->emailConfirmado = (int) ($usuarioData['email_confirmado'] ?? 1);

                    return $u;
                }
            } catch (\Throwable $th) {
                error_log('[Usuario] obtenerPorId: ' . $th->getMessage());
                return null;
            }
        } else {
            return null;
        }
    }

    public static function obtenerPorEmail($email)
    {
        $conexion = BasedeDatos::Conectar();

        if ($conexion) {

            try {

                $stmt = $conexion->prepare('SELECT * FROM usuarios WHERE email = :email LIMIT 1');
                $stmt->bindValue(':email', $email, PDO::PARAM_STR);
                $stmt->execute();

                $usuarioData = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($usuarioData !== false) {
                    $usuario = new Usuario(
                        $usuarioData['DNI'],
                        $usuarioData['nombre'],
                        $usuarioData['apellido1'],
                        $usuarioData['apellido2'],
                        $usuarioData['email'],
                        $usuarioData['clave'],
                        $usuarioData['telefono']
                    );

                    $usuario->id = (int) $usuarioData['id'];
                    $usuario->emailConfirmado = (int) ($usuarioData['email_confirmado'] ?? 1);

                    return $usuario;
                }
            } catch (Throwable $th) {
                error_log('[Usuario] obtenerPorEmail: ' . $th->getMessage());
            }
        }

        return null;
    }

    private static function normalizarDocumentoLogin(string $raw): string
    {
        return strtoupper(preg_replace('/[\s-]+/', '', trim($raw)) ?? '');
    }

    /** Busca por email (minúsculas) o por DNI/NIE normalizado. */
    public static function obtenerPorLoginIdentificador(string $raw): ?Usuario
    {
        $t = trim($raw);
        if ($t === '') {
            return null;
        }
        if (fv_email_valido(strtolower($t))) {
            return self::obtenerPorEmail(strtolower($t));
        }
        if (!fv_documento_identidad_es($t)) {
            return null;
        }

        $doc = self::normalizarDocumentoLogin($t);
        $conexion = BasedeDatos::Conectar();
        if (!$conexion) {
            return null;
        }

        try {
            $stmt = $conexion->prepare(
                'SELECT * FROM usuarios
                 WHERE UPPER(REPLACE(REPLACE(DNI, \' \', \'\'), \'-\', \'\')) = :dni
                 LIMIT 1'
            );
            $stmt->execute([':dni' => $doc]);
            $usuarioData = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($usuarioData === false) {
                return null;
            }
            $usuario = new Usuario(
                $usuarioData['DNI'],
                $usuarioData['nombre'],
                $usuarioData['apellido1'],
                $usuarioData['apellido2'],
                $usuarioData['email'],
                $usuarioData['clave'],
                $usuarioData['telefono']
            );
            $usuario->id = (int) $usuarioData['id'];
            $usuario->emailConfirmado = (int) ($usuarioData['email_confirmado'] ?? 1);

            return $usuario;
        } catch (Throwable $th) {
            return null;
        }
    }

    /**
     * Recuperación de cuenta: verifica DNI/NIE y que el teléfono coincida con el guardado.
     *
     * @return array{id: int, email: string}|null
     */
    public static function obtenerIdEmailPorDniYTelefono(string $dniRaw, string $telefonoRaw): ?array
    {
        if (!fv_documento_identidad_es($dniRaw)) {
            return null;
        }
        $tel9 = fv_telefono_es_a_digitos9($telefonoRaw);
        if ($tel9 === null) {
            return null;
        }

        $doc = self::normalizarDocumentoLogin($dniRaw);
        $conexion = BasedeDatos::Conectar();
        if (!$conexion) {
            return null;
        }

        try {
            $stmt = $conexion->prepare(
                'SELECT id, email, telefono FROM usuarios
                 WHERE UPPER(REPLACE(REPLACE(DNI, \' \', \'\'), \'-\', \'\')) = :dni
                 LIMIT 1'
            );
            $stmt->execute([':dni' => $doc]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row === false) {
                return null;
            }
            $telBd = fv_telefono_es_a_digitos9((string) ($row['telefono'] ?? ''));
            if ($telBd === null || $telBd !== $tel9) {
                return null;
            }

            return [
                'id' => (int) $row['id'],
                'email' => strtolower(trim((string) $row['email'])),
            ];
        } catch (Throwable $th) {
            return null;
        }
    }

    /** @return array{password_changed_at: ?string}|null */
    public static function obtenerMetaClave(int $id): ?array
    {
        $conexion = BasedeDatos::Conectar();
        if (!$conexion) {
            return null;
        }
        $st = $conexion->prepare('SELECT password_changed_at FROM usuarios WHERE id = ? LIMIT 1');
        $st->execute([$id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public static function marcarPasswordCambiadaAhora(int $id): void
    {
        $conexion = BasedeDatos::Conectar();
        if (!$conexion) {
            return;
        }
        $st = $conexion->prepare('UPDATE usuarios SET password_changed_at = NOW() WHERE id = ?');
        $st->execute([$id]);
    }

    public static function guardarTokenRecuperacion(int $id, string $token, string $expiraYmdHis): bool
    {
        $conexion = BasedeDatos::Conectar();
        if (!$conexion) {
            return false;
        }
        $st = $conexion->prepare(
            'UPDATE usuarios SET password_reset_token = ?, password_reset_expires = ? WHERE id = ?'
        );

        return $st->execute([$token, $expiraYmdHis, $id]);
    }

    /** @return array{id: int, email: string}|null */
    public static function obtenerPorTokenRecuperacionValido(string $tokenPlano): ?array
    {
        $tokenPlano = trim($tokenPlano);
        if (strlen($tokenPlano) < 32) {
            return null;
        }
        $conexion = BasedeDatos::Conectar();
        if (!$conexion) {
            return null;
        }
        $st = $conexion->prepare(
            'SELECT id, email, password_reset_expires FROM usuarios
             WHERE password_reset_token = ? LIMIT 1'
        );
        $st->execute([$tokenPlano]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }
        $exp = $row['password_reset_expires'] ?? null;
        if ($exp === null || strtotime((string) $exp) < time()) {
            return null;
        }

        return [
            'id' => (int) $row['id'],
            'email' => (string) $row['email'],
        ];
    }

    public static function establecerClaveFuerte(int $id, string $plain): bool
    {
        if (!fv_clave_fuerte($plain)) {
            return false;
        }
        $conexion = BasedeDatos::Conectar();
        if (!$conexion) {
            return false;
        }
        $hash = password_hash($plain, PASSWORD_DEFAULT);
        $st = $conexion->prepare(
            'UPDATE usuarios SET clave = ?, password_changed_at = NOW(),
             password_reset_token = NULL, password_reset_expires = NULL WHERE id = ?'
        );

        return $st->execute([$hash, $id]);
    }

    public static function limpiarTokensRecuperacion(int $id): void
    {
        $conexion = BasedeDatos::Conectar();
        if (!$conexion) {
            return;
        }
        $st = $conexion->prepare(
            'UPDATE usuarios SET password_reset_token = NULL, password_reset_expires = NULL WHERE id = ?'
        );
        $st->execute([$id]);
    }

    public static function actualizar($id, $DNI, $nombre, $apellido1, $apellido2, $email, $clave, $telefono, $especialidad = null, $disponibilidad = null)
    {
        $conexion = BasedeDatos::Conectar();

        if (!$conexion) {
            return false;
        }

        try {

            // QUERY BASE (sin contraseña). $especialidad / $disponibilidad solo los usa Monitor.
            $sql = "UPDATE usuarios 
                SET DNI = :DNI,
                    nombre = :nombre,
                    apellido1 = :apellido1,
                    apellido2 = :apellido2,
                    email = :email,
                    telefono = :telefono";

            // SI HAY CONTRASEÑA → se añade y se registra fecha de cambio
            if (!empty($clave)) {
                $sql .= ", clave = :clave, password_changed_at = NOW()";
            }

            $sql .= " WHERE id = :id";

            $stmt = $conexion->prepare($sql);

            // BINDS
            $stmt->bindParam(':DNI', $DNI);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':apellido1', $apellido1);
            $stmt->bindParam(':apellido2', $apellido2);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':telefono', $telefono);
            $stmt->bindParam(':id', $id);

            // SOLO si hay contraseña
            if (!empty($clave)) {
                $hashedPassword = password_hash($clave, PASSWORD_DEFAULT);
                $stmt->bindParam(':clave', $hashedPassword);
            }

            return $stmt->execute();
        } catch (Throwable $th) {
            error_log('[Usuario] actualizar: ' . $th->getMessage());
            return false;
        }
    }

    /**
     * @return array{bloqueado: bool, tipo: string, hasta: ?string, motivo: ?string}
     */
    public static function estadoBloqueo(int $id): array
    {
        $conexion = BasedeDatos::Conectar();
        $default = ['bloqueado' => false, 'tipo' => 'N', 'hasta' => null, 'motivo' => null];
        if (!$conexion) {
            return $default;
        }

        try {
            $stmt = $conexion->prepare('SELECT bloqueo_tipo, bloqueado_hasta, bloqueo_motivo FROM usuarios WHERE id = ? LIMIT 1');
            $stmt->execute([$id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) {
                return $default;
            }

            $tipo = (string) ($row['bloqueo_tipo'] ?? 'N');
            $hasta = $row['bloqueado_hasta'] ?? null;
            if ($tipo === 'T' && $hasta !== null && strtotime((string) $hasta) !== false && strtotime((string) $hasta) <= time()) {
                self::desbloquear($id);
                return $default;
            }

            return [
                'bloqueado' => in_array($tipo, ['T', 'P'], true),
                'tipo' => $tipo,
                'hasta' => $hasta !== null ? (string) $hasta : null,
                'motivo' => isset($row['bloqueo_motivo']) ? (string) $row['bloqueo_motivo'] : null,
            ];
        } catch (Throwable $th) {
            error_log('[Usuario] estadoBloqueo: ' . $th->getMessage());
            return $default;
        }
    }

    public static function bloquear(int $id, string $tipo, ?string $hasta = null, ?string $motivo = null): bool
    {
        if (!in_array($tipo, ['T', 'P'], true)) {
            return false;
        }

        $conexion = BasedeDatos::Conectar();
        if (!$conexion) {
            return false;
        }

        try {
            $stmt = $conexion->prepare(
                'UPDATE usuarios SET bloqueo_tipo = :tipo, bloqueado_hasta = :hasta, bloqueo_motivo = :motivo WHERE id = :id'
            );
            $stmt->bindValue(':tipo', $tipo);
            $stmt->bindValue(':hasta', $tipo === 'T' ? $hasta : null);
            $stmt->bindValue(':motivo', $motivo);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (Throwable $th) {
            error_log('[Usuario] bloquear: ' . $th->getMessage());
            return false;
        }
    }

    public static function desbloquear(int $id): bool
    {
        $conexion = BasedeDatos::Conectar();
        if (!$conexion) {
            return false;
        }

        try {
            $stmt = $conexion->prepare(
                "UPDATE usuarios SET bloqueo_tipo = 'N', bloqueado_hasta = NULL, bloqueo_motivo = NULL WHERE id = ?"
            );
            return $stmt->execute([$id]);
        } catch (Throwable $th) {
            error_log('[Usuario] desbloquear: ' . $th->getMessage());
            return false;
        }
    }
    
    public static function eliminar($id)
    {
        $conexion = BasedeDatos::Conectar();

        if ($conexion) {
            try {
                $stmt = $conexion->prepare("DELETE FROM usuarios WHERE id = :id");
                $stmt->bindParam(':id', $id);
                return $stmt->execute();
            } catch (Throwable $th) {
                error_log('[Usuario] eliminar: ' . $th->getMessage());
                return false;
            }
        } else {
            return false;
        }
    }
}
