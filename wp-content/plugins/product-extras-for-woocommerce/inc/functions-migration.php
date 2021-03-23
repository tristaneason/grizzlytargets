<?php
/**
 * Functions for migrating serialised data to custom post types
 * @since 3.0.0
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

function pewc_has_migrated() {
	$has_migrated = get_option( 'pewc_has_migrated', false );
	$original_version = get_option( 'pewc_original_version' );
	if( $original_version && version_compare( $original_version, '3.0.0', '>=' ) ) {
		// No need to do migration
		$has_migrated = true;
	}
	return apply_filters( 'pewc_filter_has_migrated', $has_migrated );
}

/**
 * Do the migration notice
 */
function pewc_migration_notice() {
	$screen = get_current_screen();
	if( ! isset( $screen->id ) || $screen->id == 'posts_page_pewc_migration' || $screen->id == 'pewc_product_extra_page_global' ) {
		return;
	}
	// $last_dismissed = get_option( 'pewc_migration_notice_dismissed', false );
	// $time_now = time();
	// if( $last_dismissed && ( floatVal( $last_dismissed ) + DAY_IN_SECONDS ) > $time_now ) {
	// 	return;
	// }
	// Have we done the migration?
	$has_migrated = pewc_has_migrated();
	if( ! $has_migrated ) {
		$url = pewc_get_migration_page_url(); ?>
		<div class="notice notice-error">
			<p>
				<strong><?php _e( 'Product Add-Ons Ultimate - Important Notice', 'pewc' ); ?></strong>
			</p>
			<p>
				<?php
				_e( 'WooCommerce Product Add-Ons Ultimate needs to update your database. This will only take a few seconds. Please click the button below to go to the migration page to get started.', 'pewc' ); ?>
			</p>
			<p>
				<a href="<?php echo esc_url( $url ); ?>" class="wc-update-now button-primary">
					<?php esc_html_e( 'Go to Migration Page', 'pewc' ); ?>
				</a>
			</p>
		</div>
		<script>
			jQuery( document ).ready( function( $ ) {
				$( document ).on( 'click', '.pewc-dismiss-migration .notice-dismiss', function() {
					$.ajax({
						url: ajaxurl,
						data: {
							action: 'pewc_dismiss_migration_notice',
						},
					});
				});
			});
		</script>
		<?php
	}
}
add_action( 'admin_notices', 'pewc_migration_notice' );

function pewc_dismiss_migration_notice() {
	update_option( 'pewc_migration_notice_dismissed', time() );
}
add_action( 'wp_ajax_pewc_dismiss_migration_notice', 'pewc_dismiss_migration_notice' );

/**
 * Let the user know the migration was successful
 */
function pewc_migration_success_notice() {

	if( isset( $_GET['migration_completed'] ) ) {

		// Set pewc_has_multiple to true
		update_option( 'pewc_has_migrated', true ); ?>

		<div class="notice notice-success is-dismissible pewc-is-dismissible-pewc-notice">
			<p>
				<strong><?php _e( 'WooCommerce Product Add-Ons Ultimate Version 3.0', 'pewc' ); ?></strong>
			</p>
			<p>
				<?php
				_e( 'Migration complete. All your Product Add-Ons have been migrated. Thank you.', 'pewc' ); ?>
			</p>
		</div>

	<?php }

}
add_action( 'admin_notices', 'pewc_migration_success_notice' );

/**
 * Get the URL of the migration page
 * @since 3.0.0
 */
function pewc_get_migration_page_url() {
	$url = add_query_arg(
		array(
			'page'			=> 'pewc_migration',
			'migration'	=> 'true'
		),
		admin_url( 'edit.php' )
	);
	return $url;
}

/**
 * Add a hidden page for the migration
 * @since 3.0.0
 */
function pewc_add_migration_page() {
	add_submenu_page(
		null,
		__( 'Add-Ons Migration', 'pewc' ),
		__( 'Add-Ons Migration', 'pewc' ),
		'manage_woocommerce',
		'pewc_migration',
		'pewc_do_migration_page'
	);
}
add_action( 'admin_menu', 'pewc_add_migration_page' );

/**
 * Print the settings page
 * @since 1.6.0
 */
