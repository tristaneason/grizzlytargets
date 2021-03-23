<?php
	global $porto_settings, $post;
	$skill_list             = get_the_term_list( $post->ID, 'portfolio_skills', '', '', '' );
	$portfolio_client       = get_post_meta( $post->ID, 'portfolio_client', true );
	$portfolio_client_link  = get_post_meta( $post->ID, 'portfolio_client_link', true );
	$portfolio_location     = get_post_meta( $post->ID, 'portfolio_location', true );
	$portfolio_author_quote = get_post_meta( $post->ID, 'portfolio_author_quote', true );
	$portfolio_author_name  = get_post_meta( $post->ID, 'portfolio_author_name', true );
	$portfolio_author_image = get_post_meta( $post->ID, 'portfolio_author_image', true );
	$portfolio_author_role  = get_post_meta( $post->ID, 'portfolio_author_role', true );
	$portfolio_link         = get_post_meta( $post->ID, 'portfolio_link', true );
	$portfolio_name         = empty( $porto_settings['portfolio-singular-name'] ) ? __( 'Portfolio', 'porto' ) : $porto_settings['portfolio-singular-name'];

if ( ( in_array( 'link', $porto_settings['portfolio-metas'] ) && $portfolio_link ) || ( in_array( 'skills', $porto_settings['portfolio-metas'] ) && $skill_list ) || ( in_array( 'location', $porto_settings['portfolio-metas'] ) && $portfolio_location ) || ( in_array( 'client', $porto_settings['portfolio-metas'] ) && $portfolio_client ) ) :
	?>
	<?php if ( is_single() ) : ?>
	<<?php echo isset( $title_tag ) ? esc_html( $title_tag ) : 'h5'; ?> class="portfolio-details-title <?php echo isset( $title_class ) ? esc_attr( $title_class ) : 'm-t-lg'; ?>"><?php echo porto_strip_script_tags( sprintf( __( '%s <strong>Details</strong>', 'porto' ), $portfolio_name ) ); ?></<?php echo isset( $title_tag ) ? esc_html( $title_tag ) : 'h5'; ?>>
<?php endif; ?>
	<ul class="portfolio-details">
	<?php
	if ( in_array( 'client', $porto_settings['portfolio-metas'] ) && $portfolio_client ) :
		?>
			<li>
				<h5><i class="fas fa-caret-right"></i><?php esc_html_e( 'Client', 'porto' ); ?>:</h5>
				<p>
				<?php echo esc_html( $portfolio_client ); ?>
				<?php if ( $portfolio_client_link ) : ?>
					 - <a href="<?php echo esc_url( $portfolio_client_link ); ?>" target="_blank"><i class="fas fa-external-link-alt"></i> <?php echo esc_url( $portfolio_client_link ); ?></a>
				<?php endif; ?>
				</p>
			</li>

		<?php
		endif;
	if ( in_array( 'skills', $porto_settings['portfolio-metas'] ) && $skill_list ) :
		?>
			<li class="skill-list">
				<h5><i class="fas fa-caret-right"></i><?php esc_html_e( 'Skills', 'porto' ); ?>:</h5>
				<?php echo porto_filter_output( $skill_list ); ?>
			</li>
		<?php
		endif;
	if ( in_array( 'location', $porto_settings['portfolio-metas'] ) && $portfolio_location ) :
		?>
			<li>
				<h5><i class="fas fa-caret-right"></i><?php esc_html_e( 'Location', 'porto' ); ?>:</h5>
				<p><?php echo wp_kses_post( $portfolio_location ); ?></p>
			</li>
		<?php
		endif;
	if ( in_array( 'link', $porto_settings['portfolio-metas'] ) && $portfolio_link ) :
		?>
			<li>
				<h5><i class="fas fa-caret-right"></i><?php printf( esc_html__( '%s URL', 'porto' ), $portfolio_name ); ?>:</h5>
				<p><a href="<?php echo esc_url( $portfolio_link ); ?>" target="_blank"><?php echo esc_html( $portfolio_link ); ?></a></p>
			</li>
		<?php
		endif;
	?>
	</ul>
	<?php if ( is_single() && in_array( 'quote', $porto_settings['portfolio-metas'] ) && $portfolio_author_quote ) : ?>
		<div class="testimonial testimonial-style-4">
			<blockquote>
				<p><?php echo wp_kses_post( $portfolio_author_quote ); ?></p>
			</blockquote>
			<div class="testimonial-arrow-down"></div>
			<div class="testimonial-author">
				<?php if ( $portfolio_author_image ) : ?>
					<div class="testimonial-author-thumbnail">
						<img alt="author" class="img-responsive img-circle" src="<?php echo esc_url( $portfolio_author_image ); ?>">
					</div>
				<?php endif; ?>
				<p><strong><?php echo esc_html( $portfolio_author_name ); ?></strong><span><?php echo esc_html( $portfolio_author_role ); ?></span></p>
			</div>
		</div>
	<?php endif; ?>
	<?php
endif;
