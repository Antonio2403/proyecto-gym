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
                'INSERT INTO usuarios (DNI, nombre, apellido1, apellido2, email, clave, telefono, email_confirmado, token_confirmacion, token_confirmacion_expira)
                 VALUES (:DNI, :nombre, :apellido1, :apellido2, :email, :clave, :telefono, :email_confirmado, :token_confirmacion, :token_confirmacion_expira)'
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
                if ($stmt->rowCount() == 1) {
                    $usuarioData = $stmt->fetch(PDO::FETCH_ASSOC);
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
                echo "Error al obtener el usuario: " . $th->getMessage();
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

                $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE email = :email");
                $stmt->bindParam(':email', $email);
                $stmt->execute();

                if ($stmt->rowCount() == 1) {

                    $usuarioData = $stmt->fetch(PDO::FETCH_ASSOC);

                    $usuario = new Usuario(
                        $usuarioData['DNI'],
                        $usuarioData['nombre'],
                        $usuarioData['apellido1'],
                        $usuarioData['apellido2'],
                        $usuarioData['email'],
                        $usuarioData['clave'],
                        $usuarioData['telefono']
                    );

                    $usuario->id = $usuarioData['id'];
                    $usuario->emailConfirmado = (int) ($usuarioData['email_confirmado'] ?? 1);

                    return $usuario;
                }
            } catch (Throwable $th) {

                echo "Error al obtener usuario: " . $th->getMessage();
            }
        }

        return null;
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

            // SI HAY CONTRASEÑA → se añade
            if (!empty($clave)) {
                $sql .= ", clave = :clave";
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
            echo "Error al actualizar el usuario: " . $th->getMessage();
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
                echo "Error al eliminar el usuario: " . $th->getMessage();
                return false;
            }
        } else {
            return false;
        }
    }
}
