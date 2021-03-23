<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

add_action( 'admin_notices', 'porto_theme_options_notices' );
add_action( 'redux/options/porto_settings/saved', 'porto_save_theme_settings', 10, 2 );
add_action( 'redux/options/porto_settings/saved', 'porto_update_theme_options_status', 11, 2 );
add_action( 'redux/options/porto_settings/import', 'porto_save_theme_settings', 10, 2 );
add_action( 'redux/options/porto_settings/reset', 'porto_save_theme_settings' );
add_action( 'redux/options/porto_settings/section/reset', 'porto_save_theme_settings' );
add_action( 'redux/options/porto_settings/import', 'porto_import_theme_settings', 10, 2 );
add_action( 'redux/options/porto_settings/compiler', 'porto_generate_bootstrap_css_after_options_save', 11, 3 );
add_action( 'redux/options/porto_settings/validate', 'porto_restore_empty_theme_options', 10, 3 );

add_filter( 'redux/porto_settings/localize', 'porto_settings_localize_settings', 10, 1 );
add_filter( 'redux/options/porto_settings/ajax_save/response', 'porto_settings_localize_settings', 10, 1 );

function porto_theme_options_notices() {
	global $pagenow;
	$upload_dir = wp_upload_dir();
	if ( 'themes.php' == $pagenow && isset( $_GET['page'] ) && 'porto_settings' === $_GET['page'] && ! wp_is_writable( $upload_dir['basedir'] ) ) {
		add_settings_error( 'porto_admin_theme_options', 'porto_admin_theme_options', __( 'Uploads folder must be writable. Please set write permission to your wp-content/uploads folder.', 'porto' ), 'error' );
		settings_errors( 'porto_admin_theme_options' );
	}
}

if ( ! function_exists( 'porto_check_file_write_permission' ) ) :
	function porto_check_file_write_permission( $filename ) {
		if ( is_writable( dirname( $filename ) ) == false ) {
			@chmod( dirname( $filename ), 0755 );
		}
		if ( file_exists( $filename ) ) {
			if ( is_writable( $filename ) == false ) {
				@chmod( $filename, 0755 );
			}
			@unlink( $filename );
		}
	}
endif;

