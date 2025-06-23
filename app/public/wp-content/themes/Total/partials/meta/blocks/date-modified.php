<?php
/**
 * Returns last modified date block for use with meta element.
 *
 * @package TotalTheme
 * @subpackage Partials\Meta\Blocks
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

$format   = $args['format'] ?? get_option( 'date_format' );
$singular = $args['singular'] ?? true;
$icon     = $args['icon'] ?? 'calendar-o';

if ( $singular ) { ?>
    <li class="meta-date-modified"><?php echo totaltheme_get_icon( $icon, 'meta-icon' ); ?><time class="updated" datetime="<?php echo esc_attr( get_the_modified_date( 'Y-m-d' ) ); ?>"><?php the_modified_date( $format ); ?></time></li>
<?php } else { ?>
    <li class="meta-date-modified"><?php echo totaltheme_get_icon( $icon, 'meta-icon' ); ?><time class="updated" datetime="<?php echo esc_attr( get_the_modified_date( 'Y-m-d' ) ); ?>"><?php the_modified_date( $format ); ?></time></li>
<?php } ?>