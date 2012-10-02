<?php
/**
 * Quitamos las comunidades. Su implementación va a estar en plugins.
 */

// Borro campo de los posts.
Database::get_instance()->update('ALTER TABLE `post` DROP COLUMN `comunidad_id`;');

// Borramos la tablas de las comunidades.
Database::get_instance()->update('DROP TABLE `comunidad_miembro`;');
Database::get_instance()->update('DROP TABLE `comunidad_seguidor`;');
Database::get_instance()->update('DROP TABLE `comunidad_comentario_voto`;');
Database::get_instance()->update('DROP TABLE `comunidad_comentario`;');
Database::get_instance()->update('DROP TABLE `comunidad`;');