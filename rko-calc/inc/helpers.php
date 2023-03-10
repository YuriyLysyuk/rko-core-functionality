<?php
/**
 * Helpers for rko calc component
 *
 * @package      RKOCoreFunctionality
 * @author       Yuriy Lysyuk
 * @since        1.3.0
 **/

/**
 *  Функция скачивания тарифного файла с сайта банка
 *
 */

function download_tariff_docs($url, $newFileName = null)
{
  // Подключаем нужные нам функции: download_url() и wp_handle_sideload()
  require_once ABSPATH . 'wp-admin/includes/file.php';

  // Меняем директорию загрузки файлов с тарифами на нужную
  add_filter('upload_dir', 'rko_calc_custom_upload_directory');

  // Добавляем фильтр с юзерагентом
  add_filter('http_headers_useragent', 'rko_calc_custom_useragent', 10, 2);

  // Загружаем файл во временную папку
  $temp_file = download_url($url);

  if (!is_wp_error($temp_file)) {
    // Соберем массив аналогичный $_FILE в PHP
    $file = [
      'name' => !is_null($newFileName) ? $newFileName : basename($url),
      'tmp_name' => $temp_file,
      'error' => 0,
      'size' => filesize($temp_file),
    ];

    $overrides = [
      // Скажем WP не искать поля формы, которые обычно должны быть.
      // Загружаем файл с удаленного сервера, поэтому полей формы нет.
      'test_form' => false,
    ];

    // Перемещаем временный файл в папку uploads
    $results = wp_handle_sideload($file, $overrides);

    if (!empty($results['error'])) {
      // Не удалось переместить временный файл в папку uploads — возвращаем ошибку
      return $results['error'];
    } else {
      // $filename = $results['file']; // полный путь до файла
      // $local_url = $results['url']; // URL до файла в папке uploads
      // $type = $results['type']; // MIME тип файла

      // делаем что-либо на основе полученных данных
    }
  } else {
    // Не удалось скачать файл — возвращаем ошибку
    return $temp_file;
  }

  // Удаляем фильтр с кастомной директорией для загрузки файлов, чтобы вернуть значения по умолчанию
  remove_filter('upload_dir', 'rko_calc_custom_upload_directory');

  // Удаляем фильтр с юзерагентом
  remove_filter('http_headers_useragent', 'rko_calc_custom_useragent');
}

// Директория для загрузки файлов с тарифами
function rko_calc_custom_upload_directory($args)
{
  $args['path'] = TARIFF_DOCS_UPLOAD_DIR;
  $args['url'] = TARIFF_DOCS_UPLOAD_URL;
  $args['basedir'] = TARIFF_DOCS_UPLOAD_DIR;
  $args['baseurl'] = TARIFF_DOCS_UPLOAD_URL;

  return $args;
}

// Фильтр, возвращающий юзерагент.
// Сервер Сбербанка не давал загружать pdf-файл со стандартным юзерагентом
function rko_calc_custom_useragent($user_agent, $url)
{
  $user_agent =
    'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.106 Safari/537.36';

  return $user_agent;
}
