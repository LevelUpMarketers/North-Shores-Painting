<?php

defined( 'ABSPATH' ) || exit;

$patterns['contact-1'] = [];
$patterns['contact-1']['name'] = esc_html__( 'Contact', 'total' ) . ' 1';
$patterns['contact-1']['category'] = 'contact';
$patterns['contact-1']['content'] = <<<CONTENT
[vc_row content_placement="middle" column_spacing="50" el_class="wpex-pt-50"][vc_column width="1/2"][vc_gmaps][/vc_column][vc_column width="1/2"][vcex_heading text_balance="true" text="Feel free to contact us for any inquiry, we will get back to you asap." bottom_margin="30px" font_size="2xl" font_weight="bold" tag="h2"][vc_column_text bottom_margin="30px"]Storey Ave, San Francisco, CA 94129
<strong>Email:</strong> email@example.com
<strong>Phone:</strong> 202-555-0119[/vc_column_text][vcex_social_links social_links="%5B%7B%22site%22%3A%22twitter%22%2C%22link%22%3A%22%23%22%7D%2C%7B%22site%22%3A%22facebook%22%2C%22link%22%3A%22%23%22%7D%2C%7B%22site%22%3A%22linkedin%22%2C%22link%22%3A%22%23%22%7D%5D" spacing="10px" style="flat-color-round" size="16px" width="35px" height="35px"][/vc_column][/vc_row]
CONTENT;

$patterns['contact-2'] = [];
$patterns['contact-2']['name'] = esc_html__( 'Contact', 'total' ) . ' 2';
$patterns['contact-2']['category'] = 'contact';
$patterns['contact-2']['content'] = <<<CONTENT
[vc_row full_width="stretch_row" remove_bottom_col_margin="true" wpex_bg_color="#262626" el_class="wpex-py-60"][vc_column][vcex_flex_container align_items="center" justify_content="space-between" flex_grow="true" flex_wrap="true" flex_direction="row" gap="40px"][vcex_icon_box heading="Office Location" icon="map-marker" heading_size="lg" heading_color="#fff" icon_color="#fff" icon_size="40px" font_color="#FFFFFF99" font_size="lg" heading_bottom_margin="0px"]Storey Ave, San Francisco, CA 94129[/vcex_icon_box][vcex_icon_box heading="Phone Number" icon="phone" heading_size="lg" heading_color="#fff" icon_color="#fff" icon_size="40px" font_color="#FFFFFF99" font_size="lg" heading_bottom_margin="0px"]1-800-202-555-0119[/vcex_icon_box][vcex_icon_box heading="Send a Message" icon="envelope-o" heading_size="lg" heading_color="#fff" icon_color="#fff" icon_size="40px" font_color="#FFFFFF99" font_size="lg" heading_bottom_margin="0px"]email@example.com[/vcex_icon_box][/vcex_flex_container][/vc_column][/vc_row]
CONTENT;

$patterns['contact-3'] = [];
$patterns['contact-3']['name'] = esc_html__( 'Contact', 'total' ) . ' 3';
$patterns['contact-3']['category'] = 'contact';
$patterns['contact-3']['content'] = <<<CONTENT
[vc_row full_width="stretch_row" content_placement="middle" column_spacing="50" el_class="wpex-surface-2 wpex-pt-50"][vc_column width="1/2"][vcex_heading text_balance="true" text="Feel free to contact us for any inquiry, we will get back to you asap." bottom_margin="30px" font_size="2xl" font_weight="bold" tag="h2"][vc_column_text bottom_margin="30px"]Storey Ave, San Francisco, CA 94129
<strong>Email:</strong> email@example.com
<strong>Phone:</strong> 202-555-0119[/vc_column_text][vcex_social_links social_links="%5B%7B%22site%22%3A%22twitter%22%2C%22link%22%3A%22%23%22%7D%2C%7B%22site%22%3A%22facebook%22%2C%22link%22%3A%22%23%22%7D%2C%7B%22site%22%3A%22linkedin%22%2C%22link%22%3A%22%23%22%7D%5D" spacing="10px" style="flat-color-round" size="16px" width="35px" height="35px"][/vc_column][vc_column width="1/2"][vcex_contact_form el_class="wpex-surface-1 wpex-rounded-lg wpex-p-40" enable_placeholders="true" stack_fields="true" enable_privacy_check="false" enable_required_label="false" shadow="shadow-lg" button_text="Send message"][/vc_column][/vc_row]
CONTENT;

