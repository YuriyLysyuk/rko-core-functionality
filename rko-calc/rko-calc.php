<?php
/**
 * Компонент для калькулятора тарифов РКО
 *
 * @package      RKOCoreFunctionality
 * @author       Yuriy Lysyuk
 * @since        1.3.0
 **/

// Объявляем константы
require_once YL_DIR . '/rko-calc/inc/defined.php';

// Вспомогательные функции для калькулятора
require_once YL_DIR . '/rko-calc/inc/helpers.php';

// Регистрируем произвольные типы записей для тарифов и банков
require_once YL_DIR . '/rko-calc/inc/register-cpt.php';

// Регистрируем произвольные типы записей для тарифов и банков
require_once YL_DIR . '/rko-calc/inc/acf.php';

// Функции для админки
require_once YL_DIR . '/rko-calc/inc/rest.php';

// Функции для админки
require_once YL_DIR . '/rko-calc/inc/admin.php';

// Подключаем функции для получения значений пользовательского запроса
require_once YL_DIR . '/rko-calc/inc/get-user-params.php';

// Подключаем функции для получения данных о тарифах
require_once YL_DIR . '/rko-calc/inc/functions.php';

// Подключаем функции для вычисления стоимости использования тарифа
require_once YL_DIR . '/rko-calc/inc/calculate.php';

// Добавляем шорткод для вывода калькулятора на странице
require_once YL_DIR . '/rko-calc/inc/shortcode.php';

// Автоматическая проверка изменения тарифов РКО на сайтах банков
require_once YL_DIR . '/rko-calc/inc/cron.php';

function rko_calc(WP_REST_Request $request)
{
  // Получаем параметры пользовательского запроса
  $userParams = get_user_params($request);

  // Получаем данные каждого тарифа и конструируем ассоциативный массив
  $allTariffOptions = get_all_tariff_options();

  // Основной цикл
  foreach ($allTariffOptions as $tariffOptions) {
    // Определяем для какой формы регистрации производится расчет и устанавливаем соответствующую переменную
    $userParams['ooo'] ? ($forWhom = 'ooo') : ($forWhom = 'ip');

    // Производим расчет только для той формы регистрации, для которой включен тариф
    if ($tariffOptions['for'] && in_array($forWhom, $tariffOptions['for'])) {
      // ID тарифа
      $tariff['id'] = $tariffOptions['id'];

      // Вычисляем стоимость облуживания по каждому пункту тарифа
      $tariff['calculated'] = calculate($userParams, $tariffOptions);

      // Добавляем пользовательские данные в массив
      $tariff['user_params'] = $userParams;

      // Вычисляем общую стоимость обслуживания по тарифу
      $tariff['calculated_sum'] = 0;
      foreach ($tariff['calculated'] as $valueToSum) {
        $tariff['calculated_sum'] += $valueToSum;
      }

      // Добавляем всю инфу в общий массив тарифов
      $tariffs[] = $tariff;
    }
  }

  // Сортируем тарифы (по умолчанию сортируем по 'calculated_sum' по возрастанию)
  $response = sort_tariffs($tariffs);

  return rest_ensure_response($response);
}
