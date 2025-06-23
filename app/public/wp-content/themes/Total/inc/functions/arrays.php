<?php

defined( 'ABSPATH' ) || exit;

/**
 * Site Layouts.
 */
function wpex_get_site_layouts(): array {
	return (array) apply_filters( 'wpex_get_site_layouts', [
		''           => esc_html__( 'Default', 'total' ),
		'full-width' => esc_html__( 'Full-Width', 'total' ),
		'boxed'      => esc_html__( 'Boxed', 'total' ),
	] );
}

/**
 * Accent Colors.
 *
 * @todo deprecate.
 */
function wpex_get_accent_colors(): array {
	$colors = [
		'default'  => [
			'label' => esc_html__( 'Default', 'total' ),
			'hex'   => '',
		],
		'black'  => [
			'label' => esc_html__( 'Black', 'total' ),
			'hex'   => '#333',
		],
		'blue'   => [
			'label' => esc_html__( 'Blue', 'total' ),
			'hex'   => '#4a97c2',
		],
		'brown'  => [
			'label' => esc_html__( 'Brown', 'total' ),
			'hex'   => '#804b35',
		],
		'grey'   => [
			'label' => esc_html__( 'Grey', 'total' ),
			'hex'   => '#bbb',
		],
		'green'  => [
			'label' => esc_html__( 'Green', 'total' ),
			'hex'   => '#87bf17',
		],
		'gold'   => [
			'label' => esc_html__( 'Gold', 'total' ),
			'hex'   => '#ddba00',
		],
		'orange' => [
			'label' => esc_html__( 'Orange', 'total' ),
			'hex'   => '#ee7836',
		],
		'pink'   => [
			'label' => esc_html__( 'Pink', 'total' ),
			'hex'   => '#f261c2',
		],
		'purple' => [
			'label' => esc_html__( 'Purple', 'total' ),
			'hex'   => '#9a5e9a',
		],
		'red'    => [
			'label' => esc_html__( 'Red', 'total' ),
			'hex'   => '#f73936',
		],
		'rosy'   => [
			'label' => esc_html__( 'Rosy', 'total' ),
			'hex'   => '#ea2487',
		],
		'teal'   => [
			'label' => esc_html__( 'Teal', 'total' ),
			'hex'   => '#00b3b3',
		],
		'white'  => [
			'label' => esc_html__( 'White', 'total' ),
			'hex'   => '#fff',
		],
	];
	return (array) apply_filters( 'wpex_get_accent_colors', $colors );
}

/**
 * Returns array of header styles that allow sticky.
 */
function wpex_get_header_styles_with_sticky_support(): array {
	$styles = [
		'one',
		'five',
		'dev',
		'seven',
		'eight',
		'nine',
		'ten',
		'builder',
	];
	if ( class_exists( 'Total_Sticky_Header_Two_Three_Four' ) ) {
		$styles = array_merge( $styles, [ 'two', 'three', 'four' ] );
	}
	return (array) apply_filters( 'wpex_get_header_styles_with_sticky_support', $styles );

}

/**
 * Returns array of heading styles.
 */
function wpex_get_theme_heading_styles(): array {
	return (array) apply_filters( 'wpex_get_theme_heading_styles', [
		''               => esc_html__( 'Default', 'total' ),
		'plain'          => esc_html__( 'Plain (no styling)', 'total' ),
		'border-side'    => esc_html__( 'Side Border', 'total' ),
		'border-bottom'  => esc_html__( 'Bottom Border', 'total' ),
		'border-w-color' => esc_html__( 'Bottom Border With Color', 'total' ),
	] );
}

/**
 * Returns array of image background styles.
 */
function wpex_get_bg_img_styles(): array {
	return [
		''             => esc_html__( 'Default', 'total' ),
		'cover'        => esc_html__( 'Cover', 'total' ),
		'repeat'       => esc_html__( 'Repeat', 'total' ),
		'no-repeat'    => esc_html__( 'No Repeat', 'total' ),
		'repeat-x'     => esc_html__( 'Repeat-x', 'total' ),
		'repeat-y'     => esc_html__( 'Repeat-y', 'total' ),
		'fixed-top'    => esc_html__( 'Fixed Top', 'total' ),
		'fixed'        => esc_html__( 'Fixed Center', 'total' ),
		'fixed-bottom' => esc_html__( 'Fixed Bottom', 'total' ),
		'inherit'      => esc_html__( 'Inherit', 'total' ),
		'stretched'    => esc_html__( 'Stretched (same as cover)', 'total' ),
	];
}

/**
 * Returns array of dropdown styles.
 */
function wpex_get_menu_dropdown_styles(): array {
	return (array) apply_filters( 'wpex_get_header_menu_dropdown_styles', [
		'default'    => esc_html__( 'Default', 'total' ),
		'minimal-sq' => esc_html__( 'Minimal', 'total' ),
		'minimal'    => esc_html__( 'Minimal - Rounded', 'total' ),
		'black'      => esc_html__( 'Black', 'total' ),
	] );
}

