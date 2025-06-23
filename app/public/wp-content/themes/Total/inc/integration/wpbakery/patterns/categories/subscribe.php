<?php

defined( 'ABSPATH' ) || exit;

$patterns['subscribe-1'] = [];
$patterns['subscribe-1']['name'] = esc_html__( 'Subscribe', 'total' ) . ' 1';
$patterns['subscribe-1']['category'] = 'subscribe';
$patterns['subscribe-1']['content'] = <<<CONTENT
[vc_row remove_bottom_col_margin="true" el_class="wpex-py-60"][vc_column][vcex_heading text="Join our Newsletter" text_align="center" tag="h2" bottom_margin="20px"][vc_column_text width="650px" align="center" text_align="center" bottom_margin="35px"]Subscribe to the newsletter to receive our latest tips, tricks, tutorials and news directly in your inbox. We will not spam and you can cancel at anytime.[/vc_column_text][vcex_newsletter_form input_align="center" fullwidth_mobile="true" input_width="550px"][/vc_column][/vc_row]
CONTENT;

$patterns['subscribe-2'] = [];
$patterns['subscribe-2']['name'] = esc_html__( 'Subscribe', 'total' ) . ' 2';
$patterns['subscribe-2']['category'] = 'subscribe';
$patterns['subscribe-2']['content'] = <<<CONTENT
[vc_row full_width="stretch_row" remove_bottom_col_margin="true" el_class="wpex-py-60" wpex_bg_color="#3858e9"][vc_column][vcex_heading text="Join our Newsletter" text_align="center" tag="h2" color="#fff" el_class="wpex-align-top" bottom_margin="20px"][vc_column_text width="650px" align="center" text_align="center" bottom_margin="35px" color="rgba(255,255,255,0.7)"]Subscribe to the newsletter to receive our latest tips, tricks, tutorials and news directly in your inbox. We will not spam and you can cancel at anytime.[/vc_column_text][vcex_newsletter_form input_align="center" fullwidth_mobile="true" gap="15px" input_width="550px" submit_bg="#222" submit_color="#fff" input_border="0px" input_bg="#ffffff"][/vc_column][/vc_row]
CONTENT;

$patterns['subscribe-3'] = [];
$patterns['subscribe-3']['name'] = esc_html__( 'Subscribe', 'total' ) . ' 3';
$patterns['subscribe-3']['category'] = 'subscribe';
$patterns['subscribe-3']['content'] = <<<CONTENT
[vc_row content_placement="middle" el_class="wpex-py-60" remove_bottom_col_margin="true" column_spacing="60"][vc_column width="1/2"][vcex_heading text="Join the Newsletter" tag="h2" bottom_margin="15px"][vc_column_text bottom_margin="30px"]Subscribe to the newsletter to receive our latest tips, tricks, tutorials and news directly in your inbox. We will not spam and you can cancel at anytime.[/vc_column_text][vcex_newsletter_form fullwidth_mobile="true"][/vc_column][vc_column width="1/2"][vcex_image el_class="wpex-md-text-right" source="external" external_image="{$ph_landscape}"][/vc_column][/vc_row]
CONTENT;

$patterns['subscribe-4'] = [];
$patterns['subscribe-4']['name'] = esc_html__( 'Subscribe', 'total' ) . ' 4';
$patterns['subscribe-4']['category'] = 'subscribe';
$patterns['subscribe-4']['content'] = <<<CONTENT
[vc_row full_width="stretch_row" remove_bottom_col_margin="true" el_class="wpex-surface-2 wpex-py-60"][vc_column][vcex_flex_container shadow="shadow-xl" flex_direction="column" el_class="wpex-p-50 wpex-surface-1 wpex-rounded" gap="0px" width="900px"][vcex_heading text="Join Our Newsletter" bottom_margin="20px" text_align="center" tag="h2"][vc_column_text bottom_margin="30px" width="650px" align="center" text_align="center"]Subscribe to the newsletter to receive our latest tips, tricks, tutorials and news directly in your inbox. We will not spam and you can cancel at anytime.[/vc_column_text][vcex_newsletter_form input_align="center" fullwidth_mobile="true" gap="10px" placeholder_text="email@example.com" submit_border_radius="rounded" input_border_radius="rounded" input_width="550px" submit_letter_spacing="wider"][/vcex_flex_container][/vc_column][/vc_row]
CONTENT;

$patterns['subscribe-5'] = [];
$patterns['subscribe-5']['name'] = esc_html__( 'Subscribe', 'total' ) . ' 5';
$patterns['subscribe-5']['category'] = 'subscribe';
$patterns['subscribe-5']['content'] = <<<CONTENT
[vc_row][vc_column][vcex_flex_container flex_direction="column" el_class="wpex-surface-1 wpex-rounded wpex-border wpex-border-solid wpex-border-surface-4 wpex-p-40" gap="0px" width="480px"][vcex_icon icon="ticon ticon-envelope-open-o" bottom_margin="20px" align="center" custom_size="45px" el_class="wpex-leading-none"][vcex_heading text="Join Our Newsletter" bottom_margin="15px" text_align="center" tag="h2" font_size="xl" font_weight="600"][vc_column_text text_align="center" css=".vc_custom_1626979426054{margin-bottom: 30px !important;}"]Subscribe to our newsletter for the latest news and products straight to your inbox.[/vc_column_text][vc_column_text font_weight="bold" css=".vc_custom_1626979441732{margin-bottom: 5px !important;}"]Email Address<sup style="color: #ff0000;">*</sup>[/vc_column_text][vcex_newsletter_form stack_fields="true" placeholder_text="you@example.com" submit_text="Subscribe" submit_border_radius="rounded-sm" input_border_radius="rounded-sm" input_height="auto" input_padding="12px" bottom_margin="15px"][vc_column_text width="650px" align="center" text_align="center" el_class="wpex-text-3"]We respect your privacy. No spam![/vc_column_text][/vcex_flex_container][/vc_column][/vc_row]
CONTENT;

$patterns['subscribe-6'] = [];
$patterns['subscribe-6']['name'] = esc_html__( 'Subscribe', 'total' ) . ' 6';
$patterns['subscribe-6']['category'] = 'subscribe';
$patterns['subscribe-6']['content'] = <<<CONTENT
[vc_row full_width="stretch_row" remove_bottom_col_margin="true" wpex_bg_color="#262626" el_class="wpex-py-60"][vc_column][vcex_flex_container flex_grow="true" flex_wrap="true" flex_direction="row" flex_basis="480px,auto" gap="30px"][vc_column_text color="#fff" font_size="xl" el_class="wpex-font-serif"]Join over 50,000 entrepreneurs and freelancers and take your business to the next level.[/vc_column_text][vcex_newsletter_form gap="10px" placeholder_text="email@example.com" submit_text="Subscribe" input_border_radius="rounded-sm" submit_border_radius="rounded-sm" input_bg="transparent" input_border="2px solid #FCFCFC3B" submit_weight="bold" input_color="#fff"][/vcex_flex_container][/vc_column][/vc_row]
CONTENT;
// IMPORTANT - Space required to prevent HEREDOC errors.
