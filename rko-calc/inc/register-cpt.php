<?php
/**
 * Компонент для регистрации произвольных типов записей для калькулятора
 *
 * @package      RKOCoreFunctionality
 * @author       Yuriy Lysyuk
 * @since        1.0.0
 **/

/**
 * Регистрируем произвольный тип записи для банков
 *
 */
function register_cpt_rko_banks()
{
  $labels = array(
    'name' => 'Банки',
    'singular_name' => 'Банк',
    'add_new' => 'Добавить новый',
    'add_new_item' => 'Добавить новый банк',
    'edit_item' => 'Редактировать банк',
    'new_item' => 'Новый банк',
    'new_item' => 'Все банки',
    'view_item' => 'Посмотреть банк',
    'search_items' => 'Искать банки',
    'not_found' => 'Банки не найдены',
    'not_found_in_trash' => 'В корзине нет банков',
    'menu_name' => 'Банки РКО'
  );

  $args = array(
    'labels' => $labels,
    'publicly_queryable' => false,
    'exclude_from_search' => true,
    'show_ui' => true,
    'show_in_menu' => true,
    'show_in_rest' => true,
    'menu_icon' => 'dashicons-groups',
    'supports' => array('title'),
    'has_archive' => false,
    'show_in_nav_menus' => false,
    'query_var' => false,
    'can_export' => true,
    'rewrite' => false
  );

  register_post_type('banks', $args);
}

add_action('init', 'register_cpt_rko_banks');

/**
 * Регистрируем произвольный тип записи для тарифов РКО
 *
 */
function register_cpt_rko_tariffs()
{
  $labels = array(
    'name' => 'Тарифы РКО',
    'singular_name' => 'Тариф РКО',
    'add_new' => 'Добавить новый',
    'add_new_item' => 'Добавить новый тариф РКО',
    'edit_item' => 'Редактировать тариф РКО',
    'new_item' => 'Новый тариф РКО',
    'new_item' => 'Все тарифы РКО',
    'view_item' => 'Посмотреть тариф РКО',
    'search_items' => 'Искать тарифы РКО',
    'not_found' => 'Тарифы РКО не найдены',
    'not_found_in_trash' => 'В корзине нет тарифов РКО',
    'menu_name' => 'Тарифы РКО'
  );

  $args = array(
    'labels' => $labels,
    'publicly_queryable' => false,
    'exclude_from_search' => true,
    'show_ui' => true,
    'show_in_menu' => true,
    'show_in_rest' => true,
    'menu_icon' => 'dashicons-chart-bar',
    'supports' => array('title'),
    'has_archive' => false,
    'show_in_nav_menus' => false,
    'query_var' => false,
    'can_export' => true,
    'rewrite' => false
  );

  register_post_type('tariffs', $args);
}

add_action('init', 'register_cpt_rko_tariffs');