/**
 * Returns array of form styles.
 */
function wpex_get_form_styles(): array {
	return (array) apply_filters( 'wpex_get_form_styles', [
		''        => esc_html__( 'Default', 'total' ),
		'min'     => esc_html__( 'Minimal', 'total' ),
		'gray'    => esc_html__( 'Gray', 'total' ),
		'modern'  => esc_html__( 'Modern', 'total' ),
		'white'   => esc_html__( 'White', 'total' ),
		'black'   => esc_html__( 'Black', 'total' ),
		'white-o' => esc_html__( 'White Outline', 'total' ),
		'black-o' => esc_html__( 'Black Outline', 'total' ),
	] );
}

/**
 * Returns array of page layouts.
 */
function wpex_get_post_layouts(): array {
	return (array) apply_filters( 'wpex_get_post_layouts', [
		''              => esc_html__( 'Default', 'total' ),
		'right-sidebar' => esc_html__( 'Right Sidebar', 'total' ),
		'left-sidebar'  => esc_html__( 'Left Sidebar', 'total' ),
		'full-width'    => esc_html__( 'No Sidebar', 'total' ),
		'full-screen'   => esc_html__( 'Full Screen', 'total' ),
	] );
}

/**
 * Returns array of available post types.
 */
function wpex_get_post_types( $instance = '', $exclude = [] ): array {
	$types = [];
	$get_types = get_post_types( [
		'public' => true,
	], 'objects', 'and' );
	foreach ( $get_types as $key => $val ) {
		if ( ! in_array( $key, $exclude, true ) ) {
			$types[ $key ] = $val->labels->name;
		}
	}
	return (array) apply_filters( 'wpex_get_post_types', $types, $instance );
}

/**
 * User social options.
 */
function wpex_get_user_social_profile_settings_array(): array {
	return (array) apply_filters( 'wpex_get_user_social_profile_settings_array', [
		'x-twitter' => [
			'label' => 'Twitter',
			'name' => 'X',
		],
		'facebook'  => [
			'label' => 'Facebook',
		],
		'linkedin'  => [
			'label' => 'LinkedIn',
		],
		'pinterest' => [
			'label' => 'Pinterest',
		],
		'instagram' => [
			'label' => 'Instagram',
		],
	] );
}

/**
 * Global List Social Link Options.
 */
function wpex_social_profile_options_list(): array {
	$list = [
		'apple-podcasts' => [ 'label' => 'Apple Podcasts' ],
		'google-podcasts' => [ 'label' => 'Google Podcasts' ],
		'behance' => [ 'label' => 'Behance' ],
		'weibo' => [ 'label' => 'Weibo' ],
		'snapchat' => [ 'label' => 'Snapchat' ],
		'facebook' => [ 'label' => 'Facebook' ],
		'pinterest'  => [ 'label' => 'Pinterest' ],
		'discord' => [ 'label' => 'Discord' ],
		'dribbble' => [ 'label' => 'Dribbble' ],
		'etsy'  => [ 'label' => 'Etsy' ],
		'vk' => [ 'label' => 'VK' ],
		'instagram'  => [ 'label' => 'Instagram' ],
		'linkedin' => [ 'label' => 'LinkedIn' ],
		'flickr' => [ 'label' => 'Flickr' ],
		'quora' => [ 'label' => 'Quora' ],
		'skype' => [ 'label' => 'Skype' ],
		'whatsapp' => [ 'label' => 'Whatsapp' ],
		'youtube' => [ 'label' => 'YouTube' ],
		'vimeo' => [ 'label' => 'Vimeo' ],
		'spotify' => [ 'label' => 'Spotify' ],
		'xing' => [ 'label' => 'Xing' ],
		'yelp' => [ 'label' => 'Yelp' ],
		'tiktok' => [ 'label' => 'Tiktok' ],
		'tripadvisor' => [ 'label' => 'Tripadvisor' ],
		'houzz' => [ 'label' => 'Houzz' ],
		'twitch' => [ 'label' => 'Twitch' ],
		'tumblr' => [ 'label' => 'Tumblr' ],
		'github' => [ 'label' => 'Github' ],
		'reddit' => [ 'label' => 'Reddit' ],
		'rss' => [ 'label' => esc_html__( 'RSS', 'total' ) ],
		'trello' => [ 'label' => 'Trello' ],
		'foursquare' => [ 'label' => 'Foursquare' ],
		'renren' => [ 'label' => 'Renren' ],
		'threads' => [ 'label' => 'Threads' ],
		'bluesky' => [ 'label' => 'Bluesky' ],
		'patreon' => [ 'label' => 'Patreon' ],
		'onlyfans' => [ 'label' => 'OnlyFans' ],
		'wechat' => [
			'icon' => 'weixin',
			'label' => 'WeChat',
		],
		'twitter' => [
			'label' => 'Twitter',
			'name' => 'Twitter (deprecated)', // must use it's own name!
			'icon' => 'x-twitter',
		],
		'x-twitter' => [
			'label' => 'Twitter',
			'name' => 'X',
		],
		'email' => [
			'label' => esc_html__( 'Email', 'total' ),
			'icon' => 'envelope',
		],
		'phone' => [
			'label' => esc_html__( 'Phone', 'total' ),
			'icon' => 'phone',
		],
		'website' => [
			'label' => esc_html__( 'Website', 'total' ),
			'icon' => 'link',
		],
	];

	$list = (array) apply_filters( 'wpex_social_profile_options_list', $list );

	ksort( $list );

	return $list;
}

