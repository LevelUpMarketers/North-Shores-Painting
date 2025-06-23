<?php
/**
 * The template for displaying image attachments.
 *
 * @package TotalTheme
 * @subpackage Templates
 * @version 5.6.1
 */

defined( 'ABSPATH' ) || exit;

get_header();

?>

	<div class="container wpex-clr">

		<?php wpex_hook_primary_before(); ?>

		<div id="primary" class="content-area">

			<?php wpex_hook_content_before(); ?>

			<div id="content" class="site-content">

				<?php wpex_hook_content_top(); ?>

				<?php
				while ( have_posts() ) : the_post();

					// Get attachment type
					$mime_type      = (string) get_post_mime_type();
					$attachment_url = (string) wp_get_attachment_url();

					// Image attachments
					if ( str_contains( $mime_type, 'image/' ) ) { ?>

						<div id="attachment-post-media"><?php
							// Display image
							echo wp_get_attachment_image( get_the_ID(), 'large', false, array( 'class' => 'wpex-align-middle' ) );
						?></div>

						<div id="attachment-post-content" class="entry wpex-mt-20 wpex-clr"><?php
							the_content();
						?></div>

						<div id="attachment-post-footer" class="wpex-mt-20 wpex-last-mb-0">
							<?php
							$footer_links = array();
							$sizes = array( 'full', 'large', 'medium', 'thumbnail' );
							if ( $sizes ) {
								foreach ( $sizes as $size ) {
									$image = wp_get_attachment_image_src( get_the_ID(), $size );
									if ( $image ) {
										$dims  = $image[1] . 'x' . $image[2];
										$name  = $size . ' (' . $dims . ')';
										if ( ! isset( $footer_links[$dims] ) ) {
											$footer_links[$dims] = '<a href="' . esc_url( $image[0] ) . '" download>' . esc_html( $name ) . '</a>';
										}
									}
								}
								echo '<strong>' . esc_html__( 'Downloads', 'total' ) . '</strong>: '. implode( ' | ', $footer_links );
							} ?>
						</div>

					<?php }
					// Display video details
					elseif ( str_contains( $mime_type, 'video/' ) ) { ?>

						<div id="attachment-post-media">
							<?php if ( function_exists( 'wp_video_shortcode' ) ) {
								echo wp_video_shortcode( array( 'src' => $attachment_url, 'width' => '9999' ) );
							} ?>
						</div>

						<div id="attachment-post-content" class="entry wpex-mt-20 wpex-clr"><?php the_content(); ?></div>

						<div id="attachment-post-footer" class="wpex-flex wpex-flex-col wpex-gap-5"><?php

							$footer_details = [];
							$meta = wp_get_attachment_metadata();

							// Display video format.
							if ( ! empty( $meta['fileformat'] ) ) { ?>

								<div><strong><?php esc_html_e( 'Format', 'total' ); ?>: </strong> <?php echo esc_html( $meta['fileformat'] ); ?></div>

							<?php }

							// Display video size.
							if ( ! empty( $meta['filesize'] ) ) { ?>

								<div><strong><?php esc_html_e( 'Size', 'total' ); ?>: </strong> <?php echo esc_html( size_format( $meta['filesize'], 2 ) ); ?></div>

							<?php }

							// Display video length.
							if ( ! empty( $meta['length_formatted'] ) ) { ?>

								<div><strong><?php esc_html_e( 'Length', 'total' ); ?>: </strong> <?php echo esc_html( $meta['length_formatted'] ); ?></div>

							<?php }

						 ?></div>

					<?php }

					// Display PDF details.
					elseif ( 'application/pdf' === $mime_type ) { ?>

						<div id="attachment-post-media" class="entry wpex-clr">
							<object data="<?php echo esc_url( $attachment_url ); ?>" type="application/pdf" width="100%" height="600px">
								<?php
								$object_content = sprintf( esc_html__( 'This browser does not support PDFs. Please download the PDF to view it: %sDownload PDF%s.', 'total-theme-core' ), '<a href="' . esc_url( $attachment_url ) . '" download>', '</a>' );
								echo apply_filters( 'wpex_attachment_object_content', $object_content, get_the_ID(), $mime_type ); ?>
							</object>
						</div>

					<?php }

					// Display the_content for all other attachment formats.
					else { ?>

						<div id="attachment-post-content" class="entry wpex-mt-20 wpex-clr"><?php
							the_content();
						?></div>

					<?php } ?>

				<?php endwhile; ?>

				<?php wpex_hook_content_bottom(); ?>

			</div>

			<?php wpex_hook_content_after(); ?>

		</div>

		<?php wpex_hook_primary_after(); ?>

	</div>

<?php
get_footer();