function pewc_do_migration_page() { ?>
	<div class="wrap pewc-migration-wrap">
		<input type="hidden" name="pewc_migration_referer" id="pewc_migration_referer" value="<?php echo wp_get_referer(); ?>">
		<?php printf( '<h1>%s</h2>', __( 'Product Add-Ons Ultimate Database Migration', 'pewc' ) );
		$num_products = pewc_get_products_with_add_ons();
		$num_globals = pewc_get_global_add_ons();
		$url = pewc_get_migration_page_url();
		if( pewc_has_migrated() ) {
			// Already migrated ?>
			<p>
				<?php
				$support_url = sprintf(
					'<a href="%s" target="_blank">%s</a>',
					'https://pluginrepublic.com/support/',
					__( 'the support page', 'pewc' )
				);
				$message = sprintf(
					__( 'Congratulations - all done. All your Product Add-Ons have been migrated. You need take no further action.', 'pewc' ),
					$support_url
				);
				printf(
					'<p>%s<p>',
					$message
				);

				?>
			</p>
		<?php } else { ?>
			<p>
				<?php
				_e( 'Add-Ons Ultimate would like to update your database. This is important because the plugin is changing how it stores its data, which should result in performance improvements and feature enhancements.', 'pewc' );
				?>
			</p>
			<p>
				<?php
				_e( 'You might like to test this first on a staging or development version of your site. In any case, please make a backup of your site before clicking the update button below.', 'pewc' );
				?>
			</p>
			<p>
				<?php
				_e( 'The migration should only take a few seconds (slightly longer if you have a larger number of products on your site). Please stay on this page while it runs. The page will provide you with regular status updates so you can view progress.', 'pewc' );
				?>
			</p>
			<p>
				<?php
				_e( 'For the vast majority of users, you won\'t notice a difference after the migration has completed. However, if you have extended the plugin and you are making use of the IDs of Product Add-Ons, you\'ll need to update to the new IDs.', 'pewc' );
				?>
			</p>
			<p>
				<strong>
					<?php if( $num_products > 0 || $num_globals > 0 ) {
						printf(
						__( 'You have %s products with Add-Ons that need to be updated and %s global rules. ', 'pewc' ),
						$num_products,
						$num_globals
					);
				} ?>
				</strong>
				<strong>
					<?php printf(
						__( 'Click the button below to get started.', 'pewc' ),
						$num_products,
						$num_globals
					); ?>
				</strong>
			</p>
			<p class="submit">
				<?php wp_nonce_field( 'pewc_migration_nonce', 'pewc_migration_nonce' ); ?>

				<a href="#" data-href="<?php echo esc_url( $url ); ?>" id="pewc_migration" class="button button-primary"><?php _e( 'Update Database', 'pewc' ); ?></a>
				<span class="pewc-loading"><span class="spinner"></span></span>
			</p>
			<p class="pewc-migration-message pewc-migration-0">
				<?php
				_e( 'Checking for add-ons data to migrate.', 'pewc' );
				?>
			</p>
			<p class="pewc-migration-message pewc-migration-1">
				<?php
				_e( 'Processing <span id="pewc-processing"></span> of <span id="pewc-total"></span>.', 'pewc' );
				?>
			</p>
			<p class="pewc-migration-message pewc-migration-2">
				<?php
				_e( 'Updating conditionals.', 'pewc' );
				?>
			</p>
			<p class="pewc-migration-message pewc-migration-3">
				<?php
				_e( 'Updating globals.', 'pewc' );
				?>
			</p>
			<p class="pewc-migration-message pewc-migration-4">
				<?php
				_e( 'Updating global conditionals.', 'pewc' );
				?>
			</p>
			<p class="pewc-migration-message pewc-migration-5">
				<?php
				_e( 'All done. This page will automatically reload.', 'pewc' );
				?>
			</p>
		<?php }
		$reload_url = add_query_arg(
			'migration_completed',
			'true',
			wp_get_referer()
		); ?>
		<style type="text/css">
			.pewc-migration-wrap .pewc-loading .spinner {
				float: none;
				margin-top: 0
			}
			.pewc-migration-message {
				display: none;
			}
		</style>
		<script type="text/javascript">
			jQuery( document ).ready( function( $ ) {
				$( '#pewc_migration' ).on( 'click', function( e ) {
					e.preventDefault();
					$( this ).addClass( 'disabled' );
					$( '.pewc-migration-wrap .pewc-loading .spinner' ).css( 'visibility', 'visible' );
					$( '.pewc-migration-0' ).fadeIn();
					// Start the process
					self.process_offset( 0, self );
				});
				process_offset = function( offset, self ) {
					console.log( offset );
					var total = <?php echo $num_products; ?>;
					$.ajax({
						type: 'POST',
						url: ajaxurl,
						data: {
							action: 'pewc_do_migration',
							security: $( '#pewc_migration_nonce' ).val(),
							total: total,
							offset: offset,
							url: "<?php echo esc_url( $url ); ?>"
						},
						success: function( response ) {
							response = JSON.parse( response );
							if( response.completed == true ) {
								// Finished
								console.log( 'done' );
								$( '.pewc-migration-1' ).hide();
								$( '.pewc-migration-2' ).show();
								self.process_conditionals( self );
								// location.href = location.href + "&migration_completed=true";
							} else {
								$( '.pewc-migration-1' ).show();
								$( '#pewc-processing' ).text( offset );
								$( '#pewc-total' ).text( total );
								console.log( 'offset' + response );
								console.log( 'parse' + parseInt( response ) );
								self.process_offset( parseInt( response ), self );
							}
						}
					});
				};
				process_conditionals = function( self ) {
					$.ajax({
						type: 'POST',
						url: ajaxurl,
						data: {
							action: 'pewc_process_conditionals',
							security: $( '#pewc_migration_nonce' ).val(),
							url: "<?php echo esc_url( $url ); ?>"
						},
						success: function( response ) {
							response = JSON.parse( response );
							if( response.completed == true ) {
								// Finished
								console.log( 'done conditionals' );
								$( '.pewc-migration-2' ).hide();
								$( '.pewc-migration-3' ).show();
								self.process_globals( self );
								// location.href = location.href + "&migration_completed=true";
							}
						}
					});
				};
				process_globals = function( self ) {
					$.ajax({
						type: 'POST',
						url: ajaxurl,
						data: {
							action: 'pewc_process_globals',
							security: $( '#pewc_migration_nonce' ).val(),
							url: "<?php echo esc_url( $url ); ?>"
						},
						success: function( response ) {
							response = JSON.parse( response );
							if( response.completed == true ) {
								// Finished
								console.log( 'done globals' );
								$( '.pewc-migration-3' ).hide();
								$( '.pewc-migration-4' ).show();
								self.process_global_conditionals( self );
							}
						}
					});
				};
				process_global_conditionals = function( self ) {
					$.ajax({
						type: 'POST',
						url: ajaxurl,
						data: {
							action: 'pewc_process_global_conditionals',
							security: $( '#pewc_migration_nonce' ).val(),
							url: "<?php echo wp_get_referer(); ?>"
						},
						success: function( response ) {
							response = JSON.parse( response );
							if( response.completed == true ) {
								// Finished
								console.log( 'done globals' );
								$( '.pewc-migration-4' ).hide();
								$( '.pewc-migration-5' ).show();
								location.href = "<?php echo $reload_url; ?>";
							}
						}
					});
				}
			});

		</script>
	</div>
<?php }