/**
 * Returns array of WP dashicons.
 */
function wpex_get_dashicons_array(): array {
	$dashicons = ['admin-appearance'=>'f100','admin-collapse'=>'f148','admin-comments'=>'f117','admin-generic'=>'f111','admin-home'=>'f102','admin-media'=>'f104','admin-network'=>'f112','admin-page'=>'f133','admin-plugins'=>'f106','admin-settings'=>'f108','admin-site'=>'f319','admin-tools'=>'f107','admin-users'=>'f110','align-center'=>'f134','align-full-width'=>'f114','align-pull-left'=>'f10a','align-pull-right'=>'f10b','align-wide'=>'f11b','align-left'=>'f135','align-none'=>'f138','align-right'=>'f136','analytics'=>'f183','arrow-down'=>'f140','arrow-down-alt'=>'f346','arrow-down-alt2'=>'f347','arrow-left'=>'f141','arrow-left-alt'=>'f340','arrow-left-alt2'=>'f341','arrow-right'=>'f139','arrow-right-alt'=>'f344','arrow-right-alt2'=>'f345','arrow-up'=>'f142','arrow-up-alt'=>'f342','arrow-up-alt2'=>'f343','art'=>'f309','awards'=>'f313','backup'=>'f321','block-default'=>'f12b','button'=>'f11a','book'=>'f330','book-alt'=>'f331','businessman'=>'f338','calendar'=>'f145','camera'=>'f306','cart'=>'f174','category'=>'f318','chart-area'=>'f239','chart-bar'=>'f185','chart-line'=>'f238','chart-pie'=>'f184','clock'=>'f469','cloud'=>'f176','cloud-saved'=>'f137','cloud-upload'=>'f13b','cover-image'=>'f13d','columns'=>'f13c','dashboard'=>'f226','desktop'=>'f472','dismiss'=>'f153','download'=>'f316','edit'=>'f464','editor-aligncenter'=>'f207','editor-alignleft'=>'f206','editor-alignright'=>'f208','editor-bold'=>'f200','editor-customchar'=>'f220','editor-distractionfree'=>'f211','editor-help'=>'f223','editor-indent'=>'f222','editor-insertmore'=>'f209','editor-italic'=>'f201','editor-justify'=>'f214','editor-kitchensink'=>'f212','editor-ol'=>'f204','editor-outdent'=>'f221','editor-paste-text'=>'f217','editor-paste-word'=>'f216','editor-quote'=>'f205','editor-removeformatting'=>'f218','editor-rtl'=>'f320','editor-spellcheck'=>'f210','editor-strikethrough'=>'f224','editor-textcolor'=>'f215','editor-ul'=>'f203','editor-underline'=>'f213','editor-unlink'=>'f225','editor-video'=>'f219','exit'=>'f14a','heading'=>'f10e','html'=>'f14b','info-outline'=>'f14c','insert-after'=>'f14d','insert-before'=>'f14e','insert'=>'f10f','remove'=>'f14f','shortcode'=>'f150','email'=>'f465','email-alt'=>'f466','email-alt2'=>'f467','embed-audio'=>'f13e','embed-photo'=>'f144','embed-post'=>'f146','embed-video'=>'f149','exerpt-view'=>'f164','facebook'=>'f304','facebook-alt'=>'f305','feedback'=>'f175','flag'=>'f227','format-aside'=>'f123','format-audio'=>'f127','format-chat'=>'f125','format-gallery'=>'f161','format-image'=>'f128','format-links'=>'f103','format-quote'=>'f122','format-standard'=>'f109','format-status'=>'f130','format-video'=>'f126','forms'=>'f314','googleplus'=>'f462','groups'=>'f307','hammer'=>'f308','id'=>'f336','id-alt'=>'f337','image-crop'=>'f165','image-flip-horizontal'=>'f169','image-flip-vertical'=>'f168','image-rotate-left'=>'f166','image-rotate-right'=>'f167','images-alt'=>'f232','images-alt2'=>'f233','info'=>'f348','leftright'=>'f229','lightbulb'=>'f339','list-view'=>'f163','location'=>'f230','location-alt'=>'f231','lock'=>'f160','marker'=>'f159','menu'=>'f333','migrate'=>'f310','minus'=>'f460','networking'=>'f325','no'=>'f158','no-alt'=>'f335','performance'=>'f311','plus'=>'f132','portfolio'=>'f322','post-status'=>'f173','pressthis'=>'f157','products'=>'f312','redo'=>'f172','rss'=>'f303','screenoptions'=>'f180','search'=>'f179','share'=>'f237','share-alt'=>'f240','share-alt2'=>'f242','shield'=>'f332','shield-alt'=>'f334','slides'=>'f181','smartphone'=>'f470','smiley'=>'f328','sort'=>'f156','sos'=>'f468','star-empty'=>'f154','star-filled'=>'f155','star-half'=>'f459','tablet'=>'f471','tag'=>'f323','testimonial'=>'f473','translation'=>'f326','trash'=>'f182','twitter'=>'f301','undo'=>'f171','update'=>'f463','upload'=>'f317','vault'=>'f178','video-alt'=>'f234','video-alt2'=>'f235','video-alt3'=>'f236','visibility'=>'f177','welcome-add-page'=>'f133','welcome-comments'=>'f117','welcome-edit-page'=>'f119','welcome-learn-more'=>'f118','welcome-view-site'=>'f115','welcome-widgets-menus'=>'f116','wordpress'=>'f120','wordpress-alt'=>'f324','yes'=>'f147','table-col-after'=>'f151','table-col-before'=>'f152','table-col-delete'=>'f15a','table-row-after'=>'f15b','table-row-before'=>'f15c','table-row-delete'=>'f15d','saved'=>'f15e','database-add'=>'f170','database-export'=>'f17a','database-import'=>'f17b','database-remove'=>'f17c','database-view'=>'f17d','database'=>'f17e','airplane'=>'f15f','car'=>'f16b','calculator'=>'f16e','games'=>'f18a','printer'=>'f193','beer'=>'f16c','coffee'=>'f16f','drumstick'=>'f17f','food'=>'f187','bank'=>'f16a','hourglass'=>'f18c','money-alt'=>'f18e','open-folder'=>'f18f','pdf'=>'f190','pets'=>'f191','privacy'=>'f194','superhero'=>'f198','superhero-alt'=>'f197','edit-page'=>'f186','fullscreen-alt'=>'f188','fullscreen-exit-alt'=>'f189','image-filter'=>'f533','calendar-alt'=>'f508','buddicons-activity'=>'f452','buddicons-friends'=>'f454','buddicons-community'=>'f453','buddicons-forums'=>'f449','buddicons-groups'=>'f456','buddicons-pm'=>'f457','buddicons-replies'=>'f451','buddicons-topics'=>'f450','buddicons-tracking'=>'f455','archive'=>'f480','warning'=>'f534','palmtree'=>'f527','palmtree'=>'f527','album'=>'f514','tickets'=>'f486','tickets-alt'=>'f524','nametag'=>'f486','heart'=>'f487','megaphone'=>'f488','schedule'=>'f489','tide'=>'f10d','code-standards'=>'f13a','universal-access'=>'f483','universal-access-alt'=>'f507','youtube'=>'f19b','reddit'=>'f195','spotify'=>'f196','podio'=>'f19c','clipboard'=>'f481','bell'=>'f16d','businesswoman'=>'f12f','businessperson'=>'f12e','carrot'=>'f511','phone'=>'f525','building'=>'f512','paperclip'=>'f546','color-picker'=>'f131','microphone'=>'f482','editor-code'=>'f475','editor-paragraph'=>'f476','editor-table'=>'f535','ellipsis'=>'f11c','controls-play'=>'f522','controls-volumeon'=>'f521','controls-volumeoff'=>'f520','controls-repeat'=>'f515','media-archive'=>'f501','media-audio'=>'f500','media-code'=>'f499','media-default'=>'f498','media-interactive'=>'f496','media-spreadsheet'=>'f495','media-text'=>'f491','media-video'=>'f490','playlist-audio'=>'f492','playlist-video'=>'f493','filter'=>'f536'];
	return (array) apply_filters( 'wpex_dashicons_array', $dashicons );
}

