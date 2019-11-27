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

		echo '<span class="wp-applaud-wrap">';
		wp_applaud();
		echo '</span>';

	}
}
add_action( 'genesis_entry_footer', 'rkocf_wpapplaud' );