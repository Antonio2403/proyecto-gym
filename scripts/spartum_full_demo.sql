-- Spartum — esquema completo + datos de demostración
-- Importar con: php scripts/import_demo_database.php --force
--   o manualmente: mysql -u root -p spartum < scripts/spartum_full_demo.sql
--
-- Cuentas impersonales: docs/cuentas_demo.txt

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';

-- Opcional: descomenta si quieres crear la base desde cero
-- DROP DATABASE IF EXISTS `spartum`;
-- CREATE DATABASE `spartum` CHARACTER SET utf8 COLLATE utf8_general_ci;
-- USE `spartum`;

-- ---------------------------------------------------------------------------
-- Tablas
-- ---------------------------------------------------------------------------

DROP TABLE IF EXISTS `materiales`;
DROP TABLE IF EXISTS `citas`;
DROP TABLE IF EXISTS `comentarios`;
DROP TABLE IF EXISTS `inscripciones`;
DROP TABLE IF EXISTS `cliente_subscripcion`;
DROP TABLE IF EXISTS `solicitudes`;
DROP TABLE IF EXISTS `actividad_sesion_cancelada`;
DROP TABLE IF EXISTS `actividad_dias`;
DROP TABLE IF EXISTS `actividades`;
DROP TABLE IF EXISTS `salas`;
DROP TABLE IF EXISTS `monitores`;
DROP TABLE IF EXISTS `clientes`;
DROP TABLE IF EXISTS `fisioterapeutas`;
DROP TABLE IF EXISTS `recuperacion_cuenta_ticket`;
DROP TABLE IF EXISTS `administradores`;
DROP TABLE IF EXISTS `usuarios`;
DROP TABLE IF EXISTS `feedback`;
DROP TABLE IF EXISTS `subscripciones`;
DROP TABLE IF EXISTS `admin_config`;

