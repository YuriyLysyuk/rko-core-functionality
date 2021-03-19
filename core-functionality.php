<?php

/**
 * Plugin Name: RKO Core Functionality
 * Description: This contains all your site's core functionality so that it is theme independent. <strong>It should always be activated</strong>.
 * Version:     1.3.29
 * Author:     Yuriy Lysyuk
 * Bitbucket Plugin URI: https://bitbucket.org/lysyuk-y/rko-core-functionality
 *
 * @package    RKOCoreFunctionality
 * @since      1.3.29
 * @copyright  Copyright (c) 2019, Yuriy Lysyuk
 */

defined('ABSPATH') || exit();

// Версия плагина
define('RKOCF_VER', '1.3.29');
// Plugin directory
define('YL_DIR', plugin_dir_path(__FILE__));
// Plugin URL
define('YL_URL', plugin_dir_url(__FILE__));

require_once YL_DIR . '/inc/helpers.php';
require_once YL_DIR . '/inc/general.php';
require_once YL_DIR . '/inc/wordpress-cleanup.php';
require_once YL_DIR . '/inc/kill-trackbacks.php';
require_once YL_DIR . '/inc/seo.php';
require_once YL_DIR . '/inc/custom-toc.php';
require_once YL_DIR . '/inc/addtoany-share.php';
require_once YL_DIR . '/inc/wpapplaud.php';
//require_once( YL_DIR . '/inc/login.php' );
//require_once( YL_DIR . '/inc/custom-html-widget.php' );

// Калькулятор РКО
require_once YL_DIR . '/rko-calc/rko-calc.php';

// Действия при активации плагина
register_activation_hook(__FILE__, 'activation_plugin');
function activation_plugin()
{
	// Удаляем все ранее добавленные задания для проверки изменений тарифов РКО
	wp_clear_scheduled_hook('rko_check_update_tariffs_doc');
	// Добавляем задание для проверки изменений тарифов РКО дважды в день
	wp_schedule_event(time(), 'twicedaily', 'rko_check_update_tariffs_doc');
}

// Дейтвия при деактивации плагина
register_deactivation_hook(__FILE__, 'deactivation_plugin');
function deactivation_plugin()
{
	// Удаляем все ранее добавленные задания для проверки изменений тарифов РКО
	wp_clear_scheduled_hook('rko_check_update_tariffs_doc');
}
