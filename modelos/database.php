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
                        telefono VARCHAR(20) NOT NULL,
                    )
                ");
            }
            $conexion = new PDO(
                "mysql:host=" . self::servidor . ";dbname=" . self::dbname . ";charset=utf8",
                self::usuario,
                self::clave
            );
            $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $conexion;
        } catch (\Throwable $th) {
            echo "No ha sido posible conectarse con la base de datos por el siguiente motivo: " . $th->getMessage();
            return false;
        }
    }
}
