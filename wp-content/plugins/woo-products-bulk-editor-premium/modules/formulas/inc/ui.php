<?php
if (!class_exists('WPSE_Formulas_UI')) {

	class WPSE_Formulas_UI {

		static private $instance = false;

		private function __construct() {
			
		}

		function init() {

			add_action('vg_sheet_editor/editor/before_init', array($this, 'register_toolbar_items'));
			add_action('vg_sheet_editor/after_enqueue_assets', array($this, 'register_assets'));
			add_filter('vg_sheet_editor/js_data', array($this, 'add_bulk_selector_column'));
		}

		function add_bulk_selector_column($args) {
			if (!apply_filters('vg_sheet_editor/formulas/is_bulk_selector_column_allowed', true, $args)) {
				return $args;
			}
			$new_columns = array(
				'wpseBulkSelector' => array(
					'type' => 'checkbox',
					'columnSorting' => false
				)
			);

			$args['columnsUnformat'] = array_merge($new_columns, $args['columnsUnformat']);
			$args['columnsFormat'] = array_merge($new_columns, $args['columnsFormat']);
			$args['startCols'] ++;
			$args['colWidths'] = array_merge(array(40), $args['colWidths']);
			$args['colHeaders'] = array_merge(array('<input type="checkbox" class="bulk-selector" data-row="0" data-col="0" />'), $args['colHeaders']);
			if (!empty($args['custom_handsontable_args'])) {
				$args['custom_handsontable_args'] = json_decode($args['custom_handsontable_args'], true);
			}
			if (is_array($args['custom_handsontable_args'])) {
				if (!empty($args['custom_handsontable_args']['fixedColumnsLeft'])) {
					$args['custom_handsontable_args']['fixedColumnsLeft'] ++;
				}
				$args['custom_handsontable_args'] = json_encode($args['custom_handsontable_args']);
			}

			return $args;
		}

		/**
		 * Render formulas modal html
		 */
		function render_formulas_form($current_post_type) {
			$nonce = wp_create_nonce('bep-nonce');
			$extension = VGSE()->helpers->get_extension_by_post_type($current_post_type);
			$tutorials_url = 'https://wpsheeteditor.com/blog/?utm_source=' . $current_post_type . '&utm_medium=pro-plugin&utm_campaign=formulas-top-help';
			$tutorials_url .= (!empty($extension['extension_id'])) ? '&vg_tax%5Bplugin%5D=' . (int) $extension['extension_id'] : '';
			?>


			<div class="remodal remodal4 modal-formula" data-remodal-id="modal-formula" data-remodal-options="closeOnOutsideClick: false, hashTracking: false">

				<div class="modal-content">
					<h3><?php _e('Bulk Edit', VGSE()->textname); ?></h3>

					<ul class="vgse-simple-tabs">
						<li><a href="#vgse-create-formula" class="active">Create formula</a></li>
						<li><a href="#ongoing-formulas">Manage ongoing formulas</a></li>
					</ul>
					<form action="<?php
					echo add_query_arg(array(
						'page' => 'vgse_run_formulas',
						'post_type' => $current_post_type,
							), admin_url('admin.php'));
					?>" method="POST" class="vgse-modal-form be-formulas vgse-simple-tab-content active" onsubmit="setFormSubmitting();" id="vgse-create-formula">
						<p class="formula-tool-description"><?php _e('Using this tool you can update thousands of posts at once', VGSE()->textname); ?> <?php if (is_admin() && current_user_can('manage_options')) { ?>
								<a class="help-button" href="<?php echo esc_url($tutorials_url); ?>" target="_blank" ><?php _e('Need help? Check our tutorials', VGSE()->textname); ?></a>
							<?php } ?></p>

						<ul class="unstyled-list">
							<li class="posts-query">
								<p><?php _e('1. Select the rows that you want to update.', VGSE()->textname); ?> 
									<select class="wpse-select-rows-options" required>
										<option value="">--</option>
										<option value="current_search"><?php _e('Edit all the rows from my current search (including non-visible rows).', VGSE()->textname); ?></option>
										<option value="new_search"><?php _e('I want to search rows to update and edit all the search results', VGSE()->textname); ?></option>
										<option value="selected"><?php _e('Edit the rows that I selected manually in the spreadsheet.', VGSE()->textname); ?></option>
									</select>		
									<button class="wpse-formula-post-query button"><?php _e('Make another search', VGSE()->textname); ?></button>
								</p>
								<label class="use-search-query-container">
									<input type="hidden" name="filters">
									<input type="hidden" name="filters_found_rows">
								</label>	 
							</li>
							<li class="multiple-column-selector">
								<label><?php _e('What field do you want to edit?', VGSE()->textname); ?></label>
								<select name="columns[]" required data-placeholder="<?php _e('Select column...', VGSE()->textname); ?>" class="select2" multiple>
									<option></option>
									<?php
									echo implode(apply_filters('vg_sheet_editor/formulas/available_columns_options', VGSE()->helpers->get_post_type_columns_options($current_post_type, array(
														'conditions' => array(
															'supports_formulas' => true
														),
															), false, false)), $current_post_type);
									?>
								</select>
								<?php if (is_admin() && current_user_can('manage_options')) { ?>
									<br/><span class="formula-tool-missing-column-tip"><small><?php _e('A column is missing? <a href="#" data-remodal-target="modal-columns-visibility">Enable it</a>', VGSE()->textname); ?></small></span>
								<?php } ?>
								<div class="column-selector hidden">
									<select name="column" required data-placeholder="<?php _e('Select column...', VGSE()->textname); ?>">
										<option></option>
										<?php
										echo implode(apply_filters('vg_sheet_editor/formulas/available_columns_options', VGSE()->helpers->get_post_type_columns_options($current_post_type, array(
															'conditions' => array(
																'supports_formulas' => true
															),
																), false, false)), $current_post_type);
										?>
									</select>
								</div>
							</li>
							<li class="formula-builder">
							</li>
							<li class="formula-field">
								<label><?php _e('Generated formula:', VGSE()->textname); ?> <a href="#" class="tipso" data-tipso="Formulas available:<br/>=REPLACE(&quot;&quot;Search&quot;&quot;, &quot;&quot;Replace&quot;&quot;) <br/>=MATH( &quot;5 + 6 - $current_value&quot; )">( ? )</a></label>								
								<textarea required class="be-txt-input" name="be-formula" readonly="readonly"></textarea>
							</li>
							<li class="use-slower-execution-field">
								<label><input type="checkbox" value="yes" name="use_slower_execution"><?php _e('Use slower execution method?', VGSE()->textname); ?> <a href="#" class="tipso tipso_style" data-tipso="<?php _e('The default way uses a faster execution method, but it might not work in all the cases. Use this option when the default way doesn´t work or doesn´t update all the posts.', VGSE()->textname); ?>">( ? )</a></label>		
							</li>
							<li class="apply-to-future-posts-field">
								<label><input type="checkbox" value="yes" name="apply_to_future_posts"><?php _e('Execute formula on future posts automatically (Advanced users only)', VGSE()->textname); ?> <a href="#" class="tipso tipso_style" data-tipso="<?php _e('If you mark this option , when you create or update a post in the spreadsheet, we will check if the post matches the formula parameters and execute the formula automatically on that post. For example. When you create a product with category apples we can set the description automatically, or when you change the SKU we can update the downloadable files URLs automatically.', VGSE()->textname); ?>">( ? )</a></label>		
							</li>
							<?php do_action('vg_sheet_editor/formulas/after_form_fields', $current_post_type); ?>
							<li>
								<input type="hidden" value="apply_formula" name="action">		
								<input type="hidden" value="<?php echo $current_post_type; ?>" name="post_type">						
								<input type="hidden" value="<?php echo $nonce; ?>" name="nonce">
								<input type="hidden" value="" name="visibles">
							</li>
							<li class="vgse-formula-actions">
								<button type="submit" class="remodal-confirm submit"><?php
									if (is_admin() && current_user_can('manage_options')) {
										_e('I have a database backup, Execute Now', VGSE()->textname);
									} else {
										_e('Execute Now', VGSE()->textname);
									}
									?></button>								
								<button class="remodal-secundario save-formula"><?php _e('Execute on future posts only', VGSE()->textname); ?></button> 
								<button data-remodal-action="confirm" class="remodal-cancel"><?php _e('Cancel', VGSE()->textname); ?></button>
								<br/>
								<?php if (is_admin() && current_user_can('manage_options')) { ?>
									<div class="alert alert-blue"><?php _e('<p>1- Please backup your database before executing, the changes are not reversible.</p><p>2- Make sure the bulk edit settings are correct before executing.</p>', VGSE()->textname); ?></div>
								<?php } else { ?>
									<div class="alert alert-blue"><?php _e('Careful. The changes are not reversible. Please double check proceeding.', VGSE()->textname); ?></div>
								<?php } ?>
							</li>
						</ul>
					</form>

					<div class="vgse-execute-formula" style="display: none">
						<p class="edit-running"><?php _e('The bulk edit is running. Please dont close this window until the process has finished.', VGSE()->textname); ?></p>
						<?php if (is_admin() && current_user_can('manage_options')) { ?>
							<p class="speed-tip hidden"><?php printf(__('<b>Tip:</b> The formula execution is too slow? <a href="%s" target="_blank">Save <b>more posts</b> per batch</a><br/>Are you getting errors when executing the formula? <a href="%s" target="_blank">Save <b>less posts</b> per batch</a>', VGSE()->textname), VGSE()->helpers->get_settings_page_url(), VGSE()->helpers->get_settings_page_url()); ?></p>
						<?php } ?>

						<p><a href="#" class="button pause-formula-execution button-secondary" data-action="pause"><i class="fa fa-pause"></i> <?php _e('Pause', VGSE()->textname); ?></a> - <button class="button go-back-formula-execution button-secondary" data-action="go-back"><i class="fa fa-angle-left"></i> <?php _e('Go back', VGSE()->textname); ?></button></p>
						<div class="be-response">
							<p><?php _e('Processing...', VGSE()->textname); ?></p>
						</div>
						<button data-remodal-action="confirm" class="remodal-cancel"><?php _e('Close', VGSE()->textname); ?></button>
					</div>
					<div class="vgse-simple-tab-content" id="ongoing-formulas">

						<p><?php _e('Here you can view all the formulas saved for ongoing execution. These formulas will be executed on posts matching the filters when they are created or updated in the spreadsheet.', VGSE()->textname); ?></p>
						<p><?php _e('Note. If you want to modify saved formulas you have to delete the formula and create it again in the formulas builder.', VGSE()->textname); ?></p>
						<?php
						$saved_formulas = get_option(vgse_formulas_init()->future_posts_formula_key, array());
						if (empty($saved_formulas)) {
							?>
							<p><?php _e('You haven´t saved formulas yet.', VGSE()->textname); ?></p>
							<?php
						} else {

							$spreadsheet_columns = VGSE()->helpers->get_provider_columns($current_post_type);
							?>

							<ul>
								<?php
								foreach ($saved_formulas as $formula_index => $formula) {
									if ($formula['post_type'] !== $current_post_type || empty($formula['raw_form_data']['action_name'])) {
										continue;
									}
									$column = (isset($spreadsheet_columns[$formula['column']])) ? $spreadsheet_columns[$formula['column']] : null;
									if (empty($column)) {
										continue;
									}
									?>
									<li>
										<button class="delete-saved-formula button" data-formula-index="<?php echo $formula_index; ?>">x</button>
										<b><?php _e('Field to update:', VGSE()->textname); ?></b> <?php echo $column['title']; ?>.<br/>
										<b><?php _e('Formula type:', VGSE()->textname); ?></b> <?php echo $formula['raw_form_data']['action_name']; ?>.<br/>
										<b><?php _e('Formula parameters:', VGSE()->textname); ?></b> <?php _e('Parameter:', VGSE()->textname); ?> <?php echo implode('. Parameter: ', $formula['raw_form_data']['formula_data']); ?>.<br/>
										<b><?php _e('Apply to:', VGSE()->textname); ?></b> <?php
										if (empty($formula['raw_form_data']['apply_to']) || (is_array($formula['raw_form_data']['apply_to']) && $formula['raw_form_data']['apply_to'][0] === 'all')) {
											_e('All', VGSE()->textname);
										} else {

											$taxonomy_labels = array();
											foreach ($formula['raw_form_data']['apply_to'] as $group) {
												$group_parts = explode('--', $group);
												$taxonomy_key = $group_parts[0];
												$term = get_term_by('slug', $group_parts[1], $taxonomy_key);

												if (!isset($taxonomy_labels[$taxonomy_key])) {
													$taxonomy = get_taxonomy($taxonomy_key);
													$taxonomy_labels[$taxonomy_key] = $taxonomy->label;
												}

												echo $term->name . ' (' . $taxonomy_labels[$taxonomy_key] . ')';
											}
										}
										?>.<br/>

										<input type="hidden" class="formula-full-settings" value="<?php
											   unset($formula['nonce']);
											   echo esc_attr(json_encode($formula));
											   ?>" />
									</li>
									<?php
								}
								?>

							</ul>
							<?php
						}
						?>
					</div>
				</div>
			</div>
			<?php
		}

		/**
		 * Register toolbar item
		 */
		function register_toolbar_items($editor) {

			$post_types = $editor->args['enabled_post_types'];
			foreach ($post_types as $post_type) {
				$editor->args['toolbars']->register_item('run_formula', array(
					'type' => 'button',
					'allow_in_frontend' => true,
					'help_tooltip' => __('Edit thousands of rows at once in seconds', VGSE()->textname),
					'content' => __('Bulk Edit', VGSE()->textname),
					'icon' => 'fa fa-terminal',
					'extra_html_attributes' => 'data-remodal-target="modal-formula"',
					'footer_callback' => array($this, 'render_formulas_form')
						), $post_type);
			}
		}

		/**
		 * Register frontend assets
		 */
		function register_assets() {
			$current_post = VGSE()->helpers->get_provider_from_query_string();
			wp_enqueue_style('formulas_css', vgse_formulas_init()->plugin_url . 'assets/css/styles.css', '', VGSE()->version, 'all');
			wp_enqueue_script('formulas_js', vgse_formulas_init()->plugin_url . 'assets/js/init.js', array(), VGSE()->version, false);
			wp_localize_script('formulas_js', 'vgse_formulas_data', apply_filters('vg_sheet_editor/formulas/form_settings', array(
				'texts' => array(
					'formula_required' => __('The bulk edit is missing important information, please fill the form.', VGSE()->textname),
					'action_select_label' => __('Select type of edit', VGSE()->textname),
					'action_select_placeholder' => __('- -', VGSE()->textname),
					'wrong_formula' => __('Something is wrong in the bulk edit settings. Please double check or contact us.', VGSE()->textname),
				),
				'default_actions' =>
				array(
					'math' =>
					array(
						'label' => __('Math operation', VGSE()->textname),
						'description' => __('Update existing value with the result of a math operation.<br>The result is rounded to the 2 nearest decimals. I.e. 3.845602 becomes 3.85', VGSE()->textname),
						'fields_relationship' => 'AND',
						'jsCallback' => 'vgseGenerateMathFormula',
						'allowed_column_keys' => null,
						'input_fields' =>
						array(
							array(
								'tag' => 'input',
								'html_attrs' => array(
									'type' => 'text',
								),
								'label' => __('Math formula', VGSE()->textname),
								'description' => __('Example 1: $current_value$ + 2 * 5. <br/>Example 2: $_regular_price$ * 0.7 (Set regular price - 30%)', VGSE()->textname),
							),
						),
					),
					'decrease_by_percentage' =>
					array(
						'label' => __('Decrease by percentage', VGSE()->textname),
						'description' => __('Decrease the existing value by a percentage.<br>The result is rounded to the 2 nearest decimals. I.e. 3.845602 becomes 3.85', VGSE()->textname),
						'fields_relationship' => 'AND',
						'jsCallback' => 'vgseGenerateDecreasePercentageFormula',
						'allowed_column_keys' => null,
						'input_fields' =>
						array(
							array(
								'tag' => 'input',
								'html_attrs' => array(
									'type' => 'number',
								),
								'label' => __('Decrease by', VGSE()->textname),
								'description' => __('Enter the percentage number.', VGSE()->textname),
							),
						),
					),
					'decrease_by_number' =>
					array(
						'label' => __('Decrease by number', VGSE()->textname),
						'description' => __('Decrease the existing value by a number.<br>The result is rounded to the 2 nearest decimals. I.e. 3.845602 becomes 3.85', VGSE()->textname),
						'fields_relationship' => 'AND',
						'jsCallback' => 'vgseGenerateDecreaseFormula',
						'allowed_column_keys' => null,
						'input_fields' =>
						array(
							array(
								'tag' => 'input',
								'html_attrs' => array(
									'type' => 'number',
								),
								'label' => __('Decrease by', VGSE()->textname),
								'description' => __('Enter the number.', VGSE()->textname),
							),
						),
					),
					'increase_by_percentage' =>
					array(
						'label' => __('Increase by percentage', VGSE()->textname),
						'description' => __('Increase the existing value by a percentage.<br>The result is rounded to the 2 nearest decimals. I.e. 3.845602 becomes 3.85', VGSE()->textname),
						'fields_relationship' => 'AND',
						'jsCallback' => 'vgseGenerateIncreasePercentageFormula',
						'allowed_column_keys' => null,
						'input_fields' =>
						array(
							array(
								'tag' => 'input',
								'html_attrs' => array(
									'type' => 'number',
								),
								'label' => __('Increase by', VGSE()->textname),
								'description' => __('Enter the percentage number.', VGSE()->textname),
							),
						),
					),
					'increase_by_number' =>
					array(
						'label' => __('Increase by number', VGSE()->textname),
						'description' => __('Increase the existing value by a number.<br>The result is rounded to the 2 nearest decimals. I.e. 3.845602 becomes 3.85', VGSE()->textname),
						'fields_relationship' => 'AND',
						'jsCallback' => 'vgseGenerateIncreaseFormula',
						'allowed_column_keys' => null,
						'input_fields' =>
						array(
							array(
								'tag' => 'input',
								'html_attrs' => array(
									'type' => 'number',
								),
								'label' => __('Increase by', VGSE()->textname),
								'description' => __('Enter the number.', VGSE()->textname),
							),
						),
					),
					'set_value' =>
					array(
						'label' => __('Set value', VGSE()->textname),
						'description' => sprintf(__('Replace existing value with this value. <a class="formulas-action-tip-link" href="%s" target="_blank">Read more</a>', VGSE()->textname), vgse_formulas_init()->documentation_url),
						'fields_relationship' => 'AND',
						'jsCallback' => 'vgseGenerateSetValueFormula',
						'allowed_column_keys' => null,
						'input_fields' =>
						array(
							array(
								'tag' => 'textarea',
							),
						),
					),
					'replace' =>
					array(
						'label' => __('Replace', VGSE()->textname),
						'description' => sprintf(__('Replace a word, phrase, or number with a new value. <a class="formulas-action-tip-link" href="%s" target="_blank">Read more</a>', VGSE()->textname), vgse_formulas_init()->documentation_url),
						'fields_relationship' => 'AND',
						'jsCallback' => 'vgseGenerateReplaceFormula',
						'allowed_column_keys' => null,
						'input_fields' =>
						array(
							array(
								'tag' => 'textarea',
								'label' => __('Replace this', VGSE()->textname),
							),
							array(
								'tag' => 'textarea',
								'label' => __('With this', VGSE()->textname),
							),
						),
					),
					'capitalize_words' =>
					array(
						'label' => __('Capitalize words', VGSE()->textname),
						'description' => sprintf(__('Capitalize the first letter of every word in the field. I.e. convert "my title" into "My Title".', VGSE()->textname), vgse_formulas_init()->documentation_url),
						'fields_relationship' => 'AND',
						'jsCallback' => 'vgseGenerateCapitalizeWordsFormula',
						'allowed_column_keys' => null,
						'input_fields' =>
						array(
							array(
								'tag' => 'input',
								'html_attrs' => array(
									// We hide the input because we dont need user input
									// and the JS requires at least one html input to generate the formula
									'style' => 'display: none;',
								),
							),
						),
					),
					'clear_value' =>
					array(
						'label' => __('Clear value', VGSE()->textname),
						'description' => sprintf(__('Remove the existing value and leave the field empty. <a class="formulas-action-tip-link" href="%s" target="_blank">Read more</a>', VGSE()->textname), vgse_formulas_init()->documentation_url),
						'fields_relationship' => 'AND',
						'jsCallback' => 'vgseGenerateClearValueFormula',
						'allowed_column_keys' => null,
						'input_fields' =>
						array(
							array(
								'tag' => 'input',
								'html_attrs' => array(
									// We hide the input because we dont need user input
									// and the JS requires at least one html input to generate the formula
									'style' => 'display: none;',
								),
							),
						),
					),
					'remove_duplicates' =>
					array(
						'label' => __('Remove duplicates', VGSE()->textname),
						'description' => sprintf(__('Delete all the items containing duplicate values in the column, we only leave one item per unique value. <a class="formulas-action-tip-link" href="%s" target="_blank">Read more</a>', VGSE()->textname), 'https://wpsheeteditor.com/blog/?vg_tax%5Bfeature%5D=44'),
						'fields_relationship' => 'AND',
						'jsCallback' => 'vgseGenerateClearValueFormula',
						'allowed_column_keys' => array('post_title', 'post_author', 'post_content', 'post_date', 'post_excerpt', '_sku'),
						'input_fields' =>
						array(
							array(
								'tag' => 'input',
								'html_attrs' => array(
									// We hide the input because we dont need user input
									// and the JS requires at least one html input to generate the formula
									'style' => 'display: none;',
								),
							),
						),
					),
					'append' =>
					array(
						'label' => __('Append', VGSE()->textname),
						'fields_relationship' => 'AND',
						'jsCallback' => 'vgseGenerateAppendFormula',
						'allowed_column_keys' => null,
						'input_fields' =>
						array(
							array(
								'tag' => 'input',
								'html_attrs' => array(
									'type' => 'text',
								),
								'label' => __('Enter the value to append to the existing value.', VGSE()->textname),
							),
						),
					),
					'prepend' =>
					array(
						'label' => __('Prepend', VGSE()->textname),
						'fields_relationship' => 'AND',
						'jsCallback' => 'vgseGeneratePrependFormula',
						'allowed_column_keys' => null,
						'input_fields' =>
						array(
							array(
								'tag' => 'input',
								'html_attrs' => array(
									'type' => 'text',
								),
								'label' => __('Enter the value to prepend to the existing value.', VGSE()->textname),
							),
						),
					),
					'custom' =>
					array(
						'label' => __('Custom formula', VGSE()->textname),
						'fields_relationship' => 'AND',
						'jsCallback' => 'vgseGenerateCustomFormula',
						'allowed_column_keys' => null,
						'input_fields' =>
						array(
							array(
								'tag' => 'input',
								'html_attrs' => array(
									'type' => 'text',
								),
								'label' => sprintf(__('Only for advanced users. <a class="formulas-action-tip-link" href="%s" target="_blank">Read more.</a>', VGSE()->textname), vgse_formulas_init()->documentation_url),
							),
						),
					),
					'merge_columns' =>
					array(
						'label' => __('Copy from other columns', VGSE()->textname),
						'fields_relationship' => 'OR',
						'jsCallback' => 'vgseGenerateMergeFormula',
						'allowed_column_keys' => null,
						'description' => __('Copy the value of other fields into this field.<br/>Example, copy "sale price" into the "regular price" field.', VGSE()->textname),
						'input_fields' =>
						array(
							array(
								'tag' => 'select',
								'html_attrs' =>
								array(
									'multiple' => false,
									'class' => 'select2'
								),
								'options' => '<option value="">(none)</option>' . VGSE()->helpers->get_post_type_columns_options($current_post, array(
									'conditions' => array(
										'supports_formulas' => true
									),
										), true),
								'label' => __('Copy from this column', VGSE()->textname),
							),
							array(
								'tag' => 'textarea',
								'label' => __('Copy from multiple columns', VGSE()->textname),
								'description' => __("Example: 'Articles written by \$post_author\$ on \$post_date\$' = 'Articles written by Adam on 24-12-2017'.<br/>Another example: '\$category\$-\$_regular_price\$ EUR' would be 'Videos - 25 EUR'", VGSE()->textname),
							),
						),
					),
				),
				'columns_actions' =>
				array(
					'text' => array(
						"set_value" => 'default',
						"replace" => 'default',
						'clear_value' => 'default',
						'remove_duplicates' => 'default',
						"append" => 'default',
						"prepend" => 'default',
						"capitalize_words" => 'default',
						"merge_columns" => 'default',
						'custom' => 'default',
					),
					'boton_tiny' => array(
						"set_value" => 'default',
						"replace" => 'default',
						'clear_value' => 'default',
						"append" => 'default',
						"prepend" => 'default',
						"capitalize_words" => 'default',
						"merge_columns" => 'default',
						'custom' => 'default',
					),
					'boton_gallery_multiple' =>
					array(
						'set_value' =>
						array(
							'description' => __('We will replace the existing media file(s) with these file(s).', VGSE()->textname),
							'fields_relationship' => 'OR',
							'input_fields' =>
							array(
								array(
									'tag' => 'a',
									'html_attrs' =>
									array(
										'data-multiple' => true,
										'class' => 'wp-media button'
									),
									'label' => __('Upload the files', VGSE()->textname),
								),
								array(
									'tag' => 'input',
									'html_attrs' => array(
										'type' => 'url',
									),
									'label' => __('File URLs', VGSE()->textname),
									'description' => __('Enter the URLs separated by commas. They can be from your own site.', VGSE()->textname),
								),
							),
						),
						'prepend' =>
						array(
							'description' => __('We will prepend the new file(s) to the existing media file(s).', VGSE()->textname),
							'fields_relationship' => 'OR',
							'input_fields' =>
							array(
								array(
									'tag' => 'a',
									'html_attrs' =>
									array(
										'data-multiple' => true,
										'class' => 'wp-media button'
									),
									'label' => __('Upload the files', VGSE()->textname),
								),
								array(
									'tag' => 'input',
									'html_attrs' => array(
										'type' => 'url',
									),
									'label' => __('File URLs', VGSE()->textname),
									'description' => __('Enter the URLs separated by commas. They can be from your own site.', VGSE()->textname),
								),
							),
						),
						'append' =>
						array(
							'description' => __('We will append the new file(s) to the existing media file(s).', VGSE()->textname),
							'fields_relationship' => 'OR',
							'input_fields' =>
							array(
								array(
									'tag' => 'a',
									'html_attrs' =>
									array(
										'data-multiple' => true,
										'class' => 'wp-media button'
									),
									'label' => __('Upload the files', VGSE()->textname),
								),
								array(
									'tag' => 'input',
									'html_attrs' => array(
										'type' => 'url',
									),
									'label' => __('File URLs', VGSE()->textname),
									'description' => __('Enter the URLs separated by commas. They can be from your own site.', VGSE()->textname),
								),
							),
						),
						'replace' =>
						array(
							'description' => __('Replace a media file with other file', VGSE()->textname),
							'fields_relationship' => 'AND',
							'input_fields' =>
							array(
								array(
									'tag' => 'input',
									'html_attrs' => array(
										'type' => 'url',
									),
									'label' => __('Replace these files', VGSE()->textname),
									'description' => __('Enter the URLs separated by commas. They must be from your own site.', VGSE()->textname),
								),
								array(
									'tag' => 'input',
									'html_attrs' => array(
										'type' => 'url',
									),
									'label' => __('With these files', VGSE()->textname),
									'description' => __('Enter the URLs separated by commas. They must be from your own site.', VGSE()->textname),
								),
							),
						),
						'clear_value' => 'default',
						'custom' => 'default',
					),
					'boton_gallery' =>
					array(
						'set_value' =>
						array(
							'description' => __('We will replace the existing media file with this file.', VGSE()->textname),
							'fields_relationship' => 'OR',
							'input_fields' =>
							array(
								array(
									'tag' => 'a',
									'html_attrs' =>
									array(
										'data-multiple' => false,
										'class' => 'wp-media button'
									),
									'label' => __('Upload the file', VGSE()->textname),
								),
								array(
									'tag' => 'input',
									'html_attrs' => array(
										'type' => 'text', // we don't use type=url to allow saving using filenames too
									),
									'label' => __('File URL', VGSE()->textname),
									'description' => __('Enter the URL. It can be an URL from your own site (Example http://site.com/wp-content/uploads/2016/01/file.jpg) or an external URL.', VGSE()->textname),
								),
							),
						),
						'replace' =>
						array(
							'label' => __('Replace', VGSE()->textname),
							'description' => __('Replace a media file with other file', VGSE()->textname),
							'fields_relationship' => 'AND',
							'input_fields' =>
							array(
								array(
									'tag' => 'input',
									'html_attrs' => array(
										'type' => 'url',
									),
									'label' => __('Replace this file', VGSE()->textname),
									'description' => __('Enter the URL. It must be an URL from your own site. Example: http://site.com/wp-content/uploads/2016/01/file.jpg', VGSE()->textname),
								),
								array(
									'tag' => 'input',
									'html_attrs' => array(
										'type' => 'url',
									),
									'label' => __('With this file', VGSE()->textname),
									'description' => __('Enter the URL. It must be an URL from your own site. Example: http://site.com/wp-content/uploads/2016/01/file.jpg', VGSE()->textname),
								),
							),
						),
						'clear_value' => 'default',
						'custom' => 'default',
					),
					'number' =>
					array(
						'set_value' =>
						array(
							'input_fields' =>
							array(
								array(
									'tag' => 'input',
									'html_attrs' => array(
										'type' => 'number',
										'step' => '0.01'
									),
								),
							),
						),
						'clear_value' => 'default',
						'increase_by_number' => 'default',
						'increase_by_percentage' => 'default',
						'decrease_by_number' => 'default',
						'decrease_by_percentage' => 'default',
						'math' => 'default',
						"merge_columns" => 'default',
						'custom' => 'default',
					),
					'post_terms' =>
					array(
						"merge_columns" => 'default',
						'set_value' =>
						array(
							'description' => __('We will replace the existing terms with these terms.', VGSE()->textname),
							'input_fields' =>
							array(
								array(
									'tag' => 'select',
									'html_attrs' =>
									array(
										'multiple' => true,
										'data-remote' => true,
										'data-action' => 'vgse_search_taxonomy_terms',
										'data-output-format' => '%name%',
										'data-min-input-length' => 4,
										'data-placeholder' => 'Search value by name...',
										'class' => 'select2'
									),
									'label' => __('Value', VGSE()->textname),
									'description' => __('Enter the new value.', VGSE()->textname),
								),
							),
						),
						'replace' =>
						array(
							'description' => sprintf(__('Replace some term(s) with new term(s). <a class="formulas-action-tip-link" href="%s" target="_blank">Read more</a>', VGSE()->textname), vgse_formulas_init()->documentation_url),
							'fields_relationship' => 'AND',
							'input_fields' =>
							array(
								array(
									'tag' => 'select',
									'label' => __('Replace this', VGSE()->textname),
									'html_attrs' =>
									array(
										'multiple' => true,
										'data-remote' => true,
										'data-action' => 'vgse_search_taxonomy_terms',
										'data-output-format' => '%name%',
										'data-min-input-length' => 4,
										'data-placeholder' => 'Search value by name...',
										'class' => 'select2'
									),
								),
								array(
									'tag' => 'select',
									'label' => __('With this', VGSE()->textname),
									'html_attrs' =>
									array(
										'multiple' => true,
										'data-remote' => true,
										'data-output-format' => '%name%',
										'data-action' => 'vgse_search_taxonomy_terms',
										'data-min-input-length' => 4,
										'data-placeholder' => 'Search value by name...',
										'class' => 'select2'
									),
								),
							),
						),
						'append' =>
						array(
							'input_fields' =>
							array(
								array(
									'tag' => 'select',
									'html_attrs' =>
									array(
										'multiple' => true,
										'data-remote' => true,
										'data-action' => 'vgse_search_taxonomy_terms',
										'data-output-format' => '%name%',
										'data-min-input-length' => 4,
										'data-placeholder' => 'Search value by name...',
										'class' => 'select2'
									),
									'label' => __('Terms', VGSE()->textname),
									'description' => __('Enter the term(s) to append to the existing term(s).', VGSE()->textname),
								),
							),
						),
						'clear_value' => 'default',
						"capitalize_words" => 'default',
						'custom' => 'default',
					),
				),
							), $current_post));
		}

		/**
		 * Creates or returns an instance of this class.
		 */
		static function get_instance() {
			if (null == WPSE_Formulas_UI::$instance) {
				WPSE_Formulas_UI::$instance = new WPSE_Formulas_UI();
				WPSE_Formulas_UI::$instance->init();
			}
			return WPSE_Formulas_UI::$instance;
		}

		function __set($name, $value) {
			$this->$name = $value;
		}

		function __get($name) {
			return $this->$name;
		}

	}

}

if (!function_exists('WPSE_Formulas_UI_Obj')) {

	function WPSE_Formulas_UI_Obj() {
		return WPSE_Formulas_UI::get_instance();
	}

}
WPSE_Formulas_UI_Obj();
