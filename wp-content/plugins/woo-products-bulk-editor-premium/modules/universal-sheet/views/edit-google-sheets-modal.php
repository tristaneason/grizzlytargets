<div class="remodal edit-google-sheets-modal" data-remodal-id="edit-google-sheets-modal">

	<div class="modal-content">
		<h3><?php _e('Edit in Google Sheets', VGSE()->textname); ?></h3>
		<p class="full-setup"><?php _e('Note. This works in Google Chrome only.', VGSE()->textname); ?></p>
		<ol class="full-setup">
			<?php if( !wpse_init_rest_api()->is_jwt_setup() && $is_apache){ ?>
			<li><?php _e('Add this code at the beginning of the .htaccess file in the root directory of your website. It is required for authenticating requests between Google Sheets and WordPress', VGSE()->textname); ?> <code>
						RewriteEngine on
						RewriteCond %{HTTP:Authorization} ^(.*)
						RewriteRule ^(.*) - [E=HTTP_AUTHORIZATION:%1]</code></li>
			<?php } ?>
			<li><?php printf(__('Install the "WP Sheet Editor" chrome extension. <a href="%s" target="_blank">Install</a>', VGSE()->textname), $this->chrome_extension_url); ?></li>
			<li><?php printf(__('Open Google Sheets. <a href="%s" target="_blank" class="wpse-quick-access-link">Click here</a>', VGSE()->textname), 'http://sheets.new'); ?></li>
			<li><?php _e('In Google Sheets > top toolbar. Open the "WordPress" option.', VGSE()->textname); ?></li>
			<li><?php printf(__('Copy this access link and paste it in Google Sheets. <a href="%s"  class="wpse-quick-access-link">Get quick access link</a>', VGSE()->textname), $this->chrome_extension_url); ?>
				<br><input readonly onFocus="this.select()" style="width: 100%;" class="access-link-visible">
				<small class="access-link-visible"><?php _e('You must use it privately for security reasons, this link expires after one usage.', VGSE()->textname); ?></small>
			</li>
		</ol>
		<img src='https://via.placeholder.com/500x300' />
		<br>
		<button data-remodal-action="confirm" class="remodal-cancel"><?php _e('Close', VGSE()->textname); ?></button>
	</div>								
</div>