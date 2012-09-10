<?php
/**
 * suceso.php is part of Marifa.
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
 * @package		Marifa\Base
 * @subpackage  Model
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Clase para el parseo de los sucesos.
 * Generamos un
 *
 * @since      0.1
 * @package    Marifa\Base
 * @subpackage Model
 */
class Base_Suceso {

	/**
	 * Procesamos el suceso y devolvemos toda la información necesaria.
	 * @param array $informacion Arreglo con la información del suceso.
	 * @return array
	 */
	public static function procesar($informacion)
	{
		// ID de la cache del suceso.
		$cache_id = 'suceso_data.'.$informacion['id'];

		// Obtenemos elemento a partir de la cache.
		$data = Cache::get_instance()->get($cache_id);
		//TODO: Quitar borrado de cache.
		$data = FALSE;

		// Verificamos si existe.
		if ($data === FALSE)
		{
			// Procesamos el suceso.
			$data = call_user_func('self::suceso_'.$informacion['tipo'], $informacion);

			// Guardamos en la cache.
			Cache::get_instance()->save($cache_id, $data);
		}

		return $data;
	}

	protected static function suceso_comentario_post($suceso)
	{
		// Arreglo con los datos del resultado.
		$rst = array();

		// Cargamos el comentario.
		$model_comentario = new Model_Post_Comentario($suceso['objeto_id']);

		// Cargamos el post.
		$model_post = $model_comentario->post();
		$rst['post'] = $model_post->as_array();

		// Datos del dueño del post.
		$rst['post']['usuario'] = $model_post->usuario()->as_array();
		unset($model_post);

		// Datos del usuario que comenta.
		$rst['usuario'] = $model_comentario->usuario()->as_array();

		return $rst;
	}

}