$patterns['contact-4'] = [];
$patterns['contact-4']['name'] = esc_html__( 'Contact', 'total' ) . ' 4';
$patterns['contact-4']['category'] = 'contact';
$patterns['contact-4']['content'] = <<<CONTENT
[vc_row content_placement="middle" remove_bottom_col_margin="true" el_class="wpex-py-60" column_spacing="50"][vc_column width="1/2"][vcex_heading text="Contact us" bottom_margin="20px" font_size="2xl" font_weight="bold"][vc_column_text bottom_margin="40px"]Feel free to send us a message, give us a call or stop by and say hi. Our group of friendly staff is here to help.[/vc_column_text][vcex_icon_box bottom_margin="40px" heading="Office Location" icon="map-marker" heading_size="lg" heading_bottom_margin="0px" icon_size="20px" icon_color="#454545" icon_background="#fdecb9" icon_border_radius="50%" icon_width="45px" icon_height="45px"]Storey Ave, San Francisco, CA 94129[/vcex_icon_box][vcex_icon_box bottom_margin="40px" heading="Phone Number" icon="phone" heading_size="lg" icon_size="20px" heading_bottom_margin="0px" icon_width="45px" icon_height="45px" icon_background="#fdecb9" icon_border_radius="50%" icon_color="#424242"]1-800-202-555-0119[/vcex_icon_box][vcex_icon_box bottom_margin="40px" heading="Send a Message" icon="envelope" heading_size="lg" icon_size="20px" heading_bottom_margin="0px" icon_color="#454545" icon_background="#fdecb9" icon_border_radius="50%" icon_width="45px" icon_height="45px"]email@example.com[/vcex_icon_box][/vc_column][vc_column width="1/2"][vc_gmaps link="#E-8_JTNDaWZyYW1lJTIwc3JjJTNEJTIyaHR0cHMlM0ElMkYlMkZ3d3cuZ29vZ2xlLmNvbSUyRm1hcHMlMkZlbWJlZCUzRnBiJTNEJTIxMW0xOCUyMTFtMTIlMjExbTMlMjExZDYzMDQuODI5OTg2MTMxMjcxJTIxMmQtMTIyLjQ3NDY5NjgwMzMwOTIlMjEzZDM3LjgwMzc0NzUyMTYwNDQzJTIxMm0zJTIxMWYwJTIxMmYwJTIxM2YwJTIxM20yJTIxMWkxMDI0JTIxMmk3NjglMjE0ZjEzLjElMjEzbTMlMjExbTIlMjExczB4ODA4NTg2ZTYzMDI2MTVhMSUyNTNBMHg4NmJkMTMwMjUxNzU3YzAwJTIxMnNTdG9yZXklMkJBdmUlMjUyQyUyQlNhbiUyQkZyYW5jaXNjbyUyNTJDJTJCQ0ElMkI5NDEyOSUyMTVlMCUyMTNtMiUyMTFzZW4lMjEyc3VzJTIxNHYxNDM1ODI2NDMyMDUxJTIyJTIwd2lkdGglM0QlMjI2MDAlMjIlMjBoZWlnaHQlM0QlMjI0NTAlMjIlMjBmcmFtZWJvcmRlciUzRCUyMjAlMjIlMjBzdHlsZSUzRCUyMmJvcmRlciUzQTAlMjIlMjBhbGxvd2Z1bGxzY3JlZW4lM0UlM0MlMkZpZnJhbWUlM0U="][/vc_column][/vc_row]
CONTENT;

$patterns['contact-5'] = [];
$patterns['contact-5']['name'] = esc_html__( 'Contact', 'total' ) . ' 5';
$patterns['contact-5']['category'] = 'contact';
$patterns['contact-5']['content'] = <<<CONTENT
[vc_row remove_bottom_col_margin="true" el_class="wpex-py-60"][vc_column][vcex_heading text="Get in Touch" text_align="center" font_size="5xl" font_weight="bold" bottom_margin="50px"][vcex_grid_container gap="60px"][vcex_teaser heading="For general inquiries:" heading_type="h3" css=".vc_custom_1627331652679{border-top-width: 4px !important;padding-top: 20px !important;border-top-color: #7ce89f !important;border-top-style: solid !important;}" heading_size="24px" heading_weight="normal"]support@example.com[/vcex_teaser][vcex_teaser heading="For press and marketing:" heading_type="h3" css=".vc_custom_1627331690270{border-top-width: 4px !important;padding-top: 20px !important;border-top-color: #7ad9ff !important;border-top-style: solid !important;}" heading_size="24px" heading_weight="normal"]media@example.com[/vcex_teaser][vcex_teaser heading="Our Address:" heading_type="h3" css=".vc_custom_1627331701057{border-top-width: 4px !important;padding-top: 20px !important;border-top-color: #768af7 !important;border-top-style: solid !important;}" heading_size="24px" heading_weight="normal"]Storey Ave, San Francisco, CA 94129[/vcex_teaser][/vcex_grid_container][/vc_column][/vc_row]
CONTENT;

