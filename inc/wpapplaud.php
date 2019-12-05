<?php
/**
 * WPApplaud
 *
 * @package      RKOCoreFunctionality
 * @author       Yuriy Lysyuk
 * @since        1.0.5
**/

/** 
 * Добавляем функционал аплодиментов в мета данные после статьи
 * 
 */
function rkocf_wpapplaud() {
	if ( function_exists( 'wp_applaud' ) ) {
		global $post;

		// Скрываем вывод аплодисментов, если он отключен в админке
		$value = get_post_meta( $post->ID, '_wp_applaud_exclude', true );
		if($value) return;

		echo '<span class="wp-applaud-wrap">';
		wp_applaud();
		echo '</span>';

	}
}
add_action( 'genesis_entry_footer', 'rkocf_wpapplaud' );