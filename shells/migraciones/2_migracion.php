<?php

// Clave voto usuario.
if (Database::get_instance()->update('ALTER TABLE `foto_voto` ADD COLUMN `cantidad` int(11) NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY(`foto_id`,`usuario_id`,`cantidad`)'))
{
	throw new Database_Exception("No se pudo agregar la cantidad de puntos a los votos de los usuarios.");
}

// Insertamos categorias.
$sql = 'INSERT INTO `post_categoria` (`nombre`, `seo`, `imagen`) VALUES (?, ?, ?)';

$datos = array();
$datos[] = array("Animaciones", "animaciones", "flash.png");
$datos[] = array("Apuntes y Monografías", "apuntesymonografias", "report.png");
$datos[] = array("Arte", "arte", "palette.png");
$datos[] = array("Autos y Motos", "autosymotos", "car.png");
$datos[] = array("Celulares", "celulares", "phone.png");
$datos[] = array("Ciencia y Educación", "cienciayeducacion", "lab.png");
$datos[] = array("Comics", "comics", "comic.png");
$datos[] = array("Deportes", "deportes", "sport.png");
$datos[] = array("Downloads", "downloads", "disk.png");
$datos[] = array("E-books y Tutoriales", "ebooksytutoriales", "ebook.png");
$datos[] = array("Ecología", "ecologia", "nature.png");
$datos[] = array("Economía y Negocios", "economiaynegocios", "economy.png");
$datos[] = array("Femme", "femme", "female.png");
$datos[] = array("Hazlo tu mismo", "hazlotumismo", "escuadra.png");
$datos[] = array("Humor", "humor", "humor.png");
$datos[] = array("Imágenes", "imagenes", "photo.png");
$datos[] = array("Info", "info", "book.png");
$datos[] = array("Juegos", "juegos", "controller.png");
$datos[] = array("Links", "links", "link.png");
$datos[] = array("Linux", "linux", "tux.png");
$datos[] = array("Mac", "mac", "mac.png");
$datos[] = array("Manga y Anime", "mangayanime", "manga.png");
$datos[] = array("Mascotas", "mascotas", "pet.png");
$datos[] = array("Música", "musica", "music.png");
$datos[] = array("Noticias", "noticias", "newspaper.png");
$datos[] = array("Off Topic", "offtopic", "comments.png");
$datos[] = array("Recetas y Cocina", "recetasycocina", "cake.png");
$datos[] = array("Salud y Bienestar", "saludybienestar", "heart.png");
$datos[] = array("Solidaridad", "solidaridad", "salva.png");
$datos[] = array("Prueba", "prueba", "tscript.png");
$datos[] = array("Turismo", "turismo", "brujula.png");
$datos[] = array("TV, Peliculas y series", "tvpeliculasyseries", "tv.png");
$datos[] = array("Videos On-line", "videosonline", "film.png");

// Ejecutamos modificaciones.
foreach ($datos as $dt)
{
	list(, $c) = Database::get_instance()->insert($sql, $dt);
	if ($c < 0)
	{
		throw new Database_Exception("Error insertando categoria: '{$dt[0]}'");
	}
}

// Largo contraseña.
if (Database::get_instance()->update('ALTER TABLE `usuario` CHANGE `password` `password` VARCHAR( 60 ) NOT NULL'))
{
	throw new Database_Exception("No se pudo modificar el largo de la contraseña del usuario.");
}


// Nueva clave usuario_perfil.
if (Database::get_instance()->update('ALTER TABLE `usuario_perfil` DROP PRIMARY KEY, ADD PRIMARY KEY(`usuario_id`,`campo`)'))
{
	throw new Database_Exception("No se pudo modificar la clave del perfil del usuario.");
}

return TRUE;