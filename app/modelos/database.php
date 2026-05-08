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

            $dbName = self::dbName();
            $stmt = $conexion->query("SHOW DATABASES LIKE '" . str_replace("'", "''", $dbName) . "'");
            if ($stmt->rowCount() == 0) {
                $conexion->exec(
                    'CREATE DATABASE `' . str_replace('`', '``', $dbName) . '` CHARACTER SET utf8 COLLATE utf8_general_ci'
                );
            }

            $conexionDB = new PDO(self::pdoDsn(true), self::dbUser(), self::dbPass());

            $conexionDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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
                    telefono VARCHAR(20) DEFAULT NULL,
                    email_confirmado TINYINT(1) NOT NULL DEFAULT 1,
                    token_confirmacion VARCHAR(64) DEFAULT NULL,
                    token_confirmacion_expira DATETIME DEFAULT NULL,
                    UNIQUE KEY uq_usuarios_dni (DNI),
                    UNIQUE KEY uq_usuarios_email (email),
                    KEY idx_usu_token (token_confirmacion)
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

            $password = password_hash("admin123", PASSWORD_DEFAULT);

            $stmtCheck = $conexionDB->query("SELECT id FROM usuarios WHERE email = 'admin@gym.com'");
            if ($stmtCheck->rowCount() == 0) {
                $stmt = $conexionDB->prepare("
                    INSERT INTO usuarios (DNI, nombre, apellido1, apellido2, email, clave, telefono)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute(['00000000A', 'Admin', 'Gym', '', 'admin@gym.com', $password, '000000000']);

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
                if ($stmtCheck->rowCount() == 0) {
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
    }
}
