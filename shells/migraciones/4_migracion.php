<?php
/**
 * Modificaciones de los rangos de los usuarios.
 */

// Orden en el rango.
Database::get_instance()->update('ALTER TABLE `usuario_rango` ADD COLUMN `orden` int(11) NOT NULL DEFAULT 0');

// Indice UNIQUE al orden de los rangost.
Database::get_instance()->update('ALTER TABLE `usuario_rango` ADD UNIQUE(`orden`)');

// Actualizo el nombre de la tabla de suspensiones de usuario.
Database::get_instance()->update("RENAME TABLE `usuario_suspencion` TO `usuario_suspension`");

// Clave UNIQUE para suspensiones de usuario.
Database::get_instance()->update('ALTER TABLE `usuario_suspension` ADD UNIQUE (`usuario_id`)');