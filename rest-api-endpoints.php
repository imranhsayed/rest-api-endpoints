<?php
/**
 * Plugin Name: REST API ENDPOINTS
 * Plugin URI: https://github.com/imranhsayed/rest-api-endpoints
 * Description: This plugin provides you different endpoints using WordPress REST API
 * Version: 1.0.0
 * Author: Imran Sayed
 * Author URI: https://codeytek.com
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: rest-api-endpoints
 * Domain Path: /languages
 *
 * @package WordPress Contributors
 */

// Define Constants.
define( 'RPE_URI', plugins_url( 'rest-api-endpoints' ) );
define( 'RPE_TEMPLATE_PATH', plugin_dir_path( __FILE__ ) . 'templates/' );
define( 'RPE_PLUGIN_PATH', __FILE__ );

// File Includes
include_once 'apis/class-rae-register-auth-api.php';
include_once 'apis/class-rae-register-posts-api.php';
