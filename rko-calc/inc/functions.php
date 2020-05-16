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

  // ID тарифа
  $tariffOptions['id'] = $tariffObject->ID;

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
 * Возвращает массив всех тарифов со всеми параметрами
 *
 */
function get_all_tariff_options()
{
  $allTariffOptions = [];

  // Статичные данные о тарифах храняться в файле JSON
  // Если файл с данными по тарифам существует и нет маркерного файла на обновление
  if (
    file_exists(JSON_ALL_TARIFF_OPTIONS_PATH) &&
    !file_exists(JSON_ALL_TARIFF_OPTIONS_NEED_UPDATE_PATH)
  ) {
    // Открываем JSON с данными по тарифам
    $allTariffOptionsJson = file_get_contents(JSON_ALL_TARIFF_OPTIONS_PATH);
    // Декодируем данные по тарифам из JSON
    $allTariffOptions = json_decode($allTariffOptionsJson, true);
  } else {
    // Если файла с данными по тарифам нет или есть маркерный файл на обновление...

    // Получаем все доступные тарифы
    $allTariffObjects = get_posts([
      'numberposts' => -1,
      'post_type' => 'tariffs'
    ]);

    foreach ($allTariffObjects as $tariffObject) {
      // Получаем данные каждого тарифа и конструируем ассоциативный массив
      $tariffOptions = get_tariff_options($tariffObject);
      // Ключами массива будут id тарифа
      $allTariffOptions[$tariffOptions['id']] = $tariffOptions;
    }

    // Кодируем строку в JSON
    $allTariffOptionsJson = json_encode(
      $allTariffOptions,
      JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK
    );

    // Сохраняем данные по тарифам в файл json
    atomicWrite(JSON_ALL_TARIFF_OPTIONS_PATH, $allTariffOptionsJson);

    // Удаляем маркерный файл
    if (file_exists(JSON_ALL_TARIFF_OPTIONS_NEED_UPDATE_PATH)) {
      @unlink(JSON_ALL_TARIFF_OPTIONS_NEED_UPDATE_PATH);
    }
  }

  return $allTariffOptions;
}

/**
 * Запускаем обновление данных в JSON файле по тарифам при сохранении записи с тарифом или банком или переносе ее в корзину
 *
 */
add_action('save_post_tariffs', 'update_json_all_tariff_options', 10, 3);
add_action('save_post_banks', 'update_json_all_tariff_options', 10, 3);

function update_json_all_tariff_options($post_id, $post, $update)
{
  // Если это автосохранение — ничего не делаем
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return;
  }

  // Создаем пустой файловый маркер на обновление JSON файла с данными по тарифам
  $fp = fopen(JSON_ALL_TARIFF_OPTIONS_NEED_UPDATE_PATH, "w+");
  // Закрываем файл
  fclose($fp);

  // Проверяем что запись опубликована
  if ($post->post_status == "publish") {
    // Запускаем обновление JSON файла с тарифами после сохранения данных в ACF
    add_action('acf/save_post', 'get_all_tariff_options', 20);
  }
  // Проверяем что запись перемещена в корзину
  if ($post->post_status == "trash") {
    // Запускаем обновление JSON файла с тарифами
    get_all_tariff_options();
  }
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
