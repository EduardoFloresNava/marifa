<?php
/**
 * moderado.php is part of Marifa.
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
 * Modelo para representar la acción de moderación sobre un post.
 *
 * @since      0.1
 * @package    Marifa\Base
 * @subpackage Model
 *
 * @property-read int $post_id ID del post que ha sido moderado.
 * @property-read int $usuario_id ID del usuario que ha realizado la moderación.
 * @property-read int $tipo Motivo por el cual se realizó la moderación.
 * @property-read int $padre_id ID del post que se creó como borrador.
 * @property-read string $razon Texto que describe el motivo en caso de ser uno no tabulado.
 */
class Base_Model_Post_Moderado extends Model_Dataset {

	/**
	 * Post identicó a otro.
	 */
	const TIPO_REPOST = 0;

	/**
	 * El post es spam.
	 */
	const TIPO_SPAM = 1;

	/**
	 * El post contiene links muertos.
	 */
	const TIPO_LINKS_MUERTOS = 2;

	/**
	 * El post es racista o irrespetuoso.
	 */
	const TIPO_RACISTA_IRRESPETUOSO = 3;

	/**
	 * El post contiene información personal.
	 */
	const TIPO_INFORMACION_PERSONAL = 4;

	/**
	 * El post tiene su titulo en mayusculas.
	 */
	const TIPO_TITULO_MAYUSCULA = 5;

	/**
	 * El post tiene contenido pedofilo.
	 */
	const TIPO_PEDOFILIA = 6;

	/**
	 * El post es gore o asqueroso.
	 */
	const TIPO_ASQUEROSO = 7;

	/**
	 * La fuente del post es incorrecta.
	 */
	const TIPO_FUENTE = 8;

	/**
	 * El post es pobre o crap.
	 */
	const TIPO_POBRE = 9;

	/**
	 * El sitio no es un foro.
	 */
	const TIPO_FORO = 10;

	/**
	 * El post con cumple con el protocolo.
	 */
	const TIPO_PROTOCOLO = 11;

	/**
	 * Se especifica una razón diferente.
	 */
	const TIPO_PERSONALIZADA = 12;

	/**
	 * Nombre de la tabla para el dataset
	 * @var string
	 */
	protected $table = 'post_moderado';

	/**
	 * Clave primaria.
	 * @var array
	 */
	protected $primary_key = array('post_id' => NULL);

	/**
	 * Listado de campos y sus tipos.
	 */
	protected $fields = array(
		'post_id'    => Database_Query::FIELD_INT,
		'usuario_id' => Database_Query::FIELD_INT,
		'tipo'       => Database_Query::FIELD_INT,
		'padre_id'   => Database_Query::FIELD_INT,
		'razon'      => Database_Query::FIELD_STRING
	);

	/**
	 * Cargamos una denuncia.
	 * @param int $post_id ID del post denunciado.
	 */
	public function __construct($post_id = NULL)
	{
		parent::__construct();

		$this->primary_key['post_id'] = $post_id;
	}

	/**
	 * Obtenemos el usuario que realizó la moderacion..
	 * @return Model_Usuario
	 */
	public function usuario()
	{
		return new Model_Usuario($this->get('usuario_id'));
	}

	/**
	 * Obtenemos el post que fue moderado.
	 * @return Model_Post
	 */
	public function post()
	{
		return new Model_Post($this->get('post_id'));
	}

	/**
	 * Obtenemos el post que fue creado como borrador.
	 * @return Model_Post
	 */
	public function borrador()
	{
		if ($this->get('post_id') !== NULL)
		{
			return new Model_Post($this->get('post_id'));
		}
		else
		{
			return NULL;
		}
	}

	/**
	 * Creamos una nueva acción de moderación sobre un post.
	 * @param int $post ID del post a moderar.
	 * @param int $usuario ID del usuario que realiza la moderación.
	 * @param int $tipo Tipo de motivo de la moderación realizada.
	 * @param int $padre ID del post borrador creado como resultado de la moderación.
	 * @param string $razon Motivo para el tipo personalizado.
	 * @return bool
	 */
	public function crear($post, $usuario, $tipo, $padre = NULL, $razon = NULL)
	{
		list(,$c) = $this->db->insert('INSERT INTO post_moderado (post_id, usuario_id, tipo, padre_id, razon) VALUES (?, ?, ?, ?, ?)', array($post, $usuario, $tipo, $padre, $razon));
		return $c > 0;
	}
}
