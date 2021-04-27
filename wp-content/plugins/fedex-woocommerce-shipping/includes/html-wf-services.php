<tr valign="top" id="service_options" class="fedex_rates_tab">
	<td class="titledesc" colspan="2" style="padding-left:0px">
	<strong><?php _e( 'Services', 'wf-shipping-fedex' ); ?></strong><br><br>
		<table class="fedex_services widefat">
			<thead>
				<th class="sort">&nbsp;</th>
				<th><?php _e( 'Service Code', 'wf-shipping-fedex' ); ?></th>
				<th><?php _e( 'Name', 'wf-shipping-fedex' ); ?></th>
				<th>
					<label for="fedexSelectAll" style="width: 100px;
    height: 30px;">
						<input type="checkbox" id="fedexSelectAll" style="float: left; margin: 3px 10px 0 -2px;"/><?php _e( 'Enabled', 'wf-shipping-fedex' ); ?>
					</label>
				</th>
				<th><?php echo sprintf( __( 'Price Adjustment (%s)', 'wf-shipping-fedex' ), get_woocommerce_currency_symbol() ); ?></th>
				<th><?php _e( 'Price Adjustment (%)', 'wf-shipping-fedex' ); ?></th>
			</thead>
			<tbody>
				<?php
					$sort = 0;
					$this->ordered_services = array();

					foreach ( $this->services as $code => $name ) {

						if ( isset( $this->custom_services[ $code ]['order'] ) ) {
							$sort = $this->custom_services[ $code ]['order'];
						}

						while ( isset( $this->ordered_services[ $sort ] ) )
							$sort++;

						$this->ordered_services[ $sort ] = array( $code, $name );

						$sort++;
					}

					ksort( $this->ordered_services );

					foreach ( $this->ordered_services as $value ) {
						$code = $value[0];
						$name = $value[1];
						?>
						<tr>
							<td class="sort"><input type="hidden" class="order" name="fedex_service[<?php echo $code; ?>][order]" value="<?php echo isset( $this->custom_services[ $code ]['order'] ) ? $this->custom_services[ $code ]['order'] : ''; ?>" /></td>
							<td><strong><?php echo $code; ?></strong></td>
							<td><input type="text" name="fedex_service[<?php echo $code; ?>][name]" placeholder="<?php echo $name; ?>" value="<?php echo isset( $this->custom_services[ $code ]['name'] ) ? $this->custom_services[ $code ]['name'] : ''; ?>" size="35" /></td>
							<td><input type="checkbox" name="fedex_service[<?php echo $code; ?>][enabled]" class="checkBoxClass" <?php checked( ( ! isset( $this->custom_services[ $code ]['enabled'] ) || ! empty( $this->custom_services[ $code ]['enabled'] ) ), true ); ?> size="3" /></td>
							<td><input type="number" step="any" style="padding-left: 6px;padding-right: 2px; width: 168px"  name="fedex_service[<?php echo $code; ?>][adjustment]" placeholder="0" value="<?php echo isset( $this->custom_services[ $code ]['adjustment'] ) ? $this->custom_services[ $code ]['adjustment'] : ''; ?>"/></td>
							<td><input type="number" step="any" style="padding-left: 6px;padding-right: 2px; width: 168px; margin-right:5px; "  name="fedex_service[<?php echo $code; ?>][adjustment_percent]" placeholder="0"  value="<?php echo isset( $this->custom_services[ $code ]['adjustment_percent'] ) ? $this->custom_services[ $code ]['adjustment_percent'] : ''; ?>"/></td>
						</tr>
						<?php
					}
				?>
			</tbody>
		</table>
		<script type="text/javascript">	

			jQuery(document).ready(function () {

				if (jQuery('.checkBoxClass:checked').length == jQuery('.checkBoxClass').length) {		
					jQuery("#fedexSelectAll").prop("checked",true);
				}
				jQuery("#fedexSelectAll").click(function () {
					jQuery(".checkBoxClass").prop('checked', jQuery(this).prop('checked'));
				});

				jQuery(".checkBoxClass").change(function(){
					if (!jQuery(this).prop("checked")){
						jQuery("#fedexSelectAll").prop("checked",false);
					}
					if (jQuery('.checkBoxClass:checked').length == jQuery('.checkBoxClass').length) {
						jQuery("#fedexSelectAll").prop("checked",true);
					}
				});
			});

		</script>
	</td>
</tr>