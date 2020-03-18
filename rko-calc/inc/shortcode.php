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
function rko_calc_shortcode()
{
  $form = '
    <form id="rko-calc-form">
			<input type="text" name="income" id="income" value="0">
      <input type="submit" value="Получить личный ТОП банков" id="submit">
    </form>
    <div id="rko-calc-results">
		
    </div>
  ';

  // return $form;
  rko_calc(1);
}
add_shortcode('rko_calc', 'rko_calc_shortcode');
