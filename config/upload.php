<?php defined('APP_BASE') || die('No direct access allowed.');

/**
 * De las cargas de archivos al servidor.
 */
return array(
	/**
	 * Información general sobre la carga de archivos.
	 */
	'file_type' => array(
		/**
		 * Tamaño máximo en bytes.
		 */
		'max_size' => 2097152, // 2MB

		/**
		 * Donde guardar los archivos cargados.
		 */
		'path' => CACHE_PATH.DS.'upload'.DS,

		/**
		 * Si se utiliza un hash para los nombres de archivos.
		 */
		'use_hash' => TRUE,
	),

	/**
	 * Información general sobre carga de imágenes.
	 */
	'image_data' => array(
		/**
		 * Resolución mínima permitida.
		 * array(ancho, alto)
		 */
		'resolucion_minima' => array(50, 50),

		/**
		 * Resolución máxima permitida.
		 * array(ancho, alto)
		 */
		'resolucion_maxima' => array(1000, 1000),

		/**
		 * Tamaño del archivo en bytes.
		 */
		'max_size' => 1000,

		/**
		 * MIME types permitidos.
		 * El formato del arreglo es: extensión => mime.
		 * Solo se comprueba el MIME, pero la extensión se usa para la GUI.
		 */
		'extension' => array(
			'jpg' => 'image/jpeg',
			'jpg' => 'image/pjpeg',
			'png' => 'image/png',
		),
	),

	/**
	 * Driver a utilizar para las imágenes.
	 * disk: Guarda en un directorio las imágenes.
	 */
	'image' => 'disk',

	/**
	 * Configuración del driver de disco.
	 */
	'image_disk' => array(
		/**
		 * Directorio donde almacenar las imágenes.
		 */
		'save_path' => CACHE_PATH.DS.'imagen'.DS,

		/**
		 * URL base para el PATH.
		 */
		'base_url' => '/cache/imagen/',

		/**
		 * Utilizar un hash para el nombre o no.
		 */
		'use_hash' => TRUE,
	),

	/**
	 * Configuración del driver de Imgur.
	 */
	'image_imgur' => array(
		/**
		 * Clave para realizar peticiones al API de imgur.
		 */
		'api_key' => '',

		/**
		 * Tiempo máximo en segundos permitidos para guardar la imagen.
		 */
		'timeout' => 10,
	),
);