/**
 * Array of social profiles for staff members.
 */
function wpex_staff_social_array(): array {
	$items = [
		'twitter' => [
			'meta' => 'wpex_staff_twitter',
			'label' => 'X',
			'icon'  => 'x-twitter',
		],
		'facebook' => [
			'meta' => 'wpex_staff_facebook',
			'label' => 'Facebook',
		],
		'instagram' => [
			'meta' => 'wpex_staff_instagram',
			'label' => 'Instagram',
		],
		'linkedin' => [
			'meta' => 'wpex_staff_linkedin',
			'label' => 'Linkedin',
		],
		'dribbble' => [
			'meta' => 'wpex_staff_dribbble',
			'label' => 'Dribbble',
		],
		'vk' => [
			'meta' => 'wpex_staff_vk',
			'label' => 'VK',
		],
		'skype' => [
			'meta' => 'wpex_staff_skype',
			'label' => 'Skype',
		],
		'phone_number' => [
			'meta' => 'wpex_staff_phone_number',
			'icon' => 'phone',
			'label' => esc_html__( 'Phone Number', 'total' ),
		],
		'email' => [
			'meta' => 'wpex_staff_email',
			'icon' => 'envelope',
			'label' => esc_html__( 'Email', 'total' ),
		],
		'website' => [
			'meta' => 'wpex_staff_website',
			'icon' => 'link',
			'label' => esc_html__( 'Website', 'total' ),
		],
	];
	return (array) apply_filters( 'wpex_staff_social_array', $items );
}