/**
 * See what products need to be migrated
 * @since 3.0.0
 */
function pewc_get_products_with_add_ons() {
	$products = new WP_Query(
		array(
			'post_type'			=> 'product',
			'posts_per_page'	=> -1,
			'meta_query'		=> array(
				'relation'		=> 'AND',
				array(
					'key'				=> '_product_extra_groups',
					'value'			=> '',
					'compare'		=> '!='
				)
			),
			'fields'				=> 'ids'
		)
	);
	if( $products ) {
		set_transient( 'pewc_migratable_products', $products->posts, pewc_get_transient_expiration() );
		return count( $products->posts );
	}
	return 0;
}

//14306,14317,14321

/**
 * See what globals need to be migrated
 * @since 3.0.0
 */
function pewc_get_global_add_ons() {
	$global_add_ons = get_option( 'pewc_global_extras', false );
	if( $global_add_ons ) {
		pewc_error_log( 'Global Add-Ons ----' );
		pewc_error_log( print_r( $global_add_ons, true ) );
		return count( $global_add_ons );
	}
	return 0;
}

function pewc_do_migration() {
	// Check the nonce
	if( ! isset( $_POST['security'] ) || ! wp_verify_nonce( $_POST['security'], 'pewc_migration_nonce' ) ) {
		echo json_encode( array( 'nonce_fail' => true ) );
		exit;
	}
	$offset = $_POST['offset'];
	$total = $_POST['total'];
	if( $offset > $total ) {
		// All done
		echo json_encode( array( 'completed' => true ) );
		exit;
	} else {
		// Create new group and field posts for this set of data
		$products = get_transient( 'pewc_migratable_products' );
		// Map old IDs to new IDs so that we can update conditions
		$map = get_transient( 'pewc_ids_map', array( 'groups' => array(), 'fields' => array() ) );
		if( $products ) {
			for( $i = $offset; $i < ( $offset + 10 ); $i++ ) {
				if( isset( $products[$i] ) ) {
					// Get the product extra field for this product
					$product_extra_groups = get_post_meta( $products[$i], '_product_extra_groups', true );
					if( $product_extra_groups ) {
						foreach( $product_extra_groups as $group_id=>$group ) {
							$group_order = get_post_meta( $products[$i], 'group_order', true );
							// Create a new group
							$group_already_migrated = false;
							$check_group = new WP_Query(
								array(
									'post_type'			=> 'pewc_group',
									'meta_key'			=> 'previous_id',
									'meta_value'		=> $products[$i] . '_' . $group_id,
									'meta_compare'	=> '=',
									'fields'				=> 'ids'
								)
							);
							if( $check_group->posts ) {
								pewc_error_log( 'Group ' . $group_id . ' already migrated' );
								$group_already_migrated = true;
							}
							if( ! $group_already_migrated ) {
								// Create a new group
								$group_data = pewc_create_new_group( $products[$i], $group_order );
								$new_group_id = $group_data['group_id'];
								pewc_error_log( '-- Creating new group --' );
								pewc_error_log( 'Product ID: '. $products[$i] );
								pewc_error_log( 'Old Group ID: '. $group_id );
								pewc_error_log( 'New Group ID: '. $new_group_id );
								pewc_migrate_group_data( $group, $new_group_id, $group_id, $products[$i] );
								$map['groups'][$group_id] = $new_group_id;

								// Then create new fields
								if( isset( $group['items'] ) ) {
									$group_fields = array();
									foreach( $group['items'] as $field_id=>$field ) {
										// Check if the field has already been migrated
										$field_already_migrated = false;
										$check_field = new WP_Query(
											array(
												'post_type'			=> 'pewc_field',
												'meta_key'			=> 'previous_id',
												'meta_value'		=> $group_id . '_' . $field_id,
												'meta_compare'	=> '=',
												'fields'				=> 'ids'
											)
										);
										if( $check_field->posts ) {
											pewc_error_log( 'Field ' . $field_id . ' already migrated' );
											$field_already_migrated = true;
										}
										if( ! $field_already_migrated ) {
											// Create the new field
											$new_field_id = pewc_create_new_field( $group_id );
											$group_fields[] = $new_field_id;
											pewc_error_log( '-- Creating new field --' );
											pewc_error_log( 'Product ID: '. $products[$i] );
											pewc_error_log( 'Old Field ID: '. $field_id );
											pewc_error_log( 'New Field ID: '. $new_field_id );
											// Update the new field's meta data
											pewc_migrate_field_data( $field, $new_field_id );
											// Ensure the id and group_id fields are populated correctly
											update_post_meta( $new_field_id, 'id', 'pewc_group_' . $new_group_id . '_' . $new_field_id );
											update_post_meta( $new_field_id, 'group_id', $new_group_id );
											// Save this value to ensure we don't duplicate any fields
											update_post_meta( $new_field_id, 'previous_id', $new_group_id . '_' . $new_field_id );
											$map['fields'][$field_id] = $new_field_id;
										}

									}

									// Save field list to group
									update_post_meta( $new_group_id, 'field_ids', $group_fields );

								}

							}

						} // end foreach( $product_extra_groups as $group_id=>$group )

					}
				}
			}
		}
		set_transient( 'pewc_ids_map', $map, pewc_get_transient_expiration() );
		$offset += 10;
		echo json_encode( $offset );
		exit;
	}
}
add_action( 'wp_ajax_pewc_do_migration', 'pewc_do_migration' );

