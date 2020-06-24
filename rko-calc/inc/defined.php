<?php
/**
 * Константы
 *
 * @package      RKOCoreFunctionality
 * @author       Yuriy Lysyuk
 * @since        1.3.0
 **/

// Путь к JSON файлу со статическими данными по тарифам
define(
  'JSON_ALL_TARIFF_OPTIONS_PATH',
  YL_DIR . 'rko-calc/acf-data/all-tariff-options.json'
);

// Путь к маркерному файлу, который показывает необходимость обновить JSON файл с данными по тарифам
define(
  'JSON_ALL_TARIFF_OPTIONS_NEED_UPDATE_PATH',
  YL_DIR . 'rko-calc/acf-data/all-tariff-options-need-update'
);

$uploadDir = wp_get_upload_dir();

// Директория, где храняться файлы тарифов
define('TARIFF_DOCS_UPLOAD_DIR', $uploadDir['basedir'] . '/rko-tariff-docs/');

// URL, где доступны файлы тарифов
define('TARIFF_DOCS_UPLOAD_URL', $uploadDir['baseurl'] . '/rko-tariff-docs/');
