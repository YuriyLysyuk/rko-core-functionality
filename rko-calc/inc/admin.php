<?php
/**
 * Функции для админки
 *
 * @package      RKOCoreFunctionality
 * @author       Yuriy Lysyuk
 * @since        1.3.0
 **/

/**
 * Добавляем необходимые колонки в список тарифов
 *
 */
function add_acf_columns_to_tariffs_list($columns)
{
  $tariffColumns = [
    'bank' => 'Банк',
    'date' => 'Дата'
  ];
  unset($columns['date']);
  return $columns + $tariffColumns;
}

add_filter(
  'manage_tariffs_posts_columns',
  'add_acf_columns_to_tariffs_list',
  20
);

/**
 * Заполняем колонки в списке тарифов
 *
 */
function tariffs_custom_column($column, $post_id)
{
  switch ($column) {
    case 'bank':
      echo esc_html(get_the_title(get_post_meta($post_id, 'bank', true)));
      break;
  }
}
add_action(
  'manage_tariffs_posts_custom_column',
  'tariffs_custom_column',
  10,
  2
);
