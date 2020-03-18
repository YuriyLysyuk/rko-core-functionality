<?php
/**
 * Функции для работы калькулятора
 *
 * @package      RKOCoreFunctionality
 * @author       Yuriy Lysyuk
 * @since        1.2.0
 **/

/**
 * Возвращает информацию о банке выбранного тарифа
 *
 */
function get_bank_options($bankObject = false)
{
  if (!$bankObject) {
    return;
  }

  $bankOptions = [];

  // Устанавливаем свой ключ кэша
  $cache_key = 'bank_' . $bankObject->ID . '_options';

  // Если данных нет в кэше, то делаем запрос получаем данные и записываем их в кэш
  $bankOptions = wp_cache_get($cache_key);

  if (false === $bankOptions) {
    $bankOptions = get_fields($bankObject->ID);

    // Добавим данные в кэш для повторного использования
    wp_cache_set($cache_key, $bankOptions);
  }

  return $bankOptions;
}

/**
 * Возвращает массив тарифа со всеми параметрами
 *
 */
function get_tariff_options($tariffObject = false)
{
  if (!$tariffObject) {
    return;
  }

  $tariffOptions = [];

  // Имя тарифа
  $tariffOptions['name'] = $tariffObject->post_title;

  // Получаем все остальные параметры
  $tariffOptionFields = get_fields($tariffObject->ID);

  if ($tariffOptionFields) {
    foreach ($tariffOptionFields as $optionName => $optionValue) {
      if ('bank' == $optionName) {
        $tariffOptions['bank'] = get_bank_options($optionValue);
      } else {
        $tariffOptions[$optionName] = $optionValue;
      }
    }
  }

  return $tariffOptions;
}

/**
 * Сортирует массив всех тарифов
 *
 * @param   array   $tariffs    Массив для сортировки
 * @param   string  $orderBy    Поле, по значению которого сортируются тарифы
 * @param   int     $sortOrder  Порядок сортировки, его значение можно посмотреть здесь
 *
 * @return  array   $tariffs    Отсортированный массив
 */
function sort_tariffs(
  $tariffs = false,
  $orderBy = 'calculated_sum',
  $sortOrder = SORT_ASC
) {
  if (!$tariffs) {
    return;
  }

  // Алгоритм подсмотрел здесь в примерах https://www.php.net/manual/ru/function.ksort.php
  // Оригинальный многомерный массив сортируется по выбранному параметру внутри массива

  // Создаем специальный сортировочный массив, который будет опеределять порядок сортировки
  $sortArray = array();

  // Заполняем сортировочный массив значениями
  foreach ($tariffs as $tariff) {
    foreach ($tariff as $key => $value) {
      if (!isset($sortArray[$key])) {
        $sortArray[$key] = array();
      }
      $sortArray[$key][] = $value;
    }
  }

  // Сортируем оригинальный тариф так же как отсортирются значения в специальном сортировочном массиве
  array_multisort($sortArray[$orderBy], $sortOrder, $tariffs);

  return $tariffs;
}