function porto_compile_css( $process = null ) {

	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	global $porto_settings, $porto_settings_optimize;
	if ( ! $porto_settings_optimize || empty( $porto_settings_optimize ) ) {
		$porto_settings_optimize = get_option( 'porto_settings_optimize', array() );
	}

	$template_dir = PORTO_DIR;
	$upload_dir   = wp_upload_dir();
	$style_path   = $upload_dir['basedir'] . '/porto_styles';
	if ( ! file_exists( $style_path ) ) {
		wp_mkdir_p( $style_path );
	}

	if ( 'shortcodes' === $process ) {

		global $reduxPortoSettings;
		$reduxFramework = $reduxPortoSettings->ReduxFramework;

		$is_success = false;

		// compile visual composer css file
		if ( ! class_exists( 'lessc' ) ) {
			require_once PORTO_ADMIN . '/lessphp/lessc.inc.php';
		}
		ob_start();
		include $template_dir . '/less/js_composer/less/lib/front.less.php';
		$_config_css = ob_get_clean();

		ob_start();
		$less = new lessc();
		$less->setFormatter( 'compressed' );
		try {
			$less->setImportDir( $template_dir . '/less/js_composer/less/lib' );
			echo '' . $less->compile( '@import "../config/variables.less";' . $_config_css );
			$_config_css = ob_get_clean();

			$filename = $style_path . '/js_composer.css';
			porto_check_file_write_permission( $filename );
			$reduxFramework->filesystem->execute(
				'put_contents',
				$filename,
				array(
					'content' => $_config_css,
				)
			);
		} catch ( Exception $e ) {
		}

		// compile porto shortcodes css file
		if ( ! class_exists( 'scssc' ) ) {
			require_once PORTO_ADMIN . '/scssphp/scss.inc.php';
		}
		ob_start();
		require PORTO_ADMIN . '/theme_options/config_scss_shortcodes.php';
		$_config_css = ob_get_clean();

		$scss = new scssc();
		$scss->setImportPaths( $template_dir . '/scss' );
		if ( isset( $porto_settings_optimize['minify_css'] ) && $porto_settings_optimize['minify_css'] ) {
			$scss->setFormatter( 'scss_formatter_crunched' );
		} else {
			$scss->setFormatter( 'scss_formatter' );
		}

		try {
			$shortcodes_css = $scss->compile( '$rtl: 0; $dir: ltr !default; $theme_uri: "' . PORTO_URI . '"; @import "theme/theme-imports";' . $_config_css );
			$filename       = $style_path . '/shortcodes.css';
			porto_check_file_write_permission( $filename );
			$reduxFramework->filesystem->execute(
				'put_contents',
				$filename,
				array(
					'content' => $shortcodes_css,
				)
			);
		} catch ( Exception $e ) {
		}

		// compile porto shortcodes rtl css file
		try {
			$shortcodes_css = $scss->compile( '$rtl: 1; $dir: rtl !default; $theme_uri: "' . PORTO_URI . '"; @import "theme/theme-imports";' . $_config_css );
			$filename       = $style_path . '/shortcodes_rtl.css';
			porto_check_file_write_permission( $filename );
			$reduxFramework->filesystem->execute(
				'put_contents',
				$filename,
				array(
					'content' => $shortcodes_css,
				)
			);
			$is_success = true;
		} catch ( Exception $e ) {
		}

		return $is_success;
	}

	if ( 'bootstrap' === $process ) {
		global $reduxPortoSettings;
		$reduxFramework = $reduxPortoSettings->ReduxFramework;

		// Compile SCSS files
		if ( ! class_exists( 'scssc' ) ) {
			require_once PORTO_ADMIN . '/scssphp/scss.inc.php';
		}
		// config file
		ob_start();
		require PORTO_ADMIN . '/theme_options/config_scss_bootstrap.php';
		$_config_css = ob_get_clean();

		$scss = new scssc();
		$scss->setImportPaths( $template_dir . '/scss' );
		if ( isset( $porto_settings_optimize['minify_css'] ) && $porto_settings_optimize['minify_css'] ) {
			$scss->setFormatter( 'scss_formatter_crunched' );
		} else {
			$scss->setFormatter( 'scss_formatter' );
		}
		try {
			// bootstrap styles
			ob_start();
			$optimize_suffix = '';
			if ( isset( $porto_settings_optimize['optimize_bootstrap'] ) && $porto_settings_optimize['optimize_bootstrap'] ) {
				$optimize_suffix = '.optimized';
			}

			echo '' . $scss->compile( '$rtl: 0; $dir: ltr !default; @import "plugins/directional"; ' . $_config_css . ' @import "plugins/bootstrap/bootstrap' . $optimize_suffix . '";' );
			$_config_css = ob_get_clean();

			$filename = $style_path . '/bootstrap.css';
			porto_check_file_write_permission( $filename );

			$reduxFramework->filesystem->execute(
				'put_contents',
				$filename,
				array(
					'content' => $_config_css,
				)
			);
			update_option( 'porto_bootstrap_style', true );
		} catch ( Exception $e ) {
		}
	} elseif ( 'bootstrap_rtl' === $process ) {
		global $reduxPortoSettings;
		$reduxFramework = $reduxPortoSettings->ReduxFramework;

		// Compile SCSS files
		if ( ! class_exists( 'scssc' ) ) {
			require_once PORTO_ADMIN . '/scssphp/scss.inc.php';
		}
		// config file
		ob_start();
		require PORTO_ADMIN . '/theme_options/config_scss_bootstrap.php';
		$_config_css = ob_get_clean();

		$scss = new scssc();
		$scss->setImportPaths( $template_dir . '/scss' );
		if ( isset( $porto_settings_optimize['minify_css'] ) && $porto_settings_optimize['minify_css'] ) {
			$scss->setFormatter( 'scss_formatter_crunched' );
		} else {
			$scss->setFormatter( 'scss_formatter' );
		}
		try {
			// bootstrap styles
			ob_start();
			$optimize_suffix = '';
			if ( isset( $porto_settings_optimize['optimize_bootstrap'] ) && $porto_settings_optimize['optimize_bootstrap'] ) {
				$optimize_suffix = '.optimized';
			}
			echo '' . $scss->compile( '$rtl: 1; $dir: rtl !default; @import "plugins/directional"; ' . $_config_css . ' @import "plugins/bootstrap/bootstrap' . $optimize_suffix . '";' );
			$_config_css = ob_get_clean();

			$filename = $style_path . '/bootstrap_rtl.css';
			porto_check_file_write_permission( $filename );

			$reduxFramework->filesystem->execute(
				'put_contents',
				$filename,
				array(
					'content' => $_config_css,
				)
			);
			update_option( 'porto_bootstrap_rtl_style', true );
		} catch ( Exception $e ) {
		}
	}
}

