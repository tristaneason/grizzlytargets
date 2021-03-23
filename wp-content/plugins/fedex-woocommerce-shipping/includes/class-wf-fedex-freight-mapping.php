<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Map freight classes to shipping classes
 */
class WF_Fedex_Freight_Mapping {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->classes = include( 'data-wf-freight-classes.php' );

		add_action( 'product_shipping_class_add_form_fields', array( $this, 'add_form' ) );
		add_action( 'product_shipping_class_edit_form_fields', array( $this, 'edit_form' ), 10, 2 );
		add_action( 'created_term', array( $this, 'save' ), 10, 3 );
		add_action( 'edit_term', array( $this, 'save' ), 10, 3 );
		add_filter( 'manage_edit-product_shipping_class_columns', array( $this, 'columns' ) );
		add_filter( 'manage_product_shipping_class_custom_column', array( $this, 'column' ), 10, 3 );
	}

	/**
	 * Add term - fields
	 */
	public function add_form() {
		?>
		<div class="form-field">
			<label for="fedex_freight_class"><?php _e( 'Fedex Freight Class', 'wf-shipping-fedex' ); ?></label>
			<select id="fedex_freight_class" name="fedex_freight_class" class="postform">
				<option value=""><?php _e( 'Default', 'wf-shipping-fedex' ); ?></option>
				<?php foreach ( $this->classes as $key => $value ) : ?>
					<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $value ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<?php
	}

	/**
	 * Edit term - fields
	 * @param mixed $term Term (category) being edited
	 * @param mixed $taxonomy Taxonomy of the term being edited
	 */
	public function edit_form( $term, $taxonomy ) {

		global $wp_version;

		if( version_compare( $wp_version, '4.4', '<=' ) ){
			$fedex_freight_class = get_woocommerce_term_meta( $term->term_id, 'fedex_freight_class', true );
		}else{
			$fedex_freight_class = get_term_meta( $term->term_id, 'fedex_freight_class', true );
		}

		?>
		<tr class="form-field">
			<th scope="row" valign="top"><label for="fedex_freight_class"><?php _e( 'Fedex Freight Class', 'wf-shipping-fedex' ); ?></label></th>
			<td>
				<select id="fedex_freight_class" name="fedex_freight_class" class="postform">
					<option value=""><?php _e( 'Default', 'wf-shipping-fedex' ); ?></option>
					<?php foreach ( $this->classes as $key => $value ) : ?>
						<option <?php selected( $fedex_freight_class, $key ); ?> value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $value ); ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		<?php
	}

	/**
	 * Save the freight class
	 *
	 * @param  int $term_id
	 * @param  int $tt_id
	 * @param  string $taxonomy
	 */
	public function save( $term_id, $tt_id, $taxonomy ) {
		if ( isset( $_POST['fedex_freight_class'] ) ) {
			update_woocommerce_term_meta( $term_id, 'fedex_freight_class', sanitize_text_field( $_POST['fedex_freight_class'] ) );
		}
	}
	/**
	 * Column added to shipping class admin.
	 *
	 * @param mixed $columns
	 * @return array
	 */
	public function columns( $columns ) {
		$columns['fedex_freight_class'] = __( 'Fedex Freight Class', 'wf-shipping-fedex' );

		return $columns;
	}

	/**
	 * Column value added to shipping class admin.
	 *
	 * @param mixed $columns
	 * @param mixed $column
	 * @param mixed $id
	 * @return array
	 */
	public function column( $columns, $column, $id ) {

		global $wp_version;

		if ( $column == 'fedex_freight_class' ) {
			
			if( version_compare( $wp_version, '4.4', '<=' ) ){
				$fedex_freight_class = get_woocommerce_term_meta( $id, 'fedex_freight_class', true );
			}else{
				$fedex_freight_class = get_term_meta( $id, 'fedex_freight_class', true );
			}
			
			$columns             .= $fedex_freight_class ? $this->classes[ $fedex_freight_class ] : '-';
		}

		return $columns;
	}
}

new WF_Fedex_Freight_Mapping();