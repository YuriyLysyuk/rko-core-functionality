<?php
/**
 * Шорткод калькулятора
 *
 * @package      RKOCoreFunctionality
 * @author       Yuriy Lysyuk
 * @since        1.2.0
 **/

/**
 * Добавляем шорткод для вывода калькулятора на странице
 *
 */
function rko_calc_shortcode($atts)
{
	return rko_calc();
}
add_shortcode('rko_calc', 'rko_calc_shortcode');