function pewc_process_conditionals() {
	// Check the nonce
	if( ! isset( $_POST['security'] ) || ! wp_verify_nonce( $_POST['security'], 'pewc_migration_nonce' ) ) {
		echo json_encode( array( 'nonce_fail' => true ) );
		exit;
	}

	$map = get_transient( 'pewc_ids_map', array( 'groups' => array(), 'fields' => array() ) );
	if( ! empty( $map['fields'] ) ) {
		foreach( $map['fields'] as $old_field_id=>$new_field_id ) {
			// Check if a conditional is set
			$conditions = get_post_meta( $new_field_id, 'condition_field', true );
			$new_conditions = array();
			if( $conditions ) {
				// Need to update the group/field IDs
				foreach( $conditions as $condition ) {
					pewc_error_log( 'Updating condition: '. $condition );
					if( strpos( $condition, 'pewc_group_' ) !== false ) {
						$pewc_group_id = str_replace( 'pewc_group_', '', $condition );
						$condition_ids = explode( '_', $pewc_group_id );
						// Get the new group ID
						$new_condition_group_id = $map['groups'][$condition_ids[0]];
						// Get the new field ID
						$new_condition_field_id = $map['fields'][$condition_ids[1]];
						$pewc_group_id = 'pewc_group_' . $new_condition_group_id . '_' . $new_condition_field_id;
						pewc_error_log( 'New condition: '. $pewc_group_id );
						$new_conditions[] = $pewc_group_id;
					} else {
						pewc_error_log( 'New condition: '. $condition );
						$new_conditions[] = $condition;
					}

				}
				update_post_meta( $new_field_id, 'condition_field', $new_conditions );

			}
		}
	}

	pewc_error_log( 'Conditionals completed' );

	echo json_encode( array( 'completed' => true ) );
	exit;

}
add_action( 'wp_ajax_pewc_process_conditionals', 'pewc_process_conditionals' );

