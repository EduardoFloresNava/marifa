<?php
/**
 * UserFIlter
 *
 * Procesamos citas a usuarios en las publicaciones.
 *
 * @author      Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 */

class UserFilter extends DecodaFilter {

	/**
	 * Supported tags.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_tags = array(
		'user' => array(
			'tag' => 'a',
			'type' => self::TYPE_INLINE,
			'allowed' => self::TYPE_INLINE,
			'pattern' => '/^@[a-zA-Z0-9]{4,16}$/s',
			'testNoDefault' => true,
			'attributes' => array(
				'default' => '/^[0-9]+$/s'
			),
		),
	);

	/**
	 * Using shorthand variation if enabled.
	 *
	 * @access public
	 * @param array $tag
	 * @param string $content
	 * @return string
	 */
	public function parse(array $tag, $content) {
		$tag['attributes']['href'] = SITE_URL.'/perfil/index/'.urlencode(substr($content, 1));
		return parent::parse($tag, $content);
	}

}