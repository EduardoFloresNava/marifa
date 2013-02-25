<?php defined('APP_BASE') || die('No direct access allowed.');

/**
 * Configuraciones base del sistema.
 */

return array(

	/**
	 * Clave para seguridad de las Cookies.
	 * Debe ser una cadena aleatoria única.
	 */
	'cookie_secret' => 'secret_key',

	/**
	 * Idioma por defecto de Marifa.
	 * Esta configuración será sobrescrita por la de la base de datos.
	 */
	'language' => 'esp',

	/**
	 * Zona horaria a utilizar por Marifa.
	 * Puede ser sobrescrita luego pero es la utilizada de forma interna por el sistema.
	 */
	'default_timezone' => 'UTC',
);
