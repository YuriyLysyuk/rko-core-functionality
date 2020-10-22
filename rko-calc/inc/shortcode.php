<?php
/**
 * Шорткод калькулятора
 *
 * @package      RKOCoreFunctionality
 * @author       Yuriy Lysyuk
 * @since        1.3.21
 **/

/**
 * Добавляем шорткод для вывода калькулятора на странице
 *
 */
function rko_calc_shortcode($atts)
{
  // Параметры шорткода по умолчанию
  $params = shortcode_atts(
    [
      'banks' => 'all', // Показывать тарифы всех банков
    ],
    $atts
  );

  // Проверяем наличие переменных в URL и используем их если есть
  $checkedIP = empty($_GET['ooo']) ? "checked" : "";
  $checkedOOO = isset($_GET['ooo']) && $_GET['ooo'] == "1" ? "checked" : "";
  $income = empty($_GET['income']) ? 0 : $_GET['income'];
  $payment_order = empty($_GET['payment_order']) ? 0 : $_GET['payment_order'];
  $people_transfer = empty($_GET['people_transfer'])
    ? 0
    : $_GET['people_transfer'];
  $personal_transfer = empty($_GET['personal_transfer'])
    ? 0
    : $_GET['personal_transfer'];
  $get_atm = empty($_GET['get_atm']) ? 0 : $_GET['get_atm'];
  $get_cashbox = empty($_GET['get_cashbox']) ? 0 : $_GET['get_cashbox'];
  $put_atm = empty($_GET['put_atm']) ? 0 : $_GET['put_atm'];
  $put_cashbox = empty($_GET['put_cashbox']) ? 0 : $_GET['put_cashbox'];
  $checkedCorpCard = (empty($_GET)
      ? "checked"
      : isset($_GET['corp_card']) && $_GET['corp_card'] == "on")
    ? "checked"
    : "";
  $checkedSMS = (empty($_GET)
      ? "checked"
      : isset($_GET['sms']) && $_GET['sms'] == "on")
    ? "checked"
    : "";

  $form = "
  <div class='rko-calc color-bg color-bg-green alignwide'>
    <form id='rko-calc-form' class='rko-calc-form'>
      <label class='rko-calc-field-label'>Форма регистрации</label>
      <div class='switch-field'>
      <input type='radio' name='ooo' id='ip' $checkedIP value='0'><label for='ip'><span>ИП</span></label>
      <input type='radio' name='ooo' id='ooo' $checkedOOO value='1'><label for='ooo'><span>ООО</span></label>
      </div>
      
      <div class='h5 every-month'>Ежемесячно</div>

      <label class='rko-calc-field-label' for='income'>Поступления на счет от юр. лиц и ИП</label>
      <div class='rko-calc-field-slider'>
        <input type='text' name='income' id='income' placeholder='' value='0' data-value='$income'>
        <div id='income-slider'></div>
      </div>

      <label class='rko-calc-field-label' for='payment_order'>Платежные поручения</label>
      <div class='rko-calc-field-slider'>
        <input type='text' name='payment_order' id='payment_order' value='0' data-value='$payment_order'>
        <div id='payment_order-slider'></div>
      </div>

      <label class='rko-calc-field-label' for='personal_transfer'>Переводы себе на карту</label>
      <div class='rko-calc-field-slider'>
        <input type='text' name='personal_transfer' id='personal_transfer' value='0' data-value='$personal_transfer'> 
        <div id='personal_transfer-slider'></div>
      </div>
      
      <label class='rko-calc-field-label' for='people_transfer'>Переводы физ. лицам</label>
      <div class='rko-calc-field-slider'>
        <input type='text' name='people_transfer' id='people_transfer' value='0' data-value='$people_transfer'>
        <div id='people_transfer-slider'></div>
      </div>
  ";
  $form .= '
      <div class="detailed-calculation">
        <span>Дополнительные параметры</span><span> <svg class="open" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m11.9932649 19.500812c-.6352342.000819-1.2400333-.2833911-1.65984-.78l-9.41856003-11.15199999c-.61913211-.76859096-.53599584-1.91147321.1872475-2.57410938.72324334-.66263618 1.82204752-.60264878 2.4748325.13510938l8.23584003 9.75199999c.0455704.0541548.1113462.0852092.18048.0852092.0691337 0 .1349096-.0310544.18048-.0852092l8.23584-9.75199999c.4135143-.51333692 1.0615094-.75310007 1.6932379-.626511s1.1480791.59966995 1.3492454 1.2361798c.2011663.63650986.0555875 1.33658731-.3804033 1.8293312l-9.41568 11.14799999c-.4205256.497811-1.0261445.7833703-1.66272.784z" fill-rule="evenodd"/></svg><svg class="close" enable-background="new 0 0 24 24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m12 4.5c.6 0 1.2.3 1.7.8l9.4 11.2c.6.8.5 1.9-.2 2.6s-1.8.6-2.5-.1l-8.2-9.8c0-.2-.1-.2-.2-.2s-.1 0-.2.1l-8.2 9.8c-.4.5-1.1.8-1.7.6-.6-.1-1.1-.6-1.3-1.2s-.1-1.3.4-1.8l9.4-11.1c.4-.6 1-.9 1.6-.9z"/></svg></span>
      </div>
  ';
  $form .= "
      <div class='rko-calc-field-label detailed-hidden'>Услуги и продукты</div>
      <div class='detailed-hidden'>
        <div><input type='checkbox' name='corp_card' id='corp_card' $checkedCorpCard><label for='corp_card'>Бизнес-карта</label></div>
        <div><input type='checkbox' name='sms' id='sms' $checkedSMS><label for='sms'>SMS-информирование</label></div>
      </div>
      
      <p class='h5 detailed-hidden'>Снятие наличных</p>
      <label class='rko-calc-field-label detailed-hidden' for='get_atm'>В банкомате</label>
      <div class='rko-calc-field-slider detailed-hidden'>
        <input type='text' name='get_atm' id='get_atm' value='0' data-value='$get_atm'>
        <div id='get_atm-slider'></div>
      </div>

      <label class='rko-calc-field-label detailed-hidden' for='get_cashbox'>В кассе банка</label>
      <div class='rko-calc-field-slider before-h5 detailed-hidden'>
        <input type='text' name='get_cashbox' id='get_cashbox' placeholder='' value='0' data-value='$get_cashbox'>
        <div id='get_cashbox-slider'></div>
      </div>

      <div class='h5 detailed-hidden'>Внесение наличных</div>
      <label class='rko-calc-field-label detailed-hidden' for='put_atm'>В банкомате</label>
      <div class='rko-calc-field-slider detailed-hidden'>
        <input type='text' name='put_atm' id='put_atm' placeholder='' value='0' data-value='$put_atm'>
        <div id='put_atm-slider'></div>
      </div>

      <label class='rko-calc-field-label detailed-hidden' for='put_cashbox'>В кассе банка</label>
      <div class='rko-calc-field-slider before-h5 detailed-hidden'>
        <input type='text' name='put_cashbox' id='put_cashbox' placeholder='' value='0' data-value='$put_cashbox'>
        <div id='put_cashbox-slider'></div>
      </div>
      <input type='hidden' name='banks' value='{$params['banks']}'>
    </form>
    <div id='rko-calc-results'>
    <ul class='rko-calc-results-list'>
          <div class='preloader'><div class='spin'></div></div>
        </ul>
    </div>
  </div>
";

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

  // Подключаем скрипт только если не пустой post_content, на странице есть шорткод и это не страница поиска
  if (
    !empty($post->post_content) &&
    has_shortcode($post->post_content, "rko_calc") &&
    !is_search()
  ) {
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
      plugins_url('assets/css/nouislider.min.css', dirname(__FILE__)),
      [],
      '14.2.0'
    );

    // Скрипты слайдера
    wp_enqueue_script(
      'nouislider-js',
      plugins_url('assets/js/nouislider.min.js', dirname(__FILE__)),
      [],
      '14.2.0',
      true
    );

    // Скрипты слайдера
    wp_enqueue_script(
      'wnumb',
      plugins_url('assets/js/wNumb.min.js', dirname(__FILE__)),
      ['nouislider-js'],
      '1.2.0',
      true
    );

    wp_enqueue_script(
      'rko-calc-js',
      plugins_url('assets/js/rko-calc.min.js', dirname(__FILE__)),
      ['jquery', 'nouislider-js'],
      RKOCF_VER,
      true
    );

    wp_localize_script('rko-calc-js', 'rkoCalc', [
      'restURL' => esc_url_raw(rest_url()),
      'restNonce' => wp_create_nonce('wp_rest'),
      'allTariffOptions' => $allTariffOptionsJson,
    ]);
  }
}
add_action('wp_enqueue_scripts', 'rko_calc_rest_api_scripts');
