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

  return $form;
  // rko_calc(1);
}
add_shortcode('rko_calc', 'rko_calc_shortcode');

/**
 * Подключаем скрипты для шорткода
 *
 */
function rko_calc_rest_api_scripts()
{
  global $post;
  // Подключаем скрипт только если на странице есть шорткод
  if (has_shortcode($post->post_content, "rko_calc")) {
    wp_enqueue_script(
      'rko-calc',
      plugins_url('assets/rko-calc.js', dirname(__FILE__)),
      array('jquery'),
      false,
      true
    );
    wp_localize_script('rko-calc', 'rkoCalc', array(
      'restURL' => esc_url_raw(rest_url()),
      'restNonce' => wp_create_nonce('wp_rest')
    ));
  }
}
add_action('wp_enqueue_scripts', 'rko_calc_rest_api_scripts');
