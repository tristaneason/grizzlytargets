<?php
/**
 * Handles all admin ajax requests.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    makewebbetter-hubspot-for-woocommerce
 * @subpackage makewebbetter-hubspot-for-woocommerce/includes
 */

if ( ! class_exists( 'HubWooAjaxHandler' ) ) {

	/**
	 * Handles all admin ajax requests.
	 *
	 * All the functions required for handling admin ajax requests
	 * required by the plugin.
	 *
	 * @package    makewebbetter-hubspot-for-woocommerce
	 * @subpackage makewebbetter-hubspot-for-woocommerce/includes
	 */
	class Hubwoo_Ajax_Handler {

		/**
		 * Class constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			// check oauth access token.
			add_action( 'wp_ajax_hubwoo_check_oauth_access_token', array( &$this, 'hubwoo_check_oauth_access_token' ) );
			// create group for properties.
			add_action( 'wp_ajax_hubwoo_create_property_group', array( &$this, 'hubwoo_create_property_group' ) );
			// get group properties.
			add_action( 'wp_ajax_hubwoo_get_group_properties', array( &$this, 'hubwoo_get_group_properties' ) );
			// create property.
			add_action( 'wp_ajax_hubwoo_create_group_property', array( &$this, 'hubwoo_create_group_property' ) );
			// get final lists to be created.
			add_action( 'wp_ajax_hubwoo_get_lists', array( &$this, 'hubwoo_get_lists_to_create' ) );
			// create bulk lists.
			add_action( 'wp_ajax_hubwoo_create_list', array( &$this, 'hubwoo_create_list' ) );
			// create single single group on admin call..
			add_action( 'wp_ajax_hubwoo_create_single_group', array( &$this, 'hubwoo_create_single_group' ) );
			// create single property on admin call.
			add_action( 'wp_ajax_hubwoo_create_single_property', array( &$this, 'hubwoo_create_single_property' ) );
			// create single list on admin call.
			add_action( 'wp_ajax_hubwoo_create_single_list', array( &$this, 'hubwoo_create_single_list' ) );
			// create workflow.
			add_action( 'wp_ajax_hubwoo_create_single_workflow', array( &$this, 'hubwoo_create_single_workflow' ) );
			// updating workflow which are dependent.
			add_action( 'wp_ajax_hubwoo_update_workflow_tab', array( &$this, 'hubwoo_update_workflow_tab' ) );
			// search for order statuses.
			add_action( 'wp_ajax_hubwoo_search_for_order_status', array( &$this, 'hubwoo_search_for_order_status' ) );
			// get user roles for batch sync.
			add_action( 'wp_ajax_hubwoo_get_for_user_roles', array( &$this, 'hubwoo_get_for_user_roles' ) );
			// instant sync of users to HubSpot.
			add_action( 'wp_ajax_hubwoo_ocs_instant_sync', array( &$this, 'hubwoo_ocs_instant_sync' ) );
			// emailing the errors to makewebbetter support.
			add_action( 'wp_ajax_hubwoo_email_the_error_log', array( &$this, 'hubwoo_email_the_error_log' ) );
			// disconnect the current account and delete all of the meta.
			add_action( 'wp_ajax_hubwoo_disconnect_account', array( &$this, 'hubwoo_disconnect_account' ) );
			// get all of the users for the current selected user roles.
			add_action( 'wp_ajax_hubwoo_get_user_for_current_roles', array( &$this, 'hubwoo_get_user_for_current_roles' ) );
			// get sync status of any background sync process.
			add_action( 'wp_ajax_hubwoo_get_current_sync_status', array( &$this, 'hubwoo_get_current_sync_status' ) );
			// save objects in database option table.
			add_action( 'wp_ajax_hubwoo_save_updates', array( &$this, 'hubwoo_save_updates' ) );
			// get the all of the deal stages for select2.
			add_action( 'wp_ajax_hubwoo_deals_search_for_stages', array( &$this, 'hubwoo_deals_search_for_stages' ) );
			// run processes for the ecommerce pipeline setup.
			add_action( 'wp_ajax_hubwoo_ecomm_setup', array( &$this, 'hubwoo_ecomm_setup' ) );
			// get the current ocs count for deals.
			add_action( 'wp_ajax_hubwoo_ecomm_get_ocs_count', array( &$this, 'hubwoo_ecomm_get_ocs_count' ) );
			// manage sync processes.
			add_action( 'wp_ajax_hubwoo_manage_sync', array( &$this, 'hubwoo_manage_sync' ) );
			// manage historical objects vids.
			add_action( 'wp_ajax_hubwoo_manage_vids', array( &$this, 'hubwoo_manage_vids' ) );
			// track sync statuses of background executable tasks.
			add_action( 'wp_ajax_hubwoo_sync_status_tracker', array( &$this, 'hubwoo_sync_status_tracker' ) );
			// submit the onboarding question form .
			add_action( 'wp_ajax_hubwoo_onboard_form', array( &$this, 'hubwoo_onboard_form' ) );
			// get the onboarding data questionaire.
			add_action( 'wp_ajax_hubwoo_get_onboard_form', array( &$this, 'hubwoo_get_onboard_form' ) );
		}

		/**
		 * Checking access token validity.
		 *
		 * @since 1.0.0
		 */
		public function hubwoo_check_oauth_access_token() {

			$response = array(
				'status'  => true,
				'message' => esc_html__( 'Success', 'makewebbetter-hubspot-for-woocommerce' ),
			);

			check_ajax_referer( 'hubwoo_security', 'hubwooSecurity' );

			if ( Hubwoo::is_access_token_expired() ) {

				$hapikey = HUBWOO_CLIENT_ID;
				$hseckey = HUBWOO_SECRET_ID;
				$status  = HubWooConnectionMananager::get_instance()->hubwoo_refresh_token( $hapikey, $hseckey );

				if ( ! $status ) {

					$response['status']  = false;
					$response['message'] = esc_html__( 'Something went wrong. Please verify your HubSpot Connection once.', 'makewebbetter-hubspot-for-woocommerce' );
				}
			}

			echo wp_json_encode( $response );

			wp_die();
		}

		/**
		 * Create new group for contact properties.
		 *
		 * @since 1.0.0
		 */
		public function hubwoo_create_property_group() {

			check_ajax_referer( 'hubwoo_security', 'hubwooSecurity' );
			if ( ! empty( $_POST['groupName'] ) ) {
				$group_name = sanitize_key( wp_unslash( $_POST['groupName'] ) );
			}
			$groups        = HubWooContactProperties::get_instance()->_get( 'groups' );
			$group_details = array();
			if ( ! empty( $groups ) ) {
				foreach ( $groups as $single_group ) {
					if ( $single_group['name'] == $group_name ) {
						$group_details = $single_group;
						break;
					}
				}
			}
			$response = HubWooConnectionMananager::get_instance()->create_group( $group_details );
			echo wp_json_encode( $response );
			wp_die();
		}

		/**
		 * Get hubwoo group properties by group name.
		 *
		 * @since 1.0.0
		 */
		public function hubwoo_get_group_properties() {

			check_ajax_referer( 'hubwoo_security', 'hubwooSecurity' );

			if ( isset( $_POST['groupName'] ) ) {

				$group_name = sanitize_text_field( wp_unslash( $_POST['groupName'] ) );
				$properties = HubWooContactProperties::get_instance()->_get( 'properties', $group_name );
				echo wp_json_encode( $properties );
			}

			wp_die();
		}

		/**
		 * Create an group property on ajax request.
		 *
		 * @since 1.0.0
		 */
		public function hubwoo_create_group_property() {

			// check the nonce sercurity.
			check_ajax_referer( 'hubwoo_security', 'hubwooSecurity' );

			if ( isset( $_POST['propertyDetails'] ) ) {
				$property_details = map_deep( wp_unslash( $_POST['propertyDetails'] ), 'sanitize_text_field' );
				$response         = HubWooConnectionMananager::get_instance()->create_batch_properties( $property_details, 'contact' );
				$response['body'] = json_decode( $response['body'], true );
				echo wp_json_encode( $response );
				wp_die();
			}
		}


		/**
		 * Get lists to be created on husbpot.
		 *
		 * @since 1.0.0
		 */
		public function hubwoo_get_lists_to_create() {

			check_ajax_referer( 'hubwoo_security', 'hubwooSecurity' );

			$lists = HubWooContactProperties::get_instance()->_get( 'lists' );

			echo wp_json_encode( $lists );

			wp_die();
		}

		/**
		 * Create bulk lists on hubspot.
		 *
		 * @since 1.0.0
		 */
		public function hubwoo_create_list() {

			check_ajax_referer( 'hubwoo_security', 'hubwooSecurity' );

			if ( isset( $_POST['listDetails'] ) ) {

				$list_details = map_deep( wp_unslash( $_POST['listDetails'] ), 'sanitize_text_field' );
				$response     = HubWooConnectionMananager::get_instance()->create_list( $list_details );
				echo wp_json_encode( $response );
			}

			wp_die();
		}


		/**
		 * Create single group on HubSpot.
		 *
		 * @since 1.0.0
		 */
		public function hubwoo_create_single_group() {

			check_ajax_referer( 'hubwoo_security', 'hubwooSecurity' );
			if ( ! empty( $_POST['name'] ) ) {
				$group_name = sanitize_text_field( wp_unslash( $_POST['name'] ) );
			} else {
				$group_name = '';
			}
			$groups        = HubWooContactProperties::get_instance()->_get( 'groups' );
			$group_details = '';

			if ( is_array( $groups ) && count( $groups ) ) {

				foreach ( $groups as $single_group ) {

					if ( $single_group['name'] === $group_name ) {

						$group_details = $single_group;
						break;
					}
				}
			}

			if ( ! empty( $group_details ) ) {

				$response = HubWooConnectionMananager::get_instance()->create_group( $group_details );
			}

			if ( isset( $response['status_code'] ) && ( 200 === $response['status_code'] || 409 === $response['status_code'] ) ) {

				$add_groups   = get_option( 'hubwoo-groups-created', array() );
				$add_groups[] = $group_details['name'];
				update_option( 'hubwoo-groups-created', $add_groups );
			}

			echo wp_json_encode( $response );
			wp_die();
		}

		/**
		 * Create single property on HubSpot.
		 *
		 * @since 1.0.0
		 */
		public function hubwoo_create_single_property() {

			check_ajax_referer( 'hubwoo_security', 'hubwooSecurity' );

			if ( ! empty( $_POST['group'] ) ) {
				$group_name = sanitize_text_field( wp_unslash( $_POST['group'] ) );
			} else {
				$group_name = '';
			}

			if ( ! empty( $_POST['name'] ) ) {
				$property_name = sanitize_text_field( wp_unslash( $_POST['name'] ) );
			} else {
				$property_name = '';
			}

			$properties = HubWooContactProperties::get_instance()->_get( 'properties', $group_name );

			if ( ! empty( $properties ) && count( $properties ) ) {

				foreach ( $properties as $single_property ) {

					if ( ! empty( $single_property['name'] ) && $single_property['name'] == $property_name ) {

						$property_details = $single_property;
						break;
					}
				}
			}

			if ( ! empty( $property_details ) ) {

				$property_details['groupName'] = $group_name;

				$response = HubWooConnectionMananager::get_instance()->create_property( $property_details );
			}

			if ( isset( $response['status_code'] ) && ( 200 === $response['status_code'] || 409 === $response['status_code'] ) ) {

				$add_properties   = get_option( 'hubwoo-properties-created', array() );
				$add_properties[] = $property_details['name'];
				update_option( 'hubwoo-properties-created', $add_properties );
			}

			echo wp_json_encode( $response );

			wp_die();
		}

		/**
		 * Create single list on hubspot.
		 *
		 * @since 1.0.0
		 */
		public function hubwoo_create_single_list() {

			check_ajax_referer( 'hubwoo_security', 'hubwooSecurity' );

			if ( isset( $_POST['name'] ) ) {

				$list_name = sanitize_text_field( wp_unslash( $_POST['name'] ) );

				$lists = HubWooContactProperties::get_instance()->_get( 'lists' );

				if ( ! empty( $lists ) && count( $lists ) ) {

					foreach ( $lists as $single_list ) {

						if ( ! empty( $single_list['name'] ) && $single_list['name'] == $list_name ) {

							$list_details = $single_list;
							break;
						}
					}
				}

				if ( ! empty( $list_details ) ) {

					$response = HubWooConnectionMananager::get_instance()->create_list( $list_details );
				}

				if ( isset( $response['status_code'] ) && ( 200 === $response['status_code'] || 409 === $response['status_code'] ) ) {

					$add_lists   = get_option( 'hubwoo-lists-created', array() );
					$add_lists[] = $list_name;
					update_option( 'hubwoo-lists-created', $add_lists );
				}

				echo wp_json_encode( $response );
				wp_die();
			}
		}

		/**
		 * Create single list on hubspot.
		 *
		 * @since 1.0.0
		 */
		public function hubwoo_create_single_workflow() {

			check_ajax_referer( 'hubwoo_security', 'hubwooSecurity' );

			if ( ! empty( $_POST['name'] ) ) {

				$name = sanitize_text_field( wp_unslash( $_POST['name'] ) );

				$add_workflows = get_option( 'hubwoo-workflows-created', array() );

				if ( in_array( $name, $add_workflows ) ) {
					return;
				}

				$workflows = HubWooContactProperties::get_instance()->_get( 'workflows' );

				if ( ! empty( $workflows ) ) {

					foreach ( $workflows as $single_workflow ) {

						if ( isset( $single_workflow['name'] ) && $single_workflow['name'] == $name ) {

							$workflow_details = $single_workflow;
							break;
						}
					}
				}

				if ( ! empty( $workflow_details ) ) {

					$response = HubWooConnectionMananager::get_instance()->create_workflow( $workflow_details );

					if ( isset( $response['status_code'] ) && ( 200 != $response['status_code'] ) ) {

						$response = HubwooErrorHandling::get_instance()->hubwoo_handle_response( $response, HubwooConst::HUBWOOWORKFLOW, array( 'current_workflow' => $workflow_details ) );
					}

					if ( 200 == $response['status_code'] ) {

						$add_workflows[] = $workflow_details['name'];
						update_option( 'hubwoo-workflows-created', $add_workflows );

						$workflow_data = isset( $response['body'] ) ? $response['body'] : '';

						if ( ! empty( $workflow_data ) ) {

							$workflow_data = json_decode(
								$workflow_data
							);
							$id            = isset( $workflow_data->id ) ? $workflow_data->id : '';
							update_option( $workflow_details['name'], $id );
						}
					}

					echo wp_json_encode( $response );
					wp_die();
				}
			}
		}

		/**
		 * Ajax search for order statuses.
		 *
		 * @since 1.0.0
		 */
		public function hubwoo_search_for_order_status() {

			$order_statuses = wc_get_order_statuses();

			$modified_order_statuses = array();

			if ( ! empty( $order_statuses ) ) {

				foreach ( $order_statuses as $status_key => $single_status ) {

					$modified_order_statuses[] = array( $status_key, $single_status );
				}
			}

			echo wp_json_encode( $modified_order_statuses );

			wp_die();
		}

		/**
		 * User roles for batch sync.
		 *
		 * @since 1.0.0
		 */
		public function hubwoo_get_for_user_roles() {

			global $hubwoo;

			$user_roles = $hubwoo->hubwoo_get_user_roles();

			$modified_order_statuses = array();

			if ( ! empty( $user_roles ) ) {

				foreach ( $user_roles as $user_key => $single_role ) {

					$modified_order_statuses[] = array( $user_key, $single_role );
				}
			}

			echo wp_json_encode( $modified_order_statuses );

			wp_die();
		}

		/**
		 * Callback to sync contacts to hubspot in 1 click.
		 *
		 * @since 1.0.0
		 */
		public function hubwoo_ocs_instant_sync() {

			check_ajax_referer( 'hubwoo_security', 'hubwooSecurity' );
			if ( ! empty( $_POST['step'] ) ) {
				$step = sanitize_text_field( wp_unslash( $_POST['step'] ) );
			} else {
				$step = '';
			}

			$contact_sync    = true;
			$hubwoo_datasync = new HubwooDataSync();

			$total_need_syncing = $hubwoo_datasync->hubwoo_get_all_unique_user( true );

			if ( ! $total_need_syncing ) {

				$percentage_done = 100;
				$response        = array(
					'step'       => $step + 1,
					'progress'   => $percentage_done,
					'completed'  => true,
					'nocontacts' => true,
				);

				echo wp_json_encode( $response );
				wp_die();
			}

			$users_need_syncing = $hubwoo_datasync->hubwoo_get_all_unique_user();

			$user_data = array();

			$args = array();

			if ( is_array( $users_need_syncing ) && count( $users_need_syncing ) ) {

				$user_data    = $hubwoo_datasync->get_sync_data( $users_need_syncing );
				$args['ids']  = $users_need_syncing;
				$args['type'] = 'user';
			} else {

				$roles = get_option( 'hubwoo_customers_role_settings', array() );

				if ( in_array( 'guest_user', $roles, true ) ) {

					$contact_sync = false;

					$user_to_sync = $hubwoo_datasync->hubwoo_get_all_unique_user( false, 'guestOrder' );

					$user_data    = $hubwoo_datasync->get_guest_sync_data( $user_to_sync );
					$args['ids']  = $user_to_sync;
					$args['type'] = 'order';
				}
			}

			if ( ! empty( $user_data ) ) {

				if ( Hubwoo::is_valid_client_ids_stored() ) {

					$flag = true;

					if ( Hubwoo::is_access_token_expired() ) {

						$hapikey = HUBWOO_CLIENT_ID;
						$hseckey = HUBWOO_SECRET_ID;
						$status  = HubWooConnectionMananager::get_instance()->hubwoo_refresh_token( $hapikey, $hseckey );

						if ( ! $status ) {

							$flag = false;
						}
					}

					if ( $flag ) {
						$response = HubWooConnectionMananager::get_instance()->create_or_update_contacts( $user_data, $args );

						if ( ( count( $user_data ) > 1 ) && isset( $response['status_code'] ) && 400 === $response['status_code'] ) {

							if ( isset( $response['response'] ) ) {
								$error_response = json_decode( $response['response'], true );

								if ( isset( $error_response['failureMessages'] ) ) {

									$failure_messages = $error_response['failureMessages'];

									if ( is_array( $failure_messages ) ) {
										foreach ( $failure_messages as $failure_error ) {
											$property_validation_error = isset( $failure_error['propertyValidationResult'] ) ? 1 : 0;
											if ( $property_validation_error ) {
												$percentage_done = 100;
												$response        = array(
													'step' => $step + 1,
													'progress' => $percentage_done,
													'completed' => true,
													'propertyError' => true,
												);

												echo wp_json_encode( $response );
												wp_die();
											}
										}
									}
								}
							}
							$response = Hubwoo_Admin::hubwoo_split_contact_batch( $user_data );
						}
						$hsocssynced = get_option( 'hubwoo_ocs_contacts_synced', 0 );

						if ( ! empty( $user_to_sync ) ) {
							$hsocssynced += count( $user_to_sync );
						}

						if ( $contact_sync ) {
							foreach ( $users_need_syncing as $user_id ) {
								update_user_meta( $user_id, 'hubwoo_pro_user_data_change', 'synced' );
							}
						} else {
							foreach ( $user_to_sync as $order_id ) {
								update_post_meta( $order_id, 'hubwoo_pro_guest_order', 'synced' );
							}
						}

						update_option( 'hubwoo_ocs_contacts_synced', $hsocssynced );
					}
				}
			}

			$percentage_done = 0;
			$total_users     = get_option( 'hubwoo_total_ocs_need_sync', 0 );
			if ( $total_users ) {

				$synced          = $total_users - $total_need_syncing;
				$percentage      = ( $synced / $total_users ) * 100;
				$percentage_done = sprintf( '%.2f', $percentage );
			}

			$response = array(
				'step'             => $step + 1,
				'progress'         => $percentage_done,
				'totalNeedSyncing' => $total_need_syncing,
				'synced'           => wp_json_encode( $users_need_syncing ),
			);

			$contactqueue = $hubwoo_datasync->hubwoo_get_all_unique_user( true );

			if ( ! $contactqueue ) {
				$response['progress']  = 100;
				$response['completed'] = true;
				delete_option( 'hubwoo_total_ocs_need_sync' );
				delete_option( 'hubwoo_ocs_contacts_synced' );
			}

			echo wp_json_encode( $response );

			wp_die();
		}


		/**
		 * Update workflow listing window when a workflow is created.
		 *
		 * @since 1.0.0
		 */
		public function hubwoo_update_workflow_tab() {

			global $hubwoo;

			$created_workflows = get_option( 'hubwoo-workflows-created', '' );

			$workflows_dependencies = $hubwoo->hubwoo_workflows_dependency();

			$updated_tabs = array();

			$dependencies_count = 0;

			if ( is_array( $workflows_dependencies ) && count( $workflows_dependencies ) ) {
				foreach ( $workflows_dependencies as $workflows ) {
					$dependencies_count = count( $workflows['dependencies'] );
					$counter            = 0;
					foreach ( $workflows['dependencies'] as $dependencies ) {
						if ( is_array( $created_workflows ) && count( $created_workflows ) ) {
							if ( in_array( $dependencies, $created_workflows, true ) ) {
								$counter++;
							}
						}
					}
					if ( $counter === $dependencies_count ) {
						$updated_tabs[] = $workflows['workflow'];
					}
				}
			}
			echo wp_json_encode( $updated_tabs );
			wp_die();
		}

		/**
		 * Email the hubspot API error log.
		 *
		 * @since 1.0.0
		 */
		public function hubwoo_email_the_error_log() {

			check_ajax_referer( 'hubwoo_security', 'hubwooSecurity' );
			$log_dir     = WC_LOG_DIR . 'hubspot-for-woocommerce-logs.log';
			$attachments = array( $log_dir );
			$to          = 'integrations@makewebbetter.com';
			$subject     = 'HubSpot Pro Error Logs';
			$headers     = array( 'Content-Type: text/html; charset=UTF-8' );
			$message     = 'admin email: ' . get_option( 'admin_email', '' ) . '<br/>';
			$status      = wp_mail( $to, $subject, $message, $headers, $attachments );

			if ( 1 === $status ) {
				$status = 'success';
			} else {
				$status = 'failure';
			}
			update_option( 'hubwoo_pro_alert_param_set', false );
			echo wp_json_encode( $status );
			wp_die();
		}


		/**
		 * Disconnect hubspot account.
		 *
		 * @since 1.0.0
		 */
		public function hubwoo_disconnect_account() {

			check_ajax_referer( 'hubwoo_security', 'hubwooSecurity' );

			global $hubwoo;

			$delete_meta = false;

			if ( isset( $_POST['data'] ) ) {
				$data        = map_deep( wp_unslash( $_POST['data'] ), 'sanitize_text_field' );
				$delete_meta = 'yes' == $data['delete_meta'] ? true : false;
			}

			$hubwoo->hubwoo_switch_account( true, $delete_meta );
			echo wp_json_encode( true );
			wp_die();
		}

		/**
		 * Get wordpress/woocommerce user roles.
		 *
		 * @since 1.0.0
		 */
		public function hubwoo_get_user_for_current_roles() {
			// check the nonce sercurity.
			check_ajax_referer( 'hubwoo_security', 'hubwooSecurity' );

			$hubwoo_data_sync = new HubwooDataSync();
			$unique_users     = $hubwoo_data_sync->hubwoo_get_all_unique_user( true );
			update_option( 'hubwoo_total_ocs_need_sync', $unique_users );
			echo wp_json_encode( $unique_users );
			wp_die();
		}

		/**
		 * Get sync status for contact/deal.
		 *
		 * @since 1.0.0
		 */
		public function hubwoo_get_current_sync_status() {
			// check the nonce sercurity.
			check_ajax_referer( 'hubwoo_security', 'hubwooSecurity' );
			if ( ! empty( $_POST['data'] ) ) {
				$type = map_deep( wp_unslash( $_POST['data'] ), 'sanitize_text_field' );
			} else {
				$type = '';
			}
			if ( isset( $type['type'] ) ) {
				switch ( $type['type'] ) {
					case 'contact':
						$status = get_option( 'hubwoo_background_process_running', false );
						break;
					case 'deal':
						$status = get_option( 'hubwoo_deals_sync_running', 0 );
						break;
					default:
						$status = false;
						break;
				}
				echo wp_json_encode( $status );
			}
			wp_die();
		}

		/**
		 * Saving Updates to the Database.
		 *
		 * @since 1.0.0
		 */
		public function hubwoo_save_updates() {

			// check the nonce sercurity.
			check_ajax_referer( 'hubwoo_security', 'hubwooSecurity' );

			if ( isset( $_POST['updates'] ) && ! empty( $_POST['action'] ) ) {
				$updates = map_deep( wp_unslash( $_POST['updates'] ), 'sanitize_text_field' );

				if ( isset( $_POST['type'] ) ) {
					$action = map_deep( wp_unslash( $_POST['type'] ), 'sanitize_text_field' );
					$status = false;
					if ( count( $updates ) ) {

						foreach ( $updates as $db_key => $value ) {

							if ( 'update' === $action ) {
								$value = 'EMPTY_ARRAY' === $value ? array() : $value;
								update_option( $db_key, $value );
							} elseif ( 'delete' === $action ) {
								delete_option( $value );
							}
						}

						$status = true;
					}
					echo wp_json_encode( $status );
					wp_die();
				}
			}
		}

		/**
		 * Saving Updates to the Database.
		 *
		 * @since 1.0.0
		 */
		public function hubwoo_manage_sync() {

			// check the nonce sercurity.
			check_ajax_referer( 'hubwoo_security', 'hubwooSecurity' );

			if ( ! empty( $_POST['process'] ) ) {
				$process = map_deep( wp_unslash( $_POST['process'] ), 'sanitize_text_field' );

				if ( ! empty( $process ) ) {

					if ( 'start-deal' === $process ) {
						$orders_needs_syncing = Hubwoo_Admin::hubwoo_orders_count_for_deal();
						if ( $orders_needs_syncing ) {

							update_option( 'hubwoo_deals_sync_running', 1 );
							update_option( 'hubwoo_deals_sync_total', $orders_needs_syncing );
							as_schedule_recurring_action( time(), 300, 'hubwoo_deals_sync_background' );
						}
					} else {
						Hubwoo::hubwoo_stop_sync( $process );
					}
					echo true;
				}
			}

			wp_die();
		}

		/**
		 * Manages Vids to Database.
		 *
		 * @since 1.0.0
		 */
		public function hubwoo_manage_vids() {

			// check the nonce sercurity.
			check_ajax_referer( 'hubwoo_security', 'hubwooSecurity' );

			$status = false;

			if ( ! empty( $_POST['process'] ) ) {
				$process = map_deep( wp_unslash( $_POST['process'] ), 'sanitize_text_field' );
				if ( ! empty( $process ) ) {
					switch ( $process ) {
						case 'contact':
							update_option( 'hubwoo_contact_vid_update', 1 );
							as_schedule_recurring_action( time(), 300, 'hubwoo_update_contacts_vid' );
							$status = true;
							break;
						case 'deal':
							delete_option( 'hubwoo_ecomm_order_date_allow' );
							$orders_needs_syncing = Hubwoo_Admin::hubwoo_orders_count_for_deal();
							if ( $orders_needs_syncing ) {
								update_option( 'hubwoo_deals_sync_running', 1 );
								update_option( 'hubwoo_deals_sync_total', $orders_needs_syncing );
								as_schedule_recurring_action( time(), 300, 'hubwoo_deals_sync_background' );
								$status = true;
							}
							break;
						default:
							break;
					}
				}
			}
			echo wp_json_encode( $status );
			wp_die();
		}

		/**
		 * Ajax call to search for deal stages.
		 *
		 * @since 1.0.0
		 */
		public function hubwoo_deals_search_for_stages() {

			$stages = get_option( 'hubwoo_fetched_deal_stages', array() );

			$existing_stages = array();

			if ( is_array( $stages ) && count( $stages ) ) {

				foreach ( $stages as $stage ) {

					$existing_stages[] = array( $stage['stageId'], $stage['label'] );
				}
			}

			echo wp_json_encode( $existing_stages );
			wp_die();
		}

		/**
		 * Get orders count for 1 click sync.
		 *
		 * @since 1.0.0
		 */
		public function hubwoo_ecomm_get_ocs_count() {

			check_ajax_referer( 'hubwoo_security', 'hubwooSecurity' );

			$ocs_order_count = Hubwoo_Admin::hubwoo_orders_count_for_deal();
			echo wp_json_encode( $ocs_order_count );
			wp_die();
		}

		/**
		 * Track sync percentage and eta for background processes
		 *
		 * @since 1.0.0
		 */
		public function hubwoo_sync_status_tracker() {

			check_ajax_referer( 'hubwoo_security', 'hubwooSecurity' );
			$response = array(
				'percentage' => 0,
				'is_running' => 'no',
			);

			if ( isset( $_POST['process'] ) ) {
				$process = map_deep( wp_unslash( $_POST['process'] ), 'sanitize_text_field' );
				if ( ! empty( $process ) ) {
					switch ( $process ) {
						case 'contact':
							if ( get_option( 'hubwoo_background_process_running', false ) ) {

								$users_to_sync          = get_option( 'hubwoo_total_ocs_need_sync', 0 );
								$current_user_sync      = get_option( 'hubwoo_ocs_contacts_synced', 0 );
								$response['percentage'] = round( $current_user_sync * 100 / $users_to_sync );
								$response['is_running'] = 'yes';
							}

							break;
						case 'product':
							if ( 'yes' == get_option( 'hubwoo_start_product_sync', 'no' ) ) {
								$total_products = get_option( 'hubwoo_products_to_sync', 0 );
								$sync_result    = Hubwoo::hubwoo_make_db_query( 'total_synced_products' );
								if ( ! empty( $sync_result ) ) {
									$sync_result     = (array) $sync_result[0];
									$synced_products = $sync_result['COUNT(post_id)'];
									if ( 0 != $total_products ) {
										$response['percentage'] = round( $synced_products * 100 / $total_products );
										$response['eta']        = Hubwoo::hubwoo_create_sync_eta( $synced_products, $total_products, 3, 5 );
										$response['is_running'] = 'yes';
									}
								}
							}
							break;
						case 'order':
							if ( 1 == get_option( 'hubwoo_deals_sync_running', 0 ) ) {
								$data                   = Hubwoo::get_sync_status();
								$response['percentage'] = $data['deals_progress'];
								$response['eta']        = $data['eta_deals_sync'];
								$response['is_running'] = 'yes';
							}
							break;
					}
					echo wp_json_encode( $response );
				}
			}
			wp_die();
		}
		/**
		 * Upserting ecomm bridge settings for hubspot objects-contact,deal,product,line-item
		 *
		 * @since    1.0.0
		 */
		public function hubwoo_ecomm_setup() {

			check_ajax_referer( 'hubwoo_security', 'hubwooSecurity' );

			$response = array(
				'status_code' => 404,
				'response'    => 'E-Commerce Bridge Setup Failed.',
			);

			if ( ! empty( $_POST['process'] ) ) {
				$process = map_deep( wp_unslash( $_POST['process'] ), 'sanitize_text_field' );

				if ( ! empty( $process ) ) {
					switch ( $process ) {
						case 'upsert-store':
							$store    = Hubwoo::get_store_data();
							$response = HubWooConnectionMananager::get_instance()->create_or_update_store( $store );
							break;
						case 'get-total-products':
							$store = Hubwoo::get_store_data();
							break;
						case 'update-deal-stages':
							$deal_stages           = Hubwoo::fetch_deal_stages_from_pipeline( 'Ecommerce Pipeline', false );
							$deal_model            = Hubwoo::hubwoo_deal_stage_model();
							$process_deal_stages   = array_map(
								function ( $deal_stage_data ) use ( $deal_model ) {
									$updates = $deal_model[ $deal_stage_data['stageId'] ];
									if ( ! empty( $updates ) ) {
										foreach ( $updates as $key => $value ) {
											if ( array_key_exists( $key, $deal_stage_data ) ) {
												  $deal_stage_data[ $key ] = $value;
											}
										}
									}
									return $deal_stage_data;
								},
								$deal_stages['stages']
							);
							$deal_stages['stages'] = $process_deal_stages;
							$response              = HubWooConnectionMananager::get_instance()->update_deal_pipeline( $deal_stages );
							update_option( 'hubwoo_ecomm_final_mapping', Hubwoo::hubwoo_deals_mapping() );
							break;
						case 'reset-mapping':
							update_option( 'hubwoo_ecomm_final_mapping', Hubwoo::hubwoo_deals_mapping() );
							break;
						case 'start-products-sync':
							if ( ! as_next_scheduled_action( 'hubwoo_products_sync_background' ) ) {
								update_option( 'hubwoo_start_product_sync', 'yes' );
								as_schedule_recurring_action( time(), 180, 'hubwoo_products_sync_background' );
							}
							$response['status_code'] = 200;
							$response['response']    = 'Product Sync-Status has been initiated';
							break;

					}
					echo wp_json_encode( $response );
				}
			}
			wp_die();
		}

		/**
		 * Get the onboarding submission data.
		 *
		 * @since    1.0.4
		 */
		public function hubwoo_get_onboard_form() {

			// check the nonce sercurity.
			check_ajax_referer( 'hubwoo_security', 'hubwooSecurity' );
			if ( ! empty( $_POST['key'] ) ) {
				$key     = map_deep( wp_unslash( $_POST['key'] ), 'sanitize_text_field' );
				$key     = str_replace( '[]', '', $key );
				$options = array_map(
					function( $option ) {
						return array( $option, $option );
					},
					Hubwoo::hubwoo_onboarding_questionaire()[ $key ]['options']
				);
				echo json_encode( $options );
			}
			wp_die();
		}

		/**
		 * Handle the onboarding form submision.
		 *
		 * @since    1.0.4
		 */
		public function hubwoo_onboard_form() {
			// check the nonce sercurity.
			check_ajax_referer( 'hubwoo_security', 'hubwooSecurity' );

			if ( ! empty( $_POST['formData'] ) ) {
				$form_data = map_deep( wp_unslash( $_POST['formData'] ), 'sanitize_text_field' );
				if ( ! empty( $form_data ) ) {
					$form_details = array();
					array_walk(
						$form_data,
						function( $field, $name ) use ( &$form_details ) {
							if ( is_array( $field ) ) {
								$field = HubwooGuestOrdersManager::hubwoo_format_array( $field );
							}
							$form_details['fields'][] = array(
								'name'  => $name,
								'value' => $field,
							);
						}
					);
					echo json_encode( HubWooConnectionMananager::get_instance()->submit_form_data( $form_details, '6493626', 'fe467fde-b918-4a13-9f21-a92bddadff56' ) );
				}
			}
			wp_die();
		}
	}
}

new Hubwoo_Ajax_Handler();