function pewc_process_globals() {
	// Check the nonce
	if( ! isset( $_POST['security'] ) || ! wp_verify_nonce( $_POST['security'], 'pewc_migration_nonce' ) ) {
		echo json_encode( array( 'nonce_fail' => true ) );
		exit;
	}

	$already_done = get_option( 'pewc_global_group_migration', false );

	// Map old IDs to new IDs so that we can update conditions
	$map = array( 'groups' => array(), 'fields' => array() );

	$global_add_ons = get_option( 'pewc_global_extras', false );
	$group_order = array();

	if( $global_add_ons && ! $already_done ) {

		foreach( $global_add_ons as $group_id=>$group ) {

			// Create a new group
			$group_already_migrated = false;
			$check_group = new WP_Query(
				array(
					'post_type'			=> 'pewc_group',
					'meta_key'			=> 'previous_id',
					'meta_value'		=> 'global_' . $group_id,
					'meta_compare'	=> '=',
					'fields'				=> 'ids'
				)
			);
			if( $check_group->posts ) {
				pewc_error_log( 'Group ' . $group_id . ' already migrated' );
				$group_already_migrated = true;
			}
			if( ! $group_already_migrated ) {
				// Create a new group
				$group_data = pewc_create_new_group( 0, $group_order );
				pewc_error_log( '-- Group data --' );
				pewc_error_log( print_r( $group_data, true ) );
				$group_order[] = $group_data['group_order'];
				$new_group_id = $group_data['group_id'];
				pewc_error_log( '-- Creating new global group --' );
				pewc_error_log( 'Old Group ID: '. $group_id );
				pewc_error_log( 'New Group ID: '. $new_group_id );
				pewc_migrate_group_data( $group, $new_group_id, $group_id, 'global' );
				$map['groups'][$group_id] = $new_group_id;

				// Then create new fields
				if( isset( $group['items'] ) ) {
					$group_fields = array();
					foreach( $group['items'] as $field_id=>$field ) {
						// Check if the field has already been migrated
						$field_already_migrated = false;
						$check_field = new WP_Query(
							array(
								'post_type'			=> 'pewc_field',
								'meta_key'			=> 'previous_id',
								'meta_value'		=> $group_id . '_' . $field_id,
								'meta_compare'	=> '=',
								'fields'				=> 'ids'
							)
						);
						if( $check_field->posts ) {
							pewc_error_log( 'Field ' . $field_id . ' already migrated' );
							$field_already_migrated = true;
						}
						if( ! $field_already_migrated ) {
							// Create the new field
							$new_field_id = pewc_create_new_field( $group_id );
							$group_fields[] = $new_field_id;
							pewc_error_log( '-- Creating new field --' );
							pewc_error_log( 'Old Field ID: '. $field_id );
							pewc_error_log( 'New Field ID: '. $new_field_id );
							// Update the new field's meta data
							pewc_migrate_field_data( $field, $new_field_id );
							// Ensure the id and group_id fields are populated correctly
							update_post_meta( $new_field_id, 'id', 'pewc_group_' . $new_group_id . '_' . $new_field_id );
							update_post_meta( $new_field_id, 'group_id', $new_group_id );
							// Save this value to ensure we don't duplicate any fields
							update_post_meta( $new_field_id, 'previous_id', $new_group_id . '_' . $new_field_id );
							$map['fields'][$field_id] = $new_field_id;
						}

					}

					update_post_meta( $new_group_id, 'field_ids', $group_fields );

				}

			}

		}

		// Save field list to group
		update_option( 'pewc_global_group_order', join( ',', $group_order ) );
		update_option( 'pewc_global_group_migration', 'done' ); // Prevent duplication
		pewc_error_log( 'Group order: ' . join( ',', $group_order ) );
		// Set transient for conditions
		set_transient( 'pewc_global_ids_map', $map, pewc_get_transient_expiration() );
	}

	pewc_error_log( 'Globals completed' );

	echo json_encode( array( 'completed' => true ) );
	exit;

}
add_action( 'wp_ajax_pewc_process_globals', 'pewc_process_globals' );

