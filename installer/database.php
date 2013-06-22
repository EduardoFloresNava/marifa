<?php
/**
 * database.php is part of Marifa.
 *
 * Marifa is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Marifa is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Marifa. If not, see <http://www.gnu.org/licenses/>.
 *
 * @license     http://www.gnu.org/licenses/gpl-3.0-standalone.html GNU Public License
 * @since		Versión 0.1
 * @filesource
 * @package		Marifa\Installer
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Listado de consultas a ejecutar para instalar el sistema.
 */
$consultas = array();

/**
 * El formato de las consultas es:
 * array('NOMBRE_DE_LA_TABLA', array(LISTADO_DE_CONSULTAS));
 * Donde NOMBRE_DE_LA_TABLA es una descripción a mostrar en la vista para informar al usuario.
 * LISTADO_DE_CONSULTAS son un listado de arreglos. Cada elemento es una consulta que se ejecutan en una transacción si se permite.
 * Cada consulta tiene el formato: array('tipo', 'consulta', 'parametros', 'verificar').
 * tipo puede ser: INSERT, DELETE, UPDATE, QUERY, ALTER
 * parametros una lista de parametros a inyectar en la consulta.
 * verificar, arreglo con claves a utilizar para verificar si hay que implementarlo.
 *	  Posibles valores de verificar:
 *        error_no: Si el número de error coincide, se toma como correcta.
 */

/**
 * Categorias con sus valores.
 */
$consultas[] = array(
	'Tabla de categorias',
	array(
		array('ALTER', 'CREATE TABLE `categoria` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`nombre` varchar(50) NOT NULL,
				`seo` varchar(50) NOT NULL,
				`imagen` varchar(32) NOT NULL DEFAULT \'\',
				UNIQUE INDEX `seo` (`seo`),
				PRIMARY KEY (`id`)
			) ENGINE = MYISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;', NULL, array('error_no' => 1050)
		),
		array('INSERT', 'INSERT INTO categoria (nombre, seo, imagen) VALUES (?, ?, ?)', array('Animaciones', 'animaciones', 'flash'), array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO categoria (nombre, seo, imagen) VALUES (?, ?, ?)', array('Apuntes y Monografías', 'apuntes-y-monografias', 'report'), array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO categoria (nombre, seo, imagen) VALUES (?, ?, ?)', array('Arte', 'arte', 'palette'), array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO categoria (nombre, seo, imagen) VALUES (?, ?, ?)', array('Autos y Motos', 'autos-y-motos', 'car'), array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO categoria (nombre, seo, imagen) VALUES (?, ?, ?)', array('Celulares', 'celulares', 'phone'), array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO categoria (nombre, seo, imagen) VALUES (?, ?, ?)', array('Ciencia y Educación', 'ciencia-y-educacion', 'lab'), array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO categoria (nombre, seo, imagen) VALUES (?, ?, ?)', array('Comics', 'comics', 'comic'), array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO categoria (nombre, seo, imagen) VALUES (?, ?, ?)', array('Deportes', 'deportes', 'sport'), array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO categoria (nombre, seo, imagen) VALUES (?, ?, ?)', array('Downloads', 'downloads', 'disk'), array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO categoria (nombre, seo, imagen) VALUES (?, ?, ?)', array('E-books y Tutoriales', 'ebooks-y-tutoriales', 'ebook'), array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO categoria (nombre, seo, imagen) VALUES (?, ?, ?)', array('Ecología', 'ecologia', 'nature'), array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO categoria (nombre, seo, imagen) VALUES (?, ?, ?)', array('Economía y Negocios', 'economia-y-negocios', 'economy'), array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO categoria (nombre, seo, imagen) VALUES (?, ?, ?)', array('Femme', 'femme', 'female'), array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO categoria (nombre, seo, imagen) VALUES (?, ?, ?)', array('Hazlo tu mismo', 'hazlo-tu-mismo', 'escuadra'), array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO categoria (nombre, seo, imagen) VALUES (?, ?, ?)', array('Humor', 'humor', 'humor'), array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO categoria (nombre, seo, imagen) VALUES (?, ?, ?)', array('Imágenes', 'imagenes', 'photo'), array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO categoria (nombre, seo, imagen) VALUES (?, ?, ?)', array('Info', 'info', 'book'), array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO categoria (nombre, seo, imagen) VALUES (?, ?, ?)', array('Juegos', 'juegos', 'controller'), array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO categoria (nombre, seo, imagen) VALUES (?, ?, ?)', array('Links', 'links', 'link'), array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO categoria (nombre, seo, imagen) VALUES (?, ?, ?)', array('Linux', 'linux', 'tux'), array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO categoria (nombre, seo, imagen) VALUES (?, ?, ?)', array('Mac', 'mac', 'mac'), array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO categoria (nombre, seo, imagen) VALUES (?, ?, ?)', array('Manga y Anime', 'manga-y-anime', 'manga'), array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO categoria (nombre, seo, imagen) VALUES (?, ?, ?)', array('Mascotas', 'mascotas', 'pet'), array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO categoria (nombre, seo, imagen) VALUES (?, ?, ?)', array('Música', 'musica', 'music'), array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO categoria (nombre, seo, imagen) VALUES (?, ?, ?)', array('Noticias', 'noticias', 'newspaper'), array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO categoria (nombre, seo, imagen) VALUES (?, ?, ?)', array('Off Topic', 'off-topic', 'comments'), array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO categoria (nombre, seo, imagen) VALUES (?, ?, ?)', array('Recetas y Cocina', 'recetas-y-cocina', 'cake'), array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO categoria (nombre, seo, imagen) VALUES (?, ?, ?)', array('Salud y Bienestar', 'salud-y-bienestar', 'heart'), array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO categoria (nombre, seo, imagen) VALUES (?, ?, ?)', array('Solidaridad', 'solidaridad', 'salva'), array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO categoria (nombre, seo, imagen) VALUES (?, ?, ?)', array('Prueba', 'prueba', 'tscript'), array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO categoria (nombre, seo, imagen) VALUES (?, ?, ?)', array('Turismo', 'turismo', 'brujula'), array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO categoria (nombre, seo, imagen) VALUES (?, ?, ?)', array('TV, Peliculas y series', 'tv-peliculas-y-series', 'tv'), array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO categoria (nombre, seo, imagen) VALUES (?, ?, ?)', array('Videos On-line', 'videos-online', 'film'), array('error_no' => 1062))
	)
);

// Tabla de configuraciones.
$consultas[] = array(
	'Tabla de configuraciones',
	array(
		array('ALTER', 'CREATE TABLE `configuracion` (
				`clave` varchar(100) NOT NULL,
				`valor` mediumtext,
				`defecto` mediumtext,
				PRIMARY KEY (`clave`)
			) ENGINE = MYISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;', NULL, array('error_no' => 1050)
		),
		array('INSERT', 'INSERT INTO configuracion (clave, valor, defecto) VALUES (?, ?, ?)', array('registro', 1, 1), array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO configuracion (clave, valor, defecto) VALUES (?, ?, ?)', array('activacion_usuario', 2, 2), array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO configuracion (clave, valor, defecto) VALUES (?, ?, ?)', array('elementos_pagina', 20, 20), array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO configuracion (clave, valor, defecto) VALUES (?, ?, ?)', array('ip_mantenimiento', serialize(array()), serialize(array())), array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO configuracion (clave, valor, defecto) VALUES (?, ?, ?)', array('rango_defecto', 3, 3), array('error_no' => 1062)),
		array('UPDATE', 'UPDATE configuracion SET valor = ? WHERE clave = ?', array(VERSION, 'version_actual'), array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO configuracion (clave, valor, defecto) VALUES (?, ?, ?)', array('version_actual', VERSION, VERSION), array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO configuracion (clave, valor, defecto) VALUES (?, ?, ?)', array('habilitar_fotos', 1, 1), array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO configuracion (clave, valor, defecto) VALUES (?, ?, ?)', array('privacidad_fotos', 1, 1), array('error_no' => 1062))
	)
);

// Tabla de fotos.
$consultas[] = array(
	'Tabla de fotos',
	array(
		array('ALTER', 'CREATE TABLE `foto` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`usuario_id` int(11) NOT NULL,
				`creacion` datetime NOT NULL,
				`titulo` varchar(200) NOT NULL,
				`descripcion` mediumtext NOT NULL,
				`url` varchar(300) DEFAULT NULL,
				`estado` int(11) NOT NULL DEFAULT 0,
				`ultima_visita` datetime DEFAULT NULL,
				`visitas` int(11) DEFAULT NULL,
				`categoria_id` int(11) DEFAULT NULL,
				`comentar` bit(1) NOT NULL,
				PRIMARY KEY (`id`),
				KEY `usuario_id` (`usuario_id`)
			) ENGINE = MYISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;', NULL, array('error_no' => 1050)
		)
	)
);

// Tabla de comentarios en fotos.
$consultas[] = array(
	'Tabla de comentario en fotos',
	array(
		array('ALTER', 'CREATE TABLE `foto_comentario` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`foto_id` int(11) NOT NULL,
				`usuario_id` int(11) NOT NULL,
				`comentario` mediumtext NOT NULL,
				`fecha` datetime NOT NULL,
				`estado` int(11) NOT NULL,
				PRIMARY KEY (`id`),
				KEY `foto_id` (`foto_id`),
				KEY `usuario_id` (`usuario_id`)
			) ENGINE = MYISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;', NULL, array('error_no' => 1050)
		)
	)
);

// Tabla de denuncias a fotos.
$consultas[] = array(
	'Tabla de denuncias a fotos',
	array(
		array('ALTER', 'CREATE TABLE `foto_denuncia` (
				`id` INTEGER NOT NULL AUTO_INCREMENT,
				`foto_id` INTEGER NOT NULL,
				`usuario_id` INTEGER NOT NULL,
				`motivo` INTEGER NOT NULL,
				`comentario` MEDIUMTEXT NULL DEFAULT NULL,
				`fecha` DATETIME NOT NULL,
				`estado` INTEGER NOT NULL DEFAULT 0,
				PRIMARY KEY (`id`)
			) ENGINE = MYISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;', NULL, array('error_no' => 1050)
		)
	)
);

// Tabla de favoritos de fotos.
$consultas[] = array(
	'Tabla de favoritos de fotos',
	array(
		array('ALTER', 'CREATE TABLE `foto_favorito` (
				`foto_id` int(11) NOT NULL,
				`usuario_id` int(11) NOT NULL,
				PRIMARY KEY (`foto_id`,`usuario_id`),
				KEY `usuario_id` (`usuario_id`)
			) ENGINE = MYISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;', NULL, array('error_no' => 1050)
		)
	)
);

// Tabla de votos a fotos.
$consultas[] = array(
	'Tabla de votos a fotos',
	array(
		array('ALTER', 'CREATE TABLE `foto_voto` (
				`foto_id` int(11) NOT NULL,
				`usuario_id` int(11) NOT NULL,
				`cantidad` int(11) NOT NULL,
				PRIMARY KEY (`foto_id`,`usuario_id`,`cantidad`),
				KEY `usuario_id` (`usuario_id`)
			) ENGINE = MYISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;', NULL, array('error_no' => 1050)
		)
	)
);

