<?php
/**
 * Настройки для ACF
 *
 * @package      RKOCoreFunctionality
 * @author       Yuriy Lysyuk
 * @since        
 **/

// Изменяем расположение сохранения настроек ACF Local JSON в /acf папку внутри этого плагина
// ToDo кроме нужных настроек, сохраняются и все остальные, не относящиеся к плагину
add_filter('acf/settings/save_json', function () {
  return YL_DIR . '/rko-calc/acf';
});

// Включаем /acf папку в список мест, в которых ищутся файлы ACF Local JSON
add_filter('acf/settings/load_json', function ($paths) {
  $paths[] = YL_DIR . '/rko-calc/acf';
  return $paths;
});