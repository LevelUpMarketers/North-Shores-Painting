<?php

/**
 * Returns event date block for use with meta element.
 *
 * @package TotalTheme
 * @subpackage Partials\Meta\Blocks
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

$format   = $args['format'] ?? '';
$singular = $args['singular'] ?? true;
$icon     = $args['icon'] ?? 'calendar-o';

$date = '';

if ( 'just_event' === get_post_type() && function_exists( 'Just_Events\get_event_formatted_date' ) ) {
    $date = Just_Events\get_event_formatted_date();
}

if ( ! $date ) {
    return;
}

$allowed_html = [
    'br' => [],
    'span' => [
        'class' => true,
    ],
    'time' => [
        'class'    => true,
        'datetime' => true,
    ],
];

if ( $singular ) { ?>
    <li class="meta-date"><?php echo totaltheme_get_icon( $icon, 'meta-icon' ); ?><span><?php echo wp_kses( $date, $allowed_html ); ?></span></li>
<?php } else { ?>
    <li class="meta-date"><?php echo totaltheme_get_icon( $icon, 'meta-icon' ); ?><span><?php echo wp_kses( $date, $allowed_html ); ?></span></li>
<?php }
