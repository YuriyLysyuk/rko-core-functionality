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
      <label for="income">Поступления на счет от юр. лиц и ИП, ₽</label>
      <input type="text" name="income" id="income" value="0">
      <label for="ooo">ИП или ООО?</label>
      <input type="checkbox" name="ooo" id="ooo">
      <br>
      <label for="personal_transfer">Перевод себе на карту, ₽</label>
      <input type="text" name="personal_transfer" id="personal_transfer" value="0">
      <label for="people_transfer">Переводы физическим лицам, ₽</label>
      <input type="text" name="people_transfer" id="people_transfer" value="0">
      <label for="payment_order">Количество платежей на счета юр. лиц и ИП, шт</label>
      <input type="text" name="payment_order" id="payment_order" value="0">
      <label for="get_cash">Будете снимать наличные?</label>
      <input type="checkbox" id="get_cash">
      <br>
      <label for="get_atm">Снятие наличных в банкомате, ₽</label>
      <input type="text" name="get_atm" id="get_atm" value="0">
      <label for="get_cashbox">Снятие наличных в кассе банка, ₽</label>
      <input type="text" name="get_cashbox" id="get_cashbox" value="0">
      <label for="put_atm">Внесение наличных в банкомате, ₽</label>
      <input type="text" name="put_atm" id="put_atm" value="0">
      <label for="put_cashbox">Внесение наличных в кассе банка, ₽</label>
      <input type="text" name="put_cashbox" id="put_cashbox" value="0">
      <label for="corp_card">Бизнес-карта</label>
      <input type="checkbox" name="corp_card" id="corp_card">
      <br>
      <label for="sms">SMS-информирование</label>
      <input type="checkbox" name="sms" id="sms">
      <br>

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
    // Если файл с данными по тарифам не существует
    if (!file_exists(JSON_ALL_TARIFF_OPTIONS_PATH)) {
      // Нужно его сформировать
      get_all_tariff_options();
    }

    // Открываем и декодируем JSON с данными по тарифам
    $allTariffOptionsJson = json_decode(
      @file_get_contents(JSON_ALL_TARIFF_OPTIONS_PATH)
    );

    wp_enqueue_script(
      'rko-calc',
      plugins_url('assets/rko-calc.js', dirname(__FILE__)),
      array('jquery'),
      false,
      true
    );

    wp_localize_script('rko-calc', 'rkoCalc', array(
      'restURL' => esc_url_raw(rest_url()),
      'restNonce' => wp_create_nonce('wp_rest'),
      'allTariffOptions' => $allTariffOptionsJson
    ));
  }
}
add_action('wp_enqueue_scripts', 'rko_calc_rest_api_scripts');
