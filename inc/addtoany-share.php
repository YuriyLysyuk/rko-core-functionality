<?php
/**
 * AddToAny Share
 *
 * @package      RKOCoreFunctionality
 * @author       Yuriy Lysyuk
 * @since        1.1.0
 **/

/**
 * Добавляем кнопки шаринга в мета данные после статьи
 *
 */

function rkocf_addtoany_share_kit()
{
  if (function_exists('ADDTOANY_SHARE_SAVE_KIT')) {
    global $post;

    // Скрываем вывод иконок шаринга, если они отключены в админке
    $sharing_disabled = get_post_meta($post->ID, 'sharing_disabled', true);
    if ($sharing_disabled) {
      return;
    }

    // Если добавляем шорткод на страницу с калькулятором
    if (has_shortcode($post->post_content, "rko_calc")) {
      // Добавляем в ссылку для шаринга всю строку с параметрами
      ADDTOANY_SHARE_SAVE_KIT([
        'linkurl' => esc_url_raw(home_url($_SERVER['REQUEST_URI'])),
      ]);
    } else {
      // Иначе выводим шаринг со стандартными параметрами
      ADDTOANY_SHARE_SAVE_KIT();
    }
  }
}
add_action('genesis_entry_footer', 'rkocf_addtoany_share_kit', 12);
