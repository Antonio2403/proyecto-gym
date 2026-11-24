-- Solo los dos fisios. Para monitores, clientes, la clase llena y los fisios juntos,
-- usa scripts/datos_demo_completos.sql.
--
-- Spartum: dos fisioterapeutas más.
-- Se puede ejecutar sobre la base que ya está en marcha. No borra nada
-- y no duplica si el correo ya existe.
--
--   fisio2@demo.local   Fisio2#Demo2026!   especialidad: deportiva
--   fisio3@demo.local   Fisio3#Demo2026!   especialidad: recuperacion

INSERT INTO `usuarios` (`DNI`, `nombre`, `apellido1`, `apellido2`, `email`, `clave`, `telefono`, `password_changed_at`, `email_confirmado`)
SELECT '10000037M', 'fisio2', 'apellido1', 'apellido2', 'fisio2@demo.local', '$2y$10$hj4KU1Iqga3JHqdNF6tSTOjONaQc.YKYMrEcUcTk2AKVJeL9HVq8q', '600000037', NOW(), 1
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM `usuarios` WHERE `email` = 'fisio2@demo.local' OR `DNI` = '10000037M'
);

INSERT INTO `usuarios` (`DNI`, `nombre`, `apellido1`, `apellido2`, `email`, `clave`, `telefono`, `password_changed_at`, `email_confirmado`)
SELECT '10000038Y', 'fisio3', 'apellido1', 'apellido2', 'fisio3@demo.local', '$2y$10$s9P961uZVEHAmLUaDTyREOZKff0YyTVh.QvieWZPZem6HCLzlg5Eu', '600000038', NOW(), 1
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM `usuarios` WHERE `email` = 'fisio3@demo.local' OR `DNI` = '10000038Y'
);

INSERT INTO `fisioterapeutas` (`nombre`, `especialidad`, `usuario_id`)
SELECT 'fisio2', 'deportiva', u.id
FROM `usuarios` u
WHERE u.email = 'fisio2@demo.local'
  AND NOT EXISTS (SELECT 1 FROM `fisioterapeutas` f WHERE f.usuario_id = u.id);

INSERT INTO `fisioterapeutas` (`nombre`, `especialidad`, `usuario_id`)
SELECT 'fisio3', 'recuperacion', u.id
FROM `usuarios` u
WHERE u.email = 'fisio3@demo.local'
  AND NOT EXISTS (SELECT 1 FROM `fisioterapeutas` f WHERE f.usuario_id = u.id);