/**
 * Creates an array for adding the staff social options to the metaboxes.
 */
function wpex_staff_social_meta_array(): array {
	$array = [];
	foreach ( wpex_staff_social_array() as $k => $v ) {
		$array[] = [
			'title' => $v['label'],
			'id'    => $v['meta'],
			'type'  => 'text',
			'icon'  => $v['icon_class'] ?? $v['svg'] ?? $v['icon'] ?? $k,
		];
	}
	return $array;
}

/**
 * Grid Columns.
 */
function wpex_grid_columns(): array {
	return (array) apply_filters( 'wpex_grid_columns', [
		'1' => '1',
		'2' => '2',
		'3' => '3',
		'4' => '4',
		'5' => '5',
		'6' => '6',
		'7' => '7',
		'8' => '8',
	] );
}

/**
 * Grid Column Gaps.
 */
function wpex_column_gaps(): array {
	return (array) apply_filters( 'wpex_column_gaps', [
		''     => esc_html__( 'Default', 'total' ),
		'none' => '0px',
		'1'    => '1px',
		'5'    => '5px',
		'10'   => '10px',
		'15'   => '15px',
		'20'   => '20px',
		'25'   => '25px',
		'30'   => '30px',
		'35'   => '35px',
		'40'   => '40px',
		'50'   => '50px',
		'60'   => '60px',
	] );
}

/**
 * Typography Styles.
 */
function wpex_typography_styles(): array {
	return (array) apply_filters( 'wpex_typography_styles', [
		''             => esc_html__( 'Default', 'total' ),
		'light'        => esc_html__( 'Light', 'total' ),
		'white'        => esc_html__( 'White', 'total' ),
		'white-shadow' => esc_html__( 'White with Shadow', 'total' ),
		'black'        => esc_html__( 'Black', 'total' ),
		'none'         => esc_html__( 'None', 'total' ),
	] );
}

/**
 * Button styles.
 */
function wpex_button_styles(): array {
	return (array) apply_filters( 'wpex_button_styles', [
		''               => esc_html__( 'Default', 'total' ),
		'flat'           => esc_html__( 'Flat', 'total' ),
		'graphical'      => esc_html__( 'Graphical', 'total' ),
		'clean'          => esc_html__( 'Clean', 'total' ),
		'three-d'        => esc_html__( '3D', 'total' ),
		'outline'        => esc_html__( 'Outline', 'total' ),
		'minimal-border' => esc_html__( 'Minimal Border', 'total' ),
		'plain-text'     => esc_html__( 'Plain Text', 'total' ),
	] );
}

/**
 * Array of image crop locations.
 */
function wpex_image_crop_locations(): array {
	return [
		''              => esc_html__( 'Default (middle)', 'total' ),
		'soft-crop'     => esc_html__( 'Soft Crop (scaled)', 'total' ),
		'left-top'      => esc_html__( 'Top Left', 'total' ),
		'right-top'     => esc_html__( 'Top Right', 'total' ),
		'center-top'    => esc_html__( 'Top Center', 'total' ),
		'left-center'   => esc_html__( 'Center Left', 'total' ),
		'right-center'  => esc_html__( 'Center Right', 'total' ),
		'center-center' => esc_html__( 'Center Center', 'total' ),
		'left-bottom'   => esc_html__( 'Bottom Left', 'total' ),
		'right-bottom'  => esc_html__( 'Bottom Right', 'total' ),
		'center-bottom' => esc_html__( 'Bottom Center', 'total' ),
	];
}

/**
 * Image Hovers.
 */
