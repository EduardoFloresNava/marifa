<?php
/**
 * 0.3.php is part of Marifa.
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
 * @since		Versión 3
 * @filesource
 * @package		Marifa\Installer\Update
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

// Configuraciones de las fotos.
$consultas[] = array(
	'Configuraciones de fotos',
	array(
		array('INSERT', 'INSERT INTO configuracion (clave, valor, defecto) VALUES (?, ?, ?)', array('habilitar_fotos', 1, 1), array('error_no' => 1062)),
		array('INSERT', 'INSERT INTO configuracion (clave, valor, defecto) VALUES (?, ?, ?)', array('privacidad_fotos', 1, 1), array('error_no' => 1062))
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

// Descripción a los rangos.
$consultas[] = array(
	'Descripción de los rangos',
	array(
		array('ALTER', 'ALTER TABLE `usuario_rango` ADD `descripcion` VARCHAR(250) NULL DEFAULT \'\' AFTER nombre;', NULL, array('error_no' => 1060)),
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

// Mensajes del sistema.
$consultas[] = array(
	'Mensajes del sistema',
	array(
		array('ALTER', 'ALTER TABLE `mensaje` CHANGE `emisor_id` `emisor_id` INT( 11 ) NULL;', NULL, array('error_no' => 1060)),
	)
);

// Censuras de palabras.
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
		)
	)
);

// Uniformamos charset.
$consultas[] = array(
	'Cambio charset',
	array(
		array('ALTER', 'ALTER TABLE `categoria` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `categoria` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `configuracion` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `configuracion` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `foto` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `foto` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `foto_comentario` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `foto_comentario` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `foto_denuncia` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `foto_denuncia` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `foto_favorito` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `foto_favorito` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `foto_voto` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `foto_voto` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `mensaje` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `mensaje` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `noticia` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `noticia` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `post` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `post` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `post_comentario` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `post_comentario` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `post_comentario_voto` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `post_comentario_voto` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `post_compartido` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `post_compartido` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `post_denuncia` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `post_denuncia` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `post_favorito` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `post_favorito` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `post_moderado` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `post_moderado` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `post_punto` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `post_punto` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `post_seguidor` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `post_seguidor` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `post_tag` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `post_tag` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `session` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `session` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `suceso` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `suceso` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `usuario` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `usuario` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `usuario_aviso` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `usuario_aviso` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `usuario_baneo` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `usuario_baneo` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `usuario_bloqueo` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `usuario_bloqueo` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `usuario_denuncia` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `usuario_denuncia` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `usuario_nick` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `usuario_nick` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `usuario_perfil` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `usuario_perfil` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `usuario_rango` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `usuario_rango` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `usuario_rango_permiso` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `usuario_rango_permiso` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `usuario_recuperacion` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `usuario_recuperacion` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `usuario_seguidor` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `usuario_seguidor` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `usuario_suspension` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `usuario_suspension` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `usuario_visita` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `usuario_visita` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `medalla` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `medalla` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `usuario_medalla` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `usuario_medalla` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `shout` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `shout` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `shout_comentario` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `shout_comentario` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `shout_favorito` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `shout_favorito` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `shout_tag` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `shout_tag` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `shout_usuario` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `shout_usuario` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `shout_voto` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;'),
		array('ALTER', 'ALTER TABLE `shout_voto` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;')
	)
);

return $consultas;