function pewc_process_global_conditionals() {
	// Check the nonce
	if( ! isset( $_POST['security'] ) || ! wp_verify_nonce( $_POST['security'], 'pewc_migration_nonce' ) ) {
		echo json_encode( array( 'nonce_fail' => true ) );
		exit;
	}

	pewc_error_log( 'global_conditions' );

	$map = get_transient( 'pewc_global_ids_map', array( 'groups' => array(), 'fields' => array() ) );
	if( $map['fields'] ) {
		foreach( $map['fields'] as $old_field_id=>$new_field_id ) {
			// Check if a conditional is set
			$conditions = get_post_meta( $new_field_id, 'condition_field', true );
			$condition_values = get_post_meta( $new_field_id, 'condition_value', true );
			$new_conditions = array();
			if( $conditions ) {
				// Need to update the group/field IDs
				foreach( $conditions as $condition ) {
					pewc_error_log( 'old_condition_id: ' . $condition );
					$pewc_group_id = str_replace( 'pewc_group_', '', $condition );
					$condition_ids = explode( '_', $pewc_group_id );
					// Get the new group ID
					$new_condition_group_id = $map['groups'][$condition_ids[0]];
					// Get the new field ID
					$new_condition_field_id = $map['fields'][$condition_ids[1]];
					$pewc_group_id = 'pewc_group_' . $new_condition_group_id . '_' . $new_condition_field_id;
					pewc_error_log( 'new_condition_id: ' . $pewc_group_id );
					$new_conditions[] = $pewc_group_id;
				}
				update_post_meta( $new_field_id, 'condition_field', $new_conditions );
			}
		}
	}

	pewc_error_log( 'Global conditionals completed' );

	update_option( 'pewc_has_migrated', true );

	echo json_encode( array( 'completed' => true ) );
	exit;
}
add_action( 'wp_ajax_pewc_process_global_conditionals', 'pewc_process_global_conditionals' );

/**
 * Save old group data as post data
 * @since 3.0.0
 */
function pewc_migrate_group_data( $group, $new_group_id, $old_group_id, $product_id ) {
	if( isset( $group['meta']['group_title'] ) ) {
		update_post_meta( $new_group_id, 'group_title', sanitize_text_field( $group['meta']['group_title'] ) );
	}
	if( isset( $group['meta']['group_description'] ) ) {
		update_post_meta( $new_group_id, 'group_description', wp_kses_post( $group['meta']['group_description'] ) );
	}
	if( isset( $group['global_rules'] ) ) {
		update_post_meta( $new_group_id, 'global_rules', $group['global_rules'] );
	}
	// Save this value to ensure we don't duplicate any groups
	update_post_meta( $new_group_id, 'previous_id', $product_id . '_' . $old_group_id );
}

/**
 * Save old field data as post data
 * @since 3.0.0
 */
function pewc_migrate_field_data( $field, $new_field_id ) {
	$params = pewc_get_field_params( $new_field_id );
	if( $params ) {
		foreach( $params as $param ) {
			$value = isset( $field[$param] ) ? $field[$param] : false;
			if( $value ) {
				update_post_meta( $new_field_id, $param, $value );
			}
		}
		// Check for min_chars and max_chars
		if( isset( $field['min_chars'] ) && empty( $field['field_minchars'] ) ) {
			update_post_meta( $new_field_id, 'field_minchars', $field['min_chars'] );
		}
		if( isset( $field['max_chars'] ) && empty( $field['field_maxchars'] ) ) {
			update_post_meta( $new_field_id, 'field_maxchars', $field['max_chars'] );
		}
	}
}
