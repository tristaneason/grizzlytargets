
<!--Save changes modal-->
<div class="remodal create-variation-modal" data-remodal-id="create-variation-modal" data-remodal-options="closeOnOutsideClick: false, hashTracking: false">

	<div class="modal-content">
		<form class="create-variation-form vgse-modal-form " action="<?php echo admin_url('admin-ajax.php'); ?>" method="POST">
			<h3><?php _e('Variations Manager', VGSE()->textname); ?></h3>
			<div class="vgse-variations-tool-selectors">
				<button class="button vgse-variations-tool-selector" data-target=".vgse-create-variations"><?php _e('Create variations', VGSE()->textname); ?></button> - 
				<button class="button vgse-variations-tool-selector" data-target=".vgse-copy-variations"><?php _e('Copy variations', VGSE()->textname); ?></button>
			</div>
			<div class="vgse-variations-tool vgse-copy-variations">
				<input type="hidden" name="vgse_variation_tool" value="copy">
				<h3><?php _e('Copy variations', VGSE()->textname); ?> 
					<small><a href="https://wpsheeteditor.com/woocommerce-copy-variations-product/?utm_source=product&utm_medium=pro-plugin&utm_campaign=copy-variations-help" target="_blank"><?php _e('Tutorial', VGSE()->textname); ?></a></small>
				</h3>
				<ul class="unstyled-list">
					<li>
						<label><?php _e('Copy variations and attributes from this product:', VGSE()->textname); ?> </label>
						<select name="copy_from_product" data-remote="true" data-min-input-length="4" data-action="vgse_find_post_by_name" data-post-type="<?php echo $post_type; ?>" data-nonce="<?php echo $nonce; ?>" data-placeholder="<?php _e('Select product...', VGSE()->textname); ?>" class="select2 vgse-copy-variation-from-product">
							<option></option>
						</select>
					</li>
					<li class="individual-variations-wrapper">
						<label>
							<input type="checkbox" name="copy_individual_variations"> <?php _e('I don\'t want to copy all the variations', VGSE()->textname); ?> <a href="#" class="tipso tipso_style" data-tipso="<?php _e('By default we will copy all the attributes and all the variations and replace the existing variations. You can activate this option to copy individual variations and append them to the existing variations in the target product.', VGSE()->textname); ?>">( ? )</a>
						</label>
						<br>
						<div class="individual-variations-selector-wrapper">
							<label><?php _e('Which variations do you want to copy?', VGSE()->textname); ?> </label>
							<br>
							<select name="individual_variations[]" multiple class="select2 individual-variations-select">
								<option value="">--</option>
							</select>
						</div>
					</li>
					<li>
						<label><?php _e('The variations are for these products: ', VGSE()->textname); ?>  <a href="#" class="tipso tipso_style" data-tipso="<?php _e('Copy the variations into these products.', VGSE()->textname); ?>">( ? )</a></label>
						<select name="vgse_variation_manager_source">
							<option value="">- -</option>
							<option value="individual">Select individual products</option>
							<option value="search">Select multiple products</option>
						</select>
						<label class="use-search-query-container"><input type="checkbox" value="yes"  name="use_search_query"><?php _e('I understand it will update the posts from my search.', VGSE()->textname); ?> <a href="#" class="tipso tipso_style" data-tipso="<?php _e('For example, if you searched for posts by author = Mark using the search tool, we will update only posts with author Mark', VGSE()->textname); ?>">( ? )</a><input type="hidden" name="filters"></label>

						<select name="<?php echo $this->post_type; ?>[]" data-remote="true" data-min-input-length="4" data-action="vgse_find_post_by_name" data-post-type="<?php echo $post_type; ?>" data-nonce="<?php echo $nonce; ?>"  data-placeholder="<?php _e('Select product...', VGSE()->textname); ?> " class="select2 individual-product-selector" multiple>
							<option></option>
						</select>
					</li>
					<li>
						<label><input type="checkbox" class="show-advanced-options"> <?php _e('Show advanced options ', VGSE()->textname); ?></label>
						<div class="advanced-options">
							<label>
								<input type="checkbox" name="use_parent_product_price"> <?php _e('Use prices from simple product (parent) on the variations', VGSE()->textname); ?> <a href="#" class="tipso tipso_style" data-tipso="<?php _e('You can convert simple products into variable products. You can copy variations into a simple product and keep the prices from the simple product on the variations.', VGSE()->textname); ?>">( ? )</a>
							</label>
							<br>
							<label>
								<input type="checkbox" name="ignore_variation_image"> <?php _e('Do not copy the variation images?', VGSE()->textname); ?> <a href="#" class="tipso tipso_style" data-tipso="<?php _e('Enable this option if you want to copy all the variation data, except the variation image.', VGSE()->textname); ?>">( ? )</a>
							</label> 
						</div>
					</li>								
				</ul>
				<div class="response">
				</div>
			</div>
			<div class="vgse-variations-tool vgse-create-variations">
				<input type="hidden" name="vgse_variation_tool" value="create">
				<h3><?php _e('Create variations', VGSE()->textname); ?> 							
					<small><a href="https://wpsheeteditor.com/woocommerce-how-to-create-product-variations-faster/?utm_source=product&utm_medium=pro-plugin&utm_campaign=create-variations-help" target="_blank"><?php _e('Tutorial', VGSE()->textname); ?></a></small>
				</h3>
				<ul class="unstyled-list">
					<li>
						<label><?php _e('The variations are for these products:', VGSE()->textname); ?> </label>
						<select name="<?php echo $this->post_type; ?>[]" data-remote="true" data-min-input-length="4" data-action="vgse_find_post_by_name" data-post-type="<?php echo $post_type; ?>" data-nonce="<?php echo $nonce; ?>"  data-placeholder="<?php _e('Select product...', VGSE()->textname); ?> " class="select2 individual-product-selector" multiple>
							<option></option>
						</select>
					</li>
					<li>
						<label>
							<input type="hidden" name="link_attributes" value="no" />
							<input type="checkbox" class="link-variations-attributes" name="link_attributes" /><?php _e('Create variations for every combination of attributes?', VGSE()->textname); ?></label>								
					</li>
					<li>
						<label><?php _e('Create this number of variations', VGSE()->textname); ?> <input type="number" class="link-variations-number" name="number" /></label>								
					</li>
				</ul>
			</div>

			<input type="hidden" value="vgse_create_variations" name="action">
			<input type="hidden" value=" <?php echo $nonce; ?>" name="nonce">
			<input type="hidden" value="<?php echo $post_type; ?>" name="post_type">
			<br>
			<button class="remodal-confirm" type="submit"><?php _e('Execute', VGSE()->textname); ?> </button>
			<button data-remodal-action="confirm" class="remodal-cancel"><?php _e('Close', VGSE()->textname); ?></button>
		</form>
	</div>
</div>