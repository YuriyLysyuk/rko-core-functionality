<?php
/**
 * Plugin Name: RKO Core Functionality
 * Description: This contains all your site's core functionality so that it is theme independent. <strong>It should always be activated</strong>.
 * Version:     1.2.0
 * Author:     Yuriy Lysyuk
 * Bitbucket Plugin URI: https://bitbucket.org/lysyuk-y/rko-core-functionality
 *
 * @package    RKOCoreFunctionality
 * @since      1.2.0
 * @copyright  Copyright (c) 2019, Yuriy Lysyuk
 */

// Plugin directory
define('YL_DIR', plugin_dir_path(__FILE__));

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
