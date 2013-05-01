<?php
// Tareas de actualización a Marifa v0.3.

/**
 * Función para reemplazar la imagenes viejas por el nuevo paradigma.
 * @param string $imagen
 * @return string
 */
function marifa_update_v3_change_imagen($imagen)
{
    return substr($imagen, 0, strrpos($imagen, '.'));
}

if ( ! file_exists(CONFIG_PATH.DS.'marifa.php'))
{
	// Genero archivo de configuración base de Marifa.
	$config = array(
		'cookie_secret' => Texto::random_string(20),
		'language' => 'esp',
		'default_timezone' => 'UTC'
	);

	// Guardo el archivo de configuración.
	file_put_contents(CONFIG_PATH.DS.'marifa.php', "<?php defined('APP_BASE') || die('No direct access allowed.');\nreturn array('cookie_secret' => '{$config['cookie_secret']}', 'language' => '{$config['language']}', 'default_timezone' => '{$config['default_timezone']}')");
}

// Actualizo imágenes de categorias.
$categorias = Database::get_instance()->query('SELECT id, imagen FROM categoria')->get_pairs(Database_Query::FIELD_INT, Database_Query::FIELD_STRING);

foreach ($categorias as $c_id => $c_img)
{
    Database::get_instance()->update('UPDATE categoria SET imagen = ? WHERE id = ?', array(marifa_update_v3_change_imagen($c_img), $c_id));
}
unset($categorias);

// Actualizo imagenes de las medallas.
$medallas = Database::get_instance()->query('SELECT id, imagen FROM medalla')->get_pairs(Database_Query::FIELD_INT, Database_Query::FIELD_STRING);

foreach ($medallas as $m_id => $m_img)
{
    Database::get_instance()->update('UPDATE medalla SET imagen = ? WHERE id = ?', array(marifa_update_v3_change_imagen($m_img), $m_id));
}
unset($medallas);

// Actualizo imagenes de los rangos.
$rangos = Database::get_instance()->query('SELECT id, imagen FROM usuario_rango')->get_pairs(Database_Query::FIELD_INT, Database_Query::FIELD_STRING);

foreach ($rangos as $r_id => $r_img)
{
    Database::get_instance()->update('UPDATE usuario_rango SET imagen = ? WHERE id = ?', array(marifa_update_v3_change_imagen($r_img), $r_id));
}
unset($medallas);