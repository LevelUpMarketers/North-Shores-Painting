<?php

/**
 * Template for displaying the post author bio.
 *
 * @package TotalTheme
 * @subpackage Templates
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

$data = (array) wpex_get_author_box_data();

if ( empty( $data ) ) {
	return;
}

extract( $data );

$gap = get_theme_mod( 'author_box_gap' ) ? : '20';

?>

<section class="author-bio wpex-boxed wpex-flex wpex-gap-<?php echo sanitize_html_class( $gap ); ?> wpex-flex-col wpex-sm-flex-row wpex-text-center wpex-sm-text-left wpex-mb-40">

	<?php if ( ! empty( $avatar ) ) { ?>

		<div class="author-bio-avatar wpex-flex-shrink-0"><?php

			if ( ! empty( $posts_url ) ) { ?>

				<a href="<?php echo esc_url( $posts_url ); ?>" title="<?php esc_attr_e( 'Visit Author Page', 'total' ); ?>"><?php
					echo $avatar; // @codingStandardsIgnoreLine
				?></a>

			<?php } else { ?>

				<?php echo $avatar; // @codingStandardsIgnoreLine ?>

			<?php }

		?></div>

	<?php } ?>

	<div class="author-bio-content wpex-flex-grow wpex-last-mb-0">

		<?php if ( ! empty( $author_name ) ) {

			$heading_tag = get_theme_mod( 'author_box_heading_tag' ) ?: 'h3';
			?>

			<<?php echo tag_escape( $heading_tag ); ?> class="author-bio-title wpex-heading wpex-m-0 wpex-mb-10 wpex-text-lg"><?php

				if ( ! empty( $posts_url ) ) { ?>

					<a href="<?php echo esc_url( $posts_url ); ?>" title="<?php esc_attr_e( 'Visit Author Page', 'total' ); ?>" rel="author" class="wpex-no-underline"><?php echo wp_strip_all_tags( $author_name ); ?></a>

				<?php } else { ?>

					<?php echo wp_strip_all_tags( $author_name ); ?>

				<?php }

			?></<?php echo tag_escape( $heading_tag ); ?>>

		<?php } ?>

		<?php if ( ! empty( $description ) ) { ?>

			<div class="author-bio-description wpex-mb-15 wpex-last-mb-0"><?php

				echo wpautop( do_shortcode( wp_kses_post( $description ) ) );

			?></div>

		<?php } ?>

		<?php wpex_author_box_social_links( $post_author ); ?>

	</div>

</section>