$patterns['contact-6'] = [];
$patterns['contact-6']['name'] = esc_html__( 'Contact', 'total' ) . ' 6';
$patterns['contact-6']['category'] = 'contact';
$patterns['contact-6']['content'] = <<<CONTENT
[vc_row column_spacing="50" el_class="wpex-mt-50"][vc_column width="3/5"][vcex_image source="external" external_image="{$ph_landscape}" bottom_margin="30px"][vcex_flex_container align_items="center" justify_content="space-between" flex_wrap="true" flex_direction="row"][vcex_icon_box heading="Press Inquiries:" icon="" heading_bottom_margin="0px" heading_size="lg" heading_weight="600"]press@example.com[/vcex_icon_box][vcex_icon_box heading="Phone Support:" icon="" heading_bottom_margin="0px" heading_size="lg" heading_weight="600"]1-800-202-555-0119[/vcex_icon_box][vcex_icon_box heading="Our Address:" icon="" heading_bottom_margin="0px" heading_size="lg" heading_weight="600"]Storey Ave, San Francisco, CA 94129[/vcex_icon_box][/vcex_flex_container][/vc_column][vc_column width="2/5"][vcex_heading text="Get in Touch" text_balance="true" bottom_margin="20px"][vc_column_text bottom_margin="30px"]Have an inquiry or feedback for us? Fill out the form below to contact our team and we'll get back to you asap.[/vc_column_text][vcex_contact_form enable_placeholders="true" stack_fields="true" enable_privacy_check="false" message_rows="4" label_required="(required)"][/vc_column][/vc_row]
CONTENT;

