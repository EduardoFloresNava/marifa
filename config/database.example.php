<?php defined('APP_BASE') or die('No direct access allowed.');

return array(

	/* Datos para el driver PDO, ejemplo para MySQL.
	 * Soporta otros motores como SQLite, no todos los motores tienen soporte.
	 * RECOMENDADO SU USO. */
	//'type'     => 'pdo',
	//'dsn'      => 'mysql:dbname=database;host=127.0.0.1',

	/* Datos para el driver MySQL. No recomendado. */
	'type'     => 'mysql',
	'host'     => '127.0.0.1',
	'db_name'  => 'database',

	/* Datos para el driver MySQLi.. */
	'type'     => 'mysqli',
	'host'     => '127.0.0.1',
	'db_name'  => 'database',

	/* Comunes a ambos. Datos de conección no siempre necesarios. */
	'username' => '',
	'password' => '',
);
