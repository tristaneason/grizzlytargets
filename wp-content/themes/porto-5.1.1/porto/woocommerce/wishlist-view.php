<?php
/**
 * Wishlist page template
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 2.0.0
 */
?>

<form id="yith-wcwl-form" action="<?php echo esc_url( YITH_WCWL()->get_wishlist_url( 'view' . ( 1 != $wishlist_meta['is_default'] ? '/' . $wishlist_meta['wishlist_token'] : '' ) ) ); ?>" method="post">

	<div class="featured-box featured-box-primary align-left mt-3"><div class="box-content">
		<!-- TITLE -->
		<?php
		do_action( 'yith_wcwl_before_wishlist_title' );

		if ( ! empty( $page_title ) ) :
			?>
			<div class="wishlist-title<?php echo 1 == $wishlist_meta['is_default'] || ! $is_user_owner ? '' : ' wishlist-title-with-form'; ?>">
				<?php echo apply_filters( 'yith_wcwl_wishlist_title', '<h2>' . esc_html( $page_title ) . '</h2>' ); ?>
				<?php if ( 1 != $wishlist_meta['is_default'] && $is_user_owner ) : ?>
					<a class="btn button show-title-form">
						<?php echo apply_filters( 'yith_wcwl_edit_title_icon', '<i class="fas fa-pencil-alt"></i>' ); ?>
						<?php esc_html_e( 'Edit title', 'porto' ); ?>
					</a>
				<?php endif; ?>
			</div>
			<?php if ( 1 != $wishlist_meta['is_default'] && $is_user_owner ) : ?>
				<div class="hidden-title-form">
					<input type="text" value="<?php echo esc_attr( $page_title ); ?>" name="wishlist_name"/>
					<button>
						<?php echo apply_filters( 'yith_wcwl_save_wishlist_title_icon', '<i class="fas fa-check"></i>' ); ?>
						<?php esc_html_e( 'Save', 'porto' ); ?>
					</button>
					<a class="hide-title-form btn button">
						<?php echo apply_filters( 'yith_wcwl_cancel_wishlist_title_icon', '<i class="fas fa-times"></i>' ); ?>
						<?php esc_html_e( 'Cancel', 'porto' ); ?>
					</a>
				</div>
			<?php endif; ?>
			<?php
		endif;

		do_action( 'yith_wcwl_before_wishlist' );
		?>

		<!-- WISHLIST TABLE -->
		<table class="shop_table responsive cart wishlist_table" cellspacing="0" data-pagination="<?php echo esc_attr( $pagination ); ?>" data-per-page="<?php echo esc_attr( $per_page ); ?>" data-page="<?php echo esc_attr( $current_page ); ?>" data-id="<?php echo esc_attr( is_user_logged_in() ? $wishlist_meta['ID'] : '0' ); ?>">
			<thead>
			<tr>
				<?php if ( $is_user_owner ) : ?>
				<th class="product-remove"></th>
				<?php endif; ?>

				<th class="product-thumbnail"></th>

				<th class="product-name">
					<span class="nobr"><?php echo apply_filters( 'yith_wcwl_wishlist_view_name_heading', esc_html__( 'Product Name', 'porto' ) ); ?></span>
				</th>

				<?php if ( $show_price ) : ?>
					<th class="product-price">
						<span class="nobr">
							<?php echo apply_filters( 'yith_wcwl_wishlist_view_price_heading', esc_html__( 'Unit Price', 'porto' ) ); ?>
						</span>
					</th>
				<?php endif ?>

				<?php if ( $show_stock_status ) : ?>
					<th class="product-stock-stauts">
						<span class="nobr">
							<?php echo apply_filters( 'yith_wcwl_wishlist_view_stock_heading', esc_html__( 'Stock Status', 'porto' ) ); ?>
						</span>
					</th>
				<?php endif ?>

				<?php if ( $show_add_to_cart ) : ?>
					<th class="product-add-to-cart"></th>
				<?php endif ?>
			</tr>
			</thead>

			<tbody>
			<?php
			if ( count( $wishlist_items ) > 0 ) :
				foreach ( $wishlist_items as $item ) :
					global $product;
					if ( function_exists( 'wc_get_product' ) ) {
						if ( isset( $item['prod_id'] ) ) {
							$product = wc_get_product( $item['prod_id'] );
						} else {
							$product = false;
						}
					} else {
						if ( isset( $item['prod_id'] ) ) {
							$product = get_product( $item['prod_id'] );
						} else {
							$product = false;
						}
					}

					if ( false !== $product && $product->exists() ) :
						$availability = $product->get_availability();
						$stock_status = $availability['class'];
						?>
						<tr id="yith-wcwl-row-<?php echo esc_attr( $item['prod_id'] ); ?>" data-row-id="<?php echo esc_attr( $item['prod_id'] ); ?>">
							<?php if ( $is_user_owner ) : ?>
							<td class="product-remove">
								<div>
									<a href="<?php echo esc_url( add_query_arg( 'remove_from_wishlist', $item['prod_id'] ) ); ?>" class="btn-arrow remove remove_from_wishlist" title="<?php esc_attr_e( 'Remove this product', 'porto' ); ?>">&times;</a>
								</div>
							</td>
							<?php endif; ?>

							<td class="product-thumbnail">
								<a href="<?php echo esc_url( get_permalink( apply_filters( 'woocommerce_in_cart_product', $item['prod_id'] ) ) ); ?>">
									<?php echo wp_kses_post( $product->get_image() ); ?>
								</a>
							</td>

							<td class="product-name">
								<a href="<?php echo esc_url( get_permalink( apply_filters( 'woocommerce_in_cart_product', $item['prod_id'] ) ) ); ?>"><?php echo apply_filters( 'woocommerce_in_cartproduct_obj_title', $product->get_title(), $product ); ?></a>
							</td>

							<?php if ( $show_price ) : ?>
								<td class="product-price">
									<?php
									if ( $product->get_price() != '0' ) {
										$wc_price = function_exists( 'wc_price' ) ? 'wc_price' : 'woocommerce_price';

										if ( $price_excl_tax ) {
											echo apply_filters( 'woocommerce_cart_item_price_html', $wc_price( wc_get_price_excluding_tax( $product ) ), $item, '' );
										} else {
											echo apply_filters( 'woocommerce_cart_item_price_html', $wc_price( $product->get_price() ), $item, '' );
										}
									} else {
										echo apply_filters( 'yith_free_text', esc_html__( 'Free!', 'porto' ) );
									}
									?>
								</td>
							<?php endif ?>

							<?php if ( $show_stock_status ) : ?>
								<td class="product-stock-status">
									<?php
									if ( 'out-of-stock' == $stock_status ) {
										$stock_status = 'Out';
										echo '<span class="wishlist-out-of-stock">' . esc_html__( 'Out of Stock', 'porto' ) . '</span>';
									} else {
										$stock_status = 'In';
										echo '<span class="wishlist-in-stock">' . esc_html__( 'In Stock', 'porto' ) . '</span>';
									}
									?>
								</td>
							<?php endif ?>

							<?php if ( $show_add_to_cart ) : ?>
								<td class="product-add-to-cart">
									<?php if ( isset( $stock_status ) && 'Out' != $stock_status ) : ?>
										<?php
										if ( function_exists( 'wc_get_template' ) ) {
											if ( version_compare( porto_get_woo_version_number(), '2.5', '<' ) ) {
												wc_get_template( 'loop/add-to-cart.php', $args );
											} else {
												$args     = array();
												$defaults = array(
													'quantity' => 1,
													'class'    => implode(
														' ',
														array_filter(
															array(
																'button',
																'product_type_' . $product->get_type(),
																$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
																$product->supports( 'ajax_add_to_cart' ) ? 'ajax_add_to_cart' : '',
															)
														)
													),
												);

												$args = apply_filters( 'woocommerce_loop_add_to_cart_args', wp_parse_args( $args, $defaults ), $product );
												wc_get_template( 'loop/add-to-cart.php', $args );
											}
										} else {
											woocommerce_get_template( 'loop/add-to-cart.php' );
										}
										?>
									<?php endif ?>
								</td>
							<?php endif ?>
						</tr>
						<?php
					endif;
				endforeach;
			else :
				?>
				<tr class="pagination-row">
					<td colspan="6" class="wishlist-empty"><?php esc_html_e( 'No products were added to the wishlist', 'porto' ); ?></td>
				</tr>
				<?php
			endif;

			if ( ! empty( $page_links ) ) :
				?>
				<tr>
					<td colspan="6"><?php echo porto_filter_output( $page_links ); ?></td>
				</tr>
			<?php endif ?>
			</tbody>

			<?php if ( $is_user_logged_in ) : ?>
				<tfoot>
				<tr>
					<?php if ( $is_user_owner && 2 != $wishlist_meta['wishlist_privacy'] && $share_enabled ) : ?>
						<td colspan="<?php echo ! $is_user_logged_in || ! $is_user_owner || ! $show_ask_estimate_button || $count <= 0 ? '6' : '4'; ?>">
							<?php yith_wcwl_get_template( 'share.php', $share_atts ); ?>
						</td>
					<?php endif; ?>

					<?php
					if ( $is_user_owner && $show_ask_estimate_button && $count > 0 ) :
						?>
						<td colspan="<?php echo ! $is_user_owner && 2 == $wishlist_meta['wishlist_privacy'] || ! $share_enabled ? '6' : '2'; ?>">
							<a href="<?php echo esc_url( $ask_estimate_url ); ?>" class="btn button ask-an-estimate-button">
								<?php echo apply_filters( 'yith_wcwl_ask_an_estimate_icon', '<i class="fas fa-shopping-cart"></i>' ); ?>
								<?php esc_html_e( 'Ask for an estimate', 'porto' ); ?>
							</a>
						</td>
						<?php
					endif;

					do_action( 'yith_wcwl_after_wishlist_share' );
					?>
				</tr>
				</tfoot>
			<?php endif; ?>

		</table>

		<?php wp_nonce_field( 'yith_wcwl_edit_wishlist_action', 'yith_wcwl_edit_wishlist' ); ?>

		<?php if ( 1 != $wishlist_meta['is_default'] ) : ?>
			<input type="hidden" value="<?php echo esc_attr( $wishlist_meta['wishlist_token'] ); ?>" name="wishlist_id" id="wishlist_id">
		<?php endif; ?>

		<?php do_action( 'yith_wcwl_after_wishlist' ); ?>
	</div>
</form>