// Tabla de mensajes entre usuarios a fotos.
$consultas[] = array(
	'Tabla de mensajeria',
	array(
		array('ALTER', 'CREATE TABLE `mensaje` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`emisor_id` int(11) NULL,
				`receptor_id` int(11) NOT NULL,
				`estado` int(11) NOT NULL DEFAULT 0,
				`asunto` varchar(200) NOT NULL,
				`contenido` mediumtext NOT NULL,
				`fecha` datetime NOT NULL,
				`padre_id` int(11) DEFAULT NULL,
				PRIMARY KEY (`id`),
				KEY `emisor_id` (`emisor_id`),
				KEY `receptor_id` (`receptor_id`),
				KEY `padre_id` (`padre_id`)
			) ENGINE = MYISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;', NULL, array('error_no' => 1050)
		)
	)
);

// Tabla de noticias.
$consultas[] = array(
	'Tabla de noticias',
	array(
		array('ALTER', 'CREATE TABLE `noticia` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`usuario_id` int(11) NOT NULL,
				`contenido` mediumtext NOT NULL,
				`fecha` datetime NOT NULL,
				`estado` int(11) NOT NULL,
				PRIMARY KEY (`id`),
				KEY `usuario_id` (`usuario_id`)
			) ENGINE = MYISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;', NULL, array('error_no' => 1050)
		)
	)
);

// Tabla de posts.
$consultas[] = array(
	'Tabla de posts',
	array(
		array('ALTER', 'CREATE TABLE `post` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`usuario_id` int(11) NOT NULL,
				`categoria_id` int(11) NOT NULL,
				`titulo` varchar(200) NOT NULL,
				`contenido` mediumtext NOT NULL,
				`fecha` datetime NOT NULL,
				`vistas` int(11) NOT NULL DEFAULT 0,
				`privado` bit(1) NOT NULL DEFAULT b\'0\',
				`sponsored` bit(1) NOT NULL DEFAULT b\'0\',
				`sticky` bit(1) NOT NULL DEFAULT b\'0\',
				`estado` int(11) NOT NULL DEFAULT 0,
				`tags` varchar(250) DEFAULT NULL,
				`comentar` bit(1) NOT NULL DEFAULT b\'1\',
				PRIMARY KEY (`id`),
				KEY `usuario_id` (`usuario_id`),
				KEY `post_categoria_id` (`categoria_id`),
				FULLTEXT KEY `busqueda` (`titulo`,`contenido`,`tags`)
			) ENGINE = MYISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;', NULL, array('error_no' => 1050)
		)
	)
);

// Tabla de comentarios en posts.
$consultas[] = array(
	'Tabla de comentarios en posts',
	array(
		array('ALTER', 'CREATE TABLE `post_comentario` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`post_id` int(11) NOT NULL,
				`usuario_id` int(11) NOT NULL,
				`fecha` datetime NOT NULL,
				`contenido` mediumtext NOT NULL,
				`estado` int(11) NOT NULL DEFAULT 0,
				PRIMARY KEY (`id`),
				KEY `post_id` (`post_id`),
				KEY `usuario_id` (`usuario_id`)
			) ENGINE = MYISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;', NULL, array('error_no' => 1050)
		)
	)
);

// Tabla de votos a comentarios en posts.
$consultas[] = array(
	'Tabla de votos a comentarios en posts',
	array(
		array('ALTER', 'CREATE TABLE `post_comentario_voto` (
				`post_comentario_id` int(11) NOT NULL,
				`usuario_id` int(11) NOT NULL,
				`cantidad` int(11) NOT NULL DEFAULT 1,
				PRIMARY KEY (`post_comentario_id`,`usuario_id`),
				KEY `usuario_id` (`usuario_id`)
			) ENGINE = MYISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;', NULL, array('error_no' => 1050)
		)
	)
);

// Tabla de posts compartidos.
$consultas[] = array(
	'Tabla de posts compartidos',
	array(
		array('ALTER', 'CREATE TABLE `post_compartido` (
				`post_id` int(11) NOT NULL,
				`usuario_id` int(11) NOT NULL,
				PRIMARY KEY (`post_id`,`usuario_id`),
				KEY `usuario_id` (`usuario_id`)
			) ENGINE = MYISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;', NULL, array('error_no' => 1050)
		)
	)
);

// Tabla de denuncias a posts.
$consultas[] = array(
	'Tabla de denuncias a posts',
	array(
		array('ALTER', 'CREATE TABLE `post_denuncia` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`post_id` int(11) NOT NULL,
				`usuario_id` int(11) NOT NULL,
				`motivo` int(11) NOT NULL,
				`comentario` mediumtext,
				`fecha` datetime NOT NULL,
				`estado` int(11) NOT NULL DEFAULT 0,
				PRIMARY KEY (`id`),
				KEY `post_id` (`post_id`),
				KEY `usuario_id` (`usuario_id`)
			) ENGINE = MYISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;', NULL, array('error_no' => 1050)
		)
	)
);

// Tabla de favoritos a posts.
$consultas[] = array(
	'Tabla de favoritos a posts',
	array(
		array('ALTER', 'CREATE TABLE  `post_favorito` (
				`post_id` int(11) NOT NULL,
				`usuario_id` int(11) NOT NULL,
				PRIMARY KEY (`post_id`,`usuario_id`),
				KEY `usuario_id` (`usuario_id`)
			) ENGINE = MYISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;', NULL, array('error_no' => 1050)
		)
	)
);

// Tabla de moderaciones de posts.
$consultas[] = array(
	'Tabla de moderaciones de posts',
	array(
		array('ALTER', 'CREATE TABLE  `post_moderado` (
				`post_id` int(11) NOT NULL DEFAULT 0,
				`usuario_id` int(11) NOT NULL,
				`tipo` int(11) NOT NULL,
				`padre_id` int(11) DEFAULT NULL,
				`razon` text,
				PRIMARY KEY (`post_id`),
				KEY `usuario_id` (`usuario_id`),
				KEY `padre_id` (`padre_id`)
			) ENGINE = MYISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;', NULL, array('error_no' => 1050)
		)
	)
);

// Tabla de puntos en posts.
$consultas[] = array(
	'Tabla de puntos en posts',
	array(
		array('ALTER', 'CREATE TABLE  `post_punto` (
				`post_id` int(11) NOT NULL,
				`usuario_id` int(11) NOT NULL,
				`cantidad` int(11) NOT NULL DEFAULT 1,
				PRIMARY KEY (`post_id`,`usuario_id`),
				KEY `usuario_id` (`usuario_id`)
			) ENGINE = MYISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;', NULL, array('error_no' => 1050)
		)
	)
);

// Tabla de seguidores de posts.
$consultas[] = array(
	'Tabla de seguidores de posts',
	array(
		array('ALTER', 'CREATE TABLE  `post_seguidor` (
				`post_id` int(11) NOT NULL,
				`usuario_id` int(11) NOT NULL,
				PRIMARY KEY (`post_id`,`usuario_id`),
				KEY `usuario_id` (`usuario_id`)
			) ENGINE = MYISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;', NULL, array('error_no' => 1050)
		)
	)
);

// Tabla de etiquetas de posts.
$consultas[] = array(
	'Tabla de etiquetas de posts',
	array(
		array('ALTER', 'CREATE TABLE  `post_tag` (
				`post_id` int(11) NOT NULL,
				`nombre` varchar(50) NOT NULL,
				PRIMARY KEY (`post_id`,`nombre`)
			) ENGINE = MYISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;', NULL, array('error_no' => 1050)
		)
	)
);

// Tabla de sessiones de usuarios.
$consultas[] = array(
	'Tabla de sessiones de usuarios',
	array(
		array('ALTER', 'CREATE TABLE  `session` (
				`id` varchar(32) NOT NULL,
				`usuario_id` int(11) NOT NULL,
				`ip` int(11) NOT NULL,
				`expira` datetime NOT NULL,
				PRIMARY KEY (`id`),
				KEY `usuario_id` (`usuario_id`)
			) ENGINE = MYISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;', NULL, array('error_no' => 1050)
		)
	)
);

// Tabla de sucesos.
$consultas[] = array(
	'Tabla de sucesos',
	array(
		array('ALTER', 'CREATE TABLE  `suceso` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`usuario_id` int(11) NOT NULL,
				`objeto_id` int(11) NOT NULL,
				`objeto_id1` int(11) DEFAULT NULL,
				`objeto_id2` int(11) DEFAULT NULL,
				`tipo` varchar(50) NOT NULL,
				`notificar` BIT NOT NULL DEFAULT 0,
				`visto` BIT NOT NULL DEFAULT 0,
				`desplegado` BIT(1) NOT NULL DEFAULT 0,
				`fecha` datetime NOT NULL,
				PRIMARY KEY (`id`),
				KEY `usuario_id` (`usuario_id`)
			) ENGINE = MYISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;', NULL, array('error_no' => 1050)
		)
	)
);

// Tabla de usuarios.
$consultas[] = array(
	'Tabla de usuarios',
	array(
		array('ALTER', 'CREATE TABLE  `usuario` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`nick` varchar(16) NOT NULL,
				`password` varchar(64) NOT NULL,
				`email` varchar(50) NOT NULL,
				`rango` int(11) NOT NULL DEFAULT 1,
				`puntos` int(11) NOT NULL DEFAULT 0,
				`registro` datetime NOT NULL,
				`lastlogin` datetime NULL DEFAULT NULL,
				`lastactive` datetime NULL DEFAULT NULL,
				`lastip` int(11) NULL DEFAULT NULL,
				`estado` int(11) NOT NULL DEFAULT 0,
				PRIMARY KEY (`id`),
				KEY `rango` (`rango`)
			) ENGINE = MYISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;', NULL, array('error_no' => 1050)
		)
	)
);

// Tabla de avisos a usuarios.
$consultas[] = array(
	'Tabla de avisos a usuarios',
	array(
		array('ALTER', 'CREATE TABLE  `usuario_aviso` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`usuario_id` int(11) NOT NULL,
				`moderador_id` int(11) NOT NULL,
				`asunto` varchar(50) NOT NULL,
				`contenido` mediumtext NOT NULL,
				`fecha` datetime NOT NULL,
				`estado` int(11) NOT NULL DEFAULT 0,
				PRIMARY KEY (`id`),
				KEY `usuario_id` (`usuario_id`),
				KEY `moderador_id` (`moderador_id`)
			) ENGINE = MYISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;', NULL, array('error_no' => 1050)
		)
	)
);

// Tabla de usuarios baneados.
$consultas[] = array(
	'Tabla de usuarios baneados',
	array(
		array('ALTER', 'CREATE TABLE  `usuario_baneo` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`usuario_id` int(11) NOT NULL,
				`moderador_id` int(11) NOT NULL,
				`tipo` int(11) NOT NULL,
				`razon` mediumtext NOT NULL,
				`fecha` datetime NOT NULL,
				PRIMARY KEY (`id`),
				KEY `usuario_id` (`usuario_id`),
				KEY `moderador_id` (`moderador_id`)
			) ENGINE = MYISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;', NULL, array('error_no' => 1050)
		)
	)
);