if ( ! function_exists( 'porto_config_value' ) ) :
	function porto_config_value( $value ) {
		return isset( $value ) ? $value : 0;
	}
endif;

function porto_save_theme_settings() {
	do_action( 'porto_admin_save_theme_settings' );
}

function porto_update_theme_options_status( $plugin_options, $changed_values ) {
	set_theme_mod( 'theme_options_saved', true );
	if ( wp_doing_ajax() && isset( $changed_values['product-single-content-layout'] ) && 'builder' == $plugin_options['product-single-content-layout'] ) {
		echo json_encode( array( 'action' => 'reload' ) );
		exit;
	}
}

function porto_import_theme_settings( $plugin_options, $imported_options ) {
	// import header builder settings
	if ( isset( $imported_options['header_builder_layouts'] ) && is_array( $imported_options['header_builder_layouts'] ) ) {
		update_option( 'porto_header_builder_layouts', $imported_options['header_builder_layouts'] );
	}
	if ( isset( $imported_options['header_builder'] ) && is_array( $imported_options['header_builder'] ) ) {
		update_option( 'porto_header_builder', $imported_options['header_builder'] );
	}
	if ( isset( $imported_options['header_builder_elements'] ) && is_array( $imported_options['header_builder_elements'] ) ) {
		update_option( 'porto_header_builder_elements', $imported_options['header_builder_elements'] );
	}

	if ( is_rtl() ) {
		porto_compile_css( 'bootstrap_rtl' );
	} else {
		porto_compile_css( 'bootstrap' );
	}
}

/**
 * Includes header builder settings to porto theme options on page load and after saving theme options
 */
function porto_settings_localize_settings( $args ) {
	$porto_header_builder_elements = get_option( 'porto_header_builder_elements', false );
	$current_header                = get_option( 'porto_header_builder', false );
	$header_builder_layouts        = get_option( 'porto_header_builder_layouts', false );
	if ( $current_header ) {
		$args['options']['header_builder'] = $current_header;
	}
	if ( $porto_header_builder_elements ) {
		$args['options']['header_builder_elements'] = $porto_header_builder_elements;
	}
	if ( $header_builder_layouts ) {
		$args['options']['header_builder_layouts'] = $header_builder_layouts;
	}

	return $args;
}


if ( ! function_exists( 'porto_generate_bootstrap_css_after_options_save' ) ) :
	function porto_generate_bootstrap_css_after_options_save( $options, $compilerCSS, $changed_values ) {
		if ( isset( $changed_values['placeholder-color'] ) ) {
			$upload_dir = wp_upload_dir();
			if ( file_exists( $upload_dir['basedir'] . '/porto_placeholders' ) ) {
				// unlink images
			}
			if ( 1 === count( $changed_values ) ) {
				return;
			}
		}
		porto_compile_css( 'bootstrap_rtl' );
		porto_compile_css( 'bootstrap' );
	}
endif;

if ( ! function_exists( 'porto_restore_default_options_for_old_versions' ) ) :
	function porto_restore_default_options_for_old_versions() {
		global $porto_settings;
		if ( ! isset( $porto_settings['search-layout'] ) ) {
			if ( isset( $porto_settings['header-type'] ) && in_array( $porto_settings['header-type'], array( '2', '3', '7', '8', '18', '19' ) ) ) {
				$porto_settings['search-layout'] = 'large';
			} else {
				$porto_settings['search-layout'] = 'advanced';
			}
		}

		if ( ! isset( $porto_settings['minicart-type'] ) && isset( $porto_settings['header-type'] ) ) {
			$header_type = (int) $porto_settings['header-type'];
			if ( ( $header_type >= 1 && $header_type <= 9 ) || 18 == $header_type || 19 == $header_type || ( isset( $porto_settings['header-type-select'] ) && 'header_builder' == $porto_settings['header-type-select'] ) ) {
				$porto_settings['minicart-type'] = 'minicart-arrow-alt';
			} else {
				$porto_settings['minicart-type'] = 'minicart-inline';
			}
		}
	}
endif;

function porto_restore_empty_theme_options( &$plugin_options, $old_options, $last_changed_values ) {
	if ( isset( $plugin_options ) && isset( $old_options ) ) {
		$plugin_options = wp_parse_args( $plugin_options, $old_options );
	}
}
