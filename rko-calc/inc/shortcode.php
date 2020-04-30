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
      <div class="rko-calc-field-slider">
        <input type="text" name="income" id="income" placeholder="" value="0" data-value="0">
        <div id="income-slider"></div>
      </div>

      <label class="rko-calc-field-label" for="payment_order">Платежные поручения</label>
      <div class="rko-calc-field-slider">
        <input type="text" name="payment_order" id="payment_order" value="0" data-value="0">
        <div id="payment_order-slider"></div>
      </div>
      
      <label class="rko-calc-field-label" for="people_transfer">Переводы физ. лицам</label>
      <div class="rko-calc-field-slider">
        <input type="text" name="people_transfer" id="people_transfer" value="0" data-value="0">
        <div id="people_transfer-slider"></div>
      </div>

      <label class="rko-calc-field-label" for="personal_transfer">Переводы себе на карту</label>
      <div class="rko-calc-field-slider">
        <input type="text" name="personal_transfer" id="personal_transfer" value="0" data-value="0"> 
        <div id="personal_transfer-slider"></div>
      </div>
  ';
  $form .= '
      <div class="detailed-calculation">
        <span>Подробный расчет <svg class="open" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m11.9932649 19.500812c-.6352342.000819-1.2400333-.2833911-1.65984-.78l-9.41856003-11.15199999c-.61913211-.76859096-.53599584-1.91147321.1872475-2.57410938.72324334-.66263618 1.82204752-.60264878 2.4748325.13510938l8.23584003 9.75199999c.0455704.0541548.1113462.0852092.18048.0852092.0691337 0 .1349096-.0310544.18048-.0852092l8.23584-9.75199999c.4135143-.51333692 1.0615094-.75310007 1.6932379-.626511s1.1480791.59966995 1.3492454 1.2361798c.2011663.63650986.0555875 1.33658731-.3804033 1.8293312l-9.41568 11.14799999c-.4205256.497811-1.0261445.7833703-1.66272.784z" fill-rule="evenodd"/></svg><svg class="close" enable-background="new 0 0 24 24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m12 4.5c.6 0 1.2.3 1.7.8l9.4 11.2c.6.8.5 1.9-.2 2.6s-1.8.6-2.5-.1l-8.2-9.8c0-.2-.1-.2-.2-.2s-.1 0-.2.1l-8.2 9.8c-.4.5-1.1.8-1.7.6-.6-.1-1.1-.6-1.3-1.2s-.1-1.3.4-1.8l9.4-11.1c.4-.6 1-.9 1.6-.9z"/></svg></span>
      </div>
  ';
  $form .= '
      <div class="rko-calc-field-label detailed-hidden">Дополнительные услуги</div>
      <div class="detailed-hidden">
        <div><input type="checkbox" name="corp_card" id="corp_card" checked><label for="corp_card">Бизнес-карта</label></div>
        <div><input type="checkbox" name="sms" id="sms" checked><label for="sms">SMS-информирование</label></div>
      </div>
      
      <p class="h5 detailed-hidden">Снятие наличных</p>
      <label class="rko-calc-field-label detailed-hidden" for="get_atm">В банкомате</label>
      <div class="rko-calc-field-slider detailed-hidden">
        <input type="text" name="get_atm" id="get_atm" value="0" data-value="0">
        <div id="get_atm-slider"></div>
      </div>

      <label class="rko-calc-field-label detailed-hidden" for="get_cashbox">В кассе банка</label>
      <div class="rko-calc-field-slider before-h5 detailed-hidden">
        <input type="text" name="get_cashbox" id="get_cashbox" placeholder="" value="0" data-value="0">
        <div id="get_cashbox-slider"></div>
      </div>

      <div class="h5 detailed-hidden">Внесение наличных</div>
      <label class="rko-calc-field-label detailed-hidden" for="put_atm">В банкомате</label>
      <div class="rko-calc-field-slider detailed-hidden">
        <input type="text" name="put_atm" id="put_atm" placeholder="" value="0" data-value="0">
        <div id="put_atm-slider"></div>
      </div>

      <label class="rko-calc-field-label detailed-hidden" for="put_cashbox">В кассе банка</label>
      <div class="rko-calc-field-slider before-h5 detailed-hidden">
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