// Tabla de bloqueos entre usuarios.
$consultas[] = array(
	'Tabla de bloqueos entre usuarios',
	array(
		array('ALTER', 'CREATE TABLE  `usuario_bloqueo` (
				`usuario_id` int(11) NOT NULL,
				`bloqueado_id` int(11) NOT NULL,
				PRIMARY KEY (`usuario_id`,`bloqueado_id`),
				KEY `bloqueado_id` (`bloqueado_id`)
			) ENGINE = MYISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;', NULL, array('error_no' => 1050)
		)
	)
);

// Tabla de denuncias a usuarios.
$consultas[] = array(
	'Tabla de denuncias a usuarios',
	array(
		array('ALTER', 'CREATE TABLE  `usuario_denuncia` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`denunciado_id` int(11) NOT NULL,
				`usuario_id` int(11) NOT NULL,
				`motivo` int(11) NOT NULL,
				`comentario` mediumtext,
				`fecha` datetime NOT NULL,
				`estado` int(11) NOT NULL DEFAULT 0,
				PRIMARY KEY (`id`)
			) ENGINE = MYISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;', NULL, array('error_no' => 1050)
		)
	)
);




// Tabla de nick's del usuario.
$consultas[] = array(
	'Tabla de nick\'s del usuario',
	array(
		array('ALTER', 'CREATE TABLE  `usuario_nick` (
				`usuario_id` int(11) NOT NULL,
				`nick` varchar(16) NOT NULL,
				`fecha` datetime NOT NULL,
				PRIMARY KEY (`usuario_id`,`nick`,`fecha`),
				KEY `usuario_id` (`usuario_id`)
			) ENGINE = MYISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;', NULL, array('error_no' => 1050)
		)
	)
);

// Tabla de campos del perfil del usuario.
$consultas[] = array(
	'Tabla de campos del perfil del usuario',
	array(
		array('ALTER', 'CREATE TABLE  `usuario_perfil` (
				`usuario_id` int(11) NOT NULL,
				`campo` varchar(50) NOT NULL,
				`valor` mediumtext,
				PRIMARY KEY (`usuario_id`,`campo`)
			) ENGINE = MYISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;', NULL, array('error_no' => 1050)
		)
	)
);

// Tabla de rangos de usuarios.
//TODO: agregar más rangos por defecto.
$consultas[] = array(
	'Tabla de rangos de usuarios',
	array(
		array('ALTER', 'CREATE TABLE  `usuario_rango` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`nombre` varchar(32) NOT NULL,
				`descripcion` VARCHAR(250) NULL DEFAULT \'\',
				`color` int(11) NOT NULL,
				`imagen` varchar(50) NOT NULL,
				`orden` int(11) NOT NULL DEFAULT 1,
				`puntos` INT NOT NULL DEFAULT 10,
				`puntos_dar` INT NOT NULL DEFAULT 10,
				`tipo` INT NOT NULL DEFAULT 0,
				`cantidad` INT NULL DEFAULT NULL,
				PRIMARY KEY (`id`)
			) ENGINE = MYISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;', NULL, array('error_no' => 1050)
		),
		array('INSERT', 'INSERT INTO `usuario_rango` (`id`, `nombre`, `color`, `imagen`, `orden`, `puntos`, `tipo`, `cantidad`, `puntos_dar`) VALUES (1, \'Administrador\', 14025483, \'rosette\', 1, 50, 0, NULL, 20);', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO `usuario_rango` (`id`, `nombre`, `color`, `imagen`, `orden`, `puntos`, `tipo`, `cantidad`, `puntos_dar`) VALUES (2, \'Moderador\', 16750848, \'shield\', 2, 30, 0, NULL, 10);', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO `usuario_rango` (`id`, `nombre`, `color`, `imagen`, `orden`, `puntos`, `tipo`, `cantidad`, `puntos_dar`) VALUES (3, \'Novato\', 1513239, \'novato\', 7, 5, 0, NULL, 5);', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO `usuario_rango` (`id`, `nombre`, `color`, `imagen`, `orden`, `puntos`, `tipo`, `cantidad`, `puntos_dar`) VALUES (4, \'Great User\', 106529, \'star_gold_3\', 3, 15, 0, NULL, 11);', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO `usuario_rango` (`id`, `nombre`, `color`, `imagen`, `orden`, `puntos`, `tipo`, `cantidad`, `puntos_dar`) VALUES (5, \'New Full User\', 104679, \'star_bronze_3\', 4, 10, 1, 5, 10);', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO `usuario_rango` (`id`, `nombre`, `color`, `imagen`, `orden`, `puntos`, `tipo`, `cantidad`, `puntos_dar`) VALUES (6, \'Full User\', 52479, \'star_silver_3\', 5, 20, 1, 70, 20);', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO `usuario_rango` (`id`, `nombre`, `color`, `imagen`, `orden`, `puntos`, `tipo`, `cantidad`, `puntos_dar`) VALUES (7, \'Gold User\', 13395456, \'asterisk_yellow\', 6, 25, 1, 120, 25);', NULL, array('error_no' => 1062))
	)
);

// Tabla de permisos de rangos.
$consultas[] = array(
	'Tabla de permisos de rangos',
	array(
		array('ALTER', 'CREATE TABLE  `usuario_rango_permiso` (
				`rango_id` int(11) NOT NULL,
				`permiso` int(11) NOT NULL,
				PRIMARY KEY (`rango_id`,`permiso`)
			) ENGINE = MYISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;', NULL, array('error_no' => 1050)
		),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (1, 0)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (1, 1)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (1, 2)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (1, 3)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (1, 4)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (1, 5)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (1, 20)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (1, 21)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (1, 22)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (1, 23)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (1, 24)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (1, 25)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (1, 26)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (1, 27)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (1, 28)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (1, 40)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (1, 41)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (1, 42)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (1, 43)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (1, 44)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (1, 45)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (1, 46)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (1, 47)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (1, 60)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (1, 61)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (1, 62)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (1, 63)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (1, 64)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (1, 65)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (1, 66)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (1, 80)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (1, 81)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (1, 82)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (1, 83)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (2, 0)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (2, 1)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (2, 2)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (2, 3)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (2, 20)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (2, 21)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (2, 22)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (2, 23)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (2, 24)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (2, 25)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (2, 26)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (2, 27)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (2, 28)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (2, 40)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (2, 41)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (2, 42)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (2, 43)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (2, 44)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (2, 45)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (2, 46)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (2, 47)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (2, 60)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (2, 61)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (2, 62)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (2, 63)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (2, 64)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (2, 65)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (2, 66)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (3, 4)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (3, 20)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (3, 21)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (3, 40)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (3, 41)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (3, 60)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (3, 62)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (4, 4)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (4, 20)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (4, 21)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (4, 40)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (4, 41)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (4, 60)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (4, 62)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (5, 4)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (5, 20)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (5, 21)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (5, 40)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (5, 41)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (5, 60)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (5, 62)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (6, 4)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (6, 20)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (6, 21)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (6, 40)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (6, 41)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (6, 60)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (6, 62)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (7, 4)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (7, 20)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (7, 21)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (7, 40)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (7, 41)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (7, 60)', NULL, array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO usuario_rango_permiso (rango_id, permiso) VALUES (7, 62)', NULL, array('error_no' => 1062))
	)
);

// Tabla de recuperación de claves de usuarios.
$consultas[] = array(
	'Tabla de recuperación de claves de usuarios',
	array(
		array('ALTER', 'CREATE TABLE  `usuario_recuperacion` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`usuario_id` int(11) NOT NULL,
				`email` varchar(50) NOT NULL,
				`hash` varchar(32) NOT NULL,
				`fecha` datetime NOT NULL,
				`tipo` int(11) NOT NULL,
				PRIMARY KEY (`id`),
				KEY `usuario_id` (`usuario_id`)
			) ENGINE = MYISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;', NULL, array('error_no' => 1050)
		)
	)
);

// Tabla de seguidores de usuarios.
$consultas[] = array(
	'Tabla de seguidores de usuarios',
	array(
		array('ALTER', 'CREATE TABLE  `usuario_seguidor` (
				`usuario_id` int(11) NOT NULL,
				`seguidor_id` int(11) NOT NULL,
				`fecha` datetime NOT NULL,
				PRIMARY KEY (`usuario_id`,`seguidor_id`),
				KEY `seguidor_id` (`seguidor_id`)
			) ENGINE = MYISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;', NULL, array('error_no' => 1050)
		)
	)
);

// Tabla de suspensiones de usuarios.
$consultas[] = array(
	'Tabla de suspensiones de usuarios',
	array(
		array('ALTER', 'CREATE TABLE  `usuario_suspension` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`usuario_id` int(11) NOT NULL,
				`moderador_id` int(11) NOT NULL,
				`motivo` mediumtext NOT NULL,
				`inicio` datetime NOT NULL,
				`fin` datetime NOT NULL,
				PRIMARY KEY (`id`),
				UNIQUE KEY `usuario_id` (`usuario_id`),
				KEY `moderador_id` (`moderador_id`)
			) ENGINE = MYISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;', NULL, array('error_no' => 1050)
		)
	)
);

// Tabla de visitas a los perfiles del usuarios.
$consultas[] = array(
	'Tabla de visitas a los perfiles del usuarios',
	array(
		array('ALTER', 'CREATE TABLE  `usuario_visita` (
				`usuario_id` int(11) NOT NULL,
				`visitado_id` int(11) NOT NULL,
				`fecha` datetime NOT NULL,
				PRIMARY KEY (`usuario_id`,`visitado_id`,`fecha`),
				KEY `visitado_id` (`visitado_id`)
			) ENGINE = MYISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;', NULL, array('error_no' => 1050)
		)
	)
);

// Tabla de medallas.
$consultas[] = array(
	'Tabla de medallas',
	array(
		array(
			'ALTER', 'CREATE TABLE  `medalla` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`nombre` varchar(250) NOT NULL,
				`descripcion` text NOT NULL,
				`imagen` varchar(200) NOT NULL,
				`tipo` int(11) NOT NULL,
				`condicion` int(11) NOT NULL,
				`cantidad` int(11) NOT NULL,
				PRIMARY KEY (`id`),
				UNIQUE KEY `nombre` (`nombre`),
				UNIQUE KEY `tipo` (`tipo`, `condicion`, `cantidad`)
			) ENGINE = MYISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;', NULL, array('error_no' => 1050)
		)
	)
);

// Tabla de medallas de usuarios.
$consultas[] = array(
	'Tabla de medallas',
	array(
		array(
			'ALTER', 'CREATE TABLE  `usuario_medalla` (
				`usuario_id` int(11) NOT NULL,
				`medalla_id` int(11) NOT NULL,
				`objeto_id` int(11) NULL DEFAULT NULL,
				`fecha` datetime NOT NULL,
				PRIMARY KEY (`usuario_id`,`medalla_id`),
				KEY `medalla_id` (`medalla_id`)
			) ENGINE = MYISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;', NULL, array('error_no' => 1050)
		)
	)
);

