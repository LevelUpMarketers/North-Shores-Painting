<?php

/**
 * Blog single post related entry.
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

?>

<article <?php wpex_blog_single_related_entry_class(); ?>>

	<?php if ( ! wpex_blog_entry_card() ) : ?>

		<div class="related-post-inner wpex-flex-grow">

			<?php
			// Disable embeds.
			$show_embeds = apply_filters( 'wpex_related_blog_posts_embeds', false );

			// Get post format.
			$format = get_post_format();

			// Display post video.
			if ( $show_embeds && 'video' === $format && $video = wpex_get_post_video() ) : ?>

				<div class="related-post-video wpex-mb-15"><?php echo wpex_get_post_video_html( $video ); ?></div>

			<?php
			// Display post audio.
			elseif ( $show_embeds && 'audio' === $format && $audio = wpex_get_post_audio() ) : ?>

				<div class="related-post-video wpex-mb-15"><?php echo wpex_get_post_audio_html( $audio ); ?></div>

			<?php
			// Display post thumbnail.
			elseif ( has_post_thumbnail() && apply_filters( 'wpex_related_blog_has_thumbnails', true ) ) :
				$related_img_class = 'related-post-figure wpex-mb-15 wpex-relative';

				// Overlay style.
				$overlay = totaltheme_call_static(
					'Overlays',
					'get_entry_image_overlay_style',
					'post'
				);

				if ( $overlay ) {
					$overlay_class = (string) totaltheme_call_static(
						'Overlays',
						'get_parent_class',
						(string) $overlay
					);
					if ( $overlay_class ) {
						$related_img_class .= ' ' . trim( $overlay_class );
					}
				}

				?>

				<figure class="<?php echo esc_attr( $related_img_class ); ?>">
					<a href="<?php the_permalink(); ?>" title="<?php wpex_esc_title(); ?>" class="related-post-thumb<?php wpex_entry_image_animation_classes(); ?>">
						<?php echo wpex_get_post_thumbnail( array(
							'size'  => 'blog_related',
							'class' => 'wpex-align-middle',
						) ); ?>
						<?php wpex_entry_media_after( 'blog_related' ); ?>
						<?php totaltheme_render_overlay( 'inside_link', $overlay ); ?>
					</a>
					<?php totaltheme_render_overlay( 'outside_link', $overlay ); ?>
				</figure>

			<?php endif; ?>

			<?php
			// Display post excerpt.
			if ( wpex_validate_boolean( get_theme_mod( 'blog_related_excerpt', true ) ) ) :
				$related_excerpt_class = '';
				?>
				<div class="related-post-content wpex-clr">
					<div class="related-post-title entry-title wpex-m-0 <?php echo totaltheme_has_classic_styles() ? 'wpex-mb-5' : 'wpex-mb-10'; ?>">
						<a href="<?php wpex_permalink(); ?>"><?php the_title(); ?></a>
					</div>
					<div class="related-post-excerpt wpex-leading-normal wpex-last-mb-0 wpex-clr"><?php
						echo totaltheme_get_post_excerpt( [
							'length' => get_theme_mod( 'blog_related_excerpt_length', '15' ),
						] );
					?></div>
				</div>
				<?php
				endif;
			?>
		</div>

	<?php endif; // end card check ?>

</article>