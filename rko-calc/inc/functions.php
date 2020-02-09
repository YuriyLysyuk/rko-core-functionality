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
function get_bank_options($tariffID = false)
{
  if (!$tariffID) {
    return;
  }

  $bankOptions = [];

  $bankObject = get_field('bank', $tariffID);

  $bankOptionFields = get_fields($bankObject->ID);

  if ($bankOptionFields) {
    foreach ($bankOptionFields as $optionName => $optionValue) {
      $bankOptions[$optionName] = $optionValue;
    }
  }

  return $bankOptions;
}

/**
 * Возвращает массив тарифов со всеми параметрами
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
        $tariffOptions['bank'] = get_bank_options($tariffObject->ID);
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
