<?php

/**
 * The template for displaying search forms
 *
 * @package Total WordPress theme
 * @subpackage Partials
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

$style        = $args['style'] ?? 'classic';
$input_id     = $args['input_id'] ?? uniqid( 'searchform-input-' );
$input_class  = $args['input_class'] ?? '';
$submit_class = $args['submit_class'] ?? '';
$submit_icon  = $args['submit_icon'] ?? 'search';
$submit_text  = $args['submit_text'] ?? esc_html__( 'Search', 'total' );
$placeholder  = $args['placeholder'] ?? ( 'classic' === $style ? esc_html__( 'Search', 'total' ) : '' );
$placeholder  = apply_filters( 'wpex_search_placeholder_text', $placeholder, 'main' );
$action       = apply_filters( 'wpex_search_action', esc_url( home_url( '/' ) ), 'main' );

$form_class = 'searchform';

if ( $style ) {
	$form_class .= " searchform--{$style}";
}

if ( isset( $args['form_class'] ) ) {
	$form_class .= " {$args['form_class']}";
} elseif ( 'classic' !== $style ) {
	$form_class .= ' wpex-flex wpex-gap-5';
}

if ( 'wpex_hook_topbar_inner' === current_action() ) {
	$form_class .= ' wpex-inline-block';
}

?>

<form role="search" method="get" class="<?php echo esc_attr( $form_class ); ?>" action="<?php echo esc_attr( $action ); ?>"<?php echo isset( $args['autocomplete'] ) ? ' autocomplete="' . esc_attr( $args['autocomplete'] ) . '"' : ''; ?>>
	<label for="<?php echo esc_attr( $input_id ); ?>" class="searchform-label screen-reader-text"><?php echo wpex_get_aria_label( 'search' ); ?></label>
	<input id="<?php echo esc_attr( $input_id ); ?>" type="search" class="<?php echo esc_attr( trim( "searchform-input {$input_class}" ) ); ?>" name="s" placeholder="<?php echo esc_attr( $placeholder ); ?>" required>
	<?php if ( ! empty( $args['post_type'] ) && is_string( $args['post_type'] ) ) : ?>
		<input type="hidden" name="post_type" value="<?php echo esc_attr( $args['post_type'] ); ?>">
	<?php endif; ?>
	<?php do_action( 'wpex_searchform_fields' ); ?>
	<button type="submit" class="<?php echo esc_attr( trim( "searchform-submit {$submit_class}" ) ); ?>" aria-label="<?php echo wpex_get_aria_label( 'submit_search' ); ?>"><?php echo totaltheme_get_icon( $submit_icon ); ?><?php if ( 'classic' !== $style && $submit_text ) { ?><span<?php echo $submit_icon ? ' class="wpex-ml-5"' : ''; ?>><?php echo esc_html( $submit_text ); ?></span><?php } ?></button>
</form>
