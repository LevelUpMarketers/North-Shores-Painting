<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$post_id       = get_the_ID();
$preview_width = get_post_meta( $post_id, 'preview_width', true );
$class         = get_post_meta( $post_id, 'el_class', true );

?>

	<div class="wpex-card-builder wpex-p-50">
		<?php if ( $preview_width ) {
			echo '<div class="wpex-mx-auto" style="max-width:' . esc_attr( wpex_sanitize_data( $preview_width, 'fallback_px' ) ) . '">';
		} else {
			echo '<div class="container">';
		} ?>
			<?php while ( have_posts() ) : the_post();
				$entry_class = [
					'wpex-card',
					'wpex-card-template_' . absint( $post_id ),
				];
				if ( $class ) {
					$entry_class[] = esc_attr( $class );
				}
				?>
				<div class="<?php echo esc_attr( implode( ' ', $entry_class ) ); ?>">
					<div class="wpex-card-inner">
						<?php the_content(); ?>
					</div>
				</div>
			<?php endwhile; ?>
		</div>
	</div>

<?php
get_footer();