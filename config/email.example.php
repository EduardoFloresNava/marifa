<?php defined('APP_BASE') || die('No direct access allowed.');

return array(
	/**
	 * Transporte a utilizar. Los transportes son los de SwiftMailer.
	 * Los disponibles actualmente son:
	 *     smtp     -> Envio mediante SMTP.
	 *     sendmail -> Envio mediante Sendmail.
	 *     mail     -> Utilizando mail().
	 */
	//'transport' => 'smtp',
	//'transport' => 'sendmail',
	//'transport' => 'mail',

	/**
	 * Propiedades de los transportes. Cada transporte tiene una lista de propiedades.
	 *  - Propiedades de smtp:
	 *    - host: Host donde se encuentra el servidor SMTP.
	 *    - post: Puerto a utilizar con la conección.
	 *    - timeout: Tiempo de espera para la conección.
	 *    - encryption: Tipo de encriptación, puede ser "tls" o "ssl"
	 *    - username: Nombre del usuario a utilizar para autenticar.
	 *    - passoword: Contraseña a utilizar para autenticar.
	 *  - Propiedades de sendmail:
	 *    - command: Comando a utilizar para el envio de los correos. Por defecto: /usr/sbin/sendmail
	 *  - Propiedades de mail:
	 *    - NO POSEE PROPIEDADES EXTRA.
	 */
	'parametros' => array(
		'host'       => 'smtp.gmail.com',
		'port'       => 587,
		'encryption' => 'tls',
		'username'   => '',
		'password'   => ''
	)
);
