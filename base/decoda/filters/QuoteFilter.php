<?php
/**
 * QuoteFilter
 *
 * Provides the tag for quoting users and blocks of texts.
 *
 * @author      Miles Johnson - http://milesj.me
 * @copyright   Copyright 2006-2011, Miles Johnson, Inc.
 * @license     http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link        http://milesj.me/code/php/decoda
 */

class QuoteFilter extends DecodaFilter {

	/**
	 * Supported tags.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_tags = array(
		'quote' => array(
			'template' => 'quote',
			'type' => self::TYPE_BLOCK,
			'allowed' => self::TYPE_BOTH,
			'attributes' => array(
				'default' => '/.*?/',
				'date' => '/.*?/',
				'comment' => '/([pf]{1}[0-9]*)/'
			),
			'map' => array(
				'default' => 'author'
			),
			'maxChildDepth' => 2
		)
	);

	/**
	 * Constructor de la clase.
	 * @param string $preview Si es preview no se envian sucesos de citas.
	 */
	public function __construct($preview = FALSE)
	{
		$this->preview = $preview;
	}

	/**
	 * Procesamos los sucesos de citas.
	 * @param array $tag Listado de parámetros de la etiqueta.
	 * @param string $content Contenido de la etiqueta.
	 * @return string
	 */
	public function parse(array $tag, $content)
	{
		if (isset($tag['attributes']['comment']))
		{
			// Obtengo comentario.
			if ($tag['attributes']['comment']{0} == 'p')
			{
				$model_comentario = new Model_Post_Comentario( (int) substr($tag['attributes']['comment'], 1));
			}
			else
			{
				$model_comentario = new Model_Foto_Comentario( (int) substr($tag['attributes']['comment'], 1));
			}

			// Verifico existencia.
			if ($model_comentario->existe())
			{
				// Seteo usuario.
				$tag['attributes']['default'] = $model_comentario->usuario()->nick;

				if ( ! $this->preview && $model_comentario->usuario_id !== Usuario::$usuario_id)
				{
					// Envio el suceso.
					$model_suceso = new Model_Suceso;
					$model_suceso->crear($model_comentario->usuario_id, 'usuario_comentario_citado', TRUE, (int) substr($tag['attributes']['comment'], 1), Usuario::$usuario_id, $tag['attributes']['comment']{0} == 'p');
					$model_suceso->crear(Usuario::$usuario_id, 'usuario_comentario_citado', FALSE, (int) substr($tag['attributes']['comment'], 1), Usuario::$usuario_id, $tag['attributes']['comment']{0} == 'p');
				}
			}
		}

		return parent::parse($tag, $content);
	}

}