function wpex_image_hovers(): array {
	return (array) apply_filters( 'wpex_image_hovers', [
		''                 => esc_html__( 'Default', 'total' ),
		'opacity'          => esc_html__( 'Opacity', 'total' ),
		'opacity-invert'   => esc_html__( 'Opacity Invert', 'total' ),
		'shrink'           => esc_html__( 'Shrink', 'total' ),
		'grow'             => esc_html__( 'Grow', 'total' ),
		'side-pan'         => esc_html__( 'Side Pan', 'total' ),
		'vertical-pan'     => esc_html__( 'Vertical Pan', 'total' ),
		'tilt'             => esc_html__( 'Tilt', 'total' ),
		'blurr'            => esc_html__( 'Blurr', 'total' ),
		'blurr-invert'     => esc_html__( 'Blurr Invert', 'total' ),
		'sepia'            => esc_html__( 'Sepia', 'total' ),
		'fade-out'         => esc_html__( 'Fade Out', 'total' ),
		'fade-in'          => esc_html__( 'Fade In', 'total' ),
		'grayscale'        => esc_html__( 'Grayscale', 'total' ),
		'grayscale-invert' => esc_html__( 'Grayscale Invert', 'total' ),
	] );
}

/**
 * Font Weights.
 */
function wpex_font_weights(): array {
	return (array) apply_filters( 'wpex_font_weights', [
		''          => esc_html__( 'Default', 'total' ),
		'normal'    => esc_html__( 'Normal', 'total' ),
		'semibold'  => esc_html__( 'Semibold','total' ),
		'bold'      => esc_html__( 'Bold', 'total' ),
		'extrabold' => esc_html__( 'Extra Bold', 'total' ),
		'bolder'    => esc_html__( 'Black', 'total' ), // this param was always "bolder" so leave that.
		'100'       => '100',
		'200'       => '200',
		'300'       => '300',
		'400'       => '400',
		'500'       => '500',
		'600'       => '600',
		'700'       => '700',
		'800'       => '800',
		'900'       => '900',
	] );
}

/**
 * Array of Hover CSS animations.
 *
 * @todo move to vcex.
 */
function wpex_hover_css_animations(): array {
	return (array) apply_filters( 'wpex_hover_css_animations', [
		''                       => esc_html__( 'Default', 'total' ),
		'shadow'                 => esc_html__( 'Shadow', 'total' ),
		'grow-shadow'            => esc_html__( 'Grow Shadow', 'total' ),
		'float-shadow'           => esc_html__( 'Float Shadow', 'total' ),
		'grow'                   => esc_html__( 'Grow', 'total' ),
		'shrink'                 => esc_html__( 'Shrink', 'total' ),
		'pulse'                  => esc_html__( 'Pulse', 'total' ),
		'pulse-grow'             => esc_html__( 'Pulse Grow', 'total' ),
		'pulse-shrink'           => esc_html__( 'Pulse Shrink', 'total' ),
		'push'                   => esc_html__( 'Push', 'total' ),
		'pop'                    => esc_html__( 'Pop', 'total' ),
		'bounce-in'              => esc_html__( 'Bounce In', 'total' ),
		'bounce-out'             => esc_html__( 'Bounce Out', 'total' ),
		'rotate'                 => esc_html__( 'Rotate', 'total' ),
		'grow-rotate'            => esc_html__( 'Grow Rotate', 'total' ),
		'float'                  => esc_html__( 'Float', 'total' ),
		'sink'                   => esc_html__( 'Sink', 'total' ),
		'bob'                    => esc_html__( 'Bob', 'total' ),
		'hang'                   => esc_html__( 'Hang', 'total' ),
		'skew'                   => esc_html__( 'Skew', 'total' ),
		'skew-backward'          => esc_html__( 'Skew Backward', 'total' ),
		'wobble-horizontal'      => esc_html__( 'Wobble Horizontal', 'total' ),
		'wobble-vertical'        => esc_html__( 'Wobble Vertical', 'total' ),
		'wobble-to-bottom-right' => esc_html__( 'Wobble To Bottom Right', 'total' ),
		'wobble-to-top-right'    => esc_html__( 'Wobble To Top Right', 'total' ),
		'wobble-top'             => esc_html__( 'Wobble Top', 'total' ),
		'wobble-bottom'          => esc_html__( 'Wobble Bottom', 'total' ),
		'wobble-skew'            => esc_html__( 'Wobble Skew', 'total' ),
		'buzz'                   => esc_html__( 'Buzz', 'total' ),
		'buzz-out'               => esc_html__( 'Buzz Out', 'total' ),
		'glow'                   => esc_html__( 'Glow', 'total' ),
		'shadow-radial'          => esc_html__( 'Shadow Radial', 'total' ),
		'box-shadow-outset'      => esc_html__( 'Box Shadow Outset', 'total' ),
		'box-shadow-inset'       => esc_html__( 'Box Shadow Inset', 'total' ),
	] );
}

/**
 * Image filter styles.
 *
 * @todo move to vcex
 */
function wpex_image_filters(): array {
	return (array) apply_filters( 'wpex_image_filters', [
		''             => esc_html__( 'None', 'total' ),
		'grayscale'    => esc_html__( 'Grayscale', 'total' ),
		'sepia'        => esc_html__( 'Sepia', 'total' ),
		'contrast-150' => esc_html__( 'High Contrast', 'total' ),
		'saturate-2'   => esc_html__( 'Saturate', 'total' ),
	] );
}