// Tabla de shouts.
$consultas[] = array(
	'Tabla de shouts',
	array(
		array(
			'ALTER', 'CREATE TABLE IF NOT EXISTS `shout` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`usuario_id` int(11) NOT NULL,
				`mensaje` text NOT NULL,
				`tipo` int(11) NOT NULL DEFAULT 0,
				`valor` varchar(512) DEFAULT NULL,
				`fecha` datetime NOT NULL,
				PRIMARY KEY (`id`),
				KEY `usuario_id` (`usuario_id`)
			  ) ENGINE = MYISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;', NULL, array('error_no' => 1050)
		)
	)
);

// Tabla de comentarios en shout's.
$consultas[] = array(
	'Tabla de comentarios en shout\'s',
	array(
		array(
			'ALTER', 'CREATE TABLE IF NOT EXISTS `shout_comentario` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`usuario_id` int(11) NOT NULL,
				`shout_id` int(11) NOT NULL,
				`comentario` text NOT NULL,
				`fecha` datetime NOT NULL,
				PRIMARY KEY (`id`),
				KEY `usuario_id` (`usuario_id`,`shout_id`)
			  ) ENGINE = MYISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;', NULL, array('error_no' => 1050)
		)
	)
);

// Tabla de shout's favoritos.
$consultas[] = array(
	'Tabla de shout\'s favoritos',
	array(
		array(
			'ALTER', 'CREATE TABLE IF NOT EXISTS `shout_favorito` (
				`usuario_id` int(11) NOT NULL,
				`shout_id` int(11) NOT NULL,
				PRIMARY KEY (`usuario_id`,`shout_id`)
			  ) ENGINE = MYISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;', NULL, array('error_no' => 1050)
		)
	)
);

// Shout tag.
$consultas[] = array(
	'Tabla de etiquetas de los shout\'s',
	array(
		array(
			'ALTER', 'CREATE TABLE IF NOT EXISTS `shout_tag` (
				`tag` varchar(100) NOT NULL,
				`shout_id` int(11) NOT NULL,
				PRIMARY KEY (`tag`,`shout_id`)
			  ) ENGINE = MYISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;', NULL, array('error_no' => 1050)
		)
	)
);

// Citas a usuarios en shout's
$consultas[] = array(
	'Tabla de usuarios citados en shout\'s',
	array(
		array(
			'ALTER', 'CREATE TABLE IF NOT EXISTS `shout_usuario` (
				`usuario_id` int(11) NOT NULL,
				`shout_id` int(11) NOT NULL,
				PRIMARY KEY (`usuario_id`,`shout_id`)
			  ) ENGINE = MYISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;', NULL, array('error_no' => 1050)
		)
	)
);

// Votos a los shouts.
$consultas[] = array(
	'Tabla de votos a shout\'s',
	array(
		array(
			'ALTER', 'CREATE TABLE IF NOT EXISTS `shout_voto` (
				`usuario_id` int(11) NOT NULL,
				`shout_id` int(11) NOT NULL,
				PRIMARY KEY (`usuario_id`,`shout_id`)
			  ) ENGINE = MYISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;', NULL, array('error_no' => 1050)
		)
	)
);

// Censuras de palabras.
$consultas[] = array(
	'Censuras de palabras',
	array(
		array('ALTER', 'CREATE TABLE  `censurar_palabra` (
				`id` int NOT NULL AUTO_INCREMENT,
				`valor` varchar(250) NOT NULL,
				`tipo` int,
				`censura` varchar(250) NOT NULL,
				`estado` int NOT NULL,
				PRIMARY KEY (`id`),
				UNIQUE KEY `clave` (`valor`, `tipo`)
			) ENGINE = MYISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;', NULL, array('error_no' => 1050)
		)
	)
);

// Censuras de palabras.
$consultas[] = array(
	'Formulario de contacto',
	array(
		array('ALTER', 'CREATE TABLE `contacto` (
				`id` int NOT NULL AUTO_INCREMENT,
				`nombre` varchar(100) NOT NULL,
				`asunto` varchar(100) NOT NULL,
				`contenido` varchar(300) NOT NULL,
				`estado` int NOT NULL,
				PRIMARY KEY (`id`)
			) ENGINE = MYISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;', NULL, array('error_no' => 1050)
		)
	)
);

