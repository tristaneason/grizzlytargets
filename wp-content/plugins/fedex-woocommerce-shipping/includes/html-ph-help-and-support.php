<style>

	.ph_fedex_important_links {
		margin-left: 45px !important;
		list-style-type: square !important;
	}

	.ph_fedex_important_links li {
		margin-bottom: 10px !important;
	}

	.ph_fedex_submit_ticket {
		background: #1e3368 !important;
		border-color: #1e3368 !important;
		color: white !important;
	}

	.ph_fedex_submit_ticket:hover {
		background: #15254f !important;
		border-color: #15254f !important;
		color: white !important;
	}

	.required_field {
		border: 1px solid red !important;
	}

	.ph_fedex_help_table {
		margin-top: 15px !important;
	}
	.ph_fedex_help_table tr td {
		padding: 0px !important;
	}

	.ph_fedex_ticket_number_error {

		color: red;
		font-size: small;
		padding: 10px !important;
		vertical-align: sub;
		display: none;
	}

	.ph_fedex_consent_error {
		color: red;
		font-size: small;
		vertical-align: sub;
		display: none;
	}
</style>

<tr valign="top" class="fedex_help_and_support_tab">
	<td class="titledesc" colspan="2" style="padding:0px">

		<h3>Important Links</h3>

		<ul class="ph_fedex_important_links">
			<li>
				How to Set Up WooCommerce FedEx Shipping Plugin?&nbsp;&nbsp;&nbsp;
				<a href="https://www.pluginhive.com/knowledge-base/setting-woocommerce-fedex-shipping-plugin/" target="_blank"><b>Read More</b></a>
			</li>
			<li>
				How to Troubleshoot WooCommerce FedEx Shipping plugin?&nbsp;&nbsp;&nbsp;
				<a href="https://www.pluginhive.com/knowledge-base/troubleshooting-woocommerce-fedex-shipping-plugin/" target="_blank"><b>Read More</b></a>
			</li>
			<li>
				Parcel Packing Methods used in FedEx Shipping plugin&nbsp;&nbsp;&nbsp;
				<a href="https://www.pluginhive.com/knowledge-base/fedex-woocommerce-shipping-plugin-pack-items-boxes/" target="_blank"><b>Read More</b></a>
			</li>
			<li>
				How to print FedEx Shipping Labels in bulk?&nbsp;&nbsp;&nbsp;
				<a href="https://www.pluginhive.com/knowledge-base/generating-bulk-shipping-labels-made-easy-woocommerce-fedex-shipping-plugin/" target="_blank"><b>Read More</b></a>
			</li>
			<li>
				How to use FedEx Standard Boxes?&nbsp;&nbsp;&nbsp;
				<a href="https://www.pluginhive.com/knowledge-base/insight-into-fedex-boxes-for-woocommerce/" target="_blank"><b>Read More</b></a>
			</li>
			<li>
				How to Track FedEx Shipments & Schedule Pickups?&nbsp;&nbsp;&nbsp;
				<a href="https://www.pluginhive.com/knowledge-base/woocommerce-fedex-shipment-tracking-scheduling-pickup/" target="_blank"><b>Read More</b></a>
			</li>
		</ul>

		<hr/>

		<h3>Video Tutorials</h3>

		<table style="width:100%;">
			<tr>
				<td style="text-align: center;">
					<h4>"Print Sample Labels with Test Account"</h4>
					<center>
						<div class="video_content">
							<iframe width="250" height="150" src="https://www.youtube.com/embed/qyQjCooXbto?autoplay=0" frameborder="0" allowfullscreen></iframe>
						</div>
					</center>
				</td>
				<td style="text-align: center;">
					<h4>"Debug FedEx Rates Mismatch"</h4>
					<div class="video_content">
						<iframe width="250" height="150" src="https://www.youtube.com/embed/3OEg1hvpk2U?autoplay=0" frameborder="0" allowfullscreen></iframe>
					</div>
				</td>
				<td style="text-align: center;">
					<h4>"Send FedEx Tracking Details to Customers"</h4>
					<center>
						<div class="video_content">
							<iframe width="250" height="150" src="https://www.youtube.com/embed/zfeO0X5nPjA?autoplay=0" frameborder="0" allowfullscreen></iframe>
						</div>
					</center>
				</td>
				<td style="text-align: center;">
					<h4>"FedEx Shipping Rates Adjustment"</h4>
					<div class="video_content">
						<iframe width="250" height="150" src="https://www.youtube.com/embed/MIYB04oMwVE?autoplay=0" frameborder="0" allowfullscreen></iframe>
					</div>
				</td>
			</tr>
			
		</table>

		<hr/>

		<h3>Submit Your Query</h3>

		<p>Click the button to visit the PluginHive Support page and submit your query. The support team will get back to you within 1 business day.</p>
		<br/>
		<a class="button ph_fedex_submit_ticket" href="https://www.pluginhive.com/support/" target="_blank">Contact Us</a>
		<br/><br/>
		<hr/>

		<h3>Submit a Diagnostic Report</h3>

		<p>1. Please enusre that the Debug Mode is enabled</p>
		<p>2. After enabling Debug Mode, please try recreating your issue(s)</p>
		<p>3. Submit Diagnostic Report only when asked by the PluginHive Support Team</p>
		<p>4. Clicking on Send button will send Debug Log Details and Plugin Settings to PluginHive Support Team automatically</p>
		<p>5. The details sent to PluginHive will include FedEx Account Details for debugging purposes only</p>

		<table class="ph_fedex_help_table">

			<tr>
				<td colspan="2">
					<input type="checkbox" name="ph_fedex_consent" id="ph_fedex_consent">
					Yes, I have read the above points and agreed to send the details mentioned above for debugging purposess.
					<br/>
					<span class="ph_fedex_consent_error">Please read the instructions & agree to proceed by selecting the checkbox</span>
				</td>
			</tr>

			<tr>
				<th>Reference Ticket Number</th>

				<td>
					<input type="text" name="ph_fedex_ticket_number" id="ph_fedex_ticket_number">
					<span class="ph_fedex_ticket_number_error">Please enter a valid reference ticket number.</span>
				</td>
			</tr>
			
			<tr>
				<td colspan="2">
					<p>Please enter the correct reference ticket number. The information sent with an Incorrect or Invalid ticket number will be discarded.</p>
				</td>
			</tr>

		</table>

		<br/>

		<input type="button" id="ph_fedex_submit_ticket" class="button ph_fedex_submit_ticket" value="Send Report">

	</td>
</tr>