CREATE TABLE `usuarios` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `DNI` VARCHAR(9) NOT NULL,
    `nombre` VARCHAR(100) NOT NULL,
    `apellido1` VARCHAR(100) NOT NULL,
    `apellido2` VARCHAR(100) DEFAULT NULL,
    `email` VARCHAR(255) NOT NULL,
    `clave` VARCHAR(255) NOT NULL,
    `password_changed_at` DATETIME DEFAULT NULL,
    `password_reset_token` VARCHAR(64) DEFAULT NULL,
    `password_reset_expires` DATETIME DEFAULT NULL,
    `telefono` VARCHAR(20) DEFAULT NULL,
    `avatar_path` VARCHAR(255) DEFAULT NULL,
    `bloqueo_tipo` ENUM('N','T','P') NOT NULL DEFAULT 'N',
    `bloqueado_hasta` DATETIME DEFAULT NULL,
    `bloqueo_motivo` VARCHAR(255) DEFAULT NULL,
    `ticket_usuario_cancelado_en` DATETIME DEFAULT NULL,
    `email_confirmado` TINYINT(1) NOT NULL DEFAULT 1,
    `token_confirmacion` VARCHAR(64) DEFAULT NULL,
    `token_confirmacion_expira` DATETIME DEFAULT NULL,
    UNIQUE KEY `uq_usuarios_dni` (`DNI`),
    UNIQUE KEY `uq_usuarios_email` (`email`),
    KEY `idx_usu_token` (`token_confirmacion`),
    KEY `idx_usu_pwreset` (`password_reset_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `administradores` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `usuario_id` INT DEFAULT NULL,
    `nivel_acceso` VARCHAR(50) DEFAULT NULL,
    UNIQUE KEY `uq_admin_usuario` (`usuario_id`),
    CONSTRAINT `fk_admin_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `monitores` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `usuario_id` INT DEFAULT NULL,
    `especialidad` VARCHAR(100) DEFAULT NULL,
    `disponibilidad` VARCHAR(100) DEFAULT NULL,
    UNIQUE KEY `uq_monitor_usuario` (`usuario_id`),
    CONSTRAINT `fk_monitor_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `clientes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `usuario_id` INT DEFAULT NULL,
    `metodo_pago` VARCHAR(50) DEFAULT NULL,
    UNIQUE KEY `uq_cliente_usuario` (`usuario_id`),
    CONSTRAINT `fk_cliente_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `salas` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(100) DEFAULT NULL,
    `color` CHAR(7) NOT NULL DEFAULT '#C45C26',
    `capacidad` INT DEFAULT NULL,
    `disponibilidad` ENUM('L','U','R') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `solicitudes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `monitor_id` INT DEFAULT NULL,
    `admin_id` INT DEFAULT NULL,
    `tipo` VARCHAR(100) DEFAULT NULL,
    `descripcion` TEXT,
    `estado` ENUM('P','A','R') NOT NULL DEFAULT 'P',
    `fecha_creacion` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `fecha_revision` DATETIME DEFAULT NULL,
    KEY `idx_sol_monitor` (`monitor_id`),
    KEY `idx_sol_admin` (`admin_id`),
    CONSTRAINT `fk_sol_monitor` FOREIGN KEY (`monitor_id`) REFERENCES `monitores` (`id`),
    CONSTRAINT `fk_sol_admin` FOREIGN KEY (`admin_id`) REFERENCES `administradores` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `actividades` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(100) DEFAULT NULL,
    `color` CHAR(7) NOT NULL DEFAULT '#C45C26',
    `duracion` INT DEFAULT NULL,
    `monitor_id` INT DEFAULT NULL,
    `sala_id` INT DEFAULT NULL,
    `fecha_inicio` DATETIME DEFAULT NULL,
    `fecha_fin` DATETIME DEFAULT NULL,
    `dia_semana` ENUM('L','M','X','J','V','S','D') DEFAULT NULL,
    `descripcion` TEXT,
    `plazas` INT NOT NULL DEFAULT 20,
    `recurrente` TINYINT(1) NOT NULL DEFAULT 1,
    KEY `idx_act_monitor` (`monitor_id`),
    KEY `idx_act_sala` (`sala_id`),
    CONSTRAINT `fk_act_monitor` FOREIGN KEY (`monitor_id`) REFERENCES `monitores` (`id`),
    CONSTRAINT `fk_act_sala` FOREIGN KEY (`sala_id`) REFERENCES `salas` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `actividad_dias` (
    `actividad_id` INT NOT NULL,
    `dia_semana` ENUM('L','M','X','J','V','S','D') NOT NULL,
    PRIMARY KEY (`actividad_id`, `dia_semana`),
    CONSTRAINT `fk_actividad_dias_actividad` FOREIGN KEY (`actividad_id`) REFERENCES `actividades` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `actividad_sesion_cancelada` (
    `actividad_id` INT NOT NULL,
    `fecha` DATE NOT NULL,
    `inscritos` INT NOT NULL DEFAULT 0,
    `minimo` INT NOT NULL DEFAULT 5,
    `cancelada_en` DATETIME NOT NULL,
    PRIMARY KEY (`actividad_id`, `fecha`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `inscripciones` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `cliente_id` INT DEFAULT NULL,
    `actividad_id` INT DEFAULT NULL,
    `fecha_ocurrencia` DATE DEFAULT NULL,
    `asistio` ENUM('S','N') NOT NULL DEFAULT 'S',
    `fecha_inscripcion` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY `idx_ins_cliente` (`cliente_id`),
    KEY `idx_ins_actividad` (`actividad_id`),
    UNIQUE KEY `uq_ins_cliente_sesion` (`cliente_id`, `actividad_id`, `fecha_ocurrencia`),
    CONSTRAINT `fk_ins_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
    CONSTRAINT `fk_ins_actividad` FOREIGN KEY (`actividad_id`) REFERENCES `actividades` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `comentarios` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `cliente_id` INT DEFAULT NULL,
    `actividad_id` INT DEFAULT NULL,
    `fecha_ocurrencia` DATE DEFAULT NULL,
    `texto` TEXT,
    `fecha` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY `idx_com_act_fecha` (`actividad_id`, `fecha_ocurrencia`),
    CONSTRAINT `fk_com_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
    CONSTRAINT `fk_com_actividad` FOREIGN KEY (`actividad_id`) REFERENCES `actividades` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `feedback` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(150) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `asunto` VARCHAR(255) NOT NULL,
    `mensaje` TEXT NOT NULL,
    `fecha_creacion` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `subscripciones` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(100) DEFAULT NULL,
    `precio` DECIMAL(10,2) DEFAULT NULL,
    `duracion` INT DEFAULT NULL,
    `numero_clases` INT DEFAULT NULL,
    `fisio` ENUM('S','N') NOT NULL DEFAULT 'N',
    `en_oferta` TINYINT(1) NOT NULL DEFAULT 0,
    `oferta_motivo` VARCHAR(120) DEFAULT NULL,
    `oferta_fin` DATETIME DEFAULT NULL,
    `estado` ENUM('A','I') NOT NULL DEFAULT 'A'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `cliente_subscripcion` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `cliente_id` INT DEFAULT NULL,
    `subscripcion_id` INT DEFAULT NULL,
    `fecha_inicio` DATETIME DEFAULT NULL,
    `fecha_fin` DATETIME DEFAULT NULL,
    `estado` ENUM('A','C') NOT NULL DEFAULT 'A',
    KEY `idx_cs_cliente` (`cliente_id`),
    KEY `idx_cs_sub` (`subscripcion_id`),
    CONSTRAINT `fk_cs_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
    CONSTRAINT `fk_cs_sub` FOREIGN KEY (`subscripcion_id`) REFERENCES `subscripciones` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `fisioterapeutas` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(100) DEFAULT NULL,
    `especialidad` VARCHAR(100) DEFAULT NULL,
    `usuario_id` INT DEFAULT NULL,
    UNIQUE KEY `uq_fisio_usuario` (`usuario_id`),
    CONSTRAINT `fk_fisio_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `citas` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `cliente_id` INT DEFAULT NULL,
    `fisio_id` INT DEFAULT NULL,
    `fecha` DATETIME DEFAULT NULL,
    `motivo` TEXT,
    `estado` ENUM('S','C','A','CA') DEFAULT NULL,
    CONSTRAINT `fk_cita_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
    CONSTRAINT `fk_cita_fisio` FOREIGN KEY (`fisio_id`) REFERENCES `fisioterapeutas` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `materiales` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `sala_id` INT NOT NULL,
    `nombre` VARCHAR(100) DEFAULT NULL,
    `estado` ENUM('B','M') DEFAULT NULL,
    KEY `idx_mat_sala` (`sala_id`),
    CONSTRAINT `fk_mat_sala` FOREIGN KEY (`sala_id`) REFERENCES `salas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `recuperacion_cuenta_ticket` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `usuario_id` INT NOT NULL,
    `tipo` ENUM('correo','contrasena','reactivacion','recuperacion') NOT NULL DEFAULT 'correo',
    `codigo` VARCHAR(16) NOT NULL,
    `intentos_codigo` TINYINT UNSIGNED NOT NULL DEFAULT 0,
    `acceso_token` VARCHAR(64) NOT NULL,
    `expira_en` DATETIME NOT NULL,
    `estado` ENUM('pendiente','usado','cancelado','cerrado_por_admin') NOT NULL DEFAULT 'pendiente',
    `creado_en` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uq_rec_ticket_token` (`acceso_token`),
    KEY `idx_rec_ticket_user_estado` (`usuario_id`, `estado`),
    CONSTRAINT `fk_rec_ticket_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `admin_config` (
    `clave` VARCHAR(64) NOT NULL PRIMARY KEY,
    `valor` TEXT,
    `actualizado_en` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ---------------------------------------------------------------------------
-- Restricciones CHECK (MySQL 8.0.16+ / MariaDB 10.2.1+). Omite si fallan.
-- ---------------------------------------------------------------------------
ALTER TABLE `usuarios` ADD CONSTRAINT `chk_gp_usuarios_dni`
    CHECK (`DNI` REGEXP '^[0-9]{8}[A-Za-z]$|^[XYZ][0-9]{7}[A-Za-z]$');
ALTER TABLE `usuarios` ADD CONSTRAINT `chk_gp_usuarios_email`
    CHECK (CHAR_LENGTH(`email`) BETWEEN 3 AND 255 AND `email` REGEXP '^[^[:space:]@]+@[^[:space:]@]+[.][^[:space:]@]+$');
ALTER TABLE `usuarios` ADD CONSTRAINT `chk_gp_usuarios_email_confirmado`
    CHECK (`email_confirmado` IN (0, 1));
ALTER TABLE `subscripciones` ADD CONSTRAINT `chk_gp_sub_precio`
    CHECK (`precio` IS NULL OR `precio` >= 0);
ALTER TABLE `subscripciones` ADD CONSTRAINT `chk_gp_sub_duracion`
    CHECK (`duracion` IS NULL OR `duracion` >= 0);
ALTER TABLE `subscripciones` ADD CONSTRAINT `chk_gp_sub_num_clases`
    CHECK (`numero_clases` IS NULL OR `numero_clases` >= 0);
ALTER TABLE `feedback` ADD CONSTRAINT `chk_gp_fb_email`
    CHECK (CHAR_LENGTH(`email`) BETWEEN 3 AND 255 AND `email` REGEXP '^[^[:space:]@]+@[^[:space:]@]+[.][^[:space:]@]+$');
ALTER TABLE `recuperacion_cuenta_ticket` ADD CONSTRAINT `chk_gp_rec_intentos`
    CHECK (`intentos_codigo` BETWEEN 0 AND 50);
ALTER TABLE `recuperacion_cuenta_ticket` ADD CONSTRAINT `chk_gp_rec_codigo_len`
    CHECK (CHAR_LENGTH(`codigo`) BETWEEN 1 AND 32);

-- ---------------------------------------------------------------------------
-- Datos de prueba impersonales. Usuarios y contraseñas: docs/cuentas_demo.txt
-- ---------------------------------------------------------------------------

INSERT INTO `usuarios` (`id`, `DNI`, `nombre`, `apellido1`, `apellido2`, `email`, `clave`, `telefono`, `password_changed_at`, `email_confirmado`) VALUES
(1, '10000011W', 'admin1', 'apellido1', 'apellido2', 'admin1@demo.local', '$2y$10$35eLwQfAcb6Lq4fjjfW6guYVKgLtEAhTXhHroUGOtb1YcHNqW8d0O', '600000001', NOW(), 1),
(2, '10000012A', 'monitor1', 'apellido1', 'apellido2', 'monitor1@demo.local', '$2y$10$7Z2ysqbs.UBjoWWIkwW6oOdFrzjfU/hzpK3k7x99OAtktrT8WZkQC', '600000002', NOW(), 1),
(3, '10000013G', 'monitor2', 'apellido1', 'apellido2', 'monitor2@demo.local', '$2y$10$2taC5z7xRU2iAgRgHOGSqeAIwcZqDSFUKY8KHo02xjsj7lLTICsJW', '600000003', NOW(), 1),
(4, '10000014M', 'cliente1', 'apellido1', 'apellido2', 'cliente1@demo.local', '$2y$10$RGM30FnyZc7uwMZuQrvIbulnse/LhdgAGpkMA1AfBWwKo8Q/wYax2', '600000004', NOW(), 1),
(5, '10000015Y', 'cliente2', 'apellido1', 'apellido2', 'cliente2@demo.local', '$2y$10$Ey4JM6cXYL6ZTPYy1G6JNOHX3Jxwfn0eRh46f4KbM74qtmTefrWwS', '600000005', NOW(), 1),
(6, '10000016F', 'cliente3', 'apellido1', 'apellido2', 'cliente3@demo.local', '$2y$10$rLmkOpwcPuW2Fo6Xhbi3teKtiZ3i2.VcMGlco80afzbff8W.xq7GK', '600000006', NOW(), 1),
(7, '10000017P', 'fisio1', 'apellido1', 'apellido2', 'fisio1@demo.local', '$2y$10$lojXK2s9J24xrloTB4m4yuooPpGAXko/SfDfhaVChq0OucBx1ARy6', '600000007', NOW(), 1);

INSERT INTO `administradores` (`id`, `usuario_id`, `nivel_acceso`) VALUES (1, 1, 'nivel1');

INSERT INTO `monitores` (`id`, `usuario_id`, `especialidad`, `disponibilidad`) VALUES
(1, 2, 'especialidad1', 'disponibilidad1'),
(2, 3, 'especialidad2', 'disponibilidad2');

INSERT INTO `clientes` (`id`, `usuario_id`, `metodo_pago`) VALUES
(1, 4, 'metodo1'),
(2, 5, 'metodo2'),
(3, 6, 'metodo3');

INSERT INTO `fisioterapeutas` (`id`, `nombre`, `especialidad`, `usuario_id`) VALUES
(1, 'fisio1', 'especialidad1', 7);

INSERT INTO `salas` (`id`, `nombre`, `color`, `capacidad`, `disponibilidad`) VALUES
(1, 'sala1', '#C45C26', 20, 'L'),
(2, 'sala2', '#2F8F4A', 15, 'U'),
(3, 'sala3', '#2563EB', 25, 'R');

INSERT INTO `actividades` (`id`, `nombre`, `color`, `duracion`, `monitor_id`, `sala_id`, `fecha_inicio`, `fecha_fin`, `dia_semana`, `descripcion`, `plazas`, `recurrente`) VALUES
(1, 'actividad1', '#2563EB', 45, 1, 2, '2026-05-12 08:00:00', '2026-05-12 08:45:00', 'L', 'descripcion1', 15, 1),
(2, 'actividad2', '#DC2626', 30, 2, 1, '2026-05-13 18:30:00', '2026-05-13 19:00:00', 'X', 'descripcion2', 12, 1),
(3, 'actividad3', '#2F8F4A', 60, 1, 1, '2026-05-14 10:00:00', '2026-05-14 11:00:00', 'J', 'descripcion3', 20, 1);

INSERT INTO `actividad_dias` (`actividad_id`, `dia_semana`) VALUES
(1, 'L'),
(2, 'X'),
(3, 'J');

INSERT INTO `subscripciones` (`id`, `nombre`, `precio`, `duracion`, `numero_clases`, `fisio`, `estado`) VALUES
(1, 'plan1', 29.99, 1, 1, 'N', 'A'),
(2, 'plan2', 49.99, 2, 2, 'N', 'A'),
(3, 'plan3', 79.99, 3, 4, 'S', 'A');

CREATE TABLE IF NOT EXISTS `gp_ajuste` (
  `clave` VARCHAR(64) NOT NULL,
  `aplicado_en` DATETIME NOT NULL,
  PRIMARY KEY (`clave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `gp_ajuste` (`clave`, `aplicado_en`) VALUES
('planes_clases_mitad_20261105', NOW());

INSERT INTO `cliente_subscripcion` (`cliente_id`, `subscripcion_id`, `fecha_inicio`, `fecha_fin`, `estado`) VALUES
(1, 1, '2026-05-01 00:00:00', '2026-06-01 23:59:59', 'A'),
(2, 2, '2026-05-01 00:00:00', '2026-07-01 23:59:59', 'A'),
(3, 3, '2026-05-01 00:00:00', '2026-08-01 23:59:59', 'A');

INSERT INTO `solicitudes` (`monitor_id`, `admin_id`, `tipo`, `descripcion`, `estado`, `fecha_creacion`, `fecha_revision`) VALUES
(1, 1, 'tipo1', 'descripcion1', 'P', NOW(), NULL),
(2, 1, 'tipo2', 'descripcion2', 'A', '2026-05-01 12:00:00', '2026-05-01 12:00:00'),
(1, 1, 'tipo3', 'descripcion3', 'R', '2026-05-02 09:00:00', '2026-05-02 09:00:00');

INSERT INTO `inscripciones` (`cliente_id`, `actividad_id`, `fecha_ocurrencia`, `asistio`, `fecha_inscripcion`) VALUES
(1, 1, '2026-05-12', 'S', NOW()),
(2, 2, '2026-05-13', 'N', NOW()),
(3, 3, '2026-05-14', 'S', NOW());

INSERT INTO `comentarios` (`cliente_id`, `actividad_id`, `fecha_ocurrencia`, `texto`, `fecha`) VALUES
(1, 1, '2026-05-12', 'comentario1', NOW()),
(3, 3, '2026-05-14', 'comentario2', NOW());

INSERT INTO `feedback` (`nombre`, `email`, `asunto`, `mensaje`, `fecha_creacion`) VALUES
('feedback1', 'feedback1@demo.local', 'asunto1', 'mensaje1', NOW()),
('feedback2', 'feedback2@demo.local', 'asunto2', 'mensaje2', NOW());

INSERT INTO `citas` (`cliente_id`, `fisio_id`, `fecha`, `motivo`, `estado`) VALUES
(3, 1, '2026-05-20 11:00:00', 'motivo1', 'S'),
(3, 1, '2026-05-22 16:30:00', 'motivo2', 'C');

INSERT INTO `materiales` (`sala_id`, `nombre`, `estado`) VALUES
(3, 'material1', 'B'),
(3, 'material2', 'B'),
(2, 'material3', 'M');

INSERT INTO `admin_config` (`clave`, `valor`) VALUES
('password_rotation_days', '90'),
('session_idle_timeout_seconds', '2700');

SET FOREIGN_KEY_CHECKS = 1;
