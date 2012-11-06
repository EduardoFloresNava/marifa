<?php
/**
 * Permitimos cerrar comentarios en posts.
 */

// Creamos la tabla para denunciar fotos.
Database::get_instance()->update('ALTER TABLE `post` ADD `comentar` BIT NOT NULL DEFAULT 1');