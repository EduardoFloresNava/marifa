<?php defined('APP_BASE') || die('No direct access allowed.');

return array(
	/**
	 * Tipo de cache a utilizar. Puede ser:
	 *     dummy     -> Cache desactivada.
	 *     apc       -> Alternative php cache. Requiere soporte APC.
	 *     file      -> Archivo en disco. No requiere dependencias opcionales.
	 *     memcached -> Cache en memoria. Requiere Memcached.
	 */
	'type' => 'file',

	/**
	 * Nesaria solo para cache en disco. Es donde se guardará la cache.
	 * Si no se setea se utiliza {$root}/cache/file/'
	 */
	//'path' => '/path/to/cache',

	/**
	 * Datos para memcached.
	 * Solo necesario para ese motor.
	 */
	//'hostname' => '',
	//'port' => 0,
	//'weight' => 0,
);
