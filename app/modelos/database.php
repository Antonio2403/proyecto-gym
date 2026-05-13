<?php

class BasedeDatos
{
    private static function envString(string $key, string $default = ''): string
    {
        $v = $_ENV[$key] ?? getenv($key);
        if ($v === false || $v === null) {
            return $default;
        }
        $v = (string) $v;

        return $v;
    }

    private static function dbName(): string
    {
        $v = self::envString('DB_DATABASE', 'spartum');

        return $v !== '' ? $v : 'spartum';
    }

    private static function dbUser(): string
    {
        $v = self::envString('DB_USERNAME', 'root');

        return $v !== '' ? $v : 'root';
    }

    private static function dbPass(): string
    {
        return self::envString('DB_PASSWORD', 'root');
    }

    private static function autoSetupEnabled(): bool
    {
        $flag = strtolower(trim(self::envString('DB_AUTO_SETUP', '')));
        if ($flag !== '') {
            return !in_array($flag, ['0', 'false', 'no', 'off'], true);
        }

        return strtolower(trim(self::envString('APP_ENV', 'local'))) !== 'production';
    }

    /**
     * DSN PDO. Por defecto usa 127.0.0.1 (TCP): en Linux, "localhost" fuerza socket Unix
     * y en Docker suele fallar con SQLSTATE[HY000] [2002] No such file or directory.
     */
    private static function pdoDsn(bool $withDbName): string
    {
        $charset = 'utf8';
        $socket = self::envString('DB_SOCKET', '');
        if ($socket !== '') {
            return $withDbName
                ? 'mysql:unix_socket=' . $socket . ';dbname=' . self::dbName() . ';charset=' . $charset
                : 'mysql:unix_socket=' . $socket . ';charset=' . $charset;
        }

        $host = self::envString('DB_HOST', '');
        if ($host === 'localhost') {
            $host = '127.0.0.1';
        }

        $port = self::envString('DB_PORT', '3306');
        if ($port === '' || !ctype_digit($port)) {
            $port = '3306';
        }

        $base = 'mysql:host=' . $host . ';port=' . $port;

        return $withDbName
            ? $base . ';dbname=' . self::dbName() . ';charset=' . $charset
            : $base . ';charset=' . $charset;
    }