/**
 * Social Link styles.
 */
function wpex_social_button_styles(): array {
	return (array) apply_filters( 'wpex_social_button_styles', [
		'default'            => esc_html__( 'Default', 'total' ),
		'none'               => esc_html__( 'None', 'total' ),
		'colored'            => esc_html__( 'Colored', 'total' ),
		'accent'             => esc_html__( 'Accent', 'total' ),
		'accent-rounded'     => esc_html__( 'Accent Rounded', 'total' ),
		'accent-round'       => esc_html__( 'Accent Round', 'total' ),
		'minimal'            => esc_html__( 'Minimal', 'total' ),
		'minimal-rounded'    => esc_html__( 'Minimal Rounded', 'total' ),
		'minimal-round'      => esc_html__( 'Minimal Round', 'total' ),
		'flat'               => esc_html__( 'Flat', 'total' ),
		'flat-rounded'       => esc_html__( 'Flat Rounded', 'total' ),
		'flat-round'         => esc_html__( 'Flat Round', 'total' ),
		'flat-color'         => esc_html__( 'Flat Color', 'total' ),
		'flat-color-rounded' => esc_html__( 'Flat Color Rounded', 'total' ),
		'flat-color-round'   => esc_html__( 'Flat Color Round', 'total' ),
		'3d'                 => esc_html__( '3D', 'total' ),
		'3d-color'           => esc_html__( '3D Color', 'total' ),
		'black'              => esc_html__( 'Black', 'total' ),
		'black-rounded'      => esc_html__( 'Black Rounded', 'total' ),
		'black-round'        => esc_html__( 'Black Round', 'total' ),
		'black-ch'           => esc_html__( 'Black with Color Hover', 'total' ),
		'black-ch-rounded'   => esc_html__( 'Black with Color Hover Rounded', 'total' ),
		'black-ch-round'     => esc_html__( 'Black with Color Hover Round', 'total' ),
		'graphical'          => esc_html__( 'Graphical', 'total' ),
		'graphical-rounded'  => esc_html__( 'Graphical Rounded', 'total' ),
		'graphical-round'    => esc_html__( 'Graphical Round', 'total' ),
		'bordered'           => esc_html__( 'Bordered', 'total' ),
		'bordered-rounded'   => esc_html__( 'Bordered Rounded', 'total' ),
		'bordered-round'     => esc_html__( 'Bordered Round', 'total' ),
	] );
}

/**
 * Get social share items array used for Customizer setting and front-end output.
 */
function wpex_social_share_items(): array {
	$items = [
        'x-twitter' => [
            'label'       => 'Twitter',
            'site'        => 'X',
            'reader_text' => esc_html__( 'Post on X', 'total' ),
        ],
        'facebook' => [
            'label'       => 'Facebook',
            'site'        => 'Facebook',
            'reader_text' => esc_html__( 'Share on Facebook', 'total' ),
        ],
        'pinterest' => [
            'label'       => 'Pinterest',
            'site'        => 'Pinterest',
            'reader_text' => esc_html__( 'Share on Pinterest', 'total' ),
        ],
        'linkedin' => [
            'label'       => 'LinkedIn',
            'site'        => 'LinkedIn',
            'reader_text' => esc_html__( 'Share on LinkedIn', 'total' ),
        ],
        'reddit' => [
            'label'       => 'Reddit',
            'site'        => 'Reddit',
            'reader_text' => esc_html__( 'Post on Reddit', 'total' ),
        ],
        'whatsapp' => [
            'label'       => 'Whatsapp',
            'site'        => 'Whatsapp',
            'reader_text' => esc_html__( 'Share via Whatsapp', 'total' ),
        ],
        'telegram' => [
            'label'       => 'Telegram',
            'site'        => 'Telegram',
            'reader_text' => esc_html__( 'Share via Telegram', 'total' ),
        ],
		'sms' => [
            'label'       => esc_html__( 'Message', 'total' ),
            'site'        => 'SMS',
            'reader_text' => esc_html__( 'Share via text message', 'total' ),
        ],
        'print' => [
            'label'       => esc_html__( 'Print', 'total' ),
            'site'        => 'Print',
            'reader_text' => esc_html__( 'Print', 'total' ),
        ],
        'email' => [
            'icon'        => 'envelope',
            'label'       => esc_html__( 'Email', 'total' ),
            'site'        => 'Email',
            'reader_text' => esc_html__( 'Share via Email', 'total' ),
        ],
		'twitter' => [
            'label'       => 'Twitter',
            'site'        => 'Twitter (deprecated)',
			'icon'        => 'x-twitter',
            'reader_text' => esc_html__( 'Post on X', 'total' ),
        ],
    ];
    $items = apply_filters( 'wpex_get_social_items', $items ); // @deprecated
    return (array) apply_filters( 'wpex_social_share_items', $items );
}

