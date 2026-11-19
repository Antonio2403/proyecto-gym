-- Spartum — datos de demo en un solo script.
-- Ejecútalo en la base que ya está en marcha (phpMyAdmin: elige spartum y pega todo).
-- No borra cuentas ni clases. Si lo lanzas otra vez, no duplica.
--
-- Contraseñas
--   monitor3@demo.local   Monitor3#Demo2026!    fuerza
--   monitor4@demo.local   Monitor4#Demo2026!    cardio
--   monitor5@demo.local   Monitor5#Demo2026!    movilidad
--   monitor6@demo.local   Monitor6#Demo2026!    funcional
--   cliente4@demo.local … cliente18@demo.local
--   Cliente4#Demo2026! … Cliente18#Demo2026!
--   fisio2@demo.local     Fisio2#Demo2026!      deportiva
--   fisio3@demo.local     Fisio3#Demo2026!      recuperacion
--
-- cliente1 a cliente15 dejan actividad1 (lunes) llena esta semana y la siguiente.
-- Las cuentas que ya existían (admin1, monitor1, monitor2, cliente1-3, fisio1) no se tocan,
-- salvo alargar el plan de los clientes de demo 8 meses.

SET NAMES utf8;

CREATE TABLE IF NOT EXISTS `gp_ajuste` (
  `clave` VARCHAR(64) NOT NULL,
  `aplicado_en` DATETIME NOT NULL,
  PRIMARY KEY (`clave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TEMPORARY TABLE IF EXISTS `tmp_demo_cuentas`;
CREATE TEMPORARY TABLE `tmp_demo_cuentas` (
  `rol` VARCHAR(20) NOT NULL,
  `nombre` VARCHAR(100) NOT NULL,
  `dni` VARCHAR(9) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `clave` VARCHAR(255) NOT NULL,
  `telefono` VARCHAR(20) NOT NULL,
  `extra` VARCHAR(100) NOT NULL
) ENGINE=MEMORY;

INSERT INTO `tmp_demo_cuentas` (`rol`, `nombre`, `dni`, `email`, `clave`, `telefono`, `extra`) VALUES
('monitor', 'monitor3', '10000018D', 'monitor3@demo.local', '$2y$10$SBpVZVmcrDj2scTCGF18vuL.1N9O.kkEP9ZTrak8rOr9PMswRDlvS', '600000018', 'fuerza'),
('monitor', 'monitor4', '10000019X', 'monitor4@demo.local', '$2y$10$XvgDtciRl4.tLr/ZjM4AsOJgbTGB6Z0BRsOvCWm3lC0Ew.6doC8iq', '600000019', 'cardio'),
('monitor', 'monitor5', '10000020B', 'monitor5@demo.local', '$2y$10$MTOcLyVfPKzeBdE7FuvPqe8wQuBmkKgOSh.5nodSmCkVipucQg81i', '600000020', 'movilidad'),
('monitor', 'monitor6', '10000021N', 'monitor6@demo.local', '$2y$10$xCT0TBtHXqyDVg15s/oWQ.R7zeB88VYN7ht0JosNbvVcME3QCmI8.', '600000021', 'funcional'),
('cliente', 'cliente4', '10000022J', 'cliente4@demo.local', '$2y$10$DNwfWpYa2/lQif6sh9iyC.Lt1Tsz/ERtNPUVy/Kh9lFmObHRanFMS', '60010000022', ''),
('cliente', 'cliente5', '10000023Z', 'cliente5@demo.local', '$2y$10$sk9bxmbYO6jZft38bKHc6.0T/9uDTReMlEKCGohsUnNZEjQzq2cRC', '60010000023', ''),
('cliente', 'cliente6', '10000024S', 'cliente6@demo.local', '$2y$10$dL7S7f/E04/QAAvw1rIAPeJ1sXlOsUMngvYOBs3oO95Wt4iF3yuOa', '60010000024', ''),
('cliente', 'cliente7', '10000025Q', 'cliente7@demo.local', '$2y$10$CfiBdfnh/Dt5o6tlJcviF.KjO4RsWYuH3k1lRWUL/cVK5NUOt3L9a', '60010000025', ''),
('cliente', 'cliente8', '10000026V', 'cliente8@demo.local', '$2y$10$wavSpa2PlKK52/pkHEQcNeMUWIrXvAAn4TBoorpSJVsADmlIZDq0e', '60010000026', ''),
('cliente', 'cliente9', '10000027H', 'cliente9@demo.local', '$2y$10$JiIlqjZqDBOu/4j2k22tc.nfVvjymqH2gssKCrFYbhRwEaq./dh1O', '60010000027', ''),
('cliente', 'cliente10', '10000028L', 'cliente10@demo.local', '$2y$10$32iQmit2YtC/lCmo5FteGeLAd4fXTkouZ8d.CojxZFfqHm.V16/qu', '60010000028', ''),
('cliente', 'cliente11', '10000029C', 'cliente11@demo.local', '$2y$10$PXdNiDw6WcwfoIuwu4JQ0eNYu/9AnScfhyU3lhGrR9OGVft16KItm', '60010000029', ''),
('cliente', 'cliente12', '10000030K', 'cliente12@demo.local', '$2y$10$P4Ay1BKFhb2.N.LvAZ1YoOC8BErlWN0qE8GJ9tnhFIQ3WRBhFHlIK', '60010000030', ''),
('cliente', 'cliente13', '10000031E', 'cliente13@demo.local', '$2y$10$gR0sN7Gn4wMcZUgPbdsB5.JEzhl5G51Fk3E8huvZA/ZIi9wTpLcaK', '60010000031', ''),
('cliente', 'cliente14', '10000032T', 'cliente14@demo.local', '$2y$10$rnvPfFzjGyxxP1QCRAXK7O2oHLcmnXa2ewYZXGg/slMPZqbbP5gTa', '60010000032', ''),
('cliente', 'cliente15', '10000033R', 'cliente15@demo.local', '$2y$10$tjLtDXqQ8ND.b1qaq2k.6.QfODnqOFkrLJ9J2K8qXU6vysN9emkA2', '60010000033', ''),
('cliente', 'cliente16', '10000034W', 'cliente16@demo.local', '$2y$10$96KU9I2xGSIRPcDRKnPMGOCS1MkFtkL6xDcQ30g9LZRWFRDzUOzgC', '60010000034', ''),
('cliente', 'cliente17', '10000035A', 'cliente17@demo.local', '$2y$10$cC2JamKRUfm6uSyynN6tI.dJ3Tgw1rUnmuCIkBrP5PBsYlVQDAFEm', '60010000035', ''),
('cliente', 'cliente18', '10000036G', 'cliente18@demo.local', '$2y$10$IrJR2ilHjfQuK6yIBMBiIezZ7nApQK7NchSL/ZQ/SNAcZWDZjqbSK', '60010000036', ''),
('fisio', 'fisio2', '10000037M', 'fisio2@demo.local', '$2y$10$hj4KU1Iqga3JHqdNF6tSTOjONaQc.YKYMrEcUcTk2AKVJeL9HVq8q', '600000037', 'deportiva'),
('fisio', 'fisio3', '10000038Y', 'fisio3@demo.local', '$2y$10$s9P961uZVEHAmLUaDTyREOZKff0YyTVh.QvieWZPZem6HCLzlg5Eu', '600000038', 'recuperacion');

INSERT INTO `usuarios` (`DNI`, `nombre`, `apellido1`, `apellido2`, `email`, `clave`, `telefono`, `password_changed_at`, `email_confirmado`)
SELECT t.dni, t.nombre, 'apellido1', 'apellido2', t.email, t.clave, t.telefono, NOW(), 1
FROM `tmp_demo_cuentas` t
WHERE NOT EXISTS (
    SELECT 1 FROM `usuarios` u WHERE u.email = t.email OR u.DNI = t.dni
);

INSERT INTO `monitores` (`usuario_id`, `especialidad`, `disponibilidad`)
SELECT u.id, t.extra, 'L-S'
FROM `tmp_demo_cuentas` t
INNER JOIN `usuarios` u ON u.email = t.email
WHERE t.rol = 'monitor'
  AND NOT EXISTS (SELECT 1 FROM `monitores` m WHERE m.usuario_id = u.id);

INSERT INTO `clientes` (`usuario_id`, `metodo_pago`)
SELECT u.id, 'tarjeta'
FROM `tmp_demo_cuentas` t
INNER JOIN `usuarios` u ON u.email = t.email
WHERE t.rol = 'cliente'
  AND NOT EXISTS (SELECT 1 FROM `clientes` c WHERE c.usuario_id = u.id);

INSERT INTO `fisioterapeutas` (`nombre`, `especialidad`, `usuario_id`)
SELECT t.nombre, t.extra, u.id
FROM `tmp_demo_cuentas` t
INNER JOIN `usuarios` u ON u.email = t.email
WHERE t.rol = 'fisio'
  AND NOT EXISTS (SELECT 1 FROM `fisioterapeutas` f WHERE f.usuario_id = u.id);

SET @plan := (
    SELECT id FROM `subscripciones`
    WHERE estado = 'A'
    ORDER BY numero_clases DESC, id ASC
    LIMIT 1
);

UPDATE `cliente_subscripcion` cs
INNER JOIN `clientes` c ON c.id = cs.cliente_id
INNER JOIN `usuarios` u ON u.id = c.usuario_id
SET cs.fecha_fin = DATE_ADD(CURDATE(), INTERVAL 8 MONTH),
    cs.estado = 'A'
WHERE u.email LIKE 'cliente%@demo.local'
  AND @plan IS NOT NULL;

INSERT INTO `cliente_subscripcion` (`cliente_id`, `subscripcion_id`, `fecha_inicio`, `fecha_fin`, `estado`)
SELECT c.id, @plan, NOW(), DATE_ADD(CURDATE(), INTERVAL 8 MONTH), 'A'
FROM `clientes` c
INNER JOIN `usuarios` u ON u.id = c.usuario_id
WHERE u.email LIKE 'cliente%@demo.local'
  AND @plan IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `cliente_subscripcion` cs
      WHERE cs.cliente_id = c.id AND cs.estado = 'A' AND cs.fecha_fin >= NOW()
  );

SET @sala1 := (SELECT id FROM `salas` WHERE nombre = 'sala1' ORDER BY id LIMIT 1);
SET @sala2 := (SELECT id FROM `salas` WHERE nombre = 'sala2' ORDER BY id LIMIT 1);
SET @sala3 := (SELECT id FROM `salas` WHERE nombre = 'sala3' ORDER BY id LIMIT 1);
SET @sala1 := IFNULL(@sala1, (SELECT id FROM `salas` ORDER BY id LIMIT 1));
SET @sala2 := IFNULL(@sala2, @sala1);
SET @sala3 := IFNULL(@sala3, @sala1);

INSERT INTO `actividades` (`nombre`, `color`, `duracion`, `monitor_id`, `sala_id`, `fecha_inicio`, `fecha_fin`, `dia_semana`, `descripcion`, `plazas`, `recurrente`)
SELECT 'actividad4', '#7C3AED', 45, m.id, @sala1, '2026-11-17 19:00:00', '2026-11-17 19:45:00', 'M', 'descripcion4', 10, 1
FROM `monitores` m
INNER JOIN `usuarios` u ON u.id = m.usuario_id
WHERE u.email = 'monitor3@demo.local' AND @sala1 IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM `actividades` a WHERE a.nombre = 'actividad4');

INSERT INTO `actividades` (`nombre`, `color`, `duracion`, `monitor_id`, `sala_id`, `fecha_inicio`, `fecha_fin`, `dia_semana`, `descripcion`, `plazas`, `recurrente`)
SELECT 'actividad5', '#0F766E', 45, m.id, @sala2, '2026-11-20 09:30:00', '2026-11-20 10:15:00', 'V', 'descripcion5', 10, 1
FROM `monitores` m
INNER JOIN `usuarios` u ON u.id = m.usuario_id
WHERE u.email = 'monitor4@demo.local' AND @sala2 IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM `actividades` a WHERE a.nombre = 'actividad5');

INSERT INTO `actividades` (`nombre`, `color`, `duracion`, `monitor_id`, `sala_id`, `fecha_inicio`, `fecha_fin`, `dia_semana`, `descripcion`, `plazas`, `recurrente`)
SELECT 'actividad6', '#CA8A04', 45, m.id, @sala3, '2026-11-21 11:00:00', '2026-11-21 11:45:00', 'S', 'descripcion6', 8, 1
FROM `monitores` m
INNER JOIN `usuarios` u ON u.id = m.usuario_id
WHERE u.email = 'monitor5@demo.local' AND @sala3 IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM `actividades` a WHERE a.nombre = 'actividad6');

INSERT INTO `actividades` (`nombre`, `color`, `duracion`, `monitor_id`, `sala_id`, `fecha_inicio`, `fecha_fin`, `dia_semana`, `descripcion`, `plazas`, `recurrente`)
SELECT 'actividad7', '#DB2777', 45, m.id, @sala1, '2026-11-19 19:00:00', '2026-11-19 19:45:00', 'J', 'descripcion7', 12, 1
FROM `monitores` m
INNER JOIN `usuarios` u ON u.id = m.usuario_id
WHERE u.email = 'monitor6@demo.local' AND @sala1 IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM `actividades` a WHERE a.nombre = 'actividad7');

INSERT IGNORE INTO `actividad_dias` (`actividad_id`, `dia_semana`)
SELECT a.id, a.dia_semana
FROM `actividades` a
WHERE a.nombre IN ('actividad4', 'actividad5', 'actividad6', 'actividad7');

SET @act := (SELECT id FROM `actividades` WHERE nombre = 'actividad1' ORDER BY id LIMIT 1);
SET @plazas := (SELECT plazas FROM `actividades` WHERE id = @act);
SET @lunes := DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY);
SET @lunes_sig := DATE_ADD(@lunes, INTERVAL 7 DAY);

INSERT INTO `inscripciones` (`cliente_id`, `actividad_id`, `fecha_ocurrencia`, `asistio`, `fecha_inscripcion`)
SELECT c.id, @act, @lunes, 'S', NOW()
FROM `clientes` c
INNER JOIN `usuarios` u ON u.id = c.usuario_id
WHERE @act IS NOT NULL
  AND u.email LIKE 'cliente%@demo.local'
  AND (
      SELECT COUNT(*)
      FROM `clientes` c2
      INNER JOIN `usuarios` u2 ON u2.id = c2.usuario_id
      WHERE u2.email LIKE 'cliente%@demo.local' AND c2.id <= c.id
  ) <= @plazas
  AND NOT EXISTS (
      SELECT 1 FROM `inscripciones` i
      WHERE i.cliente_id = c.id AND i.actividad_id = @act AND i.fecha_ocurrencia = @lunes
  );

INSERT INTO `inscripciones` (`cliente_id`, `actividad_id`, `fecha_ocurrencia`, `asistio`, `fecha_inscripcion`)
SELECT c.id, @act, @lunes_sig, 'S', NOW()
FROM `clientes` c
INNER JOIN `usuarios` u ON u.id = c.usuario_id
WHERE @act IS NOT NULL
  AND u.email LIKE 'cliente%@demo.local'
  AND (
      SELECT COUNT(*)
      FROM `clientes` c2
      INNER JOIN `usuarios` u2 ON u2.id = c2.usuario_id
      WHERE u2.email LIKE 'cliente%@demo.local' AND c2.id <= c.id
  ) <= @plazas
  AND NOT EXISTS (
      SELECT 1 FROM `inscripciones` i
      WHERE i.cliente_id = c.id AND i.actividad_id = @act AND i.fecha_ocurrencia = @lunes_sig
  );

INSERT IGNORE INTO `gp_ajuste` (`clave`, `aplicado_en`) VALUES
('demo_amplia_20261119', NOW()),
('demo_fisios_20261119', NOW());

DROP TEMPORARY TABLE IF EXISTS `tmp_demo_cuentas`;