<?php

defined( 'ABSPATH' ) || exit;

$tag = 'script';

?>

<script>
	window.totalThemeVcPatterns = {
		patterns: <?php echo self::get_patterns_json(); ?>
	}
</script>

<div class="vc_column vc_col-sm-12">
	<h3><?php echo esc_html( self::get_category_name() ) ; ?></h3>
	<p class="vc_description"><?php echo wp_kses_post( self::get_category_description() ); ?></p>
</div>

<div class="vc_column vc_col-sm-12">
	<div class="wpex-vc-template-list__filter">
		<span><?php esc_html_e( 'Filter by type', 'total' ); ?>:</span>
		<a href="#" data-category="*" aria-pressed="true" role="button" class="wpex-vc-template-list__filter-button"><?php esc_html_e( 'All', 'total' ); ?></a>
		<?php foreach ( self::get_categories() as $cat_id => $cat_name ) { ?>
			<a href="#" data-category="<?php echo esc_attr( $cat_id ); ?>" aria-pressed="false" role="button" class="wpex-vc-template-list__filter-button"><?php echo esc_html( $cat_name ); ?></a>
		<?php } ?>
	</div>
</div>

<div class="vc_column vc_col-sm-12">
	<div class="wpex-vc-template-list">
		<<?php echo esc_attr( $tag ); ?> type="text/html" id="wpex_template-item">
			<div class="wpex-vc-template-list__item"
				data-template_id="<%- id %>"
				data-template_id_hash="<%- id_hash %>"
				data-template_unique_id="<%- unique_id %>"
				data-wpex-category="<%- u=category %>"
				data-template_name="<%- u=name %>"
				data-template_type="<?php echo esc_attr( self::TEMPLATE_TYPE ); ?>"
				data-category="<?php echo esc_attr( self::TEMPLATE_TYPE ); ?>"
			>
				<button role="button" aria-label="<?php echo sprintf( esc_attr( 'Insert %s Pattern', 'total' ), '<%- label %>' ); ?>" data-template-handler="">
					<div class="wpex-vc-template-list__image"><img loading="lazy" src="<%- screenshot %>"></div>
			</button>
			</div>
		</<?php echo esc_attr( $tag ); ?>>
	</div>
</div>
