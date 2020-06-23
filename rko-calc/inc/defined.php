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

// Директория, где храняться файлы тарифов
define('TARIFF_DOCS_UPLOAD_DIR', YL_DIR . 'rko-calc/uploads/');

// URL, где доступны файлы тарифов
define('TARIFF_DOCS_UPLOAD_URL', YL_URL . 'rko-calc/uploads/');
