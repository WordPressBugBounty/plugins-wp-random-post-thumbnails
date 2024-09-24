<?php
/*
Plugin Name: WP Random Post Thumbnails
Plugin URI: https://wordpress.org/plugins/wp-random-post-thumbnails/
Description: Allows you to select images to be shown at random for posts without a featured image. Useful if your theme shows thumbnails for the posts, and you don't want any posts without images.
Version: 2.5.0
Author: Brianna Deleasa
Author URI: http://www.briannadeleasa.com
License: GPL v3

WP Random Post Thumbnails
Copyright (C) 2014 Brianna Deleasa

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/


// Define some constants
define( 'WPRPT_PLUGIN_FILE', __FILE__ );
define( 'WPRPT_PLUGIN_BASENAME', plugin_basename( WPRPT_PLUGIN_FILE ) );


// Require our includes and classes
require_once 'includes/template-tags.php';
require_once 'classes/class-wprpt-options.php';
require_once 'classes/class-wprpt.php';


add_action( 'init', 'wprpt_init', 20 );
/**
 * Start up the main plugin class that controls all of the functionality for
 * this plugin.
 *
 * @since 1.0.0
 *
 * @param none
 * @return null
 */
function wprpt_init() {

    WPRPT::init();

}


add_action( 'init', 'wprpt_initialize_cmb_init', 10 );
/**
 * Includes the necessary CMB init file.
 *
 * @since 1.0.2
 *
 * @param none
 * @return null
 */
function wprpt_initialize_cmb_init() {

	if ( ! defined( 'CMB2_LOADED' ) ) {
		if ( file_exists(  __DIR__ . '/includes/cmb2/init.php' ) ) {
			require_once  __DIR__ . '/includes/cmb2/init.php';
		} elseif ( file_exists(  __DIR__ . '/includes/CMB2/init.php' ) ) {
			require_once  __DIR__ . '/includes/CMB2/init.php';
		}
	}

	if ( ! function_exists( 'rnl_autoloader' ) ) {
		require_once __DIR__ . '/includes/cmb2-metatabs-options/cmb2_metatabs_options.php';
	}

}