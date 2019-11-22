<?php
/**
 * SEO
 *
 * @package      RKOCoreFunctionality
 * @author       Yuriy Lysyuk
 * @since        1.0.0
**/

/**
 * Default image in Article schema
 * If there is no featured image, or featured image is
 * < 1200px wide, use the site logo instead.
 *
 * @link https://www.billerickson.net/yoast-seo-schema-default-image/
 *
 * @param array $graph_piece
 * @return array
 */
function be_schema_default_image( $graph_piece ) {
	$use_default = false;
	if( has_post_thumbnail() ) {
		$image_src = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
		if( empty( $image_src[1] ) || 1199 > $image_src[1] )
			$use_default = true;
	} else {
		$use_default = true;
	}

	if( $use_default ) {
		$graph_piece['image']['@id'] = home_url( '#logo' );
	}
	return $graph_piece;
}
add_filter( 'wpseo_schema_article', 'be_schema_default_image' );