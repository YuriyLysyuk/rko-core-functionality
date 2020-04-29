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
  <div class="rko-calc color-bg alignwide">
    <form id="rko-calc-form" class="rko-calc-form">
      <label class="rko-calc-field-label">Форма регистрации</label>
      <div class="switch-field">
      <input type="radio" name="ooo" id="ip" checked value="0"><label for="ip"><span>ИП</span></label>
      <input type="radio" name="ooo" id="ooo" value="1"><label for="ooo"><span>ООО</span></label>
      </div>

      <label class="rko-calc-field-label" for="income">Поступления на счет от юр. лиц и ИП</label>
      <div>
        <input type="text" name="income" id="income" placeholder="" value="0" data-value="0">
        <div id="income-slider"></div>
      </div>

      <label class="rko-calc-field-label" for="payment_order">Платежные поручения</label>
      <div>
        <input type="text" name="payment_order" id="payment_order" value="0" data-value="0">
        <div id="payment_order-slider"></div>
      </div>
      
      <label class="rko-calc-field-label" for="people_transfer">Переводы физ. лицам</label>
      <div>
        <input type="text" name="people_transfer" id="people_transfer" value="0" data-value="0">
        <div id="people_transfer-slider"></div>
      </div>

      <label class="rko-calc-field-label" for="personal_transfer">Переводы себе на карту</label>
      <div>
        <input type="text" name="personal_transfer" id="personal_transfer" value="0" data-value="0"> 
        <div id="personal_transfer-slider"></div>
      </div>

      <div>Подробнее</div>

      <div class="h5">Дополнительные услуги</div>
      <div><input type="checkbox" name="corp_card" id="corp_card" checked><label for="corp_card">Бизнес-карта</label></div>
      <div><input type="checkbox" name="sms" id="sms"><label for="sms">SMS-информирование</label></div>
      
      <p class="h5">Снятие наличных</p>
      <label class="rko-calc-field-label" for="get_atm">В банкомате</label>
      <div>
        <input type="text" name="get_atm" id="get_atm" value="0" data-value="0">
        <div id="get_atm-slider"></div>
      </div>

      <label class="rko-calc-field-label" for="get_cashbox">В кассе банка</label>
      <div>
        <input type="text" name="get_cashbox" id="get_cashbox" placeholder="" value="0" data-value="0">
        <div id="get_cashbox-slider"></div>
      </div>

      <div class="h5">Внесение наличных</div>
      <label class="rko-calc-field-label" for="put_atm">В банкомате</label>
      <div>
        <input type="text" name="put_atm" id="put_atm" placeholder="" value="0" data-value="0">
        <div id="put_atm-slider"></div>
      </div>

      <label class="rko-calc-field-label" for="put_cashbox">В кассе банка</label>
      <div>
        <input type="text" name="put_cashbox" id="put_cashbox" placeholder="" value="0" data-value="0">
        <div id="put_cashbox-slider"></div>
      </div>
    </form>
    <div id="rko-calc-results">
    
    </div>
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

    // Стили слайдера
    wp_enqueue_style(
      'nouislider-css',
      plugins_url('assets/nouislider.min.css', dirname(__FILE__)),
      array(),
      '14.2.0'
    );

    // Скрипты слайдера
    wp_enqueue_script(
      'nouislider-js',
      plugins_url('assets/nouislider.min.js', dirname(__FILE__)),
      array(),
      '14.2.0',
      true
    );

    // Скрипты слайдера
    wp_enqueue_script(
      'wnumb',
      plugins_url('assets/wNumb.min.js', dirname(__FILE__)),
      array('nouislider-js'),
      '1.2.0',
      true
    );

    wp_enqueue_script(
      'rko-calc-js',
      plugins_url('assets/rko-calc.js', dirname(__FILE__)),
      array('jquery', 'nouislider-js'),
      false,
      true
    );

    wp_localize_script('rko-calc-js', 'rkoCalc', array(
      'restURL' => esc_url_raw(rest_url()),
      'restNonce' => wp_create_nonce('wp_rest'),
      'allTariffOptions' => $allTariffOptionsJson
    ));
  }
}
add_action('wp_enqueue_scripts', 'rko_calc_rest_api_scripts');
