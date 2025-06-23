<?php
/**
 * Returns author block for use with meta element.
 *
 * @package TotalTheme
 * @subpackage Partials\Meta\Blocks
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

$singular = $args['singular'] ?? true;
$has_link = $args['link'] ?? true;
$icon     = $args['icon'] ?? 'user-o';

if ( $singular ) { ?>
	<li class="meta-author"><?php echo totaltheme_get_icon( $icon, 'meta-icon' ); ?><span class="vcard author"><span class="fn"><?php
			if ( $has_link ) {
				the_author_posts_link();
			} else {
				the_author();
			}
		?></span></span></li>
<?php } else { ?>
	<li class="meta-author"><?php echo totaltheme_get_icon( $icon, 'meta-icon' ); ?><span class="vcard author"><span class="fn"><?php
			if ( $has_link ) {
				the_author_posts_link();
			} else {
				the_author();
			}
		?></span></span></li>
<?php } ?>