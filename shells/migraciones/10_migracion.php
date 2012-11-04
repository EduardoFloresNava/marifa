<?php
/**
 * Modificación a la forma de los sucesos.
 */

// Agregamos campo para saber cuales van a la lista de notificaciones.
Database::get_instance()->update('ALTER TABLE `suceso` ADD `notificar` BIT NOT NULL DEFAULT 0 AFTER `tipo`');

// Agregamos campo para saber cuales han sidos mostrados en la lista de notificaciones.
Database::get_instance()->update('ALTER TABLE `suceso` ADD `visto` BIT NOT NULL DEFAULT 0 AFTER `notificar`');

// Borramos tabla inutilizada por el nuevo paradigma.
Database::get_instance()->update('DROP TABLE `suceso_visto`');