<?php
/*
Plugin Name: Cell Helper
Plugin URI: http://google.com
Description: Helper functions for common task
Version: 0.0.1
Author: Dion
Author URI: http://google.com
Author Email: ifdion@gmail.com
License:

	Copyright 2013 (ifdion@gmail.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

//set constant values
define( 'CELL_HELPER_FILE', __FILE__ );
define( 'CELL_HELPER', dirname( __FILE__ ) );
define( 'CELL_HELPER_PATH', plugin_dir_path(__FILE__) );
define( 'CELL_HELPER_TEXT_DOMAIN', 'cell-frontend' );

// set for internationalization
function cell_helper_init() {
	load_plugin_textdomain('cell-frontend', false, basename( dirname( __FILE__ ) ) . '/lang' );
}
add_action('plugins_loaded', 'cell_helper_init');


/* session
---------------------------------------------------------------
*/

	if (!session_id()) {
		session_start();
	}

/* global 
---------------------------------------------------------------
*/

	include_once ('common-functions.php');

/* common hooks : limit wp-admin access to adminstrator
---------------------------------------------------------------
*/

	add_action('admin_init','administrator_only');

/* common hooks : disable admin bar
---------------------------------------------------------------
*/

	add_filter( 'show_admin_bar' , 'disable_admin_bar');


/* setup global message
---------------------------------------------------------------
*/
	add_action( 'init', 'setup_global_message');