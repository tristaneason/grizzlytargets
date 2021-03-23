<?php

namespace Objectiv\Plugins\Checkout\Core;

use Objectiv\Plugins\Checkout\Managers\ExtendedPathManager;
use Symfony\Component\Finder\Finder;

/**
 * Template handler for associated template piece. Typically there should only be 3 of these in total (header, footer,
 * content)
 *
 * @link objectiv.co
 * @since 1.0.0
 * @package Objectiv\Plugins\Checkout\Core
 * @author Clifton Griffin <clif@checkoutwc.com>
 */

class Template {
	private $_stylesheet_file_name = 'style.min.css';
	private $_basepath;
	private $_baseuri;
	private $_name;
	private $_description;
	private $_author;
	private $_authoruri;
	private $_version;
	private $_supports;
	private $_templates = [];
	private $_slug;

	/**
	 * @since 2.0.0
	 * @access private
	 * @static
	 * @var array $default_headers
	 */
	public static $default_headers = array(
		'Name'        => 'Template Name',
		'Description' => 'Description',
		'Author'      => 'Author',
		'AuthorURI'   => 'Author URI',
		'Version'     => 'Version',
		'Supports'    => 'Supports',
	);

	/**
	 * Template constructor.
	 *
	 * @param bool $slug
	 * @param ExtendedPathManager $path_manager
	 */
	public function __construct( $slug, $path_manager ) {
		/**
		 * Locate the template
		 *
		 * Search WordPress theme template folder first, then plugin
		 */
		if ( is_dir( trailingslashit( $path_manager->get_theme_template() ) . $slug ) ) {
			$this->_basepath = trailingslashit( $path_manager->get_theme_template() ) . $slug;
			$this->_baseuri = get_stylesheet_directory_uri() . '/checkout-wc/' . $slug;
		} elseif ( is_dir( trailingslashit( $path_manager->get_plugin_template() ) . $slug ) ) {
			$this->_basepath = trailingslashit( $path_manager->get_plugin_template() ) . $slug;
			$this->_baseuri = $path_manager->get_url_base() . '/templates/' . $slug;
		} else {
			return;
		}

		$this->_slug = $slug;

		$this->_load();
	}

	/**
	 * Load template information for given path
	 *
	 * @param $basepath
	 */
	private function _load() {
		/**
		 * Template Information
		 */
		$stylesheet_path = $this->get_stylsheet_path();

		if ( $stylesheet_path ) {
			$data = get_file_data( $stylesheet_path, self::$default_headers );

			$data['Name']     = ( $data['Name'] == '' ) ? ucfirst( basename( $this->get_basepath() ) ) : $data['Name'];
			$data['Supports'] = isset( $data['Supports'] ) ? explode( ', ', $data['Supports'] ) : array();

			foreach ( $data as $key => $value ) {
				$key          = str_replace( ' ', '_', $key );
				$key          = sanitize_key( $key );
				$this->{"_$key"} = $value;
			}
		}

		/**
		 * Template Files
		 */
		$finder = new Finder();
		$finder->files()->depth(0)->in( $this->get_basepath() )->name( '*.php' )->notName( 'functions.php' )->notName( 'header.php' )->notName( 'footer.php' );

		foreach ( $finder as $template_file ) {
			$this->_templates[] = $template_file->getFilename();
		}
	}

	/**
	 * Load the theme template functions file
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function load_functions() {
		$functions_path       = trailingslashit( $this->get_basepath() ) . 'functions.php';

		if ( file_exists( $functions_path ) ) {
			require_once $functions_path;
		}
	}

	public function view( $filename, $parameters = [] ) {
		$filename_with_basepath = trailingslashit( $this->get_basepath() ) . $filename;
		$template_name = $this->get_slug();
		$template_piece_name = basename( $filename, '.php' );

		if ( file_exists( $filename_with_basepath ) ) {
			// Before the template is actually spat out
			do_action( "cfw_template_load_before_{$template_name}_{$template_piece_name}" );

			// Extract any parameters for use in the template
			extract( $parameters );

			// Pass the parameters to the view
			require $filename_with_basepath;

			// After the template has been echoed out
			do_action( "cfw_template_load_after_{$template_name}_{$template_piece_name}" );
		}
	}

	/**
	 * @param $capability
	 *
	 * @return bool
	 */
	public function supports( $capability ) {
		return in_array( $capability, $this->get_supports() );
	}

	public function get_template_uri() {
		return $this->_baseuri;
	}

	/**
	 * Return fully qualified path to stylesheet
	 *
	 * @return string|bool $stylesheet
	 */
	public function get_stylsheet_path() {
		$stylesheet = trailingslashit( $this->get_basepath() ) . $this->get_stylesheet_filename();

		return file_exists( $stylesheet ) ? $stylesheet : false;
	}

	/**
	 * @return string
	 */
	public function get_stylesheet_filename() {
		if ( defined( 'CFW_DEV_MODE' ) && CFW_DEV_MODE ) {
			return 'style.css';
		}

		return $this->_stylesheet_file_name;
	}

	/**
	 * @param string $_stylesheet_file_name
	 */
	public function set_stylesheet_filename( $_stylesheet_file_name ) {
		$this->_stylesheet_file_name = $_stylesheet_file_name;
	}

	/**
	 * @return string
	 */
	public function get_basepath() {
		return $this->_basepath;
	}

	/**
	 * @param string $basepath
	 */
	public function set_basepath( $basepath ) {
		$this->_basepath = $basepath;
	}

	/**
	 * @return mixed
	 */
	public function get_name() {
		return $this->_name;
	}

	/**
	 * @param mixed $name
	 */
	public function set_name( $name ) {
		$this->_name = $name;
	}

	/**
	 * @return mixed
	 */
	public function get_description() {
		return $this->_description;
	}

	/**
	 * @param mixed $description
	 */
	public function set_description( $description ) {
		$this->_description = $description;
	}

	/**
	 * @return mixed
	 */
	public function get_author() {
		return $this->_author;
	}

	/**
	 * @param mixed $author
	 */
	public function set_author( $author ) {
		$this->_author = $author;
	}

	/**
	 * @return mixed
	 */
	public function get_author_uri() {
		return $this->_authoruri;
	}

	/**
	 * @param mixed $authoruri
	 */
	public function set_author_uri( $authoruri ) {
		$this->_authoruri = $authoruri;
	}

	/**
	 * @return mixed
	 */
	public function get_version() {
		return $this->_version;
	}

	/**
	 * @param mixed $version
	 */
	public function set_version( $version ) {
		$this->_version = $version;
	}

	/**
	 * @return mixed
	 */
	public function get_supports() {
		return $this->_supports;
	}

	/**
	 * @param mixed $supports
	 */
	public function set_supports( $supports ) {
		$this->_supports = $supports;
	}

	/**
	 * @return array
	 */
	public function get_templates() {
		return $this->_templates;
	}

	/**
	 * @return string
	 */
	public function get_slug() {
		return $this->_slug;
	}
}
