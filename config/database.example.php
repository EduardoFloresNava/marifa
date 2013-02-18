<?php defined('APP_BASE') || die('No direct access allowed.');

return array(

	/* Datos para el driver PDO, ejemplo para MySQL.
	 * Soporta otros motores como SQLite, no todos los motores tienen soporte.
	 * RECOMENDADO SU USO. */
	//'type'    => 'pdo',
	//'dsn'     => 'mysql:dbname=database;host=127.0.0.1;charset=utf-8',
	//'options' => array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"),

	/* Datos para el driver MySQL. Recomendado solo para depuración. */
	'type'    => 'mysql',
	'host'    => '127.0.0.1',
	'db_name' => 'database',
	'utf8'    => TRUE,

	/* Comunes a ambos. Datos de conección no siempre necesarios. */
	'username' => '',
	'password' => '',
);