$patterns['contact-7'] = [];
$patterns['contact-7']['name'] = esc_html__( 'Contact', 'total' ) . ' 7';
$patterns['contact-7']['category'] = 'contact';
$patterns['contact-7']['content'] = <<<CONTENT
[vc_row remove_bottom_col_margin="true" el_class="wpex-py-60"][vc_column][vcex_heading text="Got questions?" font_weight="800" font_size="5xl" text_align="center" bottom_margin="20px"][vc_column_text bottom_margin="40px" width="700px" align="center" text_align="center"Please contact one of our support departments for support or to report an issue.[/vc_column_text][vcex_grid_container columns_lg="" columns_md="3" gap="25px"][vcex_icon_box heading="Customization" style="five" padding_x="40px" padding_y="60px" shadow="shadow-xs" icon="paint-brush" icon_border_width="2" url_wrap="true" onclick="custom_link" heading_type="h2" heading_weight="bold" heading_size="lg" onclick_url="#" border_radius="rounded-sm" icon_color="accent" icon_border_radius="rounded-full" icon_width="45px" icon_height="45px" icon_size="20px"]Get in touch with a designer for help with product customization.[/vcex_icon_box][vcex_icon_box heading="Hosting" style="five" padding_x="40px" padding_y="60px" shadow="shadow-xs" icon="database" icon_border_width="2" url_wrap="true" onclick="custom_link" el_class="wpex-surface-3" heading_type="h2" heading_weight="bold" heading_size="lg" onclick_url="#" border_radius="rounded-sm" icon_color="accent" icon_border_radius="rounded-full" icon_width="45px" icon_height="45px" icon_size="20px"]If you are having problems with your site hosting please report issues here.[/vcex_icon_box][vcex_icon_box heading="Refunds" style="five" padding_x="40px" padding_y="60px" shadow="shadow-xs" icon="usd" icon_border_width="2" url_wrap="true" onclick="custom_link" el_class="wpex-surface-3" heading_type="h2" heading_weight="bold" heading_size="lg" onclick_url="#" border_radius="rounded-sm" icon_color="accent" icon_border_radius="rounded-full" icon_width="45px" icon_height="45px" icon_size="20px"]Need a refund? Contact our refund department for assistance.[/vcex_icon_box][/vcex_grid_container][/vc_column][/vc_row]
CONTENT;

$patterns['contact-8'] = [];
$patterns['contact-8']['name'] = esc_html__( 'Contact', 'total' ) . ' 8';
$patterns['contact-8']['category'] = 'contact';
$patterns['contact-8']['content'] = <<<CONTENT
[vc_row el_class="wpex-py-60"][vc_column][vcex_heading text="Our offices" font_size="3xl" text_align="left" tag="h2"][vc_column_text bottom_margin="30px"]We currently have three locations in the USA.[/vc_column_text][vcex_grid_container columns_lg="" columns_md="3"][vcex_teaser style="four" heading="California" heading_type="h3" image_source="external" img_bottom_margin="0px" external_image="{$ph_square}" content_padding="top:30px|right:30px|bottom:30px|left:30px"]hello-california@gmail.com
+1 800 123 456[/vcex_teaser][vcex_teaser style="four" heading="New York" heading_type="h3" image_source="external" img_bottom_margin="0px" external_image="{$ph_square}" content_padding="top:30px|right:30px|bottom:30px|left:30px"]hello-newyork@gmail.com
+1 800 123 456[/vcex_teaser][vcex_teaser style="four" heading="Texas" heading_type="h3" image_source="external" img_bottom_margin="0px" external_image="{$ph_square}" content_padding="top:30px|right:30px|bottom:30px|left:30px"]hello-texas@gmail.com
+1 800 123 456[/vcex_teaser][/vcex_grid_container][/vc_column][/vc_row]
CONTENT;

$patterns['contact-9'] = [];
$patterns['contact-9']['name'] = esc_html__( 'Contact', 'total' ) . ' 9';
$patterns['contact-9']['category'] = 'contact';
$patterns['contact-9']['content'] = <<<CONTENT
[vc_section el_class="wpex-py-60"][vc_row content_placement="middle"][vc_column width="3/5"][vcex_heading text_balance="true" text="Feel free to contact us.`{`br`}`We are here to help!" font_weight="800" font_size="3em"][/vc_column][vc_column width="2/5"][vcex_heading text="Address" bottom_margin="10px" tag="h3" font_size="lg" font_weight="bold"][vc_column_text bottom_margin="30px"]Storey Ave, San Francisco, CA 94129[/vc_column_text][vcex_heading text="Email" bottom_margin="10px" tag="h3" font_size="lg" font_weight="bold"][vc_column_text]hello-example@gmail.com[/vc_column_text][/vc_column][/vc_row][vc_row full_width="stretch_row_content_no_spaces"][vc_column][vc_gmaps link="#E-8_JTNDaWZyYW1lJTIwc3JjJTNEJTIyaHR0cHMlM0ElMkYlMkZ3d3cuZ29vZ2xlLmNvbSUyRm1hcHMlMkZlbWJlZCUzRnBiJTNEJTIxMW0xOCUyMTFtMTIlMjExbTMlMjExZDYzMDQuODI5OTg2MTMxMjcxJTIxMmQtMTIyLjQ3NDY5NjgwMzMwOTIlMjEzZDM3LjgwMzc0NzUyMTYwNDQzJTIxMm0zJTIxMWYwJTIxMmYwJTIxM2YwJTIxM20yJTIxMWkxMDI0JTIxMmk3NjglMjE0ZjEzLjElMjEzbTMlMjExbTIlMjExczB4ODA4NTg2ZTYzMDI2MTVhMSUyNTNBMHg4NmJkMTMwMjUxNzU3YzAwJTIxMnNTdG9yZXklMkJBdmUlMjUyQyUyQlNhbiUyQkZyYW5jaXNjbyUyNTJDJTJCQ0ElMkI5NDEyOSUyMTVlMCUyMTNtMiUyMTFzZW4lMjEyc3VzJTIxNHYxNDM1ODI2NDMyMDUxJTIyJTIwd2lkdGglM0QlMjI2MDAlMjIlMjBoZWlnaHQlM0QlMjI0NTAlMjIlMjBmcmFtZWJvcmRlciUzRCUyMjAlMjIlMjBzdHlsZSUzRCUyMmJvcmRlciUzQTAlMjIlMjBhbGxvd2Z1bGxzY3JlZW4lM0UlM0MlMkZpZnJhbWUlM0U=" size="300px"][/vc_column][/vc_row][/vc_section]
CONTENT;
// IMPORTANT - Space required to prevent HEREDOC errors.
