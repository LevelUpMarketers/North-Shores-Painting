<?php
/**
 * Returns date block for use with meta element.
 *
 * @package TotalTheme
 * @subpackage Partials\Meta\Blocks
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

$format   = $args['format'] ?? '';
$singular = $args['singular'] ?? true;
$icon     = $args['icon'] ?? 'calendar-o';

if ( $singular ) { ?>
    <li class="meta-date"><?php echo totaltheme_get_icon( $icon, 'meta-icon' ); ?><time class="updated" datetime="<?php echo esc_attr( the_date( 'Y-m-d', '', '', false ) ); ?>"><?php echo get_the_date( $format ); ?></time></li>
<?php } else { ?>
    <li class="meta-date"><?php echo totaltheme_get_icon( $icon, 'meta-icon' ); ?><time class="updated" datetime="<?php echo esc_attr( the_date( 'Y-m-d', '', '', false ) ); ?>"><?php echo get_the_date( $format ); ?></time></li>
<?php } ?>