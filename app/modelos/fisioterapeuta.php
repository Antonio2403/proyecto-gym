<?php

class Fisioterapeuta
{
    public static function obtenerTodas(): array
    {
        $db = BasedeDatos::Conectar();
        $st = $db->query('SELECT id, nombre, especialidad FROM fisioterapeutas ORDER BY nombre ASC');

        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerPorId(int $id): ?array
    {
        $db = BasedeDatos::Conectar();
        $st = $db->prepare(
            'SELECT id, nombre, especialidad, usuario_id FROM fisioterapeutas WHERE id = ? LIMIT 1'
        );
        $st->execute([$id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public static function obtenerPorUsuarioId(int $usuarioId): ?array
    {
        $db = BasedeDatos::Conectar();
        $st = $db->prepare(
            'SELECT id, nombre, especialidad, usuario_id FROM fisioterapeutas WHERE usuario_id = ? LIMIT 1'
        );
        $st->execute([$usuarioId]);
        $row = $st->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public static function obtenerEmailAcceso(int $fisioId): ?string
    {
        $db = BasedeDatos::Conectar();
        $st = $db->prepare(
            'SELECT u.email FROM fisioterapeutas f INNER JOIN usuarios u ON u.id = f.usuario_id WHERE f.id = ? LIMIT 1'
        );
        $st->execute([$fisioId]);
        $mail = $st->fetchColumn();

        return $mail ? (string) $mail : null;
    }

    /**
     * @return array{ok: bool, usuario_id?: int|null, error?: string}
     */
    public static function resolverVinculoUsuario(?string $email, ?int $exceptFisioId): array
    {
        $email = $email !== null ? strtolower(trim($email)) : '';
        if ($email === '') {
            return ['ok' => true, 'usuario_id' => null];
        }

        if (!fv_email_valido($email)) {
            return ['ok' => false, 'error' => 'El email de acceso no es válido.'];
        }

        $db = BasedeDatos::Conectar();
        $st = $db->prepare('SELECT id FROM usuarios WHERE email = ? LIMIT 1');
        $st->execute([$email]);
        $uid = $st->fetchColumn();
        if (!$uid) {
            return ['ok' => false, 'error' => 'No existe un usuario registrado con ese email.'];
        }
        $usuarioId = (int) $uid;

        $chk = $db->prepare('SELECT id FROM administradores WHERE usuario_id = ? LIMIT 1');
        $chk->execute([$usuarioId]);
        if ($chk->fetchColumn()) {
            return ['ok' => false, 'error' => 'Ese usuario es administrador; no puede vincularse como fisioterapeuta.'];
        }

        $chk = $db->prepare('SELECT id FROM monitores WHERE usuario_id = ? LIMIT 1');
        $chk->execute([$usuarioId]);
        if ($chk->fetchColumn()) {
            return ['ok' => false, 'error' => 'Ese usuario es monitor; no puede vincularse como fisioterapeuta.'];
        }

        $chk = $db->prepare('SELECT id FROM clientes WHERE usuario_id = ? LIMIT 1');
        $chk->execute([$usuarioId]);
        if ($chk->fetchColumn()) {
            return ['ok' => false, 'error' => 'Ese usuario es socio; use una cuenta distinta para el panel de fisioterapeuta.'];
        }

        $sql = 'SELECT id FROM fisioterapeutas WHERE usuario_id = ?';
        $params = [$usuarioId];
        if ($exceptFisioId !== null && $exceptFisioId > 0) {
            $sql .= ' AND id <> ?';
            $params[] = $exceptFisioId;
        }
        $sql .= ' LIMIT 1';
        $chk = $db->prepare($sql);
        $chk->execute($params);
        if ($chk->fetchColumn()) {
            return ['ok' => false, 'error' => 'Ese email ya está vinculado a otro fisioterapeuta.'];
        }

        return ['ok' => true, 'usuario_id' => $usuarioId];
    }

    /**
     * @return array{rows: array<int, array<string,mixed>>, total: int, page: int, per_page: int, total_pages: int}
     */
    public static function buscarPaginado(int $page, int $perPage, array $f): array
    {
        $page = max(1, $page);
        $perPage = min(50, max(5, $perPage));

        $db = BasedeDatos::Conectar();
        $bind = [];
        $conds = [];

        $qLike = gp_grid_like_contains(gp_grid_str($f['q'] ?? null));
        if ($qLike !== null) {
            $conds[] = '(nombre LIKE :q OR IFNULL(especialidad, \'\') LIKE :q)';
            $bind[':q'] = $qLike;
        }
        if (($nom = gp_grid_like_contains(gp_grid_str($f['nombre'] ?? null))) !== null) {
            $conds[] = 'nombre LIKE :fn';
            $bind[':fn'] = $nom;
        }
        if (($esp = gp_grid_like_contains(gp_grid_str($f['especialidad'] ?? null))) !== null) {
            $conds[] = 'IFNULL(especialidad, \'\') LIKE :fe';
            $bind[':fe'] = $esp;
        }

        $where = $conds !== [] ? ' WHERE ' . implode(' AND ', $conds) : '';

        $st = $db->prepare('SELECT COUNT(*) FROM fisioterapeutas' . $where);
        foreach ($bind as $k => $v) {
            $st->bindValue($k, $v);
        }
        $st->execute();
        $total = (int) $st->fetchColumn();

        $totalPages = $total > 0 ? max(1, (int) ceil($total / $perPage)) : 1;
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $perPage;

        $sql = 'SELECT id, nombre, especialidad FROM fisioterapeutas' . $where . ' ORDER BY nombre ASC LIMIT :lim OFFSET :off';
        $st = $db->prepare($sql);
        foreach ($bind as $k => $v) {
            $st->bindValue($k, $v);
        }
        $st->bindValue(':lim', $perPage, PDO::PARAM_INT);
        $st->bindValue(':off', $offset, PDO::PARAM_INT);
        $st->execute();
        $rows = $st->fetchAll(PDO::FETCH_ASSOC);

        return [
            'rows' => $rows,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => $totalPages,
        ];
    }

    public static function crear(string $nombre, ?string $especialidad, ?int $usuarioId = null): int|false
    {
        $db = BasedeDatos::Conectar();
        $st = $db->prepare('INSERT INTO fisioterapeutas (nombre, especialidad, usuario_id) VALUES (?, ?, ?)');

        return $st->execute([$nombre, $especialidad, $usuarioId]) ? (int) $db->lastInsertId() : false;
    }

    public static function actualizar(int $id, string $nombre, ?string $especialidad, ?int $usuarioId): bool
    {
        $db = BasedeDatos::Conectar();
        $st = $db->prepare(
            'UPDATE fisioterapeutas SET nombre = ?, especialidad = ?, usuario_id = ? WHERE id = ?'
        );

        return $st->execute([$nombre, $especialidad, $usuarioId, $id]);
    }

    /**
     * Citas enlazadas a este fisioterapeuta.
     */
    public static function contarCitas(int $id): int
    {
        $db = BasedeDatos::Conectar();
        $st = $db->prepare('SELECT COUNT(*) FROM citas WHERE fisio_id = ?');
        $st->execute([$id]);

        return (int) $st->fetchColumn();
    }

    /**
     * Borra la fila. No usar si contarCitas > 0 (violación FK o pérdida de vínculos).
     */
    public static function eliminar(int $id): bool
    {
        $db = BasedeDatos::Conectar();
        $st = $db->prepare('DELETE FROM fisioterapeutas WHERE id = ?');

        return $st->execute([$id]) && $st->rowCount() > 0;
    }
}
