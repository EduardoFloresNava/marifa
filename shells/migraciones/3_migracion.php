<?php

// Indice CACHE tags en posts.
if (Database::get_instance()->update('ALTER TABLE `post` ADD COLUMN `tags` varchar(250) NULL DEFAULT NULL') <= 0)
{
	throw new Database_Exception("No se pudo agregar el campo tags en la tabla post.");
}
else
{
	// Generamos los valores.
	$rst = Database::get_instance()->query('SELECT id FROM post')->get_pairs();
	foreach ($rst as $v)
	{
		$keys = implode(', ', Database::get_instance()->query('SELECT nombre FROM post_tag WHERE post_id = ?', (int) $v)->get_pairs());
		if ($keys !== '')
		{
			Database::get_instance()->update('UPDATE post SET tags = ? WHERE id = ?', array($keys, (int) $v));
		}
	}
}

// Indice FULLTEXT post.
if (Database::get_instance()->update('ALTER TABLE `post` ADD FULLTEXT INDEX `busqueda` (`titulo`, `contenido`, `tags`)') <= 0)
{
	throw new Database_Exception("No se pudieron agregar los indices FULLTEXT a la tabla post.");
}