// Páginas estáticas.
$consultas[] = array(
	'Páginas estáticas',
	array(
		array('ALTER', 'CREATE TABLE `pagina` (
				`id` int NOT NULL AUTO_INCREMENT,
				`titulo` varchar(100) NOT NULL,
				`contenido` mediumtext NOT NULL,
				`menu` int NOT NULL,
				`estado` int NOT NULL,
				`creacion` datetime NOT NULL,
				`modificacion` datetime NULL,
				PRIMARY KEY (`id`)
			) ENGINE = MYISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;', NULL, array('error_no' => 1050)
		),
		array('INSERT', 'INSERT INTO `pagina` (`id`, `titulo`, `contenido`, `menu`, `estado`, `creacion`, `modificacion`) VALUES (?, ?, ?, ?, ?, ?, ?)', array(2, 'Privacidad de datos', "<div class=\"text-justify\">\n<h1 class=\"title\">Privacidad de datos</h1>\n<p>Las presentes declaraciones están destinadas a informar a los usuarios acerca del tratamiento de datos personales llevado adelante por {{MARIFA_NOMBRE}}, con el objeto que libre y voluntariamente determinen la entrega o no, de sus datos personales cuando les sean requeridos o, que se puedan obtener a partir de la utilización de alguno de los servicios disponibles en {{MARIFA_NOMBRE}}.</p>\n<p>{{MARIFA_NOMBRE}} considera que cualquier tipo de información relativa a un usuario es información de carácter personal, y por consiguiente vela en todo momento por la privacidad y confidencialidad de la misma. La confidencialidad de la información relativa a los usuarios implicará su mantenimiento en archivos y/o bancos o bases de datos seguros, de modo tal que el acceso por parte de terceros que no se encuentren autorizados a tal efecto, se encuentre restringido.</p>\n<p>Para preguntas sobre esta Política o cualquier circunstancia relativa al tratamiento de información de carácter personal, los usuarios podrán contactarse a través del formulario de contacto.</p>\n<ol>\n<li>\n<strong>Voluntariedad en la entrega de datos.</strong>\n<p>La presente Política está destinada a informar a los usuarios acerca del tratamiento de datos personales llevado adelante por {{MARIFA_NOMBRE}}, con el objeto que libre y voluntariamente determinen la entrega o no, de sus datos personales cuando les sean requeridos o que se puedan obtener a partir de la utilización de alguno de los servicios disponibles en \"{{HOSTNAME}}\".</p>\n<p>Por regla general, cuando para utilizar un servicio o acceder a cierto contenido se solicite algún dato personal, la entrega del mismo no es obligatoria, con excepción de aquellos casos donde específicamente se indicara que es un dato requerido para la prestación del servicio o el acceso al contenido.</p>\n</li>\n<li>\n<strong>Autorización de uso de la información personal.</strong>\n<p>El usuario que facilitara sus datos personales, autoriza expresamente a {{MARIFA_NOMBRE}} para el uso de los datos aportados con los fines aquí expuestos.<strong> </strong>Implica ello además la aceptación de todos los términos contenidos en esta Política, y en los Términos y Condiciones Generales.</p>\n</li>\n<li>\n<strong>Recolección y uso de Información.</strong>\n<p>La finalidad de la recolección y tratamiento de datos de carácter personal es la prestación, gestión, administración, personalización, actualización y mejora de los de los servicios y contenidos puestos a disposición de los usuarios por parte de {{MARIFA_NOMBRE}}.</p>\n</li>\n<li>\n<strong>Intransferibilidad de los datos</strong>\n<p>Todos los datos personales recolectados de los usuarios, son de uso exclusivamente interno. Toda vez que se recabe información personal como parte de la relación directa con un usuario, en respeto a la privacidad y confidencialidad de los usuarios, {{MARIFA_NOMBRE}} no cederá ni transferirá esa información personal a ningún tercero que no sea parte de {{MARIFA_NOMBRE}} o sus asociados.</p>\n<strong>únicamente se compartirá con terceros la información personal de los usuarios, en los siguientes casos:</strong></p>\n<ol>\n<li>Cuando exista obligación legal de hacerlo.</li>\n<li>Cuando exista una orden emanada de un Tribunal de Justicia competente.</li>\n</ol>\n</li>\n<li>\n<strong>Comunicaciones del Sitio</strong>\n<p>Ocasionalmente los datos podrán ser utilizados para el envío de comunicaciones a los usuarios, en lo referente a los productos y servicios brindados por {{MARIFA_NOMBRE}}.</p>\n</li>\n<li>\n<strong>Acceso a la información por parte de las personas vinculadas a los datos registrados</strong>\n<p>De este modo, el usuario podrá ejercitar los derechos de acceso, rectificación o cancelación de datos y oposición, que más adelante se mencionarán.</p>\n<p>El ejercicio de dichos derechos podrá ser efectivizado por cada usuario mediante comunicación dirigida a {{MARIFA_NOMBRE}}, según la información de contacto aquí brindada.</p>\n<p>Efectuado el ingreso de los datos por los usuarios, {{MARIFA_NOMBRE}}, procederá a la rectificación, supresión o actualización de los datos personales del afectado, cuando ello fuere procedente.</p>\n<p>La supresión de algún/nos datos no procederá cuando pudiese causar perjuicios a derechos o intereses legítimos de terceros, o cuando existiera una obligación legal de conservar los datos.</p>\n<p><strong>El derecho a exigir la rectificación de los datos:</strong> En principio, el derecho a exigir la rectificación puede ser ejercido ante la falsedad, inexactitud, imprecisión o carácter erróneo que tengan los datos. Su reconocimiento implica el de la preservación de la veracidad de la información, condición que hace a la calidad de la misma.</p>\n<p><strong>El derecho a requerir la actualización de los datos:</strong> La actualización estriba en preservar la vigencia del dato, esto es, la correspondencia de la fracción de información que representa con el ámbito temporal en que es proporcionado</p>\n<p><strong>Los derechos a la adición y disociación:</strong> Los usuarios podrán requerir que se adicionen informaciones a los datos registrados, cuando se consideren incompletos de modo tal que no reflejen las realidades que representan.</p>\n<p>En similar sentido, también podrán exigir la disociación de datos cuyas calidades o características sólo permitan su tratamiento sin posibilidad de establecer asociaciones o vinculaciones con los titulares de los datos.</p>\n<p><strong>Los derechos a la supresión y sometimiento a la confidencialidad:</strong> La \"supresión\" de un dato implica su eliminación definitiva del archivo o registro, esto es su completa desaparición, sin que puedan quedar constancias de su anterior registración.</p>\n</li>\n<li>\n<strong>Métodos de seguridad apropiados</strong>\n<p>{{MARIFA_NOMBRE}} adopta todas las medidas de seguridad lógica y física exigidas por las reglamentaciones, y las que resultan de la adecuada prudencia y diligencia en la protección de los usuarios que han depositado su confianza en {{MARIFA_NOMBRE}}, para proteger la información personal reunida contra el acceso no autorizado, alteración o destrucción. {{MARIFA_NOMBRE}} evalúa y mejora sus sistemas de seguridad toda vez que sea necesario.</p>\n</li>\n<li>\n<strong>Cambios a esta Política</strong>\n<p>En caso de modificación de esta Política, se publicarán los cambios en esta sección. {{MARIFA_NOMBRE}} se reserva el derecho a modificar esta Política en cualquier momento, a cuyo efecto los usuarios deberán tomar conocimiento de la misma en forma regular.</p>\n</li>\n<li>\n<strong>Cierre de una cuenta</strong>\n<p>En el caso que un usuario que decida cerrar su cuenta de {{MARIFA_NOMBRE}}, nuestra Política de Privacidad continuará vigente respecto ese usuario. Toda su información personal será eliminada de los sistemas de {{MARIFA_NOMBRE}}.</p>\n</li>\n</ol>\n</div>", 1, 1, '2013-03-18 15:02:57', '2013-05-01 01:39:00'), array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO `pagina` (`id`, `titulo`, `contenido`, `menu`, `estado`, `creacion`, `modificacion`) VALUES (?, ?, ?, ?, ?, ?, ?)', array(3, 'Protocolo', "<div class=\"text-justify\">\n<h1 class=\"title\">Protocolo de {{MARIFA_NOMBRE}}</h1>\n<p>{{MARIFA_NOMBRE}} es la comunidad virtual donde millones de usuarios comparten, evalúan y seleccionan día a día la mejor información que encuentran o generan por sus propios medios de diversas temáticas (links, imágenes, noticias, videos, etc.). Este sitio tiene una funcionalidad del tipo web 2.0 (los usuarios generan el contenido).</p>\n<p>Con el objetivo de mantener toda la información publicada de la manera más limpia, ordenada y prolija posible se han creado varias reglas (protocolo) las cuales deben ser respetadas por todos los participantes del sitio.</p>\n<h3 class=\"title\">De los post:</h3>\n<h4>Se eliminan los post que contengan:</h4>\n<ul>\n<li>Material que ya fue posteado anteriormente (famosos reposts). Excepto descargas que pueden dejar de funcionar con el tiempo.</li>\n<li>Contenido de mala calidad, pobre o con errores en su creación.</li>\n<li>Títulos poco descriptivos, que sean CON MAYUSCULA (parcialmente o totalmente) o {{{ QuE QUiErAn LLamaR la AtenCióN!!! }}}.</li>\n<li>Noticias que no contienen fuente. (Es necesario agregar el link correspondiente).</li>\n<li>Chistes o humor escrito, adivinanzas, trivias, etc.</li>\n<li>Links de Torrents, Pando, Emule, etc. (P2P, P2M).</li>\n<li>Material morboso tales como cadáveres, vómitos, violaciones, sangre, heridas, enfermedades, etc.</li>\n<li>Información personal propia o de terceros tales como e-mails, msn, nombres, teléfonos, etc.</li>\n<li>Menciones y/o referencias a promociones y/o publicidades de productos y/o servicios a excepción de aquellos autorizados por la Administración del sitio.</li>\n<li>Contenido sexual explícito.</li>\n<li>Links a páginas/blogs personales/propias/amigos (SPAM).</li>\n<li>Mensajes a otros usuarios o a los moderadores.</li>\n<li>Mensajes o contenido con el claro objetivo de buscar polémica (política, fútbol, etc.).</li>\n<li>Contenido que se relacione o haga \"apología de delito\" (drogas, violencia, delincuencia). La violencia es un delito.</li>\n<li>Passwords o accesos privados (Rapidshare, Megaupload, cuentas de correo, etc.).</li>\n<li>Sorteos no autorizados por un moderador. No pueden ser realizados por novatos.</li>\n<li>Quejas y/o opiniones sobre las reglas y/o funcionamiento y/o administración del sitio.</li>\n<li>Material que la administración de {{MARIFA_NOMBRE}} encuentre no conveniente.</li>\n</ul>\n<h4>Se cierran los post que contienen:</h4>\n<ul>\n<li>Temas demasiados polémicos (políticos, religiosos, etc.) que pueden llegar a ofender a otros usuarios, ya sea por su contenido o comentarios.</li>\n<li>Posts donde se genera demasiada polémica agresiva (forobardo).</li>\n</ul>\n<h3 class=\"title\">De los comentarios (Posts) y las respuestas (Comunidades):</h3>\n<h4>Se eliminan comentarios/respuestas que contengan:</h4>\n<ul>\n<li>Mayúsculas o con abuso de mayúsculas.</li>\n<li>Insultos, ofensas, etc. (a otro usuario o de forma general).</li>\n<li>Comentarios racistas y/o peyorativos.</li>\n<li>Tipografías muy grandes o con el claro efecto de llamar la atención.</li>\n<li>Escaleras de emoticones/repetición de una o muchas imágenes en un mismo comentario.</li>\n<li>Con Flash (SWF) molestos o que emitan cualquier tipo de sonido.</li>\n</ul>\n<h3 class=\"title\">De los usuarios:</h3>\n<h4>Se suspenderán de manera temporal o permanente a los usuarios que:</h4>\n<ul>\n<li>Fomenten y/o publiquen a través de cualquier medio contenido que hagan referencia de manera directa o indirecta a la pedofilia</li>\n<li>Fomenten y/o publiquen a través de cualquier medio contenido que hagan referencia de manera directa o indirecta a actos morbosos.</li>\n<li>Fomenten y/o publiquen a través de cualquier medio contenido que hagan referencia de manera directa o indirecta actividades violentas, ilegales y/o peligrosas.</li>\n<li>Fomenten y/o publiquen a través de cualquier medio contenido que hagan referencia de manera directa o indirecta hagan apología de algún delito grave (matar, robar, corromper o dañar a otras personas).</li>\n<li>Fomenten y/o publiquen a través de cualquier medio contenido que hagan referencia de manera directa o indirecta al racismo y/o al odio. (Cualquier insulto racista, así sea de uso \"común\" en su ámbito de pertenecía.)</li>\n<li>Utilicen más de una cuenta personal sin importar el fin de las mismas.</li>\n<li>Insulten a otro usuario y/o moderador.</li>\n<li>Hagan a través de cualquier medio (Mensaje personal, Comunidad, Tema, Respuesta, Comentario o Post) promoción de sitios y/o productos ajenos a {{MARIFA_NOMBRE}} y/o posts propios del usuario.(SPAM).</li>\n<li>Busquen generar polémica por medio de comentarios desafiantes o descalificantes.</li>\n<li>Hacen \"escalerita de caritas\" (comentarios donde sólo se agregan caritas para formar figuras).</li>\n<li>Agregan links con sistemas de referencia del tipo \"marketing piramidal\" con el fin de obtener beneficios personales (Referer).</li>\n<li>Se autocomenten y borren repetidamente sus comentarios en un post para hacerlo aparecer en la página principal.</li>\n</ul>\n<h4>Se modifican o sancionan a los usuarios:</h4>\n<ul>\n<li>Con avatares de mas de 200kb. de peso.</li>\n<li>Con avatares con contenido adulto.</li>\n</ul>\n<h2 class=\"title\">RACISMO, XENOFOBIA, PEDOFILIA, VIOLENCIA no están permitidos en {{MARIFA_NOMBRE}}</h2>\n<p>Para denunciar Pedofilia, podes ingresar a:</p>\n<ul>\n<li><a href=\"http://www.pedofilia-no.org/\">Pedofilia no</a></li>\n<li><a href=\"http://www.protegeles.com/\">Protegeles.com</a></li>\n<li><a href=\"http://www.stop-pedofilia.org/\">Stop-Pedolifia</a></li>\n</ul>\n</div>", 1, 1, '2013-05-01 01:34:40', '2013-05-01 01:36:29'), array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO `pagina` (`id`, `titulo`, `contenido`, `menu`, `estado`, `creacion`, `modificacion`) VALUES (?, ?, ?, ?, ?, ?, ?)', array(4, 'Términos y condiciones', "<div class=\"text-justify\">\n<h1 class=\"title\">Términos y condiciones</h1>\n<p>En forma previa a la utilización de cualquier servicio o contenido ofrecido en {{MARIFA_NOMBRE}}, debe leerse completa y atentamente este documento.</p>\n<p>Las presentes Condiciones Generales constituyen las normas y reglas dispuestas por {{MARIFA_NOMBRE}}, relativas a todos los servicios existentes actualmente o que resulten incluidos en el futuro dentro del sitio \"{{HOSTNAME}}\" (el Sitio). Dichos servicios si bien pueden ser gratuitos, no son de libre utilización, sino que están sujetos a un conjunto de pautas que regulan su uso. El aprovechamiento que un individuo haga de los servicios incluidos en el Sitio, sólo se considerará lícito y autorizado cuando lo sea en cumplimiento de las obligaciones impuestas, con los límites y alcances aquí delineados, así como los que surjan de disposiciones complementarias o accesorias, y/o de las diferentes normativas legales de orden nacional e internacional cuya aplicación corresponda.</p>\n<p>{{MARIFA_NOMBRE}} podrá en cualquier momento y sin necesidad de previo aviso modificar estas Condiciones Generales. Tales modificaciones serán operativas a partir de su fijación en el sitio \"{{HOSTNAME}}\". Los usuarios deberán mantenerse actualizados en cuanto al los términos aquí incluidos ingresando en forma periódica al apartado de legales de {{MARIFA_NOMBRE}}.</p>\n<ol>\n<li>\n<strong>La aceptación por parte de los Usuarios.</strong>\n<p>{{MARIFA_NOMBRE}}, se reserva el derecho a exigir que cada usuario, acepte y cumpla los términos aquí expresados como condición previa y necesaria para el acceso, y utilización de los servicios y/o contenidos brindados por el Sitio.</p>\n<p>Cuando un usuario accediere al Sitio y utilizare cualquiera de los servicios y/o contenidos existentes, hará presumir el conocimiento del presente texto y que ha manifestado su plena aceptación con respecto a todas y cada una de las disposiciones que lo integran.</p>\n<p>El usuario que no acepte, se halle en desacuerdo, o incurriere en incumplimiento de las disposiciones fijadas por la {{MARIFA_NOMBRE}} en estas Condiciones Generales, no contará con autorización para el uso de los servicios y contenidos que existen o puedan existir en el Sitio, debiendo retirarse del Sitio en forma inmediata, y abstenerse de ingresar nuevamente al mismo.</p>\n</li>\n<li>\n<strong>Capacidad legal de los usuarios.</strong>\n<p>Solo podrán acceder y utilizar los servicios y/o contenidos de {{MARIFA_NOMBRE}}, quienes a tenor de la legislación vigente en su lugar de residencia puedan válidamente emitir su consentimiento para la celebración de contratos. Quienes a tenor de la legislación vigente no posean tal capacidad para acceder u obligarse válidamente a los términos y condiciones aquí establecidos, deberán obtener inexcusablemente autorización previa de sus representantes legales, quienes serán considerados responsables de todos los actos realizados por los incapaces a su cargo.</p>\n<p>Cuando se trate de falta de capacidad por minoría de edad, la responsabilidad en la determinación de los servicios y contenidos a los que acceden los menores de edad corresponde a los mayores a cuyo cargo se encuentren, sin embargo en ningún caso estará permitido el acceso al sitio por parte de menores de 18 an~os de edad.</p>\n</li>\n<li>\n<strong>Registración de los Usuarios.</strong> <br>\n<p>Para valerse de los servicios prestados en {{MARIFA_NOMBRE}}, basta la sola aceptación de estas Condiciones Generales. Sin embargo para la utilización de algunos servicios o el acceso a ciertos contenidos, podrá establecerse como requisito, el previo registro del usuario. Dicho registro tendrá por finalidad establecer la identidad e información de contacto del usuario.</p>\n<p>Toda vez que para la registración de un usuario le sea requerida información, la misma deberá ser fidedigna, y poseerá el carácter de declaración jurada. Cuando la información suministrada no atienda a las circunstancias reales de quien la brinda, se considerara tal usuario incurso en incumplimiento de estas Condiciones Generales, siendo responsable por todos los perjuicios que derivaren para {{MARIFA_NOMBRE}} o terceros como consecuencia de tal falta de veracidad o exactitud.</p>\n<p>El usuario dispondrá, una vez registrado, de un nombre de usuario y una contraseña que le permitirá el acceso personalizado, confidencial y seguro a su cuenta personal dentro del Sitio. Los servicios sujetos a registración han sido concebidos para el uso personal del usuario requirente, por tanto el nombre de usuario y la contraseña de acceso concedidos por {{MARIFA_NOMBRE}} solo podrán ser usados por este, estando prohibida su utilización por otra persona distinta al mismo. El usuario registrado asumirá la obligación de guarda y custodia de su nombre de usuario y contraseña de acceso, debiendo informar inmediatamente a {{MARIFA_NOMBRE}} cuando los mismos hubieren perdido su estado de confidencialidad, y/o cuando sean objeto de uso por un tercero.</p>\n<p>Será también responsabilidad de cada usuario mantener actualizada su información personal asentada en el registro conforme resulte necesario, debiendo comunicar a {{MARIFA_NOMBRE}} toda vez que se produzcan cambios en relación a la misma.</p>\n<p>{{MARIFA_NOMBRE}} podrá rechazar cualquier solicitud de registración o, cancelar una registración previamente aceptada, sin que tal decisión deba ser justificada, y sin que ello genere derecho alguno en beneficio del Usuario.</p>\n<p>{{MARIFA_NOMBRE}} utilizará la información suministrada por el usuario exclusivamente con el objeto expuesto, y en todo momento velará por el razonable resguardo a la intimidad y confidencialidad de las comunicaciones del usuario, pero atento que {{MARIFA_NOMBRE}} hace uso de sistemas tecnológicos que bajo ciertas condiciones pueden resultar falibles, se pone en conocimiento de los usuarios que {{MARIFA_NOMBRE}} no garantiza la inviolabilidad de sus sistemas, motivo por el cual los usuarios deberán tomar en consideración esta circunstancia al momento de decidir su registración.</p>\n<p>En todos los casos, y de acuerdo con la <a href=\"{{SITE_URL}}/pages/privacidad/\">Política de Privacidad</a> sostenida por {{MARIFA_NOMBRE}}, la información de carácter personal suministrada por los Usuarios será objeto de adecuado tratamiento y preservación, en resguardo de la privacidad de la misma. Sin embargo, los servicios de {{MARIFA_NOMBRE}} fueron diseñados entre otros fines para permitir que los usuarios accedan a ciertos datos (no sensibles) de otros usuarios permitiendo la interacción entre los mismos dentro de un esquema de red social. Por consiguiente, haciendo entrega de cualquier información personal distinta de su nombre, el usuario renuncia a cualquier expectativa de privacidad que posea con respecto al uso de esa información personal proporcionada dentro del sitio. Los usuarios que no deseen que su fotografía o imagen, página web, mensajero, ciudad de residencia, nacionalidad, o descripción personal ingresadas en el Sitio, puedan ser brindadas al público no deberán registrarse en {{MARIFA_NOMBRE}}.</p>\n</li>\n<li>\n<strong>Notificaciones y comunicaciones</strong>\n<p>A los fines que los usuarios puedan tomar contacto con {{MARIFA_NOMBRE}}, se considerarán válidas las comunicaciones dirigidas a través del formulario de contacto:</p>\n<p>Las notificaciones y comunicaciones cursadas por {{MARIFA_NOMBRE}} a la casilla de correo electrónico que surja como dirección de correo del usuario o remitente se considerarán eficaces y plenamente válidas. Asimismo se considerarán eficaces las comunicaciones que consistan en avisos y mensajes insertos en el sitio, o que se envíen durante la prestación de un servicio, que tengan por finalidad informar a los usuarios sobre determinada circunstancia.</p>\n</li>\n<li>\n<strong>Libre acceso a los Servicios</strong>\n<p>Más allá de la obligación de cumplimiento de todas y cada una de estas Condiciones Generales, todos los servicios y contenidos ofrecidos en el Sitio son libremente accesibles por parte de los usuarios. La libre accesibilidad incluye la gratuidad de los servicios, que no estarán sujetos al pago de ningún arancel o retribución hacia {{MARIFA_NOMBRE}}.</p>\n<p>Tal gratuidad no es de aplicación sobre los servicios de terceros brindados a través del sitio que podrán no ser gratuitos, y en igual sentido aquellos servicios y/o contenidos, actuales o futuros sobre los que {{MARIFA_NOMBRE}} decida establecer un canon para su utilización por parte de los usuarios.</p>\n<p>El libre acceso y gratuidad no comprenden las facilidades de conexión a Internet. En ningún caso {{MARIFA_NOMBRE}}, proveerá a los usuarios la conectividad necesaria para que estos accedan a Internet. Será por exclusiva cuenta, cargo y responsabilidad de cada usuario la disposición de los medios técnicos necesarios para acceder a Internet.</p>\n</li>\n<li>\n<strong>De los servicios y contenidos en particular</strong>\n<p>{{MARIFA_NOMBRE}} es un sitio de Internet basado en una herramienta de comunicación, que permite poner en contacto a sus usuarios para que los mismos compartan opiniones, comentarios, y en general cualquier tipo de información que sea de su interés. El objetivo de {{MARIFA_NOMBRE}} es la creación de un ámbito de comunicación y esparcimiento tan amplio como sea posible, destinado al público de Internet en general.</p>\n\n<ol>\n<li>\n<strong>De los post</strong>\n<p>El principal servicio que {{MARIFA_NOMBRE}} pone a disposición de los usuarios es la posibilidad de conocer las manifestaciones expresadas por otros usuarios, publicadas en el Sitio en forma de mensajes o \"posts\". Conforme lo establecido en el punto 3.<strong> </strong>de las Condiciones Generales, para obtener acceso para la visualización y lectura de los post solo basta la aceptación de las mismas; sin embargo la creación y fijación de post, al igual que el acceso a post determinados así como a ciertas funcionalidades, solo estará reservada a los usuarios registrados.</p>\n<ol style=\"list-style-type: lower-alpha\">\n<li>\n<strong>Creación y fijación</strong>\n<p>Quienes se registren en {{MARIFA_NOMBRE}} podrán publicar sus posts libremente, para ello {{MARIFA_NOMBRE}} pone a disposición de los usuarios registrados una herramienta para la creación y edición de sus posts, junto con los medios necesarios para su almacenamiento y exhibición dentro del Sitio.</p>\n</li>\n<li>\n<strong>Contenido de los post</strong>\n<p>Los post que los usuarios incorporen sólo podrán contener texto. Cuando el usuario pretendiere insertar en su post fotografías, imágenes, ilustraciones, videos, animaciones, o referencia a archivos o sitios ajenos a {{MARIFA_NOMBRE}}, sólo podrá hacerlo a través de links, mediante la indicación de la dirección URL (Uniform Resource Locator) en donde se encuentre alojado el archivo que pretenda asociar a su post.</p>\n<p>{{MARIFA_NOMBRE}} pone en conocimiento de los usuarios y terceros en general, que los archivos asociados a un post no forman parte de éste y no se encuentran reproducidos en ningún sistema o plataforma del Sitio. {{MARIFA_NOMBRE}} solo procederá a la publicación de la dirección URL del archivo asociado, pudiendo en determinados casos se efectuar un <strong>embedded link que permita la visualización del enlace dentro del Sitio.</strong><strong> Consecuentemente, e</strong>n ningún caso los usuarios podrán transferir archivos hacia el sitio con el objeto que los mismos sean incorporados a sus post, o en general realizar una carga o \"upload\" al propio Sitio, de tal forma que esos archivos ( o una copia de ellos) pasen a residir en los servidores de {{MARIFA_NOMBRE}}. En igual sentido no existen en el sitio archivos destinados a su descarga por parte de los usuarios.</p>\n<p>{{MARIFA_NOMBRE}} es un sitio dedicado a la comunicación entre personas, mediante una estructura de red social.</p>\n<p>{{MARIFA_NOMBRE}} NO ACTUA COMO UN CENTRO DE ALMACENAMIENTO O CONSERVACIóN ARCHIVOS.</p>\n<p>{{MARIFA_NOMBRE}} NO ACTUA COMO UN SITIO DE INTERCAMBIO DE ARCHIVOS.</p>\n<p>{{MARIFA_NOMBRE}} NO ACTUA COMO UN tracker.</p>\n<p>{{MARIFA_NOMBRE}} NO CONSTITUYE UNA RED P2P (peer to peer).</p>\n</li>\n<li>\n<strong>Sobre los links incorporados en los posts</strong>\n<p>Un link dentro de una página web (denominado también enlace, vínculo, hipervínculo o, hiperenlace) es un elemento que hace referencia a otro recurso, por ejemplo, otra página o sitio web.</p>\n<p>Así los links a diversos archivos que los usuarios incorporan en los post publicados en {{MARIFA_NOMBRE}} permiten invocar a una página web determinada, o a una posición determinada en una página web, pero en todos los casos los links siempre harán referencia a paginas web titularidad de terceros y ajenas al control de {{MARIFA_NOMBRE}}.</p>\n<p>Los links son simples enlaces que direcciona hacia cierta información o activan determinados contenidos, pero que en ningún caso constituyen reproducciones de los contenidos a los cuales enlaza.</p>\n</li>\n<li>\n<strong>Aspectos a tener en cuenta sobre la incorporación de links:</strong>\n<p>Uno de los principales derechos patrimoniales de un autor es el de reproducción de su obra, este derecho confiere la facultad de prohibir reproducciones de su obra sin autorización previa y expresa. Un link no vulnera el derecho de reproducción, las direcciones URL, son meros hechos que no están protegidos por el derecho de autor por no implicar la realización de una copia de una obra. Sin embargo cuando el autor o el titular de los derechos sobre una obra no la hubiere publicado, nadie sin autorización de éste podría lícitamente hacerlo, por consiguiente LOS USUARIOS SOLO PODRAN ASOCIAR A SUS POSTS, LINKS QUE REFIERAN A OBRAS QUE HUBIEREN SIDO LICITAMENTE PUBLICADAS EN INTERNET POR SU TITULAR.</p>\n</li>\n<li>\n<strong>Calificar posts:</strong>\n<p>Este funcionalidad consiste en la posibilidad que tienen los usuarios registrados, de efectuar una ponderación o calificación sobre los posts fijados por otros usuarios</p>\n<p>La calificación que reciba cada post será exhibida sobre el mismo, y el conjunto de calificaciones recibidas por un usuario en base a sus posts establecerá la puntuación total del usuario, de esta forma el usuario podrá conocer la opinión general de los demás usuarios expresada a través de la calificación de la que fueran merecedores sus post.</p>\n</li>\n</ol>\n</li>\n<li>\n<strong>De los comentarios</strong>\n<p>Otro de los servicios brindados por {{MARIFA_NOMBRE}}, reservado sólo a usuarios registrados, es la posibilidad de incorporar comentarios en forma de mensajes sobre un post de otro usuario, de tal forma que permita un intercambio de opiniones o aportes sobre el post que viene a comentar.</p>\n</li>\n<li>\n<strong>Del contacto entre usuarios</strong>\n<p>Adicionalmente {{MARIFA_NOMBRE}} brinda la posibilidad a los usuarios de conocer y establecer una comunicación directa con otros usuarios, estableciendo un sistema de contacto mediante sesiones de chat, o a través de la información por ellos brindada para ser incorporada en su perfil.</p>\n</li>\n<li>\n<strong>Disposiciones comunes</strong>\n<ol style=\"list-style-type: lower-alpha\">\n<li>\n<p>Conforme se detalla en el punto 7; todo usuario será exclusivo responsable por los post y comentarios que fije. </p>\n</li>\n<li>\n<p>La enumeración precedente es al solo efecto enunciativo y no taxativo. {{MARIFA_NOMBRE}} podrá agregar, modificar, suprimir total o parcialmente los servicios y contenidos, sin que para ello se requiera conformidad o notificación previa de ningún tipo. Salvo estipulación en contrario, todo nuevo contenido o ampliación de los existentes se regirá por estas Condiciones Generales. </p>\n</li>\n</ol>\n</li>\n</ol>\n</li>\n<li>\n<strong>Responsabilidades, dirección y control sobre los servicios.</strong>\n<ol>\n<li>\n<strong>Facultades reservadas</strong>\n<p>{{MARIFA_NOMBRE}} se reserva todas las facultades de control y dirección del Sitio, en particular de los servicios, contenidos y comunicaciones habidos dentro del mismo. Podrá en consecuencia {{MARIFA_NOMBRE}}, introducir todos los cambios y modificaciones que estime convenientes a su solo criterio, podrá agregar, alterar, sustituir o suprimir cualquiera de los servicios o contenidos en todo momento.</p>\n<p>En especial {{MARIFA_NOMBRE}} se reserva la facultad de controlar, editar, suprimir parcial o totalmente, cualquier post o comentario fijado por un usuario. Dicha facultad reposa en las facultades de dirección que posee {{MARIFA_NOMBRE}} en cuanto titular del Sitio, y su ejercicio no estará supeditado a justificación o causa alguna, quedando en todos los casos dicho ejercicio reservado a la discreción y voluntad de {{MARIFA_NOMBRE}}. Sin perjuicio de ello y al solo efecto de servir de guía orientativa para los usuarios, {{MARIFA_NOMBRE}} podrá establecer una serie de recomendaciones sobre los contenidos aceptados y aquellos que no lo fueren en relación a los posts y comentarios. Esta guía será accesible a los usuarios desde el propio Sitio y se la mencionará como \"protocolo\" o bajo alguna otra designación similar.</p>\n</li>\n<li>\n<strong>Responsabilidades en relación a los servicios prestados:</strong>\n<p>Cada usuario será exclusivo responsable por las manifestaciones que vierta o las acciones que lleve adelante dentro del marco del sitio. Sin embargo cuando {{MARIFA_NOMBRE}} reciba a través de su mecanismo de recepción de denuncias, la manifestación de una persona, que hubiere sufrido en forma injustificada un menoscabo en cualquiera de sus derechos, tomará en forma inmediata las medidas necesarias para evitar la continuación de la situación perjudicial, y pondrá en conocimiento de las autoridades competentes los acontecimientos del caso.</p>\n<p>Sin perjuicio de estas facultades reservadas, {{MARIFA_NOMBRE}} en respeto de la privacidad y confidencialidad de las comunicaciones de los usuarios, no ejercerá un control de legalidad directo sobre las manifestaciones y/o acciones llevadas adelante por los usuarios. Consecuentemente no será responsable por el uso contrario a derecho que de los contenidos y servicios, hagan los usuarios, ni garantiza que los datos proporcionados por estos, relativos a su identidad sean veraces y fidedignos.</p>\n<p>{{MARIFA_NOMBRE}} es una plataforma concebida para la comunicación y difusión de información, la utilización del Sitio realizada por un usuario, que impliquen un desmedro o la lisa y llana violación de derechos de terceros, en especial los relativos a la propiedad intelectual, hará plenamente responsable a ese usuario por los daños que tal conducta irrogare para los terceros y/o {{MARIFA_NOMBRE}}.</p>\n</li>\n</ol>\n</li>\n<li>\n<strong>Utilización de los servicios y contenidos brindados por el Sitio</strong>\n<p>Los usuarios deberán utilizar los servicios, y acceder a los contenidos del sitio de conformidad con las disposiciones establecidas en estas <b>Condiciones</b> Generales; con el ordenamiento jurídico al que se encuentren sometidos en razón del lugar, de las personas, o de la materia de la cual se trate, considerado en su conjunto; y según las pautas de conducta impuestas por la moral, las buenas costumbres y el debido respeto a los derechos de terceros.</p>\n\n<ol>\n<li>\n<strong>USO PROHIBIDO de los servicios o contenidos</strong>\n<p>Cualquier uso de los servicios que tenga por objeto, lesionar los derechos de terceros, contravenir el orden jurídico o constituya una práctica ofensiva al pudor público, se reputará como USO PROHIBIDO de los servicios o contenidos, en tanto transgrede los fines para los que fue puesto a disposición de los usuarios.</p>\n<p>Se considerará como USO PROHIBIDO, entre otros, la fijación de post, mensajes o comentarios, propagación, así como la indicación de vínculos a páginas web, que:</p>\n\n<ol>\n<li>Resulten ofensivos para los derechos personalísimos de los individuos, con especial referencia al derecho al honor, a la dignidad, a la intimidad, a no ser objeto de tratos discriminatorios, a la salud, a la imagen, y a la libre expresión de las ideas; con absoluta independencia del cuerpo legal donde tales derechos adquieran reconocimiento.</li>\n<li>Infrinjan los derechos de propiedad intelectual de terceros.</li>\n<li>Posea contenido inapropiado.</li>\n<li>Tenga por objeto vulnerar la seguridad, y o normal funcionamiento de los sistemas informáticos de {{MARIFA_NOMBRE}} o de terceros.</li>\n<li>Induzca, instigue o promueva acciones delictivas, ilícitas, disfuncionales o moralmente reprochables, o constituya una violación de derechos de propiedad intelectual de terceras personas.</li>\n<li>Incorporen alguna forma de publicidad o fin comercial no permitidos por {{MARIFA_NOMBRE}}.</li>\n<li>Tenga por objeto recolectar información de terceros con el objeto de remitirles publicidad o propaganda de cualquier tipo o especie, sin que esta fuera expresamente solicitada.</li>\n</ol>\n</li>\n<li>\n<strong>Medidas de control</strong>\n<p>Sin perjuicio de las acciones legales nacidas en cabeza de {{MARIFA_NOMBRE}} o terceros, cuando el uso de los servicios, llevado adelante por parte de un usuario pueda ser reputado por {{MARIFA_NOMBRE}} como USO PROHIBIDO, {{MARIFA_NOMBRE}} tomará las medidas que considere convenientes según su exclusivo criterio, pudiendo suspender o impedir el acceso a los servicios o contenidos a aquellos usuarios incursos en el uso prohibido de los mismos, y sin que para ello deba mediar comunicación previa alguna.</p>\n</li>\n</ol>\n</li>\n<li>\n<strong>Aspectos relacionados con la Propiedad Intelectual</strong>\n<ol>\n<li>\n<strong>Contenido de terceros</strong>\n<p>En uso de los servicios ofrecidos en el Sitio, el usuario puede tener acceso a contenidos provistos por otros usuarios o terceros. {{MARIFA_NOMBRE}} realiza sus mejores esfuerzos para controlar el material que le es suministrado, sin embargo, el usuario acepta que eventualmente podrá ser expuesto a contenido de terceros que sea falso, ofensivo, indecente o de otra manera inaceptable. Bajo ninguna circunstancia podrá responsabilizar a {{MARIFA_NOMBRE}} por tal circunstancia.</p>\n</li>\n<li>\n<strong>9.2. Material titularidad de {{MARIFA_NOMBRE}}</strong>\n<p>Todo el material existente en el sitio, que no corresponda a un usuario u otro tercero, constituye propiedad exclusiva de {{MARIFA_NOMBRE}}. A título meramente enunciativo, se entenderán incluidos las imágenes, fotografías, diseños, gráficos, sonidos, compilaciones de datos, marcas, nombres, títulos, designaciones, signos distintivos, y todo otro material accesible a través del sitio.</p>\n<p>La titularidad del conjunto o selección de links incorporados por los usuarios al Sitio, corresponderá a {{MARIFA_NOMBRE}} en calidad de propiedad intelectual, en tanto obra de clasificación y compilación. Dicha propiedad esta constituida no por los vínculos o links considerados en forma individual, sino por la selección del conjunto de links. En ese orden los usuarios ceden y transfieren irrevocablemente a {{MARIFA_NOMBRE}} todos los derechos que pudieran corresponderles sobre la selección de links que cada uno de ello individualmente hubiere realizado.</p>\n<p>{{MARIFA_NOMBRE}} se reserva todos los derechos sobre el mencionado material, no cede ni transfiere a favor del usuario ningún derecho sobre su propiedad intelectual o la de terceros. En consecuencia, su reproducción, distribución, y/o modificación deberá ser expresamente autorizada por parte de {{MARIFA_NOMBRE}}, so pena de considerarse una actividad ilícita violatoria de los derechos de propiedad intelectual de {{MARIFA_NOMBRE}}.</p>\n<p>Los usuarios del sitio sólo contarán con autorización para la utilización del material propiedad de {{MARIFA_NOMBRE}}, cuando las finalidades de tal utilización sean aquellas específicamente previstas por {{MARIFA_NOMBRE}}.</p>\n<p>A título informativo, se pone en conocimiento de los usuarios y visitantes del sitio, que los derechos relativos a la propiedad intelectual de {{MARIFA_NOMBRE}}, quedan resguardados internacionalmente bajo la protección del Convenio de Berna; el tratado de la WIPO (World Intellectual Property Organization) sobre derechos de autor, y demás disposiciones coincidentes; el acuerdo TRIPs (Trade Related Aspects of Intellectual Property Rights); que en su conjunto aseguran la plena vigencia internacional de los derechos de {{MARIFA_NOMBRE}}.</p>\n</li>\n</ol>\n</li>\n<li>\n<strong>Operatividad del sitio</strong>\n<p>Correspondientemente con el carácter gratuito de los servicios brindados, {{MARIFA_NOMBRE}} no garantiza la plena operatividad del sitio y el acceso a los servicios y contenidos del mismo. En ningún caso {{MARIFA_NOMBRE}} responderá por la operatividad, eficacia y seguridad de los servicios y contenidos puestos a disposición de los usuarios.</p>\n<p>{{MARIFA_NOMBRE}} no garantiza la conservación, integridad ni indemnidad de los post, mensajes o comentarios fijados por los usuarios.</p>\n</li>\n<li>\n<strong>LINKS hacia {{MARIFA_NOMBRE}}</strong>\n<p>El establecimiento de cualquier link, hipervínculo o enlace, entre una página web ajena al sitio \"{{HOSTNAME}}\" y cualquier página de este último solo podrá realizarse con expresa autorización por parte de {{MARIFA_NOMBRE}}.</p>\n<p>En ningún caso {{MARIFA_NOMBRE}} será responsable por los contenidos o manifestaciones existentes en las páginas web desde donde se establezcan los hipervínculos hacia el sitio de {{MARIFA_NOMBRE}}. El hecho que exista un link entre una página web y el sitio de {{MARIFA_NOMBRE}} no implica que {{MARIFA_NOMBRE}} tenga conocimiento de ello, o que {{MARIFA_NOMBRE}} mantenga relación alguna con los titulares de la página web desde donde se establece el enlace.</p>\n<p>{{MARIFA_NOMBRE}}, se reserva el derecho a solicitar la remoción o eliminación de cualquier enlace desde una página web ajena al Sitio, en cualquier momento, sin expresión de causa, y sin que sea necesario preaviso alguno. El responsable de la página web desde la cual se efectuare el enlace tendrá un plazo de 48hs. contados a partir del pedido de {{MARIFA_NOMBRE}} para proceder a la remoción o eliminación del mismo.</p>\n</li>\n<li>\n<strong>LINKS desde {{MARIFA_NOMBRE}}</strong>\n<ol>\n<li>\n<strong>Links provistos por {{MARIFA_NOMBRE}}</strong>\n<p>Los hipervínculos o enlaces a páginas web de terceros provistos por {{MARIFA_NOMBRE}}, tienen por finalidad mejorar la experiencia de navegación del usuario por el sitio {{MARIFA_NOMBRE}}, poniendo a su disposición canales de acceso a otros sitios.</p>\n</li>\n<li>\n<strong>Links provistos por los usuarios</strong>\n<p>De acuerdo a lo expresado en el punto 6; los usuarios podrán incorporar en sus post links que remitan a distintos recursos alojados fuera del Sitio!. El objeto de estos links es incrementar las posibilidades de comunicación de los usuarios, permitiendo así la referencia a cualquier elemento que se encuentre en Internet.</p>\n</li>\n<li>\n<strong>Responsabilidad derivada de los links</strong>\n<p>En ninguno de los casos precedentemente enunciados {{MARIFA_NOMBRE}} controla, respalda o garantiza la seguridad, calidad, licitud, veracidad e idoneidad de los servicios y contenidos a los cuales se acceda a través de un hipervículo. La inclusión del link no significa que {{MARIFA_NOMBRE}} se encuentre en forma alguna relacionada con el sitio al que dirige el link, ni que apoye, este de acuerdo, facilite o colabore en las actividades que en ese sitio se desarrollen.</p>\n<p>La responsabilidad por los servicios o contenidos en los sitios enlazados corresponderá exclusivamente a los titulares de dichos sitios. Bajo ningún supuesto {{MARIFA_NOMBRE}} será responsable por las irregularidades, ilicitudes o infracciones que en dichos sitios se registren, no respondiendo en tal sentido por los daños que pudieren experimentar los usuarios o terceros a partir de los contenidos allí publicados.</p>\n<p>El acceso y utilización de páginas web enlazadas desde el sitio de {{MARIFA_NOMBRE}} será exclusiva responsabilidad del usuario, quien deberá tomar todas las medidas de precaución necesarias de acuerdo al tipo de servicio, o contenido al que acceda.</p>\n<p>El usuario que considere inadecuada una página vinculada desde el Sitio, podrá elevar su queja o recomendación a través del mecanismo de denuncias puesto a disposición de los usuarios por parte de {{MARIFA_NOMBRE}}.</p>\n</li>\n</ol>\n<li>\n<strong>Finalización del Servicio</strong>\n<p>{{MARIFA_NOMBRE}} podrá a su sola discreción suspender temporalmente o desactivar definitivamente la cuenta de un usuario, sin que medie previa notificación al mismo, y sin que sea necesaria la invocación de causa alguna, procediéndose en tal caso a la eliminación de toda la información relacionada con la cuenta.</p>\n</li>\n<li>\n<strong>Legislación aplicable y jurisdicción.</strong>\n<p>Nos esforzamos por crear una comunidad global con normas coherentes para todos, pero tambián por respetar la legislación local.</p>\n</li>\n</ol>\n</div>", 1, 1, '2013-05-01 01:43:48', '2013-05-01 01:44:56'), array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO `pagina` (`id`, `titulo`, `contenido`, `menu`, `estado`, `creacion`, `modificacion`) VALUES (?, ?, ?, ?, ?, ?, ?)', array(5, 'Report Abuse - DMCA', "<div class=\"text-justify\">\n<h1 class=\"title\">Report Abuse - DMCA</h1>\n<h3>Take down notices:</h3>\n<p>{{MARIFA_NOMBRE}} is an Online Service Provider. It's respects the legitimate rights of copyrights owners, and has adopted an efficient notice and takedown procedure as required by the DMCA and described herein. This policy is intended to guide copyright owners in utilizing that procedure, and also to guide webmasters in restoring access to websites that are disabled due to mistake.</p>\n<h3>Notice to Owners of Copyrighted Works</h3>\n<p>The DMCA provides a legal procedure by which you can request any Online Service Provider to disable access to a website where your copyrighted work(s) are appearing without your permission. There are two parts to the legal procedure: (1) Writing a Proper DMCA Notice, and (2) Sending the Proper DMCA Notice to Alphaupload Designated Agent.</p>\n<h3>How to Write a Proper DMCA Notice</h3>\n<p>A Proper DMCA Notice will notify {{HOSTNAME}} of particular facts in a document signed under penalty of perjury. We refer to this as a \"Proper DMCA Notice\". To Write a Proper DMCA notice, please provide the following information:</p>\n<ul>\n<li>Identify yourself as either:\n<ul>\n<li>The owner of a copyrighted work(s), or</li>\n<li>A person authorized to act on behalf of the owner of an exclusive right that is allegedly infringed.</li>\n</ul>\n</li>\n<li>State your contact information, including your TRUE NAME, street address, telephone number, and email address.</li>\n<li>Identify the copyrighted work that you believe is being infringed, or if a large number of works are appearing at a single website, a representative list of the works.</li>\n<li>Identify the material that you claim is infringing your copyrighted work, to which you are requesting that Taringa.net disable access over the World Wide Web.</li>\n<li>Identify the location of the material on the World Wide Web by providing information reasonably sufficient to permit Taringa.net to locate the material. That meaning the URL of the content. </li>\n<li>State that you have a good faith belief that use of the material in the manner complained of is not authorized by the copyright owner, its agents, or the law.</li>\n<li>State that the information in the notice is accurate, under penalty of perjury. Sign the notice with either a physical or electronic signature.</li>\n</ul>\n<h3>Sending The Proper DMCA Notice to the Designated Agent</h3>\n<p>To exercise your DMCA rights, you must send your Proper DMCA Notice to the following agent designated by {{HOSTNAME}} (the \"Designated Agent\".) The contact information for {{MARIFA_NOMBRE}}'s Designated Agent is: denuncias@{{HOSTNAME}}				What We Do When We Receive A Proper DMCA Notice</p>\n<p>{{HOSTNAME}} will follow the procedures provided in the DCMA, which prescribed a notice and takedown procedure, subject to the webmaster's right to submit a Counter-notification claiming lawful use of the disabled works.</p>\n<h3>Notice and Takedown Procedure</h3>\n<p>It is expected that all users of any part of the {{HOSTNAME}} system will comply with applicable copyright laws. However, if {{HOSTNAME}} is notified of claimed copyright infringement, or otherwise becomes aware of facts and circumstances from which infringement is apparent, it will respond expeditiously by removing, or disabling access to, the material that is claimed to be infringing or to be the subject of infringing activity. {{HOSTNAME}} will comply with the appropriate provisions of the DMCA in the event a counter notification is received by its Designated Agent. Notice to Users of {{HOSTNAME}} Systems</p>\n<p>Pursuant to the Terms of Service Agreement you agreed to when you were permitted to become a System User, you are required to use only lawfully-acquired creative works as website content, and your website may be disabled upon receipt of notice that infringing material is appearing there. {{HOSTNAME}} also respects the legitimate interests of webmasters in utilizing media content lawfully, being permitted to present a response to claims of infringement, and obtaining timely restoration of access to a website that has been disabled due to a copyright complaint. Your System Use privileges will also be suspended. You may protest a DMCA notice by submitting a Counter-notification as described below.</p>\n<h3>Writing and Submitting a Counter-notification</h3>\n<p>If access to your website is disabled due to operation of the {{HOSTNAME}} notice and takedown procedure described above, and you believe the takedown was improper, you must submit a Counter-notification.</p>\n<h3>Writing a Counter-notification</h3>\n<p>To Write a Proper Counter-notification, please provide the following information:</p>\n<ul>\n<li>State that access to your website was disabled due to operation of the notice and takedown procedure.</li>\n<li>Identify the material that has been removed and designate its URL prior to removal.</li>\n<li>State, under penalty of perjury:\n<ul>\n<li>Your name, address, and telephone number,</li>\n<li>That you have a good faith belief that the material was removed or disabled as result of mistake or misidentification of the material,\"</li>\n<li>That you consent to the jurisdiction of the Federal District Court for the judicial district in which the address is located.\"</li>\n</ul>\n</li>\n</ul>\n<h3>Sending the Counter-notification</h3>\n<p>To exercise your DMCA rights, you must send your Counter-notification to the \"Designated Agent\" for {{HOSTNAME}}, whose contact information is: denuncias@{{HOSTNAME}}</p>\n<h3>Repeat Infringers</h3>\n<p>{{HOSTNAME}} may, in its discretion, use all appropriate means to terminate user access to its system or network who are repeat infringers. Accommodation of Standard Technical Measures</p>\n<p>It is {{MARIFA_NOMBRE}}'s policy to accommodate and not interfere with standard technical measures it determines are reasonable under the circumstances, i.e., technical measures that are used by copyright owners to identify or protect copyrighted works.</p>\n<h3>Policy With Regard To Non-Compliant Communications</h3>\n<p>{{HOSTNAME}} has discretion to handle non-compliant notices in whatever manner appears to be reasonable given the circumstances presented. Submission of Misleading Information</p>\n<p>The submission of misleading information of any sort in a notification or counter-notification submitted to Taringa.net voids any claim of right made by the submitting party.</p>\n</div>", 1, 1, '2013-05-01 01:46:58', '2013-05-01 01:46:58'), array('error_no' => 1062))
	)
);

return $consultas;