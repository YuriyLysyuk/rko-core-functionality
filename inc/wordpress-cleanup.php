<?php
/**
 * WordPress Cleanup
 *
 * @package      RKOCoreFunctionality
 * @author       Yuriy Lysyuk
 * @since        1.0.0
**/

/**
 * Attachment ID on Images
 *
 * @since  1.1.0
 */
function ea_attachment_id_on_images( $attr, $attachment ) {
	if( !strpos( $attr['class'], 'wp-image-' . $attachment->ID ) ) {
		$attr['class'] .= ' wp-image-' . $attachment->ID;
	}
	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'ea_attachment_id_on_images', 10, 2 );

/**
 * Default Image Link is None
 *
 * @since 1.2.0
 */
function ea_default_image_link() {
	$link = get_option( 'image_default_link_type' );
	if( 'none' !== $link )
		update_option( 'image_default_link_type', 'none' );
}
add_action( 'init', 'ea_default_image_link' );

/**
 * Remove ancient Custom Fields Metabox because it's slow and most often useless anymore
 * ref: https://core.trac.wordpress.org/ticket/33885
 */
function jb_remove_post_custom_fields_now() {
	foreach ( get_post_types( '', 'names' ) as $post_type ) {
		remove_meta_box( 'postcustom' , $post_type , 'normal' );
	}
}
add_action( 'admin_menu' , 'jb_remove_post_custom_fields_now' );

/**
 * Убираем атрибуты type у загружаемых стилей и скриптов
 *
 */
	function rko_remove_type_attr($tag, $handle) {
		return preg_replace( "/type=['\"]text\/(javascript|css)['\"]/", '', $tag );
	}
	add_filter('style_loader_tag', 'rko_remove_type_attr', 10, 2);
	add_filter('script_loader_tag', 'rko_remove_type_attr', 10, 2);

/**
 *  Disable emoji
 */
function rko_disable_emojis() {
  remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
  remove_action( 'wp_print_styles', 'print_emoji_styles' );
  remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
  remove_action( 'admin_print_styles', 'print_emoji_styles' );
  remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
  remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
  remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
 
  add_filter( 'tiny_mce_plugins', 'rko_disable_emojis_tinymce' );
  add_filter( 'wp_resource_hints', 'rko_disable_emojis_remove_dns_prefetch', 10, 2 );
}
add_action( 'init', 'rko_disable_emojis' );

function rko_disable_emojis_tinymce( $plugins ) {
  if ( is_array( $plugins ) ) {
    return array_diff( $plugins, array( 'wpemoji' ) );
  } else {
    return array();
  }
}
 
function rko_disable_emojis_remove_dns_prefetch( $urls, $relation_type ) {
  if ( 'dns-prefetch' == $relation_type ) {
    /** This filter is documented in wp-includes/formatting.php */
    $emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/11/svg/' );
    $urls = array_diff( $urls, array( $emoji_svg_url ) );

    $emoji_url = apply_filters( 'emoji_url', 'https://s.w.org/images/core/emoji/11/72x72/' );
    $urls = array_diff( $urls, array( $emoji_url ) );
  }
 
  return $urls;
}

/**
 *  Disable wp-json
 */
 
// Disable WP-API версий 1.x
add_filter( 'json_enabled', '__return_false' );
add_filter( 'json_jsonp_enabled', '__return_false' );

// Disable WP-API версий 2.x
// add_filter( 'rest_enabled', '__return_false' );
// add_filter( 'rest_jsonp_enabled', '__return_false' );

// Delete REST API HTTP headers and head section
// remove_action( 'xmlrpc_rsd_apis', 'rest_output_rsd' );
// remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
// remove_action( 'template_redirect', 'rest_output_link_header', 11 );

// Disable filters REST API
// remove_action( 'auth_cookie_malformed', 'rest_cookie_collect_status' );
// remove_action( 'auth_cookie_expired', 'rest_cookie_collect_status' );
// remove_action( 'auth_cookie_bad_username', 'rest_cookie_collect_status' );
// remove_action( 'auth_cookie_bad_hash', 'rest_cookie_collect_status' );
// remove_action( 'auth_cookie_valid', 'rest_cookie_collect_status' );
// remove_filter( 'rest_authentication_errors', 'rest_cookie_check_errors', 100 );

// Disable events REST API
// remove_action( 'init', 'rest_api_init' );
// remove_action( 'rest_api_init', 'rest_api_default_filters', 10, 1 );
// remove_action( 'parse_request', 'rest_api_loaded' );

// Disable Embeds REST API
// remove_action( 'rest_api_init', 'wp_oembed_register_route' );
// remove_filter( 'rest_pre_serve_request', '_oembed_rest_pre_serve_request', 10, 4 );

// Delete oembed links from head section
remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );

// If you want display oembed from other sites to your site, please comment next line
remove_action( 'wp_head', 'wp_oembed_add_host_js' );

/**
 * Clean WP head
 */
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'index_rel_link' );
remove_action( 'wp_head', 'feed_links', 2 );
remove_action( 'wp_head', 'feed_links_extra', 3 );
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10);
remove_action( 'wp_head', 'wp_shortlink_wp_head', 10);