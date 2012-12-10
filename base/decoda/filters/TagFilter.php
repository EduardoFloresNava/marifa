<?php
/**
 * TagFIlter
 *
 * Procesamos etiquetas en publicaciones de los muros.
 *
 * @author      Ignacio Daniel Rostagno <ignaciorostagno@vijona.com.ar>
 */

class TagFilter extends DecodaFilter {

	/**
	 * Supported tags.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_tags = array(
		'tag' => array(
			'tag' => 'a',
			'type' => self::TYPE_INLINE,
			'allowed' => self::TYPE_INLINE,
			'testNoDefault' => true
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
		$tag['attributes']['href'] = SITE_URL.'/buscador/pin/'.urlencode(substr($content, 1));
		return parent::parse($tag, $content);
	}

}