<?php
/**
 * Single Product tabs.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/tabs/tabs.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*----------------------------------------------------------------------*/
/* [ Custom Theme output ]
/*----------------------------------------------------------------------*/
if ( totaltheme_is_integration_active( 'woocommerce' )
	&& totaltheme_call_static( 'Integration\WooCommerce', 'is_advanced_mode' )
	&& wp_validate_boolean( get_theme_mod( 'woo_product_accordion_tabs', false ) )
	&& class_exists( 'Vcex_Toggle_Group_Shortcode', false )
	&& class_exists( 'VCEX_Toggle_Shortcode', false )
) : ?>

	<?php
	/**
	 * Filter tabs and allow third parties to add their own.
	 *
	 * Each tab is an array containing title, callback and priority.
	 *
	 * @see woocommerce_default_product_tabs()
	 */
	$product_tabs = apply_filters( 'woocommerce_product_tabs', array() );

	if ( ! empty( $product_tabs ) ) :

		$toggles = '';
		$is_first = true;
		foreach ( $product_tabs as $key => $product_tab ) {
			if ( $is_first ) {
				$state = get_theme_mod( 'woo_product_accordion_tabs_first_open' ) ? 'open' : 'closed';
			}

			$toggle_atts = [
				'heading'          => wp_kses_post( apply_filters( "woocommerce_product_{$key}_tab_title", $product_tab['title'], $key ) ),
				'content_id'       => 'wpex-woo-product-accordion-section--' . sanitize_html_class( $key ),
				'heading_el_class' => 'wpex-text-1 wpex-bold wpex-child-inherit-color',
				'icon_position'    => 'right',
				'heading_tag'      => get_theme_mod( 'woo_product_accordion_tab_title_tag' ) ?: 'h3',
				'animate'          => get_theme_mod( 'woo_product_accordion_tabs_animate', true ) ? 'true' : 'false',
				'icon_type'        => get_theme_mod( 'woo_product_accordion_icon_type' ) ?: 'plus',
				'icon_position'    => get_theme_mod( 'woo_product_accordion_icon_position' ) ?: 'right',
				'state'            => ( $is_first && isset( $state ) ) ? $state : 'closed',
				'parse_content'    => 'false',
			];

			$group_atts = [
				'el_class'      => 'wpex-woo-product-accordion',
				'parse_content' => 'false',
				'style'         => get_theme_mod( 'woo_product_accordion_style' ) ?: 'w-borders',
			];

			if ( 'none' === $group_atts['style'] && 'left' === $toggle_atts['icon_position'] ) {
				$toggle_atts['heading_inline'] = 'true';
			}

			if ( isset( $product_tab['callback'] ) ) {
				ob_start();
					call_user_func( $product_tab['callback'], $key, $product_tab );
				$toggle_content = ob_get_clean();
			}
			if ( is_callable( [ 'VCEX_Toggle_Shortcode', 'output' ] ) ) {
				$toggles .= VCEX_Toggle_Shortcode::output( $toggle_atts, $toggle_content, 'vcex_toggle' );
			}
			$is_first = false;
		}

		if ( is_callable( [ 'Vcex_Toggle_Group_Shortcode', 'output' ] ) ) {
			echo Vcex_Toggle_Group_Shortcode::output( $group_atts, $toggles, 'vcex_toggle_group' );
		}
		
		do_action( 'woocommerce_product_after_tabs' );

	endif;

/*----------------------------------------------------------------------*/
/* [ Default output ]
/*----------------------------------------------------------------------*/
else : ?>

	<?php
	/**
	 * Filter tabs and allow third parties to add their own.
	 *
	 * Each tab is an array containing title, callback and priority.
	 *
	 * @see woocommerce_default_product_tabs()
	 */
	$product_tabs = apply_filters( 'woocommerce_product_tabs', array() );

	if ( ! empty( $product_tabs ) ) : ?>

		<div class="woocommerce-tabs wc-tabs-wrapper">
			<ul class="tabs wc-tabs" role="tablist">
				<?php foreach ( $product_tabs as $key => $product_tab ) : ?>
					<li class="<?php echo esc_attr( $key ); ?>_tab" id="tab-title-<?php echo esc_attr( $key ); ?>">
						<a href="#tab-<?php echo esc_attr( $key ); ?>" role="tab" aria-controls="tab-<?php echo esc_attr( $key ); ?>">
							<?php echo wp_kses_post( apply_filters( 'woocommerce_product_' . $key . '_tab_title', $product_tab['title'], $key ) ); ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
			<?php foreach ( $product_tabs as $key => $product_tab ) : ?>
				<div class="woocommerce-Tabs-panel woocommerce-Tabs-panel--<?php echo esc_attr( $key ); ?> panel entry-content wc-tab" id="tab-<?php echo esc_attr( $key ); ?>" role="tabpanel" aria-labelledby="tab-title-<?php echo esc_attr( $key ); ?>">
					<?php
					if ( isset( $product_tab['callback'] ) ) {
						call_user_func( $product_tab['callback'], $key, $product_tab );
					}
					?>
				</div>
			<?php endforeach; ?>
	
			<?php do_action( 'woocommerce_product_after_tabs' ); ?>
		</div>
	
	<?php endif; ?>

<?php endif; ?>
