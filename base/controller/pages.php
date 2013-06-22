<?php
/**
 * pages.php is part of Marifa.
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
 * @subpackage  Controller
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Controlador con páginas estáticas como Protocolo, DMCA, etc.
 *
 * @since      Versión 0.2
 * @package    Marifa\Base
 * @subpackage Controller
 */
class Base_Controller_Pages extends Controller {

	/**
	 * Visualizamos una página.
	 * @param int $id ID de la página a visualizar.
	 */
	public function action_ver($id)
	{
		// Cargo la página.
		$model_pagina = new Model_Pagina( (int) $id);
		$model_pagina->load();

		// Verifico existencia.
		if ( ! $model_pagina->existe())
		{
			add_flash_message(FLASH_ERROR, __('La página a la que intentas acceder no es válida.', FALSE));
			Request::redirect('/');
		}

		// Verifico si es visible.
		if ($model_pagina->estado !== Model_Pagina::ESTADO_VISIBLE)
		{
			if ( ! Usuario::is_login() || ! Usuario::permiso(Model_Usuario_Rango::PERMISO_SITIO_ADMINISTRAR_CONTENIDO))
			{
				add_flash_message(FLASH_ERROR, __('La página a la que intentas acceder no es válida.', FALSE));
				Request::redirect('/');
			}
			else
			{
				add_flash_message(FLASH_INFO, __('La página no es accesible por otros usuarios, solo por administradores con permisos para editarla.', FALSE));
			}
		}

		// Menú principal.
		$this->template->assign('master_bar', parent::base_menu('pagina_'.$model_pagina->id));

		// Compilamos la vista.
		$this->template->assign('contenido', $this->procesar_propiedades_cuerpo($model_pagina->contenido));

		// Seteamos título.
		$this->template->assign('title', $this->procesar_propiedades_titulo($model_pagina->titulo));
	}

	/**
	 * Procesamos el cuerpo en busca de propiedades a modificar para el contenido de la página
	 * Podemos reemplazar variables predefinidas.
	 * @param string $contenido
	 */
	protected function procesar_propiedades_cuerpo($contenido)
	{
		// Armo listado de variables a reemplazar.
		$variables = array(
			'{{MARIFA_NOMBRE}}' => Utils::configuracion()->get('nombre', __('Marifa', FALSE)),
			'{{MARIFA_DESCRIPCION}}' => Utils::configuracion()->get('descripcion', __('Tu comunidad de forma simple', FALSE)),
			'{{HOSTNAME}}' => parse_url(SITE_URL, PHP_URL_HOST),
			'{{SITE_URL}}' => SITE_URL,
			'{{THEME_URL}}' => THEME_URL
		);

		// Emito evento para modificar variables del reemplazo.
		Event::trigger('Paginas.propiedades.cuerpo.variables', $variables);

		// Reemplazo contenido estático.
		$contenido = str_replace(array_keys($variables), array_values($variables), $contenido);

		// Emito evento para reemplazo personalizados.
		Event::trigger('Paginas.propiedades.cuerpo.custom', $contenido);

		return $contenido;
	}

	/**
	 * Procesamos el cuerpo en busca de propiedades a modificar para el título de la página
	 * Podemos reemplazar variables predefinidas.
	 * @param string $contenido
	 */
	protected function procesar_propiedades_titulo($contenido)
	{
		// Armo listado de variables a reemplazar.
		$variables = array(
			'{{MARIFA_NOMBRE}}' => Utils::configuracion()->get('nombre', __('Marifa', FALSE)),
			'{{MARIFA_DESCRIPCION}}' => Utils::configuracion()->get('descripcion', __('Tu comunidad de forma simple', FALSE))
		);

		// Emito evento para modificar variables del reemplazo.
		Event::trigger('Paginas.propiedades.titulo.variables', $variables);

		// Reemplazo contenido estático.
		$contenido = str_replace(array_keys($variables), array_values($variables), $contenido);

		// Emito evento para reemplazo personalizados.
		Event::trigger('Paginas.propiedades.titulo.custom', $contenido);

		return $contenido;
	}
}