/**
 * Return an array of theme defined widget areas.
 */
function wpex_choices_widget_areas(): array {
	$widget_areas = [
		'' => esc_html__( 'Default', 'total' )
	];
	if ( function_exists( 'wpex_get_widget_areas' ) ) {
		$widget_areas = array_merge( $widget_areas, wpex_get_widget_areas() );
	}
	return $widget_areas;
}

/**
 * Visibility Choices.
 */
function totaltheme_get_visibility_choices( bool $group_items = true ): array {
	$choices = [
		'main' => [
			'label'   => \esc_html__( 'Main', 'total' ),
			'choices' => [
				''       => esc_html__( 'Always Visible', 'total' ),
				'hidden' => esc_html__( 'Always Hidden', 'total' ),
			],
		],
		'mobile_menu' => [
			'label'   => \esc_html__( 'Mobile Menu', 'total' ),
			'choices' => [
				'show-at-mm-breakpoint' => esc_html__( 'Visible At Mobile Menu Breakpoint', 'total' ),
				'hide-at-mm-breakpoint' => esc_html__( 'Hidden At Mobile Menu Breakpoint', 'total' ),
			],
		],
		'responsive' => [
			'label'   => \esc_html__( 'Responsive', 'total' ),
			'choices' => [
				// Desktop Large
				'visible-desktop-large' => esc_html__( 'Visible on Large Desktops (1280px or greater)', 'total' ),
				'hidden-desktop-large'  => esc_html__( 'Hidden on Large Desktops (1280px or greater)', 'total' ),
				// Desktop
				'visible-desktop'       => esc_html__( 'Visible on Desktop (1024px or greater)', 'total' ),
				'hidden-desktop'        => esc_html__( 'Hidden on Desktop (1024px or greater)', 'total' ),
				// Phone
				'visible-phone'         => esc_html__( 'Visible on Phones (767px or smaller)', 'total' ),
				'hidden-phone'          => esc_html__( 'Hidden on Phones (767px or smaller)', 'total' ),
				// Phone small
				'visible-phone-small'   => esc_html__( 'Visible on Small Phones (639px or smaller)', 'total' ),
				'hidden-phone-small'    => esc_html__( 'Hidden on Small Phones (639px or smaller)', 'total' ),
			],
		],
		'toggles' => [
			'label'   => \esc_html__( 'Toggles', 'total' ),
			'choices' => [
				'hidden-toggle-element'          => esc_html__( 'Hidden Toggle Element', 'total' ),
				'visible-toggle-element'         => esc_html__( 'Visible Toggle Element', 'total' ),
				'hidden-toggle-element-persist'  => esc_html__( 'Persistent Hidden Toggle Element', 'total' ),
				'visible-toggle-element-persist' => esc_html__( 'Persistent Visible Toggle Element', 'total' ),
			],
		],
		/* Only useful with JS sticky - can cause confusion.
		'sticky' => [
			'label'   => \esc_html__( 'Sticky', 'total' ),
			'choices' => [
				'visible-stuck' => esc_html__( 'Visible when Sticky', 'total' ),
				'hidden-stuck' => esc_html__( 'Hidden when Sticky', 'total' ),
			],
		],*/
	];

	$custom_choices = (array) apply_filters_deprecated( 'wpex_visibility', [ [] ], '6.0' );

	if ( $custom_choices ) {
		$choices['custom'] = [
			'label'   => \esc_attr__( 'Custom', 'total' ),
			'choices' => $custom_choices,
		];
	}

	$choices['deprecated'] = [
		'label'   => \esc_attr__( 'Deprecated', 'total' ),
		'choices' => [
			'visible-tablet' => esc_html__( 'Visible on Tablets (768px to 1023px)', 'total' ),
			'hidden-tablet'  => esc_html__( 'Hidden on Tablets (768px to 1023px)', 'total' ),
		],
	];

	if ( \totaltheme_call_static( 'Header\Core', 'is_custom' ) ) {
		unset( $choices['mobile_menu'] );
	}

	return $group_items ? $choices : \array_merge( ...\array_column( $choices, 'choices' ) );
}

/**
 * Aspect ratio choices.
 */
function totaltheme_get_aspect_ratio_choices(): array {
	return [
		''     => \esc_html__( 'Default', 'total' ),
		'1/1'  => \esc_html__( 'Square - 1:1', 'total' ),
		'4/3'  => \esc_html__( 'Standard - 4:3', 'total' ),
		'3/4'  => \esc_html__( 'Portrait - 3:4', 'total' ),
		'3/2'  => \esc_html__( 'Classic - 3:2', 'total' ),
		'2/3'  => \esc_html__( 'Classic Portrait - 2:3', 'total' ),
		'16/9' => \esc_html__( 'Wide - 16:9', 'total' ),
		'9/16' => \esc_html__( 'Tall - 9:16', 'total' ),
	];
}
