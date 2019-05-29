<?php
/**
 * Plugin Name: Core Functionality
 * Description: This contains all your site's core functionality so that it is theme independent. <strong>It should always be activated</strong>.
 * Version:     1.0.0
 * Author:     Yuriy Lysyuk
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License version 2, as published by the
 * Free Software Foundation.  You may NOT assume that you can use any other
 * version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.
 *
 * @package    CoreFunctionality
 * @since      1.0.0
 * @copyright  Copyright (c) 2019, Yuriy Lysyuk
 * @license    GPL-2.0+
 */

// Plugin directory
define( 'YL_DIR' , plugin_dir_path( __FILE__ ) );

//require_once( YL_DIR . '/inc/general.php' );
//require_once( YL_DIR . '/inc/wordpress-cleanup.php' );
//require_once( YL_DIR . '/inc/kill-trackbacks.php' );
//require_once( YL_DIR . '/inc/login.php' );
require_once( YL_DIR . '/inc/custom-html-widget.php' );
//require_once( YL_DIR . '/inc/custom-toc.php' );