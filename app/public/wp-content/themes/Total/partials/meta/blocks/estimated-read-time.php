<?php
/**
 * Returns estimated read time block for use with meta element.
 *
 * @package TotalTheme
 * @subpackage Partials\Meta\Blocks
 * @version 5.18
 */

defined( 'ABSPATH' ) || exit;

$icon = $args['icon'] ?? 'clock-o';

?>

<li class="meta-read-time"><?php echo totaltheme_get_icon( $icon, 'meta-icon' ); ?><?php
	echo totaltheme_get_post_estimated_read_time();
?></li>