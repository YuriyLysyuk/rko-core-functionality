<?php
/**
 * Компонент для калькулятора тарифов РКО
 *
 * @package      RKOCoreFunctionality
 * @author       Yuriy Lysyuk
 * @since        1.2.0
 **/

//  Регистрируем произвольные типы данных
require_once YL_DIR . '/rko-calc/inc/register-cpt.php';

// Добавляем шорткод для вывода калькулятора на странице
require_once YL_DIR . '/rko-calc/inc/shortcode.php';

// Подключаем функции для получения данных о тарифах
require_once YL_DIR . '/rko-calc/inc/get-user-params.php';

// Подключаем функции для получения данных о тарифах
require_once YL_DIR . '/rko-calc/inc/functions.php';

// Подключаем функции для получения данных о тарифах
require_once YL_DIR . '/rko-calc/inc/calculate.php';

function rko_calc()
{
  // Получаем параметры пользовательского запроса
  $tariff['user_params'] = get_user_params();

  // Получаем все доступные тарифы
  $allTariffObjects = get_posts([
    'post_type' => 'tariffs'
  ]);

  // Основной цикл
  foreach ($allTariffObjects as $tariffObject) {
    // Получаем данные каждого тарифа и конструируем ассоциативный массив
    $tariff['options'] = get_tariff_options($tariffObject);

    // Вычисляем стоимость облуживания по каждому пункту тарифа
    $tariff['calculated'] = calculate(
      $tariff['user_params'],
      $tariff['options']
    );

    // Вычисляем общую стоимость обслуживания по тарифу
    $tariff['calculated_sum'] = 0;
    foreach ($tariff['calculated'] as $valueToSum) {
      $tariff['calculated_sum'] += $valueToSum;
    }

    // Добавляем всю инфу в общий массив тарифов
    $tariffs[] = $tariff;
  }

  // Сортируем тарифы (по умолчанию сортируем по 'calculated_sum' по возрастанию)
  $tariffs = sort_tariffs($tariffs);

  foreach ($tariffs as $tariff) {
    echo '<pre style="background-color:skyblue;">';
    echo $tariff['options']['name'] . ': ' . $tariff['calculated_sum'];
    echo '</pre>';
    echo '<pre>';
    print_r($tariff['calculated']);
    echo '</pre>';
  }
  // ea_pp($tariff);
}
