<?php
/**
 * Компонент для калькулятора тарифов РКО
 *
 * @package      RKOCoreFunctionality
 * @author       Yuriy Lysyuk
 * @since        1.2.0
 **/

// Путь к JSON файлу со статическими данными по тарифам
define(
  'JSON_ALL_TARIFF_OPTIONS_PATH',
  YL_DIR . '/rko-calc/acf-data/all-tariff-options.json'
);
// Путь к маркерному файлу, который показывает необходимость обновить JSON файл с данными по тарифам
define(
  'JSON_ALL_TARIFF_OPTIONS_NEED_UPDATE_PATH',
  YL_DIR . '/rko-calc/acf-data/all-tariff-options-need-update'
);

//  Регистрируем произвольные типы данных
require_once YL_DIR . '/rko-calc/inc/register-cpt.php';

// Change ACF Local JSON save location to /acf folder inside this plugin
add_filter('acf/settings/save_json', function () {
  return YL_DIR . '/rko-calc/acf';
});

// Include the /acf folder in the places to look for ACF Local JSON files
add_filter('acf/settings/load_json', function ($paths) {
  $paths[] = YL_DIR . '/rko-calc/acf';
  return $paths;
});

// Добавляем шорткод для вывода калькулятора на странице
require_once YL_DIR . '/rko-calc/inc/shortcode.php';

// Подключаем функции для получения значений пользовательского запроса
require_once YL_DIR . '/rko-calc/inc/get-user-params.php';

// Подключаем функции для получения данных о тарифах
require_once YL_DIR . '/rko-calc/inc/functions.php';

// Подключаем функции для вычисления стоимости использования тарифа
require_once YL_DIR . '/rko-calc/inc/calculate.php';

// Функции для админки
require_once YL_DIR . '/rko-calc/inc/admin.php';

function rko_calc($request)
{
  $response = [];

  // $response = $request->get_params();

  // $param = $request['income'];

  // Получаем параметры пользовательского запроса
  $userParams = get_user_params();

  // Получаем данные каждого тарифа и конструируем ассоциативный массив
  $allTariffOptions = get_all_tariff_options();

  // Основной цикл
  foreach ($allTariffOptions as $tariffOptions) {
    // ID тарифа
    $tariff['id'] = $tariffOptions['id'];

    // Вычисляем стоимость облуживания по каждому пункту тарифа
    $tariff['calculated'] = calculate($userParams, $tariffOptions);

    // Вычисляем общую стоимость обслуживания по тарифу
    $tariff['calculated_sum'] = 0;
    foreach ($tariff['calculated'] as $valueToSum) {
      $tariff['calculated_sum'] += $valueToSum;
    }

    // Добавляем всю инфу в общий массив тарифов
    $tariffs[] = $tariff;
  }

  // Сортируем тарифы (по умолчанию сортируем по 'calculated_sum' по возрастанию)
  $response = sort_tariffs($tariffs);

  return rest_ensure_response($response);
}

add_action('rest_api_init', function () {
  register_rest_route('rko-calc/v1', '/calculate/', array(
    'methods' => WP_REST_Server::READABLE,
    'callback' => 'rko_calc'
  ));
});