    public static function Conectar()
    {
        try {
            $conexion = new PDO(self::pdoDsn(false), self::dbUser(), self::dbPass());
            $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $conexion->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

            if (!self::autoSetupEnabled()) {
                $conexionDB = new PDO(self::pdoDsn(true), self::dbUser(), self::dbPass());
                $conexionDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $conexionDB->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

                return $conexionDB;
            }

            $dbName = self::dbName();
            $stmt = $conexion->query("SHOW DATABASES LIKE '" . str_replace("'", "''", $dbName) . "'");
            if ($stmt === false || $stmt->fetch(PDO::FETCH_NUM) === false) {
                $conexion->exec(
                    'CREATE DATABASE `' . str_replace('`', '``', $dbName) . '` CHARACTER SET utf8 COLLATE utf8_general_ci'
                );
            }

            $conexionDB = new PDO(self::pdoDsn(true), self::dbUser(), self::dbPass());

            $conexionDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $conexionDB->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

            $tbl = 'ENGINE=InnoDB DEFAULT CHARSET=utf8';

            $conexionDB->exec("
                CREATE TABLE IF NOT EXISTS usuarios (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    DNI VARCHAR(9) NOT NULL,
                    nombre VARCHAR(100) NOT NULL,
                    apellido1 VARCHAR(100) NOT NULL,
                    apellido2 VARCHAR(100) DEFAULT NULL,
                    email VARCHAR(255) NOT NULL,
                    clave VARCHAR(255) NOT NULL,
                    password_changed_at DATETIME DEFAULT NULL,
                    password_reset_token VARCHAR(64) DEFAULT NULL,
                    password_reset_expires DATETIME DEFAULT NULL,
                    telefono VARCHAR(20) DEFAULT NULL,
                    avatar_path VARCHAR(255) DEFAULT NULL,
                    bloqueo_tipo ENUM('N','T','P') NOT NULL DEFAULT 'N',
                    bloqueado_hasta DATETIME DEFAULT NULL,
                    bloqueo_motivo VARCHAR(255) DEFAULT NULL,
                    ticket_usuario_cancelado_en DATETIME DEFAULT NULL,
                    email_confirmado TINYINT(1) NOT NULL DEFAULT 1,
                    token_confirmacion VARCHAR(64) DEFAULT NULL,
                    token_confirmacion_expira DATETIME DEFAULT NULL,
                    UNIQUE KEY uq_usuarios_dni (DNI),
                    UNIQUE KEY uq_usuarios_email (email),
                    KEY idx_usu_token (token_confirmacion),
                    KEY idx_usu_pwreset (password_reset_token)
                ) $tbl
            ");

            $conexionDB->exec("
                CREATE TABLE IF NOT EXISTS administradores (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    usuario_id INT DEFAULT NULL,
                    nivel_acceso VARCHAR(50) DEFAULT NULL,
                    UNIQUE KEY uq_admin_usuario (usuario_id),
                    CONSTRAINT fk_admin_usuario FOREIGN KEY (usuario_id)
                        REFERENCES usuarios(id) ON DELETE CASCADE
                ) $tbl
            ");

            $conexionDB->exec("
                CREATE TABLE IF NOT EXISTS monitores (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    usuario_id INT DEFAULT NULL,
                    especialidad VARCHAR(100) DEFAULT NULL,
                    disponibilidad VARCHAR(100) DEFAULT NULL,
                    UNIQUE KEY uq_monitor_usuario (usuario_id),
                    CONSTRAINT fk_monitor_usuario FOREIGN KEY (usuario_id)
                        REFERENCES usuarios(id) ON DELETE CASCADE
                ) $tbl
            ");

            $conexionDB->exec("
                CREATE TABLE IF NOT EXISTS clientes (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    usuario_id INT DEFAULT NULL,
                    metodo_pago VARCHAR(50) DEFAULT NULL,
                    UNIQUE KEY uq_cliente_usuario (usuario_id),
                    CONSTRAINT fk_cliente_usuario FOREIGN KEY (usuario_id)
                        REFERENCES usuarios(id) ON DELETE CASCADE
                ) $tbl
            ");

            $adminEmailInicial = self::envString('INITIAL_ADMIN_EMAIL', 'alfonsojaime02@gmail.com');
            $adminPasswordInicial = self::envString('INITIAL_ADMIN_PASSWORD', '');
            if ($adminPasswordInicial === '') {
                $adminPasswordInicial = bin2hex(random_bytes(12)) . 'Aa1!';
                error_log('[Spartum] Admin inicial creado con contraseña aleatoria. Usa scripts/reset_admin_password.php para fijarla.');
            }
            $password = password_hash($adminPasswordInicial, PASSWORD_DEFAULT);
            $stmtCheck = $conexionDB->prepare('SELECT id FROM usuarios WHERE email = ? LIMIT 1');
            $stmtCheck->execute([$adminEmailInicial]);
            if ($stmtCheck->fetch(PDO::FETCH_ASSOC) === false) {
                $stmt = $conexionDB->prepare("
                    INSERT INTO usuarios (DNI, nombre, apellido1, apellido2, email, clave, telefono, password_changed_at, email_confirmado)
                    VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), 1)
                ");
                $stmt->execute(['12345678Z', 'Admin', 'Gym', '', $adminEmailInicial, $password, '000000000']);

                $usuario_id = $conexionDB->lastInsertId();

                $stmt2 = $conexionDB->prepare("
                    INSERT INTO administradores (usuario_id, nivel_acceso)
                    VALUES (?, ?)
                ");
                $stmt2->execute([$usuario_id, 'admin']);
            }

            $conexionDB->exec("
                CREATE TABLE IF NOT EXISTS salas (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    nombre VARCHAR(100) DEFAULT NULL,
                    capacidad INT DEFAULT NULL,
                    disponibilidad ENUM('L','U','R') DEFAULT NULL
                ) $tbl
            ");

            $conexionDB->exec("
                CREATE TABLE IF NOT EXISTS solicitudes (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    monitor_id INT DEFAULT NULL,
                    admin_id INT DEFAULT NULL,
                    tipo VARCHAR(100) DEFAULT NULL,
                    descripcion TEXT,
                    estado ENUM('P','A','R') NOT NULL DEFAULT 'P',
                    fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    fecha_revision DATETIME DEFAULT NULL,
                    KEY idx_sol_monitor (monitor_id),
                    KEY idx_sol_admin (admin_id),
                    CONSTRAINT fk_sol_monitor FOREIGN KEY (monitor_id) REFERENCES monitores(id),
                    CONSTRAINT fk_sol_admin FOREIGN KEY (admin_id) REFERENCES administradores(id)
                ) $tbl
            ");

            $conexionDB->exec("
                CREATE TABLE IF NOT EXISTS actividades (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    nombre VARCHAR(100) DEFAULT NULL,
                    duracion INT DEFAULT NULL,
                    monitor_id INT DEFAULT NULL,
                    sala_id INT DEFAULT NULL,
                    fecha_inicio DATETIME DEFAULT NULL,
                    fecha_fin DATETIME DEFAULT NULL,
                    dia_semana ENUM('L','M','X','J','V','S','D') DEFAULT NULL,
                    descripcion TEXT,
                    plazas INT NOT NULL DEFAULT 20,
                    recurrente TINYINT(1) NOT NULL DEFAULT 1,
                    KEY idx_act_monitor (monitor_id),
                    KEY idx_act_sala (sala_id),
                    CONSTRAINT fk_act_monitor FOREIGN KEY (monitor_id) REFERENCES monitores(id),
                    CONSTRAINT fk_act_sala FOREIGN KEY (sala_id) REFERENCES salas(id)
                ) $tbl
            ");

            $conexionDB->exec("
                CREATE TABLE IF NOT EXISTS inscripciones (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    cliente_id INT DEFAULT NULL,
                    actividad_id INT DEFAULT NULL,
                    fecha_ocurrencia DATE DEFAULT NULL,
                    asistio ENUM('S','N') NOT NULL DEFAULT 'S',
                    fecha_inscripcion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    KEY idx_ins_cliente (cliente_id),
                    KEY idx_ins_actividad (actividad_id),
                    UNIQUE KEY uq_ins_cliente_sesion (cliente_id, actividad_id, fecha_ocurrencia),
                    CONSTRAINT fk_ins_cliente FOREIGN KEY (cliente_id) REFERENCES clientes(id),
                    CONSTRAINT fk_ins_actividad FOREIGN KEY (actividad_id) REFERENCES actividades(id)
                ) $tbl
            ");

            $conexionDB->exec("
                CREATE TABLE IF NOT EXISTS comentarios (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    cliente_id INT DEFAULT NULL,
                    actividad_id INT DEFAULT NULL,
                    fecha_ocurrencia DATE DEFAULT NULL,
                    texto TEXT,
                    fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    KEY idx_com_act_fecha (actividad_id, fecha_ocurrencia),
                    CONSTRAINT fk_com_cliente FOREIGN KEY (cliente_id) REFERENCES clientes(id),
                    CONSTRAINT fk_com_actividad FOREIGN KEY (actividad_id) REFERENCES actividades(id)
                ) $tbl
            ");

            $conexionDB->exec("
                CREATE TABLE IF NOT EXISTS feedback (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    nombre VARCHAR(150) NOT NULL,
                    email VARCHAR(255) NOT NULL,
                    asunto VARCHAR(255) NOT NULL,
                    mensaje TEXT NOT NULL,
                    fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
                ) $tbl
            ");

            $conexionDB->exec("
                CREATE TABLE IF NOT EXISTS subscripciones (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    nombre VARCHAR(100) DEFAULT NULL,
                    precio DECIMAL(10,2) DEFAULT NULL,
                    duracion INT DEFAULT NULL,
                    numero_clases INT DEFAULT NULL,
                    fisio ENUM('S','N') NOT NULL DEFAULT 'N',
                    en_oferta TINYINT(1) NOT NULL DEFAULT 0,
                    oferta_motivo VARCHAR(120) DEFAULT NULL,
                    oferta_fin DATETIME DEFAULT NULL,
                    estado ENUM('A','I') NOT NULL DEFAULT 'A'
                ) $tbl
            ");

            $stmtCheck = $conexionDB->prepare("SELECT id FROM subscripciones WHERE nombre = ?");
            $stmtSub = $conexionDB->prepare(
                "INSERT INTO subscripciones (nombre, precio, duracion, numero_clases, fisio, estado) VALUES (?, ?, ?, ?, ?, ?)"
            );
            $subscripciones = [
                ['Normal', 29.99, 1, 2, 'N', 'A'],
                ['Pro', 49.99, 2, 4, 'N', 'A'],
                ['MegaPro', 79.99, 3, 8, 'S', 'A'],
            ];
            foreach ($subscripciones as $sub) {
                $stmtCheck->execute([$sub[0]]);
                if ($stmtCheck->fetch(PDO::FETCH_ASSOC) === false) {
                    $stmtSub->execute($sub);
                }
            }

            $conexionDB->exec("
                CREATE TABLE IF NOT EXISTS cliente_subscripcion (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    cliente_id INT DEFAULT NULL,
                    subscripcion_id INT DEFAULT NULL,
                    fecha_inicio DATETIME DEFAULT NULL,
                    fecha_fin DATETIME DEFAULT NULL,
                    estado ENUM('A','C') NOT NULL DEFAULT 'A',
                    KEY idx_cs_cliente (cliente_id),
                    KEY idx_cs_sub (subscripcion_id),
                    CONSTRAINT fk_cs_cliente FOREIGN KEY (cliente_id) REFERENCES clientes(id),
                    CONSTRAINT fk_cs_sub FOREIGN KEY (subscripcion_id) REFERENCES subscripciones(id)
                ) $tbl
            ");

            $conexionDB->exec("
                CREATE TABLE IF NOT EXISTS fisioterapeutas (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    nombre VARCHAR(100) DEFAULT NULL,
                    especialidad VARCHAR(100) DEFAULT NULL,
                    usuario_id INT DEFAULT NULL,
                    UNIQUE KEY uq_fisio_usuario (usuario_id),
                    CONSTRAINT fk_fisio_usuario FOREIGN KEY (usuario_id)
                        REFERENCES usuarios(id) ON DELETE SET NULL
                ) $tbl
            ");

            $conexionDB->exec("
                CREATE TABLE IF NOT EXISTS citas (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    cliente_id INT DEFAULT NULL,
                    fisio_id INT DEFAULT NULL,
                    fecha DATETIME DEFAULT NULL,
                    motivo TEXT,
                    estado ENUM('S','C','A','CA') DEFAULT NULL,
                    CONSTRAINT fk_cita_cliente FOREIGN KEY (cliente_id) REFERENCES clientes(id),
                    CONSTRAINT fk_cita_fisio FOREIGN KEY (fisio_id) REFERENCES fisioterapeutas(id)
                ) $tbl
            ");

            $conexionDB->exec("
                CREATE TABLE IF NOT EXISTS materiales (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    sala_id INT NOT NULL,
                    nombre VARCHAR(100) DEFAULT NULL,
                    estado ENUM('B','M') DEFAULT NULL,
                    KEY idx_mat_sala (sala_id),
                    CONSTRAINT fk_mat_sala FOREIGN KEY (sala_id) REFERENCES salas(id) ON DELETE CASCADE
                ) $tbl
            ");

            self::ensureSchemaUpgrades($conexionDB);

            return $conexionDB;
        } catch (\Throwable $th) {
            $hint = ' Revisa DB_HOST y DB_PORT en .env (en Docker suele ser el nombre del servicio MySQL, p. ej. mysql, y DB_PASSWORD el definido en compose).';
            if (strpos($th->getMessage(), '2002') !== false) {
                $hint = ' En Docker el host suele ser el nombre del servicio en docker-compose (p. ej. `db`), no 127.0.0.1. Reconstruye la imagen PHP si añadiste PassEnv al Dockerfile; en .env local no fuerces DB_HOST=127.0.0.1 si montas el mismo archivo dentro del contenedor.';
            }
            throw new \RuntimeException('Error BD: ' . $th->getMessage() . '.' . $hint, 0, $th);
        }
    }

    private static function columnExists(PDO $db, string $table, string $column): bool
    {
        $st = $db->prepare(
            'SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?'
        );
        $st->execute([$table, $column]);

        return (int) $st->fetchColumn() > 0;
    }

    /** Comprueba si existe una restricción con ese nombre en la tabla (CHECK, FOREIGN KEY, etc.). */
    private static function tableConstraintExists(PDO $db, string $table, string $constraintName): bool
    {
        try {
            $st = $db->prepare(
                'SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
                 WHERE CONSTRAINT_SCHEMA = DATABASE()
                   AND TABLE_NAME = ?
                   AND CONSTRAINT_NAME = ?'
            );
            $st->execute([$table, $constraintName]);

            return (int) $st->fetchColumn() > 0;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Restriciones CHECK de integridad (MySQL 8.0.16+ / MariaDB 10.2.1+ con InnoDB).
     * Refuerza formato básico en BD; la letra de control DNI/NIE y reglas de negocio siguen validándose en PHP.
     */
    private static function ensureIntegrityCheckConstraints(PDO $db): void
    {
        if (!self::tableConstraintExists($db, 'usuarios', 'chk_gp_usuarios_dni')) {
            try {
                $db->exec(
                    "ALTER TABLE usuarios ADD CONSTRAINT chk_gp_usuarios_dni
                     CHECK (DNI REGEXP '^[0-9]{8}[A-Za-z]$|^[XYZ][0-9]{7}[A-Za-z]$')"
                );
            } catch (\Throwable $e) {
                error_log('[Spartum BD] chk_gp_usuarios_dni: ' . $e->getMessage());
            }
        }
        if (!self::tableConstraintExists($db, 'usuarios', 'chk_gp_usuarios_email')) {
            try {
                $db->exec(
                    "ALTER TABLE usuarios ADD CONSTRAINT chk_gp_usuarios_email
                     CHECK (
                         CHAR_LENGTH(email) BETWEEN 3 AND 255
                         AND email REGEXP '^[^[:space:]@]+@[^[:space:]@]+[.][^[:space:]@]+$'
                     )"
                );
            } catch (\Throwable $e) {
                error_log('[Spartum BD] chk_gp_usuarios_email: ' . $e->getMessage());
            }
        }
        if (!self::tableConstraintExists($db, 'usuarios', 'chk_gp_usuarios_email_confirmado')) {
            try {
                $db->exec(
                    'ALTER TABLE usuarios ADD CONSTRAINT chk_gp_usuarios_email_confirmado
                     CHECK (email_confirmado IN (0, 1))'
                );
            } catch (\Throwable $e) {
                error_log('[Spartum BD] chk_gp_usuarios_email_confirmado: ' . $e->getMessage());
            }
        }
        if (!self::tableConstraintExists($db, 'recuperacion_cuenta_ticket', 'chk_gp_rec_intentos')) {
            try {
                $db->exec(
                    'ALTER TABLE recuperacion_cuenta_ticket ADD CONSTRAINT chk_gp_rec_intentos
                     CHECK (intentos_codigo BETWEEN 0 AND 50)'
                );
            } catch (\Throwable $e) {
                error_log('[Spartum BD] chk_gp_rec_intentos: ' . $e->getMessage());
            }
        }
        if (!self::tableConstraintExists($db, 'recuperacion_cuenta_ticket', 'chk_gp_rec_codigo_len')) {
            try {
                $db->exec(
                    'ALTER TABLE recuperacion_cuenta_ticket ADD CONSTRAINT chk_gp_rec_codigo_len
                     CHECK (CHAR_LENGTH(codigo) BETWEEN 1 AND 32)'
                );
            } catch (\Throwable $e) {
                error_log('[Spartum BD] chk_gp_rec_codigo_len: ' . $e->getMessage());
            }
        }
        if (!self::tableConstraintExists($db, 'subscripciones', 'chk_gp_sub_precio')) {
            try {
                $db->exec(
                    'ALTER TABLE subscripciones ADD CONSTRAINT chk_gp_sub_precio
                     CHECK (precio IS NULL OR precio >= 0)'
                );
            } catch (\Throwable $e) {
                error_log('[Spartum BD] chk_gp_sub_precio: ' . $e->getMessage());
            }
        }
        if (!self::tableConstraintExists($db, 'subscripciones', 'chk_gp_sub_duracion')) {
            try {
                $db->exec(
                    'ALTER TABLE subscripciones ADD CONSTRAINT chk_gp_sub_duracion
                     CHECK (duracion IS NULL OR duracion >= 0)'
                );
            } catch (\Throwable $e) {
                error_log('[Spartum BD] chk_gp_sub_duracion: ' . $e->getMessage());
            }
        }
        if (!self::tableConstraintExists($db, 'subscripciones', 'chk_gp_sub_num_clases')) {
            try {
                $db->exec(
                    'ALTER TABLE subscripciones ADD CONSTRAINT chk_gp_sub_num_clases
                     CHECK (numero_clases IS NULL OR numero_clases >= 0)'
                );
            } catch (\Throwable $e) {
                error_log('[Spartum BD] chk_gp_sub_num_clases: ' . $e->getMessage());
            }
        }
        if (!self::tableConstraintExists($db, 'feedback', 'chk_gp_fb_email')) {
            try {
                $db->exec(
                    "ALTER TABLE feedback ADD CONSTRAINT chk_gp_fb_email
                     CHECK (
                         CHAR_LENGTH(email) BETWEEN 3 AND 255
                         AND email REGEXP '^[^[:space:]@]+@[^[:space:]@]+[.][^[:space:]@]+$'
                     )"
                );
            } catch (\Throwable $e) {
                error_log('[Spartum BD] chk_gp_fb_email: ' . $e->getMessage());
            }
        }
    }

    /**
     * Ajustes para bases creadas con versiones antiguas de este archivo (CREATE IF NOT EXISTS no altera tablas viejas).
     */
    private static function ensureSchemaUpgrades(PDO $db): void
    {
        try {
            if (!self::columnExists($db, 'actividades', 'recurrente')) {
                $db->exec(
                    'ALTER TABLE actividades ADD COLUMN recurrente TINYINT(1) NOT NULL DEFAULT 1 AFTER plazas'
                );
            }
            if (!self::columnExists($db, 'inscripciones', 'asistio')) {
                $db->exec(
                    "ALTER TABLE inscripciones ADD COLUMN asistio ENUM('S','N') NOT NULL DEFAULT 'S' AFTER fecha_ocurrencia"
                );
            }
            try {
                $st = $db->query(
                    'SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
                     WHERE TABLE_SCHEMA = DATABASE()
                       AND TABLE_NAME = \'inscripciones\'
                       AND INDEX_NAME = \'uq_ins_cliente_sesion\''
                );
                if ($st && (int) $st->fetchColumn() === 0) {
                    $db->exec('CREATE UNIQUE INDEX uq_ins_cliente_sesion ON inscripciones (cliente_id, actividad_id, fecha_ocurrencia)');
                }
            } catch (\Throwable $e) {
            }
            if (!self::columnExists($db, 'fisioterapeutas', 'usuario_id')) {
                $db->exec('ALTER TABLE fisioterapeutas ADD COLUMN usuario_id INT DEFAULT NULL');
            }
            try {
                $st = $db->query(
                    'SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
                     WHERE TABLE_SCHEMA = DATABASE()
                       AND TABLE_NAME = \'fisioterapeutas\'
                       AND INDEX_NAME = \'uq_fisio_usuario\''
                );
                if ($st && (int) $st->fetchColumn() === 0) {
                    $db->exec('ALTER TABLE fisioterapeutas ADD UNIQUE KEY uq_fisio_usuario (usuario_id)');
                }
            } catch (\Throwable $e) {
            }
            try {
                $st = $db->query(
                    'SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
                     WHERE CONSTRAINT_SCHEMA = DATABASE()
                       AND TABLE_NAME = \'fisioterapeutas\'
                       AND CONSTRAINT_NAME = \'fk_fisio_usuario\''
                );
                if ($st && (int) $st->fetchColumn() === 0) {
                    $db->exec(
                        'ALTER TABLE fisioterapeutas
                         ADD CONSTRAINT fk_fisio_usuario FOREIGN KEY (usuario_id)
                             REFERENCES usuarios(id) ON DELETE SET NULL'
                    );
                }
            } catch (\Throwable $e) {
            }
            if (!self::columnExists($db, 'usuarios', 'email_confirmado')) {
                $db->exec(
                    'ALTER TABLE usuarios ADD COLUMN email_confirmado TINYINT(1) NOT NULL DEFAULT 1 AFTER telefono'
                );
            }
            if (!self::columnExists($db, 'usuarios', 'token_confirmacion')) {
                $db->exec(
                    'ALTER TABLE usuarios ADD COLUMN token_confirmacion VARCHAR(64) DEFAULT NULL AFTER email_confirmado'
                );
            }
            if (!self::columnExists($db, 'usuarios', 'token_confirmacion_expira')) {
                $db->exec(
                    'ALTER TABLE usuarios ADD COLUMN token_confirmacion_expira DATETIME DEFAULT NULL AFTER token_confirmacion'
                );
            }
            if (!self::columnExists($db, 'usuarios', 'avatar_path')) {
                $db->exec(
                    'ALTER TABLE usuarios ADD COLUMN avatar_path VARCHAR(255) DEFAULT NULL AFTER telefono'
                );
            }
            if (!self::columnExists($db, 'usuarios', 'bloqueo_tipo')) {
                $db->exec(
                    "ALTER TABLE usuarios ADD COLUMN bloqueo_tipo ENUM('N','T','P') NOT NULL DEFAULT 'N' AFTER avatar_path"
                );
            }
            if (!self::columnExists($db, 'usuarios', 'bloqueado_hasta')) {
                $db->exec(
                    'ALTER TABLE usuarios ADD COLUMN bloqueado_hasta DATETIME DEFAULT NULL AFTER bloqueo_tipo'
                );
            }
            if (!self::columnExists($db, 'usuarios', 'bloqueo_motivo')) {
                $db->exec(
                    'ALTER TABLE usuarios ADD COLUMN bloqueo_motivo VARCHAR(255) DEFAULT NULL AFTER bloqueado_hasta'
                );
            }
            if (!self::columnExists($db, 'usuarios', 'ticket_usuario_cancelado_en')) {
                $db->exec(
                    'ALTER TABLE usuarios ADD COLUMN ticket_usuario_cancelado_en DATETIME DEFAULT NULL AFTER bloqueo_motivo'
                );
            }
            if (!self::columnExists($db, 'subscripciones', 'en_oferta')) {
                $db->exec(
                    'ALTER TABLE subscripciones ADD COLUMN en_oferta TINYINT(1) NOT NULL DEFAULT 0 AFTER fisio'
                );
            }
            if (!self::columnExists($db, 'subscripciones', 'oferta_motivo')) {
                $db->exec(
                    'ALTER TABLE subscripciones ADD COLUMN oferta_motivo VARCHAR(120) DEFAULT NULL AFTER en_oferta'
                );
            }
            if (!self::columnExists($db, 'subscripciones', 'oferta_fin')) {
                $db->exec(
                    'ALTER TABLE subscripciones ADD COLUMN oferta_fin DATETIME DEFAULT NULL AFTER oferta_motivo'
                );
            }
            try {
                $db->exec(
                    'CREATE INDEX idx_usu_token ON usuarios (token_confirmacion)'
                );
            } catch (\Throwable $e) {
            }
        } catch (\Throwable $e) {
        }

        $tables = [
            'usuarios',
            'administradores',
            'monitores',
            'clientes',
            'salas',
            'solicitudes',
            'actividades',
            'inscripciones',
            'comentarios',
            'feedback',
            'subscripciones',
            'cliente_subscripcion',
            'fisioterapeutas',
            'citas',
            'materiales',
        ];

        foreach ($tables as $t) {
            try {
                $st = $db->prepare(
                    'SELECT ENGINE FROM INFORMATION_SCHEMA.TABLES
                     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?'
                );
                $st->execute([$t]);
                $row = $st->fetch(PDO::FETCH_ASSOC);
                if ($row && isset($row['ENGINE']) && strtoupper((string) $row['ENGINE']) !== 'INNODB') {
                    $db->exec('ALTER TABLE `' . str_replace('`', '``', $t) . '` ENGINE=InnoDB');
                }
            } catch (\Throwable $e) {
            }
        }

        self::ensureActividadDiasTable($db);
        self::ensurePasswordPolicySchema($db);
        self::ensureRecuperacionCuentaTicketTable($db);
        self::ensureIntegrityCheckConstraints($db);
    }

    private static function ensureRecuperacionCuentaTicketTable(PDO $db): void
    {
        try {
            $db->exec(
                'CREATE TABLE IF NOT EXISTS recuperacion_cuenta_ticket (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    usuario_id INT NOT NULL,
                    tipo ENUM(\'correo\',\'contrasena\',\'reactivacion\',\'recuperacion\') NOT NULL DEFAULT \'correo\',
                    codigo VARCHAR(16) NOT NULL,
                    intentos_codigo TINYINT UNSIGNED NOT NULL DEFAULT 0,
                    acceso_token VARCHAR(64) NOT NULL,
                    expira_en DATETIME NOT NULL,
                    estado ENUM(\'pendiente\',\'usado\',\'cancelado\',\'cerrado_por_admin\') NOT NULL DEFAULT \'pendiente\',
                    creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    UNIQUE KEY uq_rec_ticket_token (acceso_token),
                    KEY idx_rec_ticket_user_estado (usuario_id, estado),
                    CONSTRAINT fk_rec_ticket_usuario FOREIGN KEY (usuario_id)
                        REFERENCES usuarios(id) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8'
            );
        } catch (\Throwable $e) {
        }
        try {
            if (!self::columnExists($db, 'recuperacion_cuenta_ticket', 'intentos_codigo')) {
                $db->exec(
                    'ALTER TABLE recuperacion_cuenta_ticket ADD COLUMN intentos_codigo TINYINT UNSIGNED NOT NULL DEFAULT 0 AFTER codigo'
                );
            }
            if (!self::columnExists($db, 'recuperacion_cuenta_ticket', 'tipo')) {
                $db->exec(
                    "ALTER TABLE recuperacion_cuenta_ticket ADD COLUMN tipo ENUM('correo','contrasena','reactivacion','recuperacion') NOT NULL DEFAULT 'correo' AFTER usuario_id"
                );
            }
            $db->exec(
                "ALTER TABLE recuperacion_cuenta_ticket MODIFY COLUMN tipo ENUM('correo','contrasena','reactivacion','recuperacion') NOT NULL DEFAULT 'correo'"
            );
            $db->exec(
                "ALTER TABLE recuperacion_cuenta_ticket MODIFY COLUMN estado ENUM('pendiente','usado','cancelado','cerrado_por_admin') NOT NULL DEFAULT 'pendiente'"
            );
        } catch (\Throwable $e2) {
        }
    }

    /**
     * Política de contraseñas, recuperación y configuración editable por admin.
     */
    private static function ensurePasswordPolicySchema(PDO $db): void
    {
        try {
            if (!self::columnExists($db, 'usuarios', 'password_changed_at')) {
                $db->exec(
                    'ALTER TABLE usuarios ADD COLUMN password_changed_at DATETIME DEFAULT NULL AFTER clave'
                );
            }
            if (!self::columnExists($db, 'usuarios', 'password_reset_token')) {
                $db->exec(
                    'ALTER TABLE usuarios ADD COLUMN password_reset_token VARCHAR(64) DEFAULT NULL AFTER password_changed_at'
                );
            }
            if (!self::columnExists($db, 'usuarios', 'password_reset_expires')) {
                $db->exec(
                    'ALTER TABLE usuarios ADD COLUMN password_reset_expires DATETIME DEFAULT NULL AFTER password_reset_token'
                );
            }
        } catch (\Throwable $e) {
        }

        try {
            $db->exec(
                'CREATE TABLE IF NOT EXISTS admin_config (
                    clave VARCHAR(64) NOT NULL PRIMARY KEY,
                    valor TEXT,
                    actualizado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8'
            );
            $db->exec(
                "INSERT IGNORE INTO admin_config (clave, valor) VALUES ('password_rotation_days', '90'), ('session_idle_timeout_seconds', '2700')"
            );
            try {
                $db->exec('CREATE INDEX idx_usu_pwreset ON usuarios (password_reset_token)');
            } catch (\Throwable $e2) {
            }
        } catch (\Throwable $e) {
        }
    }

    /**
     * Días de la semana por actividad (varios días, misma hora). Migra desde actividades.dia_semana.
     */
    private static function ensureActividadDiasTable(PDO $db): void
    {
        try {
            $db->exec(
                'CREATE TABLE IF NOT EXISTS actividad_dias (
                    actividad_id INT NOT NULL,
                    dia_semana ENUM(\'L\',\'M\',\'X\',\'J\',\'V\',\'S\',\'D\') NOT NULL,
                    PRIMARY KEY (actividad_id, dia_semana),
                    CONSTRAINT fk_actividad_dias_actividad FOREIGN KEY (actividad_id)
                        REFERENCES actividades(id) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8'
            );
        } catch (\Throwable $e) {
        }
        try {
            $db->exec(
                'INSERT IGNORE INTO actividad_dias (actividad_id, dia_semana)
                 SELECT a.id, a.dia_semana FROM actividades a
                 WHERE a.dia_semana IS NOT NULL AND CHAR_LENGTH(TRIM(a.dia_semana)) > 0
                   AND NOT EXISTS (SELECT 1 FROM actividad_dias d WHERE d.actividad_id = a.id)'
            );
        } catch (\Throwable $e) {
        }
    }
}
