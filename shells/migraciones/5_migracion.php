<?php
/**
 * Modificaciones de las categorias. Se juntan e implementan para post, fotos y comunidades.
 */

// Cambiamos campo comunidades.
Database::get_instance()->update('ALTER TABLE `comunidad` CHANGE `comunidad_categoria_id` `categoria_id` INT NOT NULL');

// Quitamos categorias comunidades.
Database::get_instance()->update('DROP TABLE `comunidad_categoria`');

// Renombramos la tabla de categorias.
Database::get_instance()->update("RENAME TABLE `post_categoria` TO `categoria`");

// Renombramos el campo de las categorias de los posts.
Database::get_instance()->update('ALTER TABLE `post` CHANGE `post_categoria_id` `categoria_id` INT NOT NULL');

// Agregamos categoria a las fotos.
Database::get_instance()->update('ALTER TABLE `foto` ADD `categoria_id` INT NOT NULL');