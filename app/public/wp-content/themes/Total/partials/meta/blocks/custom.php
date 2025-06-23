<?php
/**
 * Returns custom block for use with meta element.
 *
 * @package TotalTheme
 * @subpackage Partials\Meta\Blocks
 * @version 5.8.0
 */

defined( 'ABSPATH' ) || exit;

$block_type      = ! empty( $args['block_type'] ) ? sanitize_key( $args['block_type'] ) : 'custom';
$render_callback = $args['render_callback'] ?? null;

if ( ! $render_callback ) {
    return;
}

if ( is_callable( $render_callback ) ) { ?>
    <li class="meta-<?php echo esc_attr( $block_type ); ?>"><?php
        echo call_user_func( $render_callback );
    ?></li>
<?php } elseif ( 'meta' !== $render_callback ) {
    $render_callback_safe = sanitize_text_field( $render_callback );
    ?>
    <li class="meta-<?php echo esc_attr( $block_type ); ?>"><?php
        get_template_part( "partials/meta/{$render_callback}" );
    ?></li>
<?php } ?>
