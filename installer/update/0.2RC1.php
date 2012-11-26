<?php
/**
 * 0.2RC1.php is part of Marifa.
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
 * @since		Versión 0.2RC1
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

// Versión instalada para actualizador.
$consultas[] = array(
	'Versión actual',
	array(
		array('INSERT', 'INSERT INTO configuracion (clave, valor, defecto) VALUES (?, ?, ?)', array('version_actual', '0.2RC1', '0.2RC1'), array('error_no' => 1062))
	)
);

// Categorias con sus valores.
$consultas[] = array(
	'Opciones faltantes a los rangos',
	array(
		array('ALTER', 'ALTER TABLE `usuario_rango` ADD `puntos` INT NOT NULL DEFAULT 10;', NULL, array('error_no' => 1060)),
		array('ALTER', 'ALTER TABLE `usuario_rango` ADD `puntos_dar` INT NOT NULL DEFAULT 10;', NULL, array('error_no' => 1060)),
		array('ALTER', 'ALTER TABLE `usuario_rango` ADD `tipo` INT NOT NULL DEFAULT 0;', NULL, array('error_no' => 1060)),
		array('ALTER', 'ALTER TABLE `usuario_rango` ADD `cantidad` INT NULL DEFAULT NULL;', NULL, array('error_no' => 1060)),
		array('ALTER', 'ALTER TABLE `usuario` DROP `puntos_disponibles`;', NULL, array('error_no' => 1091))
	)
);

// Indices faltantes.
$consultas[] = array(
	'Indices faltantes para mejorar integridad',
	array(
		array('ALTER', 'ALTER TABLE `usuario` ADD UNIQUE `email` (`email`);', NULL, array('error_no' => 1061)),
		array('ALTER', 'ALTER TABLE `usuario` ADD UNIQUE `nick` (`nick`);', NULL, array('error_no' => 1061)),
		array('ALTER', 'ALTER TABLE `usuario_rango` ADD UNIQUE `nombre` (`nombre`);', NULL, array('error_no' => 1061)),
		array('ALTER', 'ALTER TABLE `usuario_rango` ADD UNIQUE `tipo` (`tipo`, `cantidad`);', NULL, array('error_no' => 1061)),
		array('ALTER', 'ALTER TABLE `usuario_rango` ADD `puntos_dar` INT NOT NULL DEFAULT 10;', NULL, array('error_no' => 1060)),
		array('ALTER', 'ALTER TABLE `usuario_rango` ADD `tipo` INT NOT NULL DEFAULT 0;', NULL, array('error_no' => 1060)),
		array('ALTER', 'ALTER TABLE `usuario_rango` ADD `cantidad` INT NULL DEFAULT NULL;', NULL, array('error_no' => 1060)),
	)
);

// Solucionamos problema del orden de los rangos.
$consultas[] = array(
	'Orden rangos',
	array(
		array('ALTER', 'ALTER TABLE usuario_rango DROP INDEX orden', NULL, array('error_no' => 1091))
	)
);

// Tabla de medallas.
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
			) ENGINE = MYISAM ;', NULL, array('error_no' => 1050),
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
			) ENGINE = MYISAM ;', NULL, array('error_no' => 1050)
		)
	)
);

return $consultas;