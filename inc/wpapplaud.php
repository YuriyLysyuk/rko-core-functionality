<?php
/**
 * WPApplaud
 *
 * @package      RKOCoreFunctionality
 * @author       Yuriy Lysyuk
 * @since        1.1.0
 **/

/**
 * Добавляем функционал аплодиментов в мета данные после статьи
 *
 */
function rkocf_wpapplaud()
{
  if (function_exists('wp_applaud')) {
    global $post;

    // Скрываем вывод аплодисментов, если он отключен в админке
    $applaudExclude = get_post_meta($post->ID, '_wp_applaud_exclude', true);
    if ($applaudExclude) {
      return;
    }

    echo '<span class="wp-applaud-wrap">';
    wp_applaud();
    echo '</span>';
  }
}
add_action('genesis_entry_footer', 'rkocf_wpapplaud');
