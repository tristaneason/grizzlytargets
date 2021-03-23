<?php

namespace Objectiv\Plugins\Checkout\Managers;

use Objectiv\Plugins\Checkout\Core\Template;
use Symfony\Component\Finder\Finder;

/**
 * The templates manager loads the active template
 * as well as provides information on all available templates
 *
 * @link objectiv.co
 * @since 2.31.0
 * @package Objectiv\Plugins\Checkout\Managers
 * @author Clifton Griffin <clif@checkoutwc.com>
 */

class TemplatesManager {
	private $_active_template;
	public $path_manager;

	/**
	 * TemplatesManager constructor.
	 *
	 * @param ExtendedPathManager $path_manager
	 * @param string $active_template_slug
	 */
	public function __construct( $path_manager, $active_template_slug ) {
		$this->_active_template = new Template( $active_template_slug, $path_manager );
		$this->path_manager = $path_manager;

		add_action( 'cfw_load_template_assets', array( $this, 'enqueue_assets') );
		$this->getActiveTemplate()->load_functions();
	}

	function enqueue_assets() {
		$min            = ( ! CFW_DEV_MODE ) ? '.min' : '';

		wp_enqueue_style( 'cfw_front_template_css', $this->getActiveTemplate()->get_template_uri() . "/style{$min}.css", array(), $this->getActiveTemplate()->get_version() );
		wp_enqueue_script( 'wc-checkout', $this->getActiveTemplate()->get_template_uri() . "/theme{$min}.js", array( 'jquery' ), $this->getActiveTemplate()->get_version(), true );
	}

	function getUserTemplatePath() {
		return get_stylesheet_directory() . '/checkout-wc';
	}

	function getPluginTemplatePath() {
		return $this->path_manager->get_base() . '/templates';
	}

	/**
	 * @return Template[]
	 */
	function getAvailableTemplates() {
		$templates = [];
		$finder = new Finder();

		$finder->directories()->depth(0)->in( $this->getPluginTemplatePath() );

		foreach( $finder as $template ) {
			$templates[ $template->getBasename() ] = new Template( $template->getBasename(), $this->path_manager );
		}

		if ( is_dir( $this->getUserTemplatePath() ) ) {
			$finder = new Finder();
			$finder->directories()->depth(0)->in( $this->getUserTemplatePath() );

			foreach( $finder as $template ) {
				$templates[ $template->getBasename() ] = new Template( $template->getBasename(), $this->path_manager );
			}
		}

		return $templates;
	}

	/**
	 * @return Template
	 */
	public function getActiveTemplate() {
		return $this->_active_template;
	}

	/**
	 * @param Template $activate_template
	 */
	public function setActiveTemplate( $activate_template ) {
		$this->_active_template = $activate_template;
	}
}