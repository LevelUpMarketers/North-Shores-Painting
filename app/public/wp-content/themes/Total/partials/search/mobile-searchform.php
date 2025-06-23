<?php

/**
 * Mobile menu searchform.
 *
 * @package TotalTheme
 * @subpackage Partials
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

// Placeholder text.
$placeholder = $args['placeholder'] ?? esc_html__( 'Search', 'total' );
$placeholder = (string) apply_filters( 'wpex_mobile_searchform_placeholder', $placeholder );

// Action attr.
$action = $args['action'] ?? esc_url( home_url( '/' ) );
$action = (string) apply_filters( 'wpex_search_action', $action, 'mobile' );

// Wrap attributes.
$wrap_attributes = [
	'id'    => $args['id'] ?? 'mobile-menu-search',
	'class' => $args['class'] ?? 'wpex-hidden',
];

// Inner classes.
$form_class   = $args['form_class'] ?? '';
$input_class  = $args['input_class'] ?? '';
$submit_class = $args['submit_class'] ?? '';

// Submit.
$submit_icon = $args['submit_icon'] ?? 'search';
$submit_text  = $args['submit_text'] ?? esc_html__( 'Search', 'total' );
?>

<div <?php echo wpex_parse_attrs( $wrap_attributes ); ?>>
	<form method="get" action="<?php echo esc_attr( $action ); ?>" class="<?php echo esc_attr( trim( "mobile-menu-searchform {$form_class}" ) ); ?>">
		<label for="mobile-menu-search-input" class="screen-reader-text"><?php echo wpex_get_aria_label( 'search' ); ?></label>
		<input id="mobile-menu-search-input" class="<?php echo esc_attr( trim( "mobile-menu-searchform__input {$input_class}" ) ); ?>" type="search" name="s" autocomplete="off" placeholder="<?php echo esc_attr( $placeholder ); ?>" required>
		<?php if ( $current_language = (string) apply_filters( 'wpml_current_language', null ) ) : ?>
			<input type="hidden" name="lang" value="<?php echo esc_attr( $current_language ); ?>">
		<?php endif; ?>
		<?php if ( totaltheme_is_integration_active( 'woocommerce' ) && get_theme_mod( 'woo_header_product_searchform' ) ) { ?>
			<input type="hidden" name="post_type" value="product">
		<?php } ?>
		<button type="submit" class="<?php echo esc_attr( trim( "mobile-menu-searchform__submit searchform-submit {$submit_class}" ) ); ?>" aria-label="<?php echo wpex_get_aria_label( 'submit_search' ); ?>"><?php echo totaltheme_get_icon( $submit_icon ); ?><?php if ( $submit_text ) { ?><span<?php echo $submit_icon ? ' class="wpex-ml-5"' : ''; ?>><?php echo esc_html( $submit_text ); ?></span><?php } ?></button>
	</form>
</div>
