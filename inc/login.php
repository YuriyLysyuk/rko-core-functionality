<?php
/**
 * Login
 *
 * @package      CoreFunctionality
 * @author       Yuriy Lysyuk
 * @since        1.0.0
 * @license      GPL-2.0+
**/

/**
 * Shows less information to users when error login.
 *
 * @since  1.0.0
 */
function etidni_show_less_login_info() {
  return '<strong>Ошибка</strong>: неверный логин или пароль!';
}
add_filter( 'login_errors', 'etidni_show_less_login_info' );