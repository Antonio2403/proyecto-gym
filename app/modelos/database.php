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

            $conexionDB->exec("
                    CREATE TABLE IF NOT EXISTS usuarios (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        DNI VARCHAR(9) NOT NULL UNIQUE,
                        nombre VARCHAR(100) NOT NULL,
                        apellido1 VARCHAR(100) NOT NULL,
                        apellido2 VARCHAR(100) NOT NULL,
                        email VARCHAR(255) NOT NULL UNIQUE,
                        clave VARCHAR(255) NOT NULL,
                        telefono VARCHAR(20) NOT NULL
                    )
                ");

            $conexionDB->exec("
                    CREATE TABLE IF NOT EXISTS administradores (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        usuario_id INT UNIQUE,
                        nivel_acceso VARCHAR(50),

                        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
                        ON DELETE CASCADE
                    )
                ");
            $conexionDB->exec("
                    CREATE TABLE IF NOT EXISTS monitores (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        usuario_id INT UNIQUE,
                        especialidad VARCHAR(100),
                        disponibilidad VARCHAR(100),

                        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
                        ON DELETE CASCADE
                    )
                ");
            $conexionDB->exec("
                    CREATE TABLE IF NOT EXISTS clientes (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        usuario_id INT UNIQUE,
                        metodo_pago VARCHAR(50),

                        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
                        ON DELETE CASCADE
                    )
                ");

            $password = password_hash("admin123", PASSWORD_DEFAULT);

            $stmtCheck = $conexionDB->query("SELECT id FROM usuarios WHERE email = 'admin@gym.com'");

            if ($stmtCheck->rowCount() == 0) {
                $stmt = $conexionDB->prepare("
                        INSERT INTO usuarios (DNI, nombre, apellido1, apellido2, email, clave, telefono)
                        VALUES (?, ?, ?, ?, ?, ?, ?)
                    ");
                $stmt->execute(['00000000A', 'Admin', '123', '', 'admin@gym.com', $password, '000000000']);

                $usuario_id = $conexionDB->lastInsertId();

                $stmt2 = $conexionDB->prepare("
                        INSERT INTO administradores (usuario_id, nivel_acceso)
                        VALUES (?, ?)
                    ");
                $stmt2->execute([$usuario_id, 'admin']);
            }
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

                    FOREIGN KEY (monitor_id) REFERENCES monitores(usuario_id),
                    FOREIGN KEY (admin_id) REFERENCES administradores(usuario_id)
                )
            ");

            $conexionDB->exec("
                CREATE TABLE IF NOT EXISTS actividades (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    nombre VARCHAR(100),
                    duracion INT
                )
            ");

            $conexionDB->exec("
                CREATE TABLE IF NOT EXISTS sesiones (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    actividad_id INT,
                    monitor_id INT,
                    fecha_hora DATETIME,

                    FOREIGN KEY (actividad_id) REFERENCES actividades(id),
                    FOREIGN KEY (monitor_id) REFERENCES monitores(usuario_id)
                )
            ");

            $conexionDB->exec("
                CREATE TABLE IF NOT EXISTS inscripciones (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    cliente_id INT,
                    actividad_id INT,
                    fecha_inscripcion DATETIME DEFAULT CURRENT_TIMESTAMP,

                    FOREIGN KEY (cliente_id) REFERENCES clientes(usuario_id),
                    FOREIGN KEY (actividad_id) REFERENCES actividades(id)
                )
            ");

            $conexionDB->exec("
                CREATE TABLE IF NOT EXISTS comentarios (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    cliente_id INT,
                    actividad_id INT,
                    texto TEXT,
                    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,

                    FOREIGN KEY (cliente_id) REFERENCES clientes(usuario_id),
                    FOREIGN KEY (actividad_id) REFERENCES actividades(id)
                )
            ");

            $conexionDB->exec("
                CREATE TABLE IF NOT EXISTS subscripciones (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    nombre VARCHAR(100),
                    precio DECIMAL(10,2),
                    duracion INT
                )
            ");

            $conexionDB->exec("
                CREATE TABLE IF NOT EXISTS cliente_subscripcion (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    cliente_id INT,
                    subscripcion_id INT,
                    fecha_inicio DATETIME,
                    fecha_fin DATETIME,

                    FOREIGN KEY (cliente_id) REFERENCES clientes(usuario_id),
                    FOREIGN KEY (subscripcion_id) REFERENCES subscripciones(id)
                )
            ");

            $conexionDB->exec("
                CREATE TABLE IF NOT EXISTS fisioterapeutas (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    nombre VARCHAR(100),
                    especialidad VARCHAR(100)
                )
            ");

            $conexionDB->exec("
                CREATE TABLE IF NOT EXISTS citas (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    cliente_id INT,
                    fisio_id INT,
                    fecha DATETIME,
                    motivo TEXT,
                    estado ENUM('S','C','A','CA'),

                    FOREIGN KEY (cliente_id) REFERENCES clientes(usuario_id),
                    FOREIGN KEY (fisio_id) REFERENCES fisioterapeutas(id)
                )
            ");

            $conexionDB->exec("
                CREATE TABLE IF NOT EXISTS salas (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    nombre VARCHAR(100),
                    capacidad INT,
                    disponibilidad ENUM('L','U','R')
                )
            ");

            $conexionDB->exec("
                CREATE TABLE IF NOT EXISTS materiales (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    sala_id INT NOT NULL,
                    nombre VARCHAR(100),
                    estado ENUM('B','M'),

                    FOREIGN KEY (sala_id) REFERENCES salas(id)
                        ON DELETE CASCADE
                );
            ");



            return $conexionDB;
        } catch (\Throwable $th) {
            echo "No ha sido posible conectarse con la base de datos por el siguiente motivo: " . $th->getMessage();
            return false;
        }
    }
}
