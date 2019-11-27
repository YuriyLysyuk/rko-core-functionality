<?php
/**
 * AddToAny Share
 *
 * @package      RKOCoreFunctionality
 * @author       Yuriy Lysyuk
 * @since        1.0.5
**/

/** 
 * Добавляем кнопки шаринга в мета данные после статьи
 * 
 */

function rkocf_addtoany_share_kit() {
	
	if ( function_exists( 'ADDTOANY_SHARE_SAVE_KIT' ) ) {
		ADDTOANY_SHARE_SAVE_KIT();
	}

}
add_action( 'genesis_entry_footer', 'rkocf_addtoany_share_kit', 12 );