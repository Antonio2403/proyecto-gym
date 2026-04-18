<?php

class BasedeDatos
{
    const servidor = "localhost";
    const usuario = "root";
    const clave = "";
    const dbname = "Pgym";

    public static function Conectar()
    {
        try {
            $conexion = new PDO("mysql:host=" . self::servidor . ";charset=utf8", self::usuario, self::clave);
            $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Crear BD si no existe
            $stmt = $conexion->query("SHOW DATABASES LIKE '" . self::dbname . "'");
            if ($stmt->rowCount() == 0) {
                $conexion->exec("CREATE DATABASE `" . self::dbname . "` CHARACTER SET utf8 COLLATE utf8_general_ci");
            }

            $conexionDB = new PDO(
                "mysql:host=" . self::servidor . ";dbname=" . self::dbname . ";charset=utf8",
                self::usuario,
                self::clave
            );

            $conexionDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // USUARIOS
            $conexionDB->exec("
                CREATE TABLE IF NOT EXISTS usuarios (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    DNI VARCHAR(9) NOT NULL UNIQUE,
                    nombre VARCHAR(100) NOT NULL,
                    apellido1 VARCHAR(100) NOT NULL,
                    apellido2 VARCHAR(100),
                    email VARCHAR(255) NOT NULL UNIQUE,
                    clave VARCHAR(255) NOT NULL,
                    telefono VARCHAR(20)
                )
            ");

            // ROLES
            $conexionDB->exec("
                CREATE TABLE IF NOT EXISTS administradores (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    usuario_id INT UNIQUE,
                    nivel_acceso VARCHAR(50),
                    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
                )
            ");

            $conexionDB->exec("
                CREATE TABLE IF NOT EXISTS monitores (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    usuario_id INT UNIQUE,
                    especialidad VARCHAR(100),
                    disponibilidad VARCHAR(100),
                    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
                )
            ");

            $conexionDB->exec("
                CREATE TABLE IF NOT EXISTS clientes (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    usuario_id INT UNIQUE,
                    metodo_pago VARCHAR(50),
                    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
                )
            ");

            // ADMIN POR DEFECTO
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

            // SOLICITUDES
            $conexionDB->exec("
                CREATE TABLE IF NOT EXISTS solicitudes (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    monitor_id INT,
                    admin_id INT NULL,
                    tipo VARCHAR(100),
                    descripcion TEXT,
                    estado ENUM('P','A','R') DEFAULT 'P',
                    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
                    fecha_revision DATETIME NULL,

                    FOREIGN KEY (monitor_id) REFERENCES monitores(id),
                    FOREIGN KEY (admin_id) REFERENCES administradores(id)
                )
            ");
            // SALAS
            $conexionDB->exec("
                CREATE TABLE IF NOT EXISTS salas (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    nombre VARCHAR(100),
                    capacidad INT,
                    disponibilidad ENUM('L','U','R')
                )
            ");

            // ACTIVIDADES
            $conexionDB->exec("
                CREATE TABLE IF NOT EXISTS actividades (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    nombre VARCHAR(100),
                    duracion INT,
                    monitor_id INT,
                    sala_id INT,
                    descripcion TEXT,
                    FOREIGN KEY (monitor_id) REFERENCES monitores(id),
                    FOREIGN KEY (sala_id) REFERENCES salas(id)
                )
            ");

            


            // INSCRIPCIONES
            $conexionDB->exec("
                CREATE TABLE IF NOT EXISTS inscripciones (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    cliente_id INT,
                    actividad_id INT,
                    fecha_inscripcion DATETIME DEFAULT CURRENT_TIMESTAMP,

                    FOREIGN KEY (cliente_id) REFERENCES clientes(id),
                    FOREIGN KEY (actividad_id) REFERENCES actividades(id)
                )
            ");

            // COMENTARIOS
            $conexionDB->exec("
                CREATE TABLE IF NOT EXISTS comentarios (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    cliente_id INT,
                    actividad_id INT,
                    texto TEXT,
                    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,

                    FOREIGN KEY (cliente_id) REFERENCES clientes(id),
                    FOREIGN KEY (actividad_id) REFERENCES actividades(id)
                )
            ");

            // SUBSCRIPCIONES
            $conexionDB->exec("
                CREATE TABLE IF NOT EXISTS subscripciones (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    nombre VARCHAR(100),
                    precio DECIMAL(10,2),
                    duracion INT,
                    numero_clases INT,
                    fisio enum('S','N') DEFAULT 'N',
                    estado ENUM('A','I') DEFAULT 'A'
                )
            ");
            // INSERTAR SUBSCRIPCIONES POR DEFECTO
            $stmtCheck = $conexionDB->prepare("SELECT id FROM subscripciones WHERE nombre = ?");
            $stmtSub = $conexionDB->prepare("INSERT INTO subscripciones (nombre, precio, duracion, numero_clases, fisio, estado) VALUES (?, ?, ?, ?, ?, ?)");
            $subscripciones = [['Normal', 29.99, 1, 2, 'N', 'A'], ['Pro', 49.99, 2, 4, 'N', 'A'], ['MegaPro', 79.99, 3, 8, 'S', 'A']];
            foreach ($subscripciones as $sub) {
                $stmtCheck->execute([$sub[0]]);
                if ($stmtCheck->rowCount() == 0) {
                    $stmtSub->execute($sub);
                }
            }

            // HISTORIAL SUBSCRIPCIONES
            $conexionDB->exec("
                CREATE TABLE IF NOT EXISTS cliente_subscripcion (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    cliente_id INT,
                    subscripcion_id INT,
                    fecha_inicio DATETIME,
                    fecha_fin DATETIME,
                    estado ENUM('A','C') DEFAULT 'A',

                    FOREIGN KEY (cliente_id) REFERENCES clientes(id),
                    FOREIGN KEY (subscripcion_id) REFERENCES subscripciones(id)
                )
            ");

            // FISIOTERAPEUTAS
            $conexionDB->exec("
                CREATE TABLE IF NOT EXISTS fisioterapeutas (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    nombre VARCHAR(100),
                    especialidad VARCHAR(100)
                )
            ");

            // CITAS
            $conexionDB->exec("
                CREATE TABLE IF NOT EXISTS citas (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    cliente_id INT,
                    fisio_id INT,
                    fecha DATETIME,
                    motivo TEXT,
                    estado ENUM('S','C','A','CA'),

                    FOREIGN KEY (cliente_id) REFERENCES clientes(id),
                    FOREIGN KEY (fisio_id) REFERENCES fisioterapeutas(id)
                )
            ");

            // MATERIALES
            $conexionDB->exec("
                CREATE TABLE IF NOT EXISTS materiales (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    sala_id INT NOT NULL,
                    nombre VARCHAR(100),
                    estado ENUM('B','M'),

                    FOREIGN KEY (sala_id) REFERENCES salas(id) ON DELETE CASCADE
                )
            ");

            return $conexionDB;

        } catch (\Throwable $th) {
            echo "Error BD: " . $th->getMessage();
            return false;
        }